<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
	
	if(empty($clientId) || $clientId == null)
	{
		$video_array = array();
		$response['status']=false;
		$response['message']="clientId is mandatory.";
		$response['data'] = $video_array;
	}
	elseif(!is_numeric($clientId))
	{
		$arr = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in clientId parameter";
		$response['data']=$arr;
	}
	else
	{
		// Video Listing By Client ID
		$query = "SELECT * FROM contents WHERE client_id ='$clientId' and content_type ='video' and status = '1'";
		wh_log("Query - ".$query);
		$query_rs = mysqli_query($link,$query);
		if($query_rs)
		{
			if(mysqli_num_rows($query_rs) > 0)
			{
				while($row  = mysqli_fetch_assoc($query_rs))
				{  
					$video_array[] = videoArray($row,$localhost_base_url);
				}
				if(!empty($video_array))
				{
				$response['status']=true;
				$response['message']="Success";
				$response['data'] = $video_array;	
				} else {
				$video_array = array();
				$response['status']=false;
				$response['message']="No Videos Found For This Client Id.";
				$response['data'] = $video_array;	
				}
			}
		}
		else
		{
			$video_array = array();
			$response['status']=false;
			$response['message']=mysqli_error($link);
			$response['data'] = $video_array;
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


