<?php
declare(strict_types=1);
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__."/bootstrap.php";

use MVQN\REST\UCRM\Endpoints\WebhookEvent;

use MVQN\UCRM\Plugins\Log;
use UCRM\Plugins\Config;

use UCRM\Plugins\Settings;

use UCRM\Plugins\Data\TowerCoverage;

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
    // Parse the input received from TowerCoverage.com.
    $dataRaw = file_get_contents("php://input");
    //Log::write("RECEIVED: ".$data);

    // Parse the XML payload into an object for further handling.
    $json = json_encode(new \SimpleXMLElement($dataRaw, LIBXML_NOCDATA));
    $data = json_decode($json, true);

    $towerCoverage = new TowerCoverage($data);
    //print_r($towerCoverage);

    Log::writeObject($towerCoverage);











    http_response_code(200);



})();
