<?php
session_start();
//echo "hello";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$epicID = "Rml0Qml0UmlpczpyaWlzLmNvbTo0MzUyMDg2MQ";
$epicSecret = "MTY1NzcwNzc4IC0gSGkhIFRoaXMgaXNuJ3QgYSByZWFsIHNlY3JldCB5ZXQ7IDE2NTc3MDc3OCBpcyBqdXN0IGEgY2hlY2tzdW0u";
$authURL = "https://open-ic.epic.com/api/v0.1/OAuth2/Authorize";
$redirectURL = urlencode("http://ec2-52-1-5-92.compute-1.amazonaws.com/epic.php");
$tokenReqURL = "https://open-mc.epic.com/Authentication/OAuth/v2?scope=writeClinicalDataFromPatient&redirect_uri=".$redirectURL."&client_id=".$epicID."&response_type=code";
$bloodPressuerURL = "https://open-ic.epic.com/api/v0.1/bloodpressure";
$refreshURL = "https://open-ic.epic.com/api/v0.1/OAuth2/Refresh";

if(!isset($_GET["code"])){
	header("Location:".$tokenReqURL);
	exit;
}

//GET ACCESS TOKENS
if(!isset($_SESSION["auth_token"])){
	$fields_string = "";
	$fields = array(
	            'authorizationCode'=>urlencode($_GET["code"]),
	            'clientID'=>urlencode($epicID),
	            'clientSecret'=>urlencode($epicSecret)
	        );

	$fields_string = json_encode($fields);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $authURL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);

	$response = json_decode($response);

	$_SESSION["auth_token"] = $response->authorizationToken;
	$_SESSION["epic_token"] = $response->accessToken;
}else{
	$fields_string = "";
	$fields = array(
	            'authorizationToken'=>$_SESSION["auth_token"],
	            'clientID'=>$epicID,
	            'clientSecret'=>$epicSecret
	        );

	$fields_string = json_encode($fields);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $refreshURL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch);
	curl_close($ch);

	$response = json_decode($response);

	$_SESSION["epic_token"] = $response->accessToken;
}

//update bp
$fields_string = "";
	$fields = array(
	            "Reading"=>array(
	            	"Systolic"=>120,
		            "Diastolic"=> 80,
		            "Pulse"=> 55,
		            "InstantTaken"=> "2013-12-31T17:25:47Z",
		            "Location"=> "upper arm"
	           	)
	        );

	$fields_string = json_encode($fields);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $bloodPressuerURL);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Authorization: bearer '.$_SESSION["epic_token"]));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response  = curl_exec($ch);
	curl_close($ch);
	var_dump($response);
	