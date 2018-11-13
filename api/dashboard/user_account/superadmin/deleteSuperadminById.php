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
		
		$roleid = $check['loginRoleId'];
		
		$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
		$uid = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'0';
			
		if(empty($clientId) || $clientId == null)
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
		elseif(empty($uid) || $uid == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="uid is mandatory.";
			$response['data'] = $Client_data;
		}
		elseif(!is_numeric($uid))
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in user Id parameter";
			$response['data']=$Client_data;
		}
		else
		{
			// Check uid is present or not in users table
			$check_query = "select * from users WHERE uid = $uid and status = 1 and role = 2 and client_id = $clientId";
			$check_query_rs = mysqli_query($link,$check_query);
			wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
			if(mysqli_num_rows($check_query_rs) > 0)
			{
				$query = "update users set status =0 WHERE uid = $uid and status = 1 and role = 2 and client_id = $clientId";
				wh_log("Super Admin By Id update Query - ".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					$Client_data = array();
					$response['status']=true;
					$response['message']="User Successfully Deleted.";
					$response['data']= $Client_data; 
				}
				else
				{
					$Client_data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $Client_data;
				}
			}
			else
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Invalid id";
				$response['data']=$Client_data;
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


