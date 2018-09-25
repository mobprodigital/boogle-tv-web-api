<?php
include "../includes/config.php";
include "../includes/functions.php";

$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	$client_id = mysqli_real_escape_string($link,isset($req_data['id'])) ? mysqli_real_escape_string($link,trim($req_data['id'])) :'';
	$response = array();

	if(empty($client_id) || $client_id == null)
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="id Parameter Missing.";
	$response['data']= $video_data;
	}
	elseif (!is_numeric($client_id))
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="Allowed only numbers in Id Parameter";
	$response['data']= $video_data;
	}
	else
	{
		$data = getVideosByClientID($client_id,$start,$count,$link);
		
		wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
		if(!empty($data))
		{
		$response['status']=true;
		$response['message']="Success";
		$response['data'] = $data;	
		} else {
		$data = array();
		$response['status']=false;
		$response['message']="No Videos Found.";
		$response['data'] = $data;	
		} 
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>


