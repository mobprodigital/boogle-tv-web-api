<?php
include "../includes/config.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$video_id = mysqli_real_escape_string($link,isset($req_data['id'])) ? mysqli_real_escape_string($link,trim($req_data['id'])) :'';
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
	else
	{
		$updateList = "update `videos` set `like` = `like`+1 where id = ?";
		if($stmt = mysqli_prepare($link, $updateList))
		{
			$vid = $video_id;
			mysqli_stmt_bind_param($stmt,'i', $vid);
			$status = mysqli_stmt_execute($stmt);
			$count = mysqli_stmt_affected_rows($stmt);
			if($count > 0)
			{
				$response['status']=true;
				$response['message']="Like Count Increased.";
			}
			else
			{
				$response['status']=false;
				$response['message']= "Invalid Video Id";
			}
			
		}
		mysqli_stmt_close($stmt);		
	  
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


