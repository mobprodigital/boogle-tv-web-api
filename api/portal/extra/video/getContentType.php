<?php
include "../../../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$sql = "SELECT * from portals where name";
	
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,true);
?>


