<?php



$amb_agent = '89uw02'; //แก้ไขได้

$amb_clientname = 'AQUABET'; //แก้ไขได้

$amb_key = 'f80035fc10d620c4a07d74568d0b148c'; //แก้ไขได้

$amb_hash = "621344503ee4d8002289cd7c"; //แก้ไขได้

$amb_api_endpoint = 'https://topup-aquabet.askmebet.io'; //แก้ไขได้ 



function GetSignatureAMB($fields) 

{

	$signature = md5($fields);

	return $signature;

}

?>