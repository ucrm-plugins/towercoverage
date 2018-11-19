<?php
declare(strict_types=1);
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__."/bootstrap.php";


use UCRM\Common\Config;
use UCRM\Common\Log;
use UCRM\Common\LogEntry;
use UCRM\Plugins\Settings;

use UCRM\Plugins\Data\TowerCoverage;

use UCRM\REST\Endpoints\Client;
use UCRM\REST\Endpoints\ClientContact;
use UCRM\REST\Endpoints\ClientLog;
use UCRM\REST\Endpoints\Country;
use UCRM\REST\Endpoints\State;

/**
 * public.php
 *
 * Handles XML push data received from TowerCoverage.com.
 *
 * Use an immediately invoked function here to prevent pollution of the global namespace.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */
(function()
{
    // Store the payload received from TowerCoverage.com.
    $dataRaw = file_get_contents("php://input");

    // -----------------------------------------------------------------------------------------------------------------
    // DEVELOPMENT MODE...
    // -----------------------------------------------------------------------------------------------------------------

    // IF no payload has been received...
    if(!$dataRaw)
    {
        // ...AND the Plugin is in Development mode...
        if(Settings::getDevelopment())
        {
            // THEN load some example data before continuing!
            $dataRaw = file_get_contents(__DIR__ . "/examples/push-data.xml");
            Log::debug("Using example push data, as no valid payload was received and 'Development?' is enabled.");
        }
        else
        {
            // OTHERWISE, return an HTTP 400 - Bad Request!
            Log::http("No TowerCoverage data was received!", 400);
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // PAYLOAD PARSING
    // -----------------------------------------------------------------------------------------------------------------

    // Attempt to parse the XML payload.
    try
    {
        // NOTE: The XML data must be JSON encoded and then decoded to an associative array for the parser to succeed!
        $json = json_encode(new \SimpleXMLElement($dataRaw, LIBXML_NOCDATA));
        $data = json_decode($json, true);

        // IF Verbose Logging is enabled, THEN log the data as JSON.
        if(Settings::getVerboseLogging())
            Log::info("RECEIVED: ".$json);

        // Attempt to convert the payload to a TowerCoverage object.
        $towerCoverage = new TowerCoverage($data);

        // Get the CustomerDetails section, as that is the most useful here!
        $customerDetails = $towerCoverage->getCustomerDetails();

        // -------------------------------------------------------------------------------------------------------------
        // PRE-CHECKS
        // -------------------------------------------------------------------------------------------------------------

        if (Settings::getApiKey() !== null && Settings::getApiKey() !== $customerDetails->getApiKey())
            Log::http("API Key does not match the one set in this Plugin's Settings.", 401);

        if (Settings::getApiUsername() !== null && Settings::getApiUsername() !== $customerDetails->getUsername())
            Log::http("API Username does not match the one set in this Plugin's Settings.", 401);

        if (Settings::getApiPassword() !== null && Settings::getApiPassword() !== $customerDetails->getPassword())
            Log::http("API Password does not match the one set in this Plugin's Settings.", 401);

        Log::info("Valid TowerCoverage data has been received!");

        // IF the Plugin is in Development mode, THEN dump the XML data to a file for later inspection!
        if(Settings::getDevelopment())
        {
            $file = __DIR__ . "/data/dumps/" . (new DateTimeImmutable())->format("Y-m-d_His.u") . ".xml";
            file_put_contents($file, $dataRaw);
            Log::debug("TowerCoverage XML data has been dumped to file '$file', due to being in Development mode.");
        }

        // -------------------------------------------------------------------------------------------------------------
        // CLIENT HANDLING
        // -------------------------------------------------------------------------------------------------------------

        /** @var Client|null $existingClient */
        $existingClient = null;

        // Handle Duplicate Modes here...
        switch (Settings::getDuplicateMode())
        {
            // Primary Email
            case "email":
                $existingResidentialLeads = Client::getLeadsOnly()->whereAll([
                    "clientType" => Client::CLIENT_TYPE_RESIDENTIAL
                ]);

                /** @var Client $client */
                foreach ($existingResidentialLeads as $client)
                {
                    /** @var ClientContact|null $contact */
                    $contact = $client->getContacts()->first();

                    if ($contact !== null && $contact->getEmail() === $customerDetails->getEmailAddress())
                    {
                        $existingClient = $client;
                        break;
                    }
                }

                break;

            // Street Address
            case "street":
                $existingResidentialLeads = Client::getLeadsOnly()->whereAll([
                    "clientType" => Client::CLIENT_TYPE_RESIDENTIAL
                ]);

                /** @var Client $client */
                foreach ($existingResidentialLeads as $client)
                {
                    $street = $client->getStreet1();

                    if ($street !== null && $street !== "" && $street === $customerDetails->getStreetAddress())
                    {
                        $existingClient = $client;
                        break;
                    }
                }
                break;

            // First & Last Names (DEFAULT)
            case "":
            default:
                $existingResidentialLeads = Client::getLeadsOnly()->whereAll([
                    "clientType" => Client::CLIENT_TYPE_RESIDENTIAL,
                    "firstName" => $customerDetails->getFirstName(),
                    "lastName" => $customerDetails->getLastName(),
                ]);

                $existingClient = $existingResidentialLeads->first();
                break;
        }

        // Set a bool flag indicating whether or not an existing Client Lead was found.
        $clientExists = $existingClient !== null;

        // And then assign either the existing Client Lead or a newly created one...

        /** @var Client $client */
        $client = $clientExists ? $existingClient : Client::createResidentialLead("", "");

        // NOTE: All Client Leads are currently created with a type of "Residential", as there is no easy way to
        // determine otherwise from the TowerCoverage EUS Form.

        // Get the Country and then State for later use.
        $country = Country::getByName($customerDetails->getCountry());
        $state = State::getByName($country, $customerDetails->getState());

        // NOTE: <Comment> comes through as a collapsed node when empty, so this has to handle an empty array or string!
        // Get any included comments for Client notes.
        $note = $customerDetails->getComments() === [] ? "" : $customerDetails->getComments();

        // Create/Update the Client's information.
        $client
            ->setFirstName($customerDetails->getFirstName())
            ->setLastName($customerDetails->getLastName())
            ->setAddress(
                $customerDetails->getStreetAddress(),
                $customerDetails->getCity(),
                $state->getCode(),
                $country->getCode(),
                $customerDetails->getZip())
            ->setAddressGpsLat($customerDetails->getCustomerLat())
            ->setAddressGpsLon($customerDetails->getCustomerLong())
            ->setNote($note);

        // Attempt to insert/update the Client Lead in the UCRM...

        /** @var Client $upsertedClient */
        $upsertedClient = ($clientExists) ? $client->update() : $client->insert();

        // NOTE: In the case of a non-existent Client Lead, the insertion MUST be done first to get a valid Client ID
        // which will be needed when inserting the new Contact information.

        // Get any existing Contacts from the upserted Client Lead.
        $contacts = $upsertedClient->getContacts();

        // Set a bool flag indicating whether or not an existing set of Contacts was found.
        $contactExists = ($contacts->count() !== 0);

        // And then assign either the existing primary Contact or a newly created one...

        /** @var ClientContact $contact */
        $contact = ($contacts->count() === 0 ?
            new ClientContact(["clientId" => $upsertedClient->getId()]) : $contacts->first())
            ->setName($customerDetails->getFirstName() . " " . $customerDetails->getLastName())
            ->setEmail($customerDetails->getEmailAddress())
            ->setPhone($customerDetails->getPhoneNumber());

        // Attempt to insert/update the Contact in the UCRM...

        /**
         * @noinspection PhpUnusedLocalVariableInspection
         * @var ClientContact $upsertedContact
         */
        $upsertedContact = ($contacts->count() === 0) ? $contact->insert() : $contact->update();

        // NOTE: In the case of a non-existent Contact, the insertion MUST be done first to get a valid Contact ID
        // which will be needed when inserting the new Client Log information.

        // Create a new Client Log entry and set it's timestamp to NOW.
        $log = new ClientLog([ "clientId" => $upsertedClient->getId() ]);
        $log->setCreatedDate(new DateTime());

        // IF the Client Lead already existed...
        if ($clientExists)
        {
            // THEN generate and insert a Client Log message indicating the Client Lead was updated.
            $message = "Client".($contactExists ? " & Contact" : "")." Updated by TowerCoverage EUS Submission!";
            $log->setMessage($message)->insert();

            // Return HTTP 200 - OK
            Log::http($message, 200);
        }
        else
        {
            // OTHERWISE generate and insert a Client Log message indicating the Client Lead was created.
            $message = "Client".(!$contactExists ? " & Contact" : "")." Created by TowerCoverage EUS Submission!";
            $log->setMessage($message)->insert();

            // Return HTTP 201 - Created
            Log::http($message, 201);
        }
    }
    catch(\Exception $e)
    {
        // When an Exception is caught, Log the error and return HTTP 400 - Bad Request!
        Log::http("Invalid TowerCoverage.com data received: ".$e->getMessage(), 400);
    }

    // WE SHOULD NEVER REACH THIS LINE!!!

})();
