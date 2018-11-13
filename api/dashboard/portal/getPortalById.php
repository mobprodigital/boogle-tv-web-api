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
		
		$portalId = mysqli_real_escape_string($link,trim(isset($req_json['portalId']))) ? mysqli_real_escape_string($link,trim($req_json['portalId'])) :'0';
		
		if(empty($portalId) || $portalId == null)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="portalId is mandatory.";
			$response['data'] = $portal_data;
		}
		elseif(!is_numeric($portalId))
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in portal Id parameter";
			$response['data']=$portal_data;
		}
		else
		{
			// Get Poratl Array By portal ID
			$query = "SELECT * FROM portals WHERE portal_id = $portalId and status = '1'";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					while($row  = mysqli_fetch_assoc($query_rs))
					{  
						$portal_data = singlePortalArray($row,$link);
					}
					if(!empty($portal_data))
					{
					$response['status']=true;
					$response['message']="Success";
					$response['data'] = $portal_data;	
					} else {
					$portal_data = array();
					$response['status']=false;
					$response['message']="No Data Found For This portal Id.";
					$response['data'] = $portal_data;	
					}
				}
				else
				{
					$portal_data = array();
					$response['status']=false;
					$response['message']="No Data Found For This portal Id.";
					$response['data'] = $portal_data;
				}
			}
			else
			{
				$portal_data = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data'] = $portal_data;
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


