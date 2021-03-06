<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
//$apiKey = "SAList";
/* Ends */

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		//$roleid = $check['loginRoleId'];
		//$res = validateApi($apiKey,$roleid);
		/* if($res)
		{ */
			$login_uid = $check['loginUserId'];
			$login_clientid = $check['loginClientId'];
			wh_log("ClientId - ".$login_clientid." | Login UId -".$login_uid);

			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
			
			$roleId = mysqli_real_escape_string($link,trim(isset($req_json['roleId']))) ? mysqli_real_escape_string($link,trim($req_json['roleId'])) :'0';
			$id = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'';

			if(empty($roleId) || $roleId == null)
			{
				$user_array = array();
				$response['status']=false;
				$response['message']="Role Id is mandatory.";
				$response['data'] = $user_array;
			}
			elseif(!is_numeric($roleId))
			{
				$user_array = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Role Id parameter";
				$response['data']=$user_array;
			}
			elseif(empty($id) || $id == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="User id is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($id))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in user Id parameter";
				$response['data']=$Client_data;
			}
			else
			{
				$query = "SELECT * FROM users WHERE uid = $id and reports_to = $login_uid and client_id = $login_clientid and role = $roleId and status = 1";
				wh_log("ClientId - ".$login_clientid." | User By Id Select query -".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					if(mysqli_num_rows($query_rs) > 0)
					{
						while($row  = mysqli_fetch_assoc($query_rs))
						{  
							// Get Super Admin Details
							$user_array[] = singleUserArray($row,$link);
							//Ends
						}
					}
					if(empty($user_array))
					{
						$user_array = array();
						$response['status']=false;
						$response['message']= "No Data Found";
						$response['data'] = $user_array;
					}
					else
					{
						$response['status']=true;
						$response['message']= "Success";
						$response['data'] = $user_array;
					}
					
				}
				else
				{
					$user_array = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $user_array;
				}
		}
			
	/*	}
		else
		{
			header('HTTP/1.1 400 Bad Request', true, 400);
			die;
		} */
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


