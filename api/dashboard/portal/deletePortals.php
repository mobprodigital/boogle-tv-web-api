<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "DelPor1";
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
	
		    $id = mysqli_real_escape_string($link,trim(isset($req_json['portalId']))) ? mysqli_real_escape_string($link,trim($req_json['portalId'])) :'';
		    wh_log("Portal ID - ".$id);
		
			if((empty($id) || $id == null))
			{
				$arr = array();
				$response['status']=false;
				$response['message']="Portal Id Parameter Missing.";
				$response['data']=$arr;
			}
			elseif(!is_numeric($id))
			{
				$arr = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in Portal Id parameter";
				$response['data']=$arr;
			}
			else
			{
				// Check Poratl Id exist or not
				$check = "select * from portals where portal_id = '$id' and status = 1";
				$check_rs = mysqli_query($link,$check);
				if($check_rs)
				{
					if(mysqli_num_rows($check_rs) > 0)
					{
						// Insert Into Logs
						if($row  = mysqli_fetch_assoc($check_rs))
						{
							$name   = $row['name'];
							$email  = $row['email'];
							$tenure = $row['agreement_tenure'];
							$create_time = $row['insertion_time'];
							$insert_logs = "Insert into logs (name,email,tenure,type,created_on) values ('$name','$email',$tenure,'portal','$create_time')";
							$insert_logs_rs = mysqli_query($link,$insert_logs);
							wh_log("Portal Log Query - ".$insert_logs);
						}
						// Ends

						// Delete Portal Details
						$del_portal = "delete from portals where portal_id = '$id' and status =1";
						$del_portal_rs = mysqli_query($link,$del_portal);
						wh_log("delete Portal Query - ".$del_portal);
						// Ends
						if($del_portal_rs)
						{
							// Update Category Details
							$del_cat = "UPDATE category SET `portal_ids` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `portal_ids`, ','), ',$id,', ','))
							WHERE FIND_IN_SET('$id', `portal_ids`) and status =1";
							$del_cat_rs = mysqli_query($link,$del_cat);
							wh_log("Update Category Query - ".$del_cat);
							// Ends
							if($del_cat_rs)
							{
								// Update Clients Details
								$update_cl = "UPDATE clients SET `portal_ids` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `portal_ids`, ','), ',$id,', ','))
								WHERE FIND_IN_SET('$id', `portal_ids`) and status =1";
								$update_cl_rs = mysqli_query($link,$update_cl);
								wh_log("Update Clients Query - ".$update_cl);
								// Ends
								if($update_cl_rs)
								{
									// Update Content Metadata Details
									$update_con = "UPDATE content_metadata SET `portal_ids` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `portal_ids`, ','), ',$id,', ','))
									WHERE FIND_IN_SET('$id', `portal_ids`) and status =1";
									$update_con_rs = mysqli_query($link,$update_con);
									wh_log("Update Content Metadata Query - ".$update_con);
									// Ends
									if($update_con_rs)
									{
										// Update News Metadata Details
										$update_news_con = "UPDATE news_metadata SET `portal_ids` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `portal_ids`, ','), ',$id,', ','))
										WHERE FIND_IN_SET('$id', `portal_ids`) and status =1";
										$update_news_con_rs = mysqli_query($link,$update_news_con);
										wh_log("Update News Metadata Query - ".$update_news_con);
										// Ends
										if($update_news_con_rs)
										{
											// Update Users Details
											$update_users = "UPDATE users SET `portal_ids` = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', `portal_ids`, ','), ',$id,', ','))
											WHERE FIND_IN_SET('$id', `portal_ids`) and status =1";
											$update_users_rs = mysqli_query($link,$update_users);
											wh_log("Update Users Query - ".$update_users);
											// Ends
											if($update_news_con_rs)
											{
												$arr = array();
												$response['status']=true;
												$response['message']="Successfully Deleted Portal Data";
												$response['data']=$arr;

											}
											else
											{
												$arr = array();
												$response['status']=false;
												$response['message']=mysqli_error($link);
												$response['data']=$arr;
											}
										}
										else
										{
											$arr = array();
											$response['status']=false;
											$response['message']=mysqli_error($link);
											$response['data']=$arr;
										}
									}
									else
									{
										$arr = array();
										$response['status']=false;
										$response['message']=mysqli_error($link);
										$response['data']=$arr;
									}
								}
								else
								{
									$arr = array();
									$response['status']=false;
									$response['message']=mysqli_error($link);
									$response['data']=$arr;
								}
							}
							else
							{
								$arr = array();
								$response['status']=false;
								$response['message']=mysqli_error($link);
								$response['data']=$arr;
							}
						}
						else
						{
							$arr = array();
							$response['status']=false;
							$response['message']=mysqli_error($link);
							$response['data']=$arr;
						}
					}
					else
					{
							$response['status']=false;
							$response['message']="No Data Found Regarding This Portal Id ".$id;
							$response['data']=$arr;	
					}
				}
				else
				{
					$arr = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']=$arr;
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


