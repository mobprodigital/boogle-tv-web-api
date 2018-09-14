<?php
include "../includes/config.php";
include "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	$video_tag = isset($req_data['tag']) ? trim($req_data['tag']) :'';
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
		$videoList = "select DISTINCT(`video_url`),`id`,`cat_id`,`title`,`video_tags`,`insertion_time`,`description`,`view`,
		`like`,`dislike` from videos where video_tags like '%$video_tag%' order by id desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = @mysql_query($videoList);
		wh_log("Rows Found for video Tag List -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{  
				$data[] = videoArray($row);
			}
		}
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
	/* $response['status']=false;
	$response['message']="No Page Found."; */
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_NUMERIC_CHECK);
?>


