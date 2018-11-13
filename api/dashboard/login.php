<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	deleteCookie();
	
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$uemail = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
	$upassword = mysqli_real_escape_string($link,trim(isset($req_json['password']))) ? mysqli_real_escape_string($link,trim($req_json['password'])) :'';
	
	wh_log("Email - ".$uemail." | Password - ".$upassword);
	if(empty($upassword) || $upassword == null)
	{
		$user_data = array();
		$response['status']=false;
		$response['message']="Password Parameter Missing.";
		$response['data'] = $user_data;
	}
	elseif(empty($uemail) || $uemail == null)
	{
		$user_data = array();
		$response['status']=false;
		$response['message']="Email Parameter Missing.";
		$response['data'] = $user_data;
	}
	elseif(!filter_var($uemail, FILTER_VALIDATE_EMAIL))
	{
		$user_data = array();
		$response['status']=false;
		$response['message']="Invalid email format";
		$response['data'] = $user_data;
	}
	else
	{
		$query = "SELECT * FROM users WHERE email = '$uemail' and password = '$upassword' and status = '1'";
		wh_log("Query - ".$query);
		$query_rs = mysqli_query($link,$query);
		if($query_rs)
		{
			if(mysqli_num_rows($query_rs) > 0)
			{
				wh_log("Select Query Rows Count - ".mysqli_num_rows($query_rs));
				if($row  = mysqli_fetch_assoc($query_rs)) 
				{
					// Get User Details
					$user_data = singleUserArray($row,$link);
					//Ends
				}
				wh_log("User Array : ".str_replace("\n"," ", print_r($user_data, true)));
				$setcookie = setcookies($user_data['clientId'],$user_data['role']['roleId'],$user_data['userId']);
				//print_r($_COOKIE);
				if(!empty($user_data))
				{
				$response['status']=true;
				$response['message']="Success";
				$response['data'] = $user_data;
				}
			}
			else
			{
				$user_data = array();
				$response['status']=false;
				$response['message']="Invalid Credentials";
				$response['data'] = $user_data;
			}
		}
		else
		{
			$user_data = array();
			$response['status']=false;
			$response['message']=mysqli_error($link);
			$response['data'] = $user_data;
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


