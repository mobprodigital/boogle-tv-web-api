<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
//$apiKey = "EditSA";
/* Ends */

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		/* $roleid = $check['loginRoleId'];
		$res = validateApi($apiKey,$roleid);
		if($res)
		{ */
			$login_uid = $check['loginUserId'];
			$login_clientid = $check['loginClientId'];

			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
			$firstName = mysqli_real_escape_string($link,trim(isset($req_json['firstName']))) ? mysqli_real_escape_string($link,trim($req_json['firstName'])) :'';
			$lastName = mysqli_real_escape_string($link,trim(isset($req_json['lastName']))) ? mysqli_real_escape_string($link,trim($req_json['lastName'])) :'';
			$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
			$phone = mysqli_real_escape_string($link,trim(isset($req_json['phone']))) ? mysqli_real_escape_string($link,trim($req_json['phone'])) :'';
			$uid = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'';
			$roleId = mysqli_real_escape_string($link,trim(isset($req_json['roleId']))) ? mysqli_real_escape_string($link,trim($req_json['roleId'])) :'';
			
			if(! empty($req_json['assignedPortals'])) { $portal_array_status = check_array_values($req_json['assignedPortals']); }
			
			wh_log("FirstName - ".$firstName." | lastName - ".$lastName." | Phone - ".$phone." | Email - ".$email." | User Id-".$uid." | roleId -".$roleId);
			
			if(empty($email) || $email == null)
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="Some Parameter Missing.";
				$response['data']= $user_data;
			}
			elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="Invalid email format";
				$response['data']= $user_data;
			}
			elseif(empty($uid) || $uid == null)
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="uid is mandatory.";
				$response['data'] = $user_data;
			}
			elseif(!is_numeric($uid))
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in user Id parameter";
				$response['data']=$user_data;
			}
			elseif(empty($firstName) || $firstName == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="FirstName Parameter Missing";
				$response['data']= $Client_data;
			}
			elseif(!preg_match("/^[a-zA-Z ]+$/", $firstName))
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="Only letters and white space allowed in name parameter.";
				$response['data']= $user_data;
			}
			elseif(empty($roleId) || $roleId == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Role Parameter Missing";
				$response['data']= $Client_data;
			}
			elseif(!is_numeric($roleId))
			{
				$response['status']=false;
				$response['message']="Allowed only numbers in role parameter";
			}
			/* elseif(empty($req_json['assignedPortals']))
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="assignedPortals are mandatory.";
				$response['data']= $user_data;
			}
			elseif(!$portal_array_status)
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="assignedPortals parameter should be numeric.";
				$response['data']= $user_data;
			} */
			else
			{ 
				// Make Poratlids array in comma seperate string
				//$pids = implode(",",$req_json['assignedPortals']); 
				// Ends
				
				// Check user is exist in users table with email id
				$check_user = "SELECT * FROM users WHERE uid = $uid and status = 1 and role = $roleId and client_id = $login_clientid  and reports_to = $login_uid";
				wh_log("User Select Query - ".$check_user);
				$check_user_rs = mysqli_query($link,$check_user);
				if($check_user_rs)
				{
					if(mysqli_num_rows($check_user_rs) > 0)
					{ 
						// update data
						wh_log("User select Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs)." | Poratl Ids -".$pids);
						$lastName = !empty($lastName) ? "$lastName" : "NULL";
						$phone = !empty($phone) ? $phone : "NULL";
						$add_user = "update users set first_name = '$firstName',last_name = '$lastName',email= '$email',mobile = $phone,role = $roleId where uid = $uid and status = 1  and client_id = $login_clientid and reports_to = $login_uid";
						$add_user_rs = mysqli_query($link,$add_user);
						wh_log("Add User User Query - ".$add_user);
						// Get User Details
						$user_data = getSingleUserData($uid,$link);
						//Ends
						if($add_user_rs)
						{
							$response['status']=true;
							$response['message']="Successfully Updated User Details.";
							$response['data']= $user_data;
						}
						else
						{
							$user_data = array();
							$response['status']=false;
							$response['message']=mysqli_error($link);
							$response['data']= $user_data;
						}
					}
					else
					{
						$user_data = array();
						$response['status']=false;
						$response['message']="Invalid User Id";
						$response['data']= $user_data;
					}
				}
				else
				{
					$user_data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']= $user_data;
				}
			}
			
		/* }
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


