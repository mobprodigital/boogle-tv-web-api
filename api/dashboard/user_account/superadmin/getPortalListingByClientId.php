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
		
		if($roleid == 1)
		{
			// when owner logins
			$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
			
			if(empty($clientId) || $clientId == null)
			{
				$portal_array = array();
				$response['status']=false;
				$response['message']="clientId is mandatory.";
				$response['data'] = $portal_array;
			}
			elseif(!is_numeric($clientId))
			{
				$portal_array = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Client Id parameter";
				$response['data']=$portal_array;
			}
			else
			{
				$query = "SELECT portal_ids FROM clients WHERE client_id = $clientId and status = '1'";
				wh_log("Select Client Query - ".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					if(mysqli_num_rows($query_rs) > 0)
					{
						if($row  = mysqli_fetch_assoc($query_rs))
						{  
							$portal_ids = $row['portal_ids'];
							$portal_array = getPortalArrayByIds($portal_ids,$link);
						}
						if(empty($portal_array))
						{
							$portal_array = array();
							$response['status']=false;
							$response['message']= "No Poratls available";
							$response['data'] = $portal_array;
						}
						else
						{
							$response['status']=true;
							$response['message']= "Success";
							$response['data'] = $portal_array;
						}
					}
					else
					{
						$portal_array = array();
						$response['status']=false;
						$response['message']="Invalid Client ID.";
						$response['data'] = $portal_array;
					}
				}
				else
				{
					$portal_array = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $portal_array;
				}
			}
			
		}
		else
		{
			// when supwer admin logins
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
				$query = "SELECT portal_ids FROM users WHERE uid = $uid and client_id = $clientId and status = '1' and role =2";
				wh_log("Select User Query - ".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					if(mysqli_num_rows($query_rs) > 0)
					{
						if($row  = mysqli_fetch_assoc($query_rs))
						{  
							$portal_ids = $row['portal_ids'];
							$portal_array = getPortalArrayByIds($portal_ids,$link);
						}
						if(empty($portal_array))
						{
							$portal_array = array();
							$response['status']=false;
							$response['message']= "No Poratls available";
							$response['data'] = $portal_array;
						}
						else
						{
							$response['status']=true;
							$response['message']= "Success";
							$response['data'] = $portal_array;
						}
					}
					else
					{
						$portal_array = array();
						$response['status']=false;
						$response['message']="Invalid Request.";
						$response['data'] = $portal_array;
					}
				}
				else
				{
					$portal_array = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $portal_array;
				}
				
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


