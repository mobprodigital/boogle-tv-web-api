<?php
include "../includes/config.php";
include "../includes/functions.php";

$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$video_id = mysqli_real_escape_string($link,isset($req_data['vid'])) ? mysqli_real_escape_string($link,trim($req_data['vid'])) :'';
	$cat_id = mysqli_real_escape_string($link,isset($req_data['cid'])) ? mysqli_real_escape_string($link,trim($req_data['cid'])) :'';
	
	$response = array();

	if(empty($video_id) || $video_id == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Video id Parameter Missing.";
		$response['data']= $data;
	}
	elseif (!is_numeric($video_id))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in Video Id Parameter";
		$response['data']= $data;
	}
	elseif(empty($cat_id) || $cat_id == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Category id Parameter Missing.";
		$response['data']= $data;
	}
	elseif(!is_numeric($cat_id))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in Category Id Parameter";
		$response['data']= $data;
	}
	else
	{
		$data = getRelatedVideosByCategoryID($cat_id,$video_id,$link);
		
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


