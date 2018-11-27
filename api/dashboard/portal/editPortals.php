<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "EditPor";
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
			$name = mysqli_real_escape_string($link,trim(isset($req_json['portalName']))) ? mysqli_real_escape_string($link,trim($req_json['portalName'])) :'';
			$url = mysqli_real_escape_string($link,trim(isset($req_json['url']))) ? mysqli_real_escape_string($link,trim($req_json['url'])) :'';
			$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
			$agreement_tenure = mysqli_real_escape_string($link,trim(isset($req_json['agreementTenure']))) ? mysqli_real_escape_string($link,trim($req_json['agreementTenure'])) :'0';
			
			if(! empty($req_json['contentType'])) { $content_type_status = check_array_values($req_json['contentType']); }
			
			wh_log("Poratl Id - ".$id." | Name - ".$name." | Url - ".$url." | Email - ".$email." | Agreement Tenure - ".$agreement_tenure);
			
			if(empty($id) || $id == null)
			{
				$response['status']=false;
				$response['message']="Poratl ID Parameter Missing.";
			}
			elseif(empty($name) || $name == null)
			{
				$response['status']=false;
				$response['message']="Poratl Name Parameter Missing.";
			}
			elseif(empty($url) || $url == null)
			{
				$response['status']=false;
				$response['message']="Poratl Url Parameter Missing.";
			}
			elseif(!is_numeric($id))
			{
				$response['status']=false;
				$response['message']="Allowed only numbers in id parameter";
			}
			elseif(!preg_match("/^[a-zA-Z ]+$/", $name))
			{
				$response['status']=false;
				$response['message']="Only letters and white space allowed in portal name parameter.";
			}
			elseif(empty($req_json['contentType']))
			{
				$portal_data = array();
				$response['status']=false;
				$response['message']="Content Type Parameter Missing.";
				$response['data']= $portal_data;
			}
			elseif(!$content_type_status)
			{
				$portal_data = array();
				$response['status']=false;
				$response['message']="Content type should be numeric.";
				$response['data']= $portal_data;
			}
			else
			{
				$query = "SELECT * FROM portals WHERE portal_id = '$id' and status =1";
				wh_log("Query - ".$query);
				$query_rs = mysqli_query($link,$query);
				if($query_rs)
				{
					if(mysqli_num_rows($query_rs) > 0)
					{
						// update case
						// Convert Array To Comma Seperated Strings
						$contentTypeIds = array_to_comma_separated($req_json['contentType']);
						
						//Edit Category Details
						$edit_query = "update portals set name = '$name',url='$url',email='$email',content_type = '$contentTypeIds', agreement_tenure='$agreement_tenure' where portal_id = '$id'";
						$edit_query_rs = mysqli_query($link,$edit_query);
						
						wh_log("Update Portal Query - ".$edit_query_rs);
						if($edit_query_rs)
						{
							// Get Portal Details
							$portal_data = getPortalDataById($id,$link);
							//Ends
							
							$response['status']=true;
							$response['message']="Successfully Updated.";
							$response['data']= $portal_data;
						}
						else
						{
							$portal_data = array();
							$response['status']=false;
							$response['message']=mysqli_error($link);
							$response['data']= $portal_data;
						}
					}
					else
					{
						// Show error
						$portal_data = array();
						$response['status']=false;
						$response['message']="Invalid Portal Id";
						$response['data']= $portal_data;
					}
				}
				else
				{
					$portal_data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']= $portal_data;
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


