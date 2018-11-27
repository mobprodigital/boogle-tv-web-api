<?php
include "../includes/config.php";
include "../includes/functions.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	$video_id = mysqli_real_escape_string($link,isset($req_data['id'])) ? mysqli_real_escape_string($link,trim($req_data['id'])) :'';
	$view_time = mysqli_real_escape_string($link,isset($req_data['time'])) ? mysqli_real_escape_string($link,trim($req_data['time'])) :'';
	
	$response = array();
	
	if(empty($video_id) || $video_id == null)
	{
	$response['status']=false;
	$response['message']="Id is missing.";
	}
	elseif(!is_numeric($video_id))
	{
	$response['status']=false;
	$response['message']="Id should be numeric.";
	}
	elseif(empty($view_time) || $view_time == null)
	{
	$response['status']=false;
	$response['message']="View Time is missing.";
	}
	elseif(!is_numeric($view_time))
	{
	$response['status']=false;
	$response['message']="View Time should be numeric.";
	}
	else
	{
		$view_time_in_hour_minutes = gmdate("H:i:s",$view_time);
		
		$videoList = "SELECT view_time FROM `videos` where id = $video_id";
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("view_time_in_hour_minutes : ".$view_time_in_hour_minutes. " | Select Query - ".$videoList. " | Toytal Rows - ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			if($row  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$result = secondsToMinutes($row['view_time'],$view_time_in_hour_minutes);
				$updateList = "update `videos` set view_time = '$result' where id = $video_id";
				wh_log("Total hh:mm:ss : ".$result. " | Update Query - ".$updateList);
				$updateList_rs = mysqli_query($link,$updateList);
				if($updateList_rs)
				{
					$response['status']=true;
					$response['message']="View Time Increased by ".$view_time. " seconds.";
				}
				else
				{
					$response['status']=false;
					$response['message']= "Some error occured.";
				}
			} 	
		}
		else
		{
			$response['status']=false;
			$response['message']="No Data Found.";
		}
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


