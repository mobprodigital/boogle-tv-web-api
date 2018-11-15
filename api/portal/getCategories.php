<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$name = mysqli_real_escape_string($link,trim(isset($req_json['portalName']))) ? mysqli_real_escape_string($link,trim($req_json['portalName'])) :'';
	$contentTypeId = mysqli_real_escape_string($link,trim(isset($req_json['contentTypeId']))) ? mysqli_real_escape_string($link,trim($req_json['contentTypeId'])) :'';
	$start = isset($req_json['start']) ? trim($req_json['start']) :'0';
	$count = isset($req_json['count']) ? trim($req_json['count']) :'9';
	
	if(empty($name) || $name == null)
	{
		$cat_array = array();
		$response['status']=false;
		$response['message']="Portal name is mandatory.";
		$response['data'] = $cat_array;
	}
	elseif(empty($contentTypeId) || $contentTypeId == null)
	{
		$cat_array = array();
		$response['status']=false;
		$response['message']="Content Type Id is mandatory.";
		$response['data'] = $cat_array;
	}
	else
	{
		// Check Portal Name With ContentType exist or not
	    $portalIds = portalExist($name,$link,$contentTypeId);
		if($portalIds)
		{
			$getcategory = "SELECT * FROM `category` WHERE find_in_set($portalIds,`portal_ids`) and find_in_set($contentTypeId,`content_type_id`) and status =1 ORDER BY `insertion_time` desc limit $start,$count";
			wh_log("Category Query - ".$getcategory);
			$getcategory_rs = mysqli_query($link,$getcategory);
			if($getcategory_rs)
			{
				if(mysqli_num_rows($getcategory_rs) > 0)
				{
					while($cat_row  = mysqli_fetch_assoc($getcategory_rs))
					{  
						$cat_array[] = singleCategoryArray($cat_row,$link);
					}
					if(empty($cat_array))
					{
						$cat_array = array();
						$response['status']=false;
						$response['message']="No Categories Found Regarding Portal ". $name;
						$response['data'] = $cat_array;
					}
					else
					{
						$response['status']=true;
						$response['message']= "Success";
						$response['data'] = $cat_array;
					}
				}
				else
				{
					$cat_array = array();
					$response['status']=false;
					$response['message']="No Categories Found Regarding Portal ". $name;
					$response['data'] = $cat_array;
				}
			}
			else
			{
				$cat_array = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data'] = $cat_array;
			}
		}
		else
		{
			$cat_array = array();
			$response['status']=false;
			$response['message']="Invalid Portal Name";
			$response['data'] = $cat_array;
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


