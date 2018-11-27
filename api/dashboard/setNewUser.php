<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		//$client_id = $check['loginClientId'];
		
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
		$firstName = mysqli_real_escape_string($link,trim(isset($req_json['firstName']))) ? mysqli_real_escape_string($link,trim($req_json['firstName'])) :'';
		$lastName = mysqli_real_escape_string($link,trim(isset($req_json['lastName']))) ? mysqli_real_escape_string($link,trim($req_json['lastName'])) :'';
		$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		$phone = mysqli_real_escape_string($link,trim(isset($req_json['phone']))) ? mysqli_real_escape_string($link,trim($req_json['phone'])) :'';
		$role = mysqli_real_escape_string($link,trim(isset($req_json['role']))) ? mysqli_real_escape_string($link,trim($req_json['role'])) :'';
		$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
		
		if(! empty($req_json['assignedPortals'])) { $cat_array_status = check_array_values($req_json['assignedPortals']); }
		wh_log("FirstName - ".$firstName." | lastName - ".$lastName." | Phone - ".$phone." | Email - ".$email." Role -".$role." | ClientId - ".$clientId);
		
		if(empty($email) || $email == null)
		{
			$user_data = array();
			$response['status']=false;
			$response['message']="Some Parameter Missing.";
			$response['data']= $user_data;
		}
		elseif(empty($firstName) || $firstName == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="FirstName Parameter Missing";
			$response['data']= $Client_data;
		}
		elseif(empty($role) || $role == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Role Parameter Missing";
			$response['data']= $Client_data;
		}
		elseif(empty($clientId) || $clientId == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="ClientId Parameter Missing";
			$response['data']= $Client_data;
		}
		elseif(!is_numeric($clientId))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in clientId parameter";
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$user_data = array();
			$response['status']=false;
			$response['message']="Invalid email format";
			$response['data']= $user_data;
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $firstName))
		{
			$user_data = array();
			$response['status']=false;
			$response['message']="Only letters and white space allowed in name parameter.";
			$response['data']= $user_data;
		}
		elseif(empty($req_json['assignedPortals']))
		{
			$response['status']=false;
			$response['message']="assignedPortals are mandatory.";
			$response['data']="";
		}
		elseif(!$cat_array_status)
		{
			$response['status']=false;
			$response['message']="assignedPortals parameter should be numeric.";
			$response['data']="";
		}
		else
		{
			// Make Poratlids array in comma seperate string
			$pids = implode(",",$req_json['assignedPortals']); 
			// Ends
			
			// Check user is exist in users table with email id
			$check_user = "SELECT * FROM users WHERE email = '$email'";
			wh_log("Query - ".$check_user);
			$check_user_rs = mysqli_query($link,$check_user);
			if($check_user_rs)
			{
				if(mysqli_num_rows($check_user_rs) > 0)
				{
					wh_log("Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs));
					
					$user_data = array();
					$response['status']=false;
					$response['message']="This Emailid is already registered with us.";
					$response['data']= $user_data;
				}
				else
				{
					wh_log("Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs)." | Poratl Ids -".$pids);
					
					//Add New User
					$pwd = random_num(6);
					$lastName = !empty($lastName) ? "$lastName" : "NULL";
					$phone = !empty($phone) ? "$phone" : "NULL";
					
					$add_user = "INSERT INTO users (first_name,last_name,email,password,mobile,role,client_id,portal_ids,status,insertion_time) VALUES ('$firstName','$lastName','$email','$pwd','$phone',$role,'$clientId','$pids','1',NOW())";
					$add_user_rs = mysqli_query($link,$add_user);
					$last_insert_id = mysqli_insert_id($link);
					wh_log("Add User Query - ".$add_user." | Last Insert_id - ".$last_insert_id);
					
					// Get User Details
					$user_data = getSingleUserData($last_insert_id,$link);
					//Ends
					
					if($add_user_rs)
					{
						$subject = "Login credentials";
						$msg = "Dear User,<br><br>Thankyou registering with us.<br>Your login credentials are -<br>Email -".$email."<br>Password - ".$pwd."<br><br>Regards<br>Panel Team";
						$result_email = send_email($email,$msg,$subject);
						
						
						$response['status']=true;
						$response['message']="Successfully Registered.";
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
			}
			else
			{
				$user_data = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data']= $user_data;
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


