<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "DeactivateCl";
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
			$login_uid = $check['loginUserId'];
			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
			
			$id = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'';
			$action = mysqli_real_escape_string($link,trim(isset($req_json['action']))) ? mysqli_real_escape_string($link,trim($req_json['action'])) :'';
				
			if(empty($id) || $id == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Client id is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($id))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Client Id parameter";
				$response['data']=$Client_data;
			}
			elseif(empty($action) || $action == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Action is mandatory.";
				$response['data'] = $Client_data;
			}
			else
			{
				// Check client id is present or not in client table
				$check_query = "select * from clients WHERE client_id = $id and status = 1";
				$check_query_rs = mysqli_query($link,$check_query);
				wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
				if(mysqli_num_rows($check_query_rs) > 0)
				{
					if($action == 'delete with content')
					{
						$query = "update clients set status = 0 WHERE client_id = $id and status = 1";
						wh_log("Client By Id update Query - ".$query);
						$query_rs = mysqli_query($link,$query);
						if($query_rs)
						{
							$query1 = "update users set status = 0 WHERE client_id = $id and status = 1";
						    wh_log("Users of Client Id update Query - ".$query1);
							$query_rs1 = mysqli_query($link,$query1);
							if($query_rs1)
							{
								$query2 = "update content_metadata set status = 0 WHERE client_id = $id and status = 1";
						    	wh_log("Update query for content metadata - ".$query2);
								$query_rs2 = mysqli_query($link,$query2);

								$query3 = "update news_metadata set status = 0 WHERE client_id = $id and status = 1";
						    	wh_log("Update query for news metadata - ".$query3);
								$query_rs3 = mysqli_query($link,$query3);
								if($query_rs2 && $query_rs3)
								{
									$Client_data = array();
									$response['status']=true;
									$response['message']="Client And Its Users Successfully Deactivated.";
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
								$response['message']=mysqli_error($link);
								$response['data'] = $Client_data;
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
					else
					{
						$query = "update clients set status = 0 WHERE client_id = $id and status = 1";
						wh_log("Client By Id update Query - ".$query);
						$query_rs = mysqli_query($link,$query);
						if($query_rs)
						{
							$query1 = "update users set status = 0 WHERE client_id = $id and status = 1";
						    wh_log("Users of Client Id update Query - ".$query1);
							$query_rs1 = mysqli_query($link,$query1);
							if($query_rs1)
							{
								$Client_data = array();
								$response['status']=true;
								$response['message']="Client And Its Users Successfully Deactivated.";
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
							$response['message']=mysqli_error($link);
							$response['data'] = $Client_data;
						}
					}
				}
				else
				{
					$Client_data = array();
					$response['status']=false;
					$response['message']="Invalid Client id";
					$response['data']=$Client_data;
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


