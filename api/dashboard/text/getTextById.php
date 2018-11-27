<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "TextById";
/* Ends */


if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$roleid = $check['loginRoleId'];
		$res = validateApi($apiKey,$roleid);
		if($res)
		{
			$client_id = $check['loginClientId'];
			$login_uid = $check['loginUserId'];

			$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :'0';
			$loginuid = mysqli_real_escape_string($link,trim(isset($login_uid))) ? mysqli_real_escape_string($link,trim($login_uid)) :'';

			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
			
			$textId = mysqli_real_escape_string($link,trim(isset($req_json['textId']))) ? mysqli_real_escape_string($link,trim($req_json['textId'])) :'0';
			
			if(empty($textId) || $textId == null)
			{
				$text_array = array();
				$response['status']=false;
				$response['message']="textId is mandatory.";
				$response['data'] = $text_array;
			}
			elseif(!is_numeric($textId))
			{
				$text_array = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Text Id parameter";
				$response['data']=$text_array;
			}
			elseif(empty($clientId) || $clientId == null)
			{
				$response['status']=false;
				$response['message']="clientId is mandatory.";
				$response['data']="";
			}
			elseif(!is_numeric($clientId))
			{
				$arr = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in clientId parameter";
				$response['data']=$arr;
			}
			elseif(empty($loginuid) || $loginuid == null)
			{
				$video_array = array();
				$response['status']=false;
				$response['message']="loginuid is mandatory.";
				$response['data'] = $video_array;
			}
			elseif(!is_numeric($loginuid))
			{
				$arr = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in loginuid parameter";
				$response['data']=$arr;
			}
			else
			{
				// Get Text Array By text ID
				$query = "SELECT * FROM news_metadata where id = $textId and uploaded_by = '$loginuid' and content_type ='text' and status = 1 and client_id = $client_id";
				wh_log("Query - ".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					if(mysqli_num_rows($query_rs) > 0)
					{
						while($row  = mysqli_fetch_assoc($query_rs))
						{  
							$text_array = textArray($row,$imageBaseURL,$link,$videoBaseURL);
						}
						if(!empty($text_array))
						{
						$response['status']=true;
						$response['message']="Success";
						$response['data'] = $text_array;	
						} else {
						$text_array = array();
						$response['status']=false;
						$response['message']="No Data Found For This Text Id.";
						$response['data'] = $text_array;	
						}
					}
					else
					{
						$text_array = array();
						$response['status']=false;
						$response['message']="No Data Found For This text Id.";
						$response['data'] = $text_array;
					}
				}
				else
				{
					$text_array = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $text_array;
				}
			
			}
		}
		else
		{
			header('HTTP/1.1 400 Bad Request', true, 400);
			die;
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


