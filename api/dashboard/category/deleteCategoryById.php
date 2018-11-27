<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "DelCat";
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
			
			$id = mysqli_real_escape_string($link,trim(isset($req_json['catId']))) ? mysqli_real_escape_string($link,trim($req_json['catId'])) :'';
				
			if(empty($id) || $id == null)
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Category id is mandatory.";
				$response['data'] = $Client_data;
			}
			elseif(!is_numeric($id))
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']="Allowed only numbers in category Id parameter";
				$response['data']=$Client_data;
			}
			else
			{
				// Check uid is present or not in users table
				$check_query = "select * from category WHERE id = $id and status = 1";
				$check_query_rs = mysqli_query($link,$check_query);
				wh_log("Category Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
				if(mysqli_num_rows($check_query_rs) > 0)
				{
					// Insert Into Logs
					if($row  = mysqli_fetch_assoc($check_query_rs))
					{
						 $name = $row['cat_name'];
						 $create_time = $row['insertion_time'];
						 $insert_logs = "Insert into logs (name,'type',created_on) values ('$name','category','$create_time')";
						 $insert_logs_rs = mysqli_query($link,$insert_logs);
						 //print_r($row);
					}
					// Ends

					$query = "delete from category WHERE id = $id and status = 1";
					wh_log("Delete Category Query - ".$query);
					$query_rs = mysqli_query($link,$query);
					if($query_rs)
					{
						$Client_data = array();
						$response['status']=true;
						$response['message']="Category Successfully Deleted.";
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


