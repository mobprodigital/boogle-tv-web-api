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
	
		$uid = decodeId($req_json['id']);
		
		$password = mysqli_real_escape_string($link,isset($req_json['password'])) ? mysqli_real_escape_string($link,trim($req_json['password'])) :'';
		wh_log("Uid - ".$uid." | Password - ".$password);
		if(empty($uid) || $uid == null)
		{
			$response['status']=false;
			$response['message']="Uid Parameter Missing.";
		}
		elseif(!is_numeric($uid))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in id parameter";
		}
		elseif(empty($password) || $password == null)
		{
			$response['status']=false;
			$response['message']="Password Parameter Missing.";
		}
		else
		{
			//Get Data from users table on basis of email and status
			$check_user = "SELECT * FROM users WHERE uid = '$uid' and status = '1'";
			wh_log("Select User Query - ".$check_user);
			$check_user_rs = mysqli_query($link,$check_user);
			if($check_user_rs)
			{
				if(mysqli_num_rows($check_user_rs) > 0)
				{
					wh_log("Select User Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs));
					// Update Password
					$update_user = "update users set password = '$password' where uid = '$uid' and status = '1'";
					wh_log("Update Password Query - ".$update_user);
					$update_user_rs = mysqli_query($link,$update_user);
					if($update_user_rs)
					{
						$response['status']=true;
						$response['message']= "Password Successfully Updated.";
					}
					else
					{
						$response['status']=false;
						$response['message']=mysqli_error($link);
					}
					//Ends
				}
				else
				{
					$response['status']=false;
					$response['message']="This User is not registered with us.";
				}
			}
			else
			{
				$response['status']=false;
				$response['message']=mysqli_error($link);
			}
			mysqli_stmt_close($stmt);
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


