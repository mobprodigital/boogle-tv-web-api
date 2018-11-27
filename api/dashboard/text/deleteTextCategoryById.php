<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();
$content_type_id = 4;

/* Validate Api */
$apiKey = "DelTextCatById";
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
			$client_id = $check['loginClientId'];
			$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :'';
			
			$req_json = json_decode(file_get_contents("php://input"), true);
			wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
			
			$id = mysqli_real_escape_string($link,trim(isset($req_json['categoryId']))) ? mysqli_real_escape_string($link,trim($req_json['categoryId'])) :'';
			wh_log("categoryId - ".$id." | client id from cookie -".$clientId);
				
			if((empty($id) || $id == null))
			{
				$response['status']=false;
				$response['message']="categoryId Parameter is Missing.";
				$response['data']="";
			}
			elseif(!is_numeric($id))
			{
				$response['status']=false;
				$response['message']="Allowed only numbers in categoryId parameter";
				$response['data']="";
			}
			else
			{
				// Check Ctegory Id exist or not
				$check = "select * from category where id = $id and content_type_id = $content_type_id";
				$check_rs = mysqli_query($link,$check);
				if($check_rs)
				{
					if(mysqli_num_rows($check_rs) > 0)
					{
						//Delete Category Details
						$edit_query = "update category set status = '0' where id = $id and content_type_id = $content_type_id";
						$edit_query_rs = mysqli_query($link,$edit_query);
						
						wh_log("Update Category Query - ".$edit_query_rs);
						if($edit_query_rs)
						{
							$response['status']=true;
							$response['message']="Successfully Deleted.";
							$response['data']="";
						}
						else
						{
							$response['status']=false;
							$response['message']=mysqli_error($link);
							$response['data']="";
						}
					}
					else
					{
						$response['status']=false;
						$response['message']="No Data Found Regarding This category Id ".$id;
						$response['data']="";
					}
				}
				else
				{
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']="";
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


