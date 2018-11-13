<?php
include "../../includes/config.php";
include "../../includes/functions.php";

$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$portal = mysqli_real_escape_string($link,isset($req_data['portalName'])) ? mysqli_real_escape_string($link,trim($req_data['portalName'])) :'';
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	
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
			$data = getAllText($portalid,$start,$count,$link,$imageBaseURL);
			wh_log("Final Text Array : ".str_replace("\n"," ", print_r($data, true)));
			if(!empty($data))
			{
			$response['status']=true;
			$response['message']="Success";
			$response['data'] = $data;	
			} else {
			$data = array();
			$response['status']=false;
			$response['message']="No Text Found Regarding This Portal.";
			$response['data'] = $data;	
			}
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="No Data Found.";
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


