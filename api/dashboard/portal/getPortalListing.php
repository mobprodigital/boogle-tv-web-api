<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "LisPor";
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
			$query = "SELECT * FROM portals WHERE status = '1'";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					while($row  = mysqli_fetch_assoc($query_rs))
					{  
						// Get Porat Details
						$portal_array[] = singlePortalArray($row,$link);
						//Ends
					}
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
				$response['message']=mysqli_error($link);
				$response['data'] = $portal_array;
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


