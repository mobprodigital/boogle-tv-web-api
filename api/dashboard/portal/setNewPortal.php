<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
		$name = mysqli_real_escape_string($link,trim(isset($req_json['portalName']))) ? mysqli_real_escape_string($link,trim($req_json['portalName'])) :'';
		$url = mysqli_real_escape_string($link,trim(isset($req_json['url']))) ? mysqli_real_escape_string($link,trim($req_json['url'])) :'';
		$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		
		if(! empty($req_json['contentType'])) { $content_type_status = check_array_values($req_json['contentType']); }
		
		wh_log("Email - ".$email." | Url - ".$url." | Name - ".$name);
		if(empty($email) || $email == null)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Email Parameter Missing.";
			$response['data']= $portal_data;
		}
		elseif(empty($url) || $url == null)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Url Parameter Missing.";
			$response['data']= $portal_data;
			
		}
		elseif(empty($name) || $name == null)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Portal Name Parameter Missing.";
			$response['data']= $portal_data;
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Invalid email format";
			$response['data']= $portal_data;
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $name))
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Only letters allowed in name parameter.";
			$response['data']= $portal_data;
		}
		elseif(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url))
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Invalid Url";
			$response['data']= $portal_data;
		}
		elseif(empty($req_json['contentType']))
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Content Type Parameter Missing.";
			$response['data']= $portal_data;
		}
		elseif(!$content_type_status)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Content type should be numeric.";
			$response['data']= $portal_data;
		}
		else
		{
			// Convert Array To Comma Seperated Strings
			$contentTypeIds = array_to_comma_separated($req_json['contentType']);
			
			$query = "SELECT * FROM portals WHERE email = '$email' and url = '$url' and name = '$name' and status = '1'";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					$portal_data = array();
					$response['status']=false;
					$response['message']="Details Already Exist";
					$response['data']= $portal_data;
				}
				else
				{
					$insert_portal_sql = "INSERT INTO portals (name,url,email,content_type,status,insertion_time) VALUES ('$name','$url','$email','$contentTypeIds','1',NOW())";
					$insert_portal_sql_rs = mysqli_query($link,$insert_portal_sql);
					$last_insert_id = mysqli_insert_id($link);
					wh_log("Add Portal Query - ".$insert_portal_sql." | Last Insert_id - ".$last_insert_id);
					if($insert_portal_sql_rs)
					{
						// Get Portal Details
						$portal_data = getPortalDataById($last_insert_id,$link);
						//Ends
						
						$response['status']=true;
						$response['message']="Successfully Registered.";
						$response['data']= $portal_data;
						
					}
					else
					{
						$portal_data = array();
						$response['status']=false;
						$response['message']=mysqli_error($link);
						$response['data']= $portal_data;
					}
				}
			}
			else
			{
				$portal_data = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data']= $portal_data;
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


