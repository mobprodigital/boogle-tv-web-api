<?php
include "../../includes/config.php";

$result = deleteCookie();
if($result)
{
	$response['status']=true;
	$response['message']= "Success";
	$response['data'] = "";
}
else
{
	$response['status']=true;
	$response['message']= "Failure";
	$response['data'] = "";
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>