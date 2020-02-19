<?php

require_once('config.php');

$url_root   = API_URL;
$org_id     = ORGANIZATION_ID;
$api_key    = API_KEY;
$api_secret = API_SECRET_KEY;

$curl = curl_init();
//curl_setopt($curl, CURLOPT_VERBOSE, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_URL, $url_root."/api/v2/time");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
curl_close($curl);

$time = json_decode($result, true)['serverTime'];
echo "server time: ".$time."\n<br>";

//do auth request
$nonce     = uniqid();
$path      = "/main/api/v2/mining/info";
//$path      = "/main/api/v2/mining/rigs/";
$signature = $api_key."\x00".$time."\x00".$nonce."\x00"."\x00".$org_id."\x00"."\x00"."GET"."\x00".$path."\x00";
$signhash  = hash_hmac('sha256', $signature, $api_secret);

$headers = array(
	"X-Time: {$time}",
	"X-Nonce: {$nonce}",
	"X-Organization-Id: {$org_id}",
	"X-Request-Id: {$nonce}",
	"X-Auth: {$api_key}:{$signhash}",
);

$curl = curl_init();
//curl_setopt($curl, CURLOPT_VERBOSE, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_URL, $url_root.$path);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($curl);
curl_close($curl);

$info = json_decode($result, true);
print_r($info);
?>