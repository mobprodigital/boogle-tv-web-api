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
			$arr = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in clientId parameter";
			$response['data']=$arr;
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
			$arr = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in uploaded By parameter";
			$response['data']=$arr;
		}
		else
		{
			// Video Listing By User Id And Client ID
			$query = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN 
			content_multimedia as t2 ON t1.id = t2.content_id where t1.`client_id`= $clientId and t1.uploaded_by = '$uploadedBy' and t1.content_type ='video' and t1.status = 1";
			
			
			//SELECT * FROM content_metadata WHERE client_id ='$clientId' and content_type ='video' and status = '1'";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{ 
					while($row  = mysqli_fetch_assoc($query_rs))
					{  
						//print_r($row);
						$video_array[] = videoArray($row,$imageBaseURL,$videoBaseURL,$link);
					}
					if(!empty($video_array))
					{
					$response['status']=true;
					$response['message']="Success";
					$response['data'] = $video_array;	
					} else {
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


