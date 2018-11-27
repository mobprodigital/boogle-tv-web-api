<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "SAList";
/* Ends */

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$roleid = $check['loginRoleId'];
		$res = validateApi($apiKey,$roleid);
		if($res)
		{
			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
			
			$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
			
			$uid  = $check['loginUserId'];
			
			if(empty($clientId) || $clientId == null)
			{
				$user_array = array();
				$response['status']=false;
				$response['message']="clientId is mandatory.";
				$response['data'] = $user_array;
			}
			elseif(!is_numeric($clientId))
			{
				$user_array = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Client Id parameter";
				$response['data']=$user_array;
			}
			else
			{
				$client_check = getSingleClientData($clientId,$link);
				if(empty($client_check))
				{
					$user_array = array();
					$response['status']=false;
					$response['message']="Invalid Client Id";
					$response['data']= $user_array;
				}
				else
				{
					$query = "SELECT * FROM users WHERE client_id = $clientId and status = 1 and role = 2 and uid != $uid";
					wh_log("ClientId - ".$clientId." | Super Admin Listing Select query -".$query);
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
							$response['message']= "No Super Admins available For this Clientid -".$clientId;
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
			}
		}
		else
		{
			header('HTTP/1.1 400 Bad Request', true, 400);
			die;
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


