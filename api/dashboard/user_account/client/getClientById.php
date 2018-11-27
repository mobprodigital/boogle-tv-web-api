<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "getCl";
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
			else
			{
				// Check client/partner is exist in client table
				$check_user = "SELECT * FROM clients WHERE client_id = $clientId";
				wh_log("Query - ".$check_user);
				$check_user_rs = mysqli_query($link,$check_user);
				if($check_user_rs)
				{
					if(mysqli_num_rows($check_user_rs) > 0)
					{
						// get Clent Data
						$Client_data = getSingleClientData($clientId,$link);
						//Ends
						$response['status']=true;
						$response['message']="Success";
						$response['data']= $Client_data;
					}
					else
					{
						$Client_data = array();
						$response['status']=false;
						$response['message']="Invalid Client Id.".$clientId;
						$response['data']= $Client_data;
					}
				}
				else
				{
					$Client_data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $Client_data;
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


