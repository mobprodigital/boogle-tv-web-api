<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));

	$portal = mysqli_real_escape_string($link,isset($req_data['portalName'])) ? mysqli_real_escape_string($link,trim($req_data['portalName'])) :'';
	$contentType = mysqli_real_escape_string($link,isset($req_data['contentType'])) ? mysqli_real_escape_string($link,trim($req_data['contentType'])) :'';
	$contentId = mysqli_real_escape_string($link,isset($req_data['contentId'])) ? mysqli_real_escape_string($link,trim($req_data['contentId'])) :'';
	$categoryId = mysqli_real_escape_string($link,isset($req_data['categoryId'])) ? mysqli_real_escape_string($link,trim($req_data['categoryId'])) :'';

	if(empty($portal) || $portal == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Portal Name Parameter Missing.";
		$response['data']= $data;
	}
	elseif(empty($contentType) || $contentType == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Content Type Id Parameter Missing";
		$response['data']= $data;
	}
	elseif(!is_numeric($contentType))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in content type parameter";
		$response['data']= $data;
	}
	elseif(empty($contentId) || $contentId == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Content Id Parameter Missing";
		$response['data']= $data;
	}
	elseif(!is_numeric($contentId))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in content id parameter";
		$response['data']= $data;
	}
	elseif(empty($categoryId) || $categoryId == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Category Id Parameter Missing";
		$response['data']= $data;
	}
	elseif(!is_numeric($categoryId))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in category id parameter";
		$response['data']= $data;
	}
	else
	{
		// Check Portal Name With ContentType exist or not
		$portalCheck = "SELECT * FROM `portals` WHERE status =1 and `name` ='$portal' and find_in_set($contentType,`content_type`)";
		$portalCheck_rs = mysqli_query($link,$portalCheck);
		wh_log("Portal Check Query Executed : ".$portalCheck);
		if(mysqli_num_rows($portalCheck_rs) > 0)
		{
            //Get Portal ID
			if($portalrow = mysqli_fetch_assoc($portalCheck_rs))
			{
				$portalid = $portalrow['portal_id'];
				$data = getRelatedContentsByContentID($categoryId,$contentId,$portalid,$contentType,$link,$videoBaseURL,$imageBaseURL);
		
				wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
				if(!empty($data))
				{
				$response['status']=true;
				$response['message']="Success";
				$response['data'] = $data;	
				} else {
				$data = array();
				$response['status']=false;
				$response['message']="No Videos Found.";
				$response['data'] = $data;	
				} 
			}
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Invalid Request";
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