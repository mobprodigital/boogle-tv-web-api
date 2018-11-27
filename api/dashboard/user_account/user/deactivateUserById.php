<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
//$apiKey = "DelSA";
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
			
			$roleId = mysqli_real_escape_string($link,trim(isset($req_json['roleId']))) ? mysqli_real_escape_string($link,trim($req_json['roleId'])) :'';
			$id = mysqli_real_escape_string($link,trim(isset($req_json['userId']))) ? mysqli_real_escape_string($link,trim($req_json['userId'])) :'';
				
			if(empty($roleId) || $roleId == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Role Id is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($roleId))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Role Id parameter";
				$response['data']=$Client_data;
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
				// Check uid is present or not in users table
				$check_query = "select * from users WHERE uid = $id and status = 1 and role = $roleId and client_id = $login_clientid and reports_to = $login_uid";
				$check_query_rs = mysqli_query($link,$check_query);
				wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
				if(mysqli_num_rows($check_query_rs) > 0)
				{
					$query = "update users set status = 0 WHERE uid = $id and role = $roleId and client_id = $login_clientid and reports_to = $login_uid and status = 1";
					wh_log("Update Users By Id update Query - ".$query);
					$query_rs = mysqli_query($link,$query);
					if($query_rs)
					{
						$Client_data = array();
						$response['status']=true;
						$response['message']="User Successfully Deactivated.";
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
					$response['message']="Invalid User id";
					$response['data']=$Client_data;
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


