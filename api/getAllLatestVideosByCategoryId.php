<?php
include "../includes/config.php";
include "../includes/functions.php";

$response = array();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	if(empty($req_data['id'])) 
	{
		// Get Array Of Most Recent/Latest Videos From All Categories
		$data = getAllLatestVideos($start,$count);
	}
	else
	{
		$value = check_array_values($req_data['id']);
		wh_log("Array contains integer values result : ".$value);
		if($value)
		{
			// Array have all integers value.
			// Get Array Of Most Recent/Latest Videos By Category Id
			$data = getMostLatestVideosByCategoryID($req_data['id'],$start,$count);
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Id parameter should be numeric.";
			$response['data'] = $data;
		}
	}
	usort($data, 'sortByRecent');
	wh_log("Sorted Final Latestb Video Array : ".str_replace("\n"," ", print_r($data, true)));
	if(!empty($data))
	{
	$response['status']=true;
	$response['message']="Success";
	$response['data'] = $data;	
	} else {
	$data = array();
	$response['status']=false;
	$response['message']="No Videos Found For This Category.";
	$response['data'] = $data;	
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_NUMERIC_CHECK);
?>