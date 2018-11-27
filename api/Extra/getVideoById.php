<?php
include "../includes/config.php";
include "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$video_id = mysqli_real_escape_string($link,isset($req_data['id'])) ? mysqli_real_escape_string($link,trim($req_data['id'])) :'';
	$response = array();

	if(empty($video_id) || $video_id == null)
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="id Parameter Missing.";
	$response['data']= $video_data;
	}
	elseif (!is_numeric($video_id))
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="Allowed only numbers in Id Parameter";
	$response['data']= $video_data;
	}
	else
	{
		//Increase View Count 
		$updateList = "update `videos` set view = view+1 where id = ?";
		$stmt = mysqli_prepare($link, $updateList);
		$vid = $video_id;
		mysqli_stmt_bind_param($stmt,'i', $vid);
		$status = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		// Ends
		
		$video_data = getVideosByID($video_id,$link);
		wh_log("Final Array : ".str_replace("\n"," ", print_r($video_data, true)));
		
		if(!empty($video_data))
		{
		$response['status']=true;
		$response['message']="Success";
		$response['data'] = $video_data;
		} 
		else 
		{
		$video_data = array();
		$response['status']=false;
		$response['message']="No Video Found.";
		$response['data'] = $video_data;
		}
		
	}
}
else
{
	/* $response['status']=false;
	$response['message']="No Page Found."; */
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));

echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>


