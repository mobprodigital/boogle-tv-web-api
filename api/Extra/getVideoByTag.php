<?php
include "../includes/config.php";
include "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	$video_tag = mysqli_real_escape_string($link,isset($req_data['tag'])) ? mysqli_real_escape_string($link,trim($req_data['tag'])) :'';
	$response = array();

	if(empty($video_tag) || $video_tag == null)
	{
	$data = array();
	$response['status']=false;
	$response['message']="tag parameter is missing.";
	$response['data'] = $data;
	}
	elseif(!preg_match("/^[a-zA-Z]+$/", $video_tag))
	{
	$data = array();
	$response['status']=false;
	$response['message']="Allowed only alphabets in Tag Field";
	$response['data'] = $data;	
	}
	else
	{
		$data = getVideosByTag(trim($video_tag),$start,$count,$link);
		wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
		
		if(!empty($data))
		{
		$response['status']=true;
		$response['message']="Success";
		$response['data'] = $data;	
		} else {
		$data = array();
		$response['status']=false;
		$response['message']="No Videos Found For This Tag";
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


