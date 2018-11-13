<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		//$client_id = $check['loginClientId'];
		//$uid = $check['loginUserId'];
		$roleid = $check['loginRoleId'];
		
		if($roleid == 1)
		{
			//echo $roleid;
			// When Owner Login
			$uid = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'0';
			if(empty($uid) || $uid == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="userId is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($uid))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in userId parameter";
				$response['data']=$Client_data;
			}
			else
			{
				echo $query = "SELECT * FROM users WHERE status = '1' and uid != $uid";
				wh_log("Role Id - ".$roleid." | Logged in User ID - ".$uid." | Select query -".$query);
			}
		}
		else
		{
			// When Superadmin/admin login
			$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
			$uid = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'0';
			if(empty($uid) || $uid == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="userId is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($uid))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in userId parameter";
				$response['data']=$Client_data;
			}
			elseif(empty($clientId) || $clientId == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="clientId is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($clientId))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Client Id parameter";
				$response['data']=$Client_data;
			}
			else
			{
				$query = "SELECT * FROM users WHERE client_id = '$clientId' and status = '1' and uid != $uid";
				wh_log("Role Id - ".$roleid." | Logged in User ID - ".$uid." | ClientId - ".$clientId." | Select query -".$query);
			}
		}
		
		$query_rs = mysqli_query($link,$query);
		if($query_rs)
		{
			if(mysqli_num_rows($query_rs) > 0)
			{
				while($row  = mysqli_fetch_assoc($query_rs))
				{  
					// Get User Details
					$user_array[] = singleUserArray($row,$link);
					//Ends
				}
			}
			if(empty($user_array))
			{
				$user_array = array();
				$response['status']=false;
				$response['message']= "No Users available";
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


