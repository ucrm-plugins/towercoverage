<?php
declare(strict_types=1);
require_once __DIR__."/vendor/autoload.php";

use UCRM\Plugins\Data\TowerCoverage;
use UCRM\Plugins\Data\CustomerDetails;
use UCRM\Plugins\Data\CustomerLinkInfo;
use UCRM\Plugins\Data\Coverage;

//$data = json_decode(json_encode(simplexml_load_string(file_get_contents(__DIR__."/examples/push-data.xml"))), true);
//$data = simplexml_load_string(file_get_contents(__DIR__."/examples/push-data.xml"));

//print_r($data);
//echo $data->CustomerDetails->CustomerLinkInfo->coverage[0]->ImageData."\n";

$xml = file_get_contents(__DIR__."/examples/push-data.xml");
//$coverageData = $data["CustomerDetails"]["CustomerLinkInfo"]["coverage"][0];
//$coverageData = $data->{"CustomerDetails"}->{"CustomerLinkInfo"}->{"coverage"}[0];

$json = json_encode(new \SimpleXMLElement($xml, LIBXML_NOCDATA));
$data = json_decode($json, true);



//$coverage = new Coverage($data["CustomerDetails"]["CustomerLinkInfo"]["coverage"][0]);
//print_r($coverage);

//$customerLinkInfo = new CustomerLinkInfo($data["CustomerDetails"]["CustomerLinkInfo"]);
//print_r($customerLinkInfo);

//$customerDetails = new CustomerDetails($data["CustomerDetails"]);
//print_r($customerDetails);

$towerCoverage = new TowerCoverage($data);
print_r($towerCoverage);