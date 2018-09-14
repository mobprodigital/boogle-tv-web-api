<?php
include "../includes/config.php";
include "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$video_id = isset($req_data['id']) ? trim($req_data['id']) :'';
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
		$getVideo = "select * from videos where status =1 and id = $video_id";
		wh_log("Video Query Executed : ".$getVideo);
		$getVideo_rs = @mysql_query($getVideo);
		if(mysql_num_rows($getVideo_rs) > 0)
		{
			wh_log("Rows Found for video -- ".mysql_num_rows($getVideo_rs));
			while($row  = mysql_fetch_assoc($getVideo_rs))
			{ 
				$video_data = videoArray($row);
			}
		}
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
echo json_encode($response,JSON_NUMERIC_CHECK);

?>


