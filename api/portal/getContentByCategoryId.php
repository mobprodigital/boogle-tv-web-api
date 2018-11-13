<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$portal = mysqli_real_escape_string($link,isset($req_data['portalName'])) ? mysqli_real_escape_string($link,trim($req_data['portalName'])) :'';
	//$categoryId = mysqli_real_escape_string($link,isset($req_data['categoryId'])) ? mysqli_real_escape_string($link,trim($req_data['categoryId'])) :'';
	$contentType = mysqli_real_escape_string($link,isset($req_data['contentType'])) ? mysqli_real_escape_string($link,trim($req_data['contentType'])) :'';
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	
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
	/* elseif(!is_numeric($categoryId))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in category id parameter";
		$response['data']= $data;
	}
	elseif(empty($categoryId) || $categoryId == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Category Id Parameter Missing";
		$response['data']= $data;
	} */
	else
	{
		// Check Array Values
		$value = check_array_values($req_data['categoryId']);
		wh_log("Array contains integer values result : ".$value);
		if($value)
		{   
			// Array have all integers value.
			//Check Portal Name With ContentType exist or not
			$portalCheck = "SELECT * FROM `portals` WHERE status =1 and `name` ='$portal' and find_in_set($contentType,`content_type`)";
			$portalCheck_rs = mysqli_query($link,$portalCheck);
			wh_log("Portal Check Query Executed : ".$portalCheck);
			if(mysqli_num_rows($portalCheck_rs) > 0)
			{
				//Get Portal ID
				if($portalrow = mysqli_fetch_assoc($portalCheck_rs))
				{ 
					$portalid = $portalrow['portal_id'];
					
					// Get content By Category Id
					$data = getContentByCategoryID($contentType,$videoBaseURL,$imageBaseURL,$portalid,$req_data['categoryId'],$start,$count,$link);
					wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
					usort($data, 'sortByRecent');
					wh_log("Sorted Final Array : ".str_replace("\n"," ", print_r($data, true)));
				}
				wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
				if(!empty($data))
				{
				$response['status']=true;
				$response['message']="Success";
				$response['data'] = $data;	
				} else {
				$data = array();
				$response['status']=false;
				$response['message']="No Content Found For This Portal ".$portal;
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
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Category Id should be numeric.";
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


