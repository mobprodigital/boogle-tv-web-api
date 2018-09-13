<?php
include "../includes/config.php";
wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));

$video_id = isset($_POST['video_id']) ? trim($_POST['video_id']) :'';
$response = array();

if(empty($video_id) || $video_id == null)
{
$response['status']=false;
$response['message']="Kindly Provide Video Id.";
}
elseif(!is_numeric($video_id))
{
$response['status']=false;
$response['message']="Please provide valid video id";
}
else
{
	$updateList = "update `videos` set `dislike` = `dislike`+1 where id = $video_id";
    wh_log("Query Executed : ".$updateList);
	$updateList_rs = @mysql_query($updateList);
	if($updateList_rs)
	{
		$response['status']=true;
	    $response['message']="Dislike Count Increased.";
	}
	else
	{
		$response['status']=false;
	    $response['message']= "Some error occured.";
	}	
  
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response);
?>


