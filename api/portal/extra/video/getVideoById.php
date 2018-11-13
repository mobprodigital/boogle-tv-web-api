<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$videoId = mysqli_real_escape_string($link,isset($req_data['videoId'])) ? mysqli_real_escape_string($link,trim($req_data['videoId'])) :'';
	$portal = mysqli_real_escape_string($link,isset($req_data['portalName'])) ? mysqli_real_escape_string($link,trim($req_data['portalName'])) :'';
	

	if(empty($videoId) || $videoId == null)
	{
	$data = array();
	$response['status']=false;
	$response['message']="videoId Parameter Missing.";
	$response['data']= $data;
	}
	elseif (!is_numeric($videoId))
	{
	$data = array();
	$response['status']=false;
	$response['message']="Allowed only numbers in videoId Parameter";
	$response['data']= $data;
	}
	if(empty($portal) || $portal == null)
	{
	$data = array();
	$response['status']=false;
	$response['message']="Portal Name Parameter Missing.";
	$response['data']= $data;
	}
	else
	{
		// Check Portal Name exist or not
		$portalCheck = "SELECT * FROM `portals` where name = '$portal' and status =1";
		$portalCheck_rs = mysqli_query($link,$portalCheck);
		wh_log("Portal Check Query Executed : ".$portalCheck." | Rows Found for video -- ".mysqli_num_rows($portalCheck_rs));
		if(mysqli_num_rows($portalCheck_rs) > 0)
		{
			//Get Portal ID
			if($row = mysqli_fetch_assoc($portalCheck_rs))
			{ 
				$portalid = $row['portal_id'];
			}
			//Ends
			// Get Content
			$data = getVideoByID($portalid,$videoId,$link,$imageBaseURL,$videoBaseURL);
			wh_log("Final Video Array : ".str_replace("\n"," ", print_r($data, true)));
			if(!empty($data))
			{
			$response['status']=true;
			$response['message']="Success";
			$response['data'] = $data;	
			} else {
			$data = array();
			$response['status']=false;
			$response['message']="No Video Found Regarding This Portal.";
			$response['data'] = $data;	
			}
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Invalid Portal Name";
			$response['data'] = $data;	
		}
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


