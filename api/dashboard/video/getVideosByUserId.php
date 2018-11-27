<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$client_id = $check['loginClientId'];
		$uploaded_By = $check['loginUserId'];

		//$req_json = json_decode(file_get_contents("php://input"), true);
		//wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
		//$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
		$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :' ';
		$uploadedBy = mysqli_real_escape_string($link,trim(isset($uploaded_By))) ? mysqli_real_escape_string($link,trim($uploaded_By)) :'0';

		if(empty($clientId) || $clientId == null)
		{
			$video_array = array();
			$response['status']=false;
			$response['message']="clientId is mandatory.";
			$response['data'] = $video_array;
		}
		elseif(!is_numeric($clientId))
		{
			$video_array = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in clientId parameter";
			$response['data']=$video_array;
		}
		elseif(empty($uploadedBy) || $uploadedBy == null)
		{
			$video_array = array();
			$response['status']=false;
			$response['message']="Uploaded By is mandatory.";
			$response['data'] = $video_array;
		}
		elseif(!is_numeric($uploadedBy))
		{
			$video_array = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in uploaded By parameter";
			$response['data']=$video_array;
		}
		else
		{
			// Video Listing By User Id And Client ID
			//$query = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`client_id`= $clientId and t1.uploaded_by = '$uploadedBy' and t1.content_type ='video' and t1.status = 1";
			
			$content_query = "SELECT * from  `content_metadata` where `client_id`= $clientId and uploaded_by = '$uploadedBy' and content_type ='video' and status = 1";
			wh_log("Content Query - ".$content_query);
			$content_query_rs = mysqli_query($link,$content_query);
			if($content_query_rs)
			{
				if(mysqli_num_rows($content_query_rs) > 0)
				{
					while($con_row  = mysqli_fetch_assoc($content_query_rs))
					{
						// Get Content array
						$video_array[] = videoArray($con_row,$link,$imageBaseURL,$videoBaseURL);
						// Ends
					}
					if(!empty($video_array))
					{
						$response['status']=true;
						$response['message']="Success";
						$response['data'] = $video_array;
					}
				}
				else
				{
					$video_array = array();
					$response['status']=false;
					$response['message']="No Videos Found For This User Id.";
					$response['data'] = $video_array;
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
		header('HTTP/1.1 401 Unauthorized', true, 401);
		die;
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


