<?php
declare(strict_types=1);
require_once __DIR__."/vendor/autoload.php";

use MVQN\Localization\Translator;
use MVQN\REST\RestClient;


use UCRM\Common\Config;
use UCRM\Common\Log;
use UCRM\Common\Plugin;

use UCRM\Plugins\Settings;

/**
 * bootstrap.php
 *
 * A common configuration and initialization file.
 *
 * @author Ryan Spaeth <rspaeth@mvqn.net>
 */

// IF there is a /.env file, THEN load it!
if(file_exists(__DIR__."/../.env"))
    (new \Dotenv\Dotenv(__DIR__."/../"))->load();

// Initialize the Plugin libraries using this directory as the plugin root!
Plugin::initialize(__DIR__);

// Regenerate the Settings class, in case anything has changed in the manifest.json file.
Plugin::createSettings("UCRM\\Plugins");

// Generate the REST API URL from either a .env file, environment variable or the Plugin Settings.
//$restUrl = rtrim(getenv("UCRM_REST_URL_DEV") ?: Settings::UCRM_PUBLIC_URL, "/")."/api/v1.0";
$restUrl = rtrim(getenv("UCRM_REST_URL_DEV") ?: Settings::UCRM_LOCAL_URL, "/")."/api/v1.0";

if(Settings::getDevelopment())
{
    Log::debug("Using REST URL: $restUrl");
    Log::debug("Using REST KEY: " . Settings::PLUGIN_APP_KEY);
}

try
{
    // Configure the REST Client...
    RestClient::setBaseUrl($restUrl);
    RestClient::setHeaders([
        "Content-Type: application/json",
        "X-Auth-App-Key: " . Settings::PLUGIN_APP_KEY
    ]);

}
catch(Exception $e)
{
    Log::error($e->getMessage());
}

// Set the dictionary directory and "default" locale.
try
{
    Translator::setDictionaryDirectory(__DIR__."/translations/");
    Translator::setCurrentLocale(str_replace("_", "-", Config::getLanguage()) ?: "en-US", true);
}
catch (\MVQN\Localization\Exceptions\TranslatorException $e)
{
    Log::http("The locale '".Config::getLanguage()."' is not currently supported!\n{$e->getMessage()}", 500);
    die($e->getMessage());
}
