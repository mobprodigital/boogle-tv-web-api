<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
		$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		wh_log("Email - ".$email);
		
		if((empty($email) || $email == null))
		{
			$response['status']=false;
			$response['message']="Email Parameter Is Missing.";
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$response['status']=false;
			$response['message']="Invalid email format";
		}
		else
		{
			// Check User Email is exist or not
			$check_user = "SELECT * FROM users WHERE email = '$email' and status='1'";
			wh_log("Query - ".$check_user);
			$check_user_rs = mysqli_query($link,$check_user);
			if($check_user_rs)
			{
				if(mysqli_num_rows($check_user_rs) > 0)
				{
					wh_log("Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs));
					// Send Reset Password link on email
					if($row  = mysqli_fetch_assoc($check_user_rs))
					{
						$encode_id = encodeId($row['uid']);
						$url = $panel_url.'api/dashboard/updatePassword.php?id='.$encode_id;
						$subject = 'CMS - Update Password';
						$msg = 'Please Click on below link,to change your password.<br>'.$url;
					    wh_log("Userid - ".$row['uid']." | Encode Userid -".$encode_id." | Url - ".$url." | Subject - ".$subject."| Message - ".$msg." | Uemail -".$uemail);
						$result_email = send_email($email,$msg,$subject);
						wh_log("Result Email -".$result_email);
					}
					//Ends
					
					$response['status']=true;
					$response['message']="Reset Paasword Link is sent on your registered email.";
				}
				else
				{
					$response['status']=false;
					$response['message']="This email is not registered with us.";
				}
			}
			else
			{
				$response['status']=false;
				$response['message']=mysqli_error($link);
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


