<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require("openepicphp.php");

$epicID = "EPICID";
$epicSecret = "SECRECTCODE";
$redirectURL = urlencode("MyServer.com");

$epic = new OpenEpicPHP($epicID, $epicSecret);

$epic->authenticate($redirectURL);
//$response = $epic->addBloodPressure(120,80,55,"2013-12-31T17:25:47Z","Upper Arm");
$response = $epic->addGlucose(7.1,"mmol/L","breakfast","before","2013-12-31T17:25:47Z");
//$response = $epic->addWeight(190, "[lb_av]","2013-12-31T17:25:47Z");
var_dump($response);
exit;
