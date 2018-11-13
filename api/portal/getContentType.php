<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$name = mysqli_real_escape_string($link,trim(isset($req_json['portalName']))) ? mysqli_real_escape_string($link,trim($req_json['portalName'])) :'';
	
	if(empty($name) || $name == null)
	{
		$con_array = array();
		$response['status']=false;
		$response['message']="Portal name is mandatory.";
		$response['data'] = $con_array;
	}
	else
	{
		$getPortal = "SELECT * from portals where name = '$name' and status =1";
		wh_log("Portal Query - ".$getPortal);
		$getPortal_rs = mysqli_query($link,$getPortal);
		if($getPortal_rs)
		{
			if(mysqli_num_rows($getPortal_rs) > 0)
			{
				if($row  = mysqli_fetch_assoc($getPortal_rs))
				{
					$contentIds = $row['content_type'];
					$getcontent = "SELECT * FROM `content_type` WHERE id IN ($contentIds) and status =1";
					wh_log("Content Type Query - ".$getcontent);
					$getcontent_rs = mysqli_query($link,$getcontent);
					if($getcontent_rs)
					{
						if(mysqli_num_rows($getcontent_rs) > 0)
						{
							while($con_row  = mysqli_fetch_assoc($getcontent_rs))
							{  
								$con_array[] = singleContentTypeArray($con_row,$link);
							}
							if(empty($con_array))
							{
								$con_array = array();
								$response['status']=false;
								$response['message']="No Content Type Found Regarding Portal ". $name;
								$response['data'] = $con_array;
							}
							else
							{
								$response['status']=true;
								$response['message']= "Success";
								$response['data'] = $con_array;
							}
						}
						else
						{
							$con_array = array();
							$response['status']=false;
							$response['message']="No Content Type Found Regarding Portal ". $name;
							$response['data'] = $con_array;
						}
					}
					else
					{
						$con_array = array();
						$response['status']=false;
						$response['message']=mysqli_error($link);
						$response['data'] = $con_array;
					}
					
					
				}
			}
			else
			{
				$con_array = array();
				$response['status']=false;
				$response['message']="Invalid Portal Name";
				$response['data'] = $con_array;
			}
		}
		else
		{
			$con_array = array();
			$response['status']=false;
			$response['message']=mysqli_error($link);
			$response['data'] = $con_array;
		}
	}
	
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,true);
?>


