<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
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
					//Delete Category Details
					$edit_query = "update portals set status = '0' where portal_id = '$id'";
					$edit_query_rs = mysqli_query($link,$edit_query);
					
					wh_log("Update Portal Query - ".$edit_query_rs);
					if($edit_query_rs)
					{
						$arr = array();
						$response['status']=true;
						$response['message']="Successfully Deleted.";
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


