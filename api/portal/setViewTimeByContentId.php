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
	$view_time = mysqli_real_escape_string($link,isset($req_data['time'])) ? mysqli_real_escape_string($link,trim($req_data['time'])) :'';

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
		$response['message']="Content Id Parameter Missing.";
		$response['data']= $data;
	}
	elseif(!is_numeric($contentId))
	{
        $data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in content Id parameter";
		$response['data']= $data;
	}
	elseif(empty($view_time) || $view_time == null)
	{
		$data = array();
	    $response['status']=false;
		$response['message']="View Time is missing.";
		$response['data']= $data;
	}
	elseif(!is_numeric($view_time))
	{
		$data = array();
	    $response['status']=false;
		$response['message']="View Time should be numeric.";
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
				$view_time_in_hour_minutes = gmdate("H:i:s",$view_time);
				
				/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
                elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
				
				$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
				$dataTable = $carr['dataTable'];

				// Check Content Id Exist Or Not
				$check_contentid = "SELECT * FROM $dataTable WHERE id = $contentId and status = 1 and content_type = '".$carr['type']."' 
                and find_in_set($portalid,`portal_ids`)";
				wh_log("Check Content Id Select Query - ".$check_contentid." | view_time_in_hour_minutes : ".$view_time_in_hour_minutes." | Total Rows - ".mysqli_num_rows($check_contentid_rs));
				$check_contentid_rs = mysqli_query($link,$check_contentid);
				if($check_contentid_rs)
				{
					if(mysqli_num_rows($check_contentid_rs) > 0)
					{
						if($row  = mysqli_fetch_assoc($check_contentid_rs))
			            { 
				            $result = secondsToMinutes($row['view_time'],$view_time_in_hour_minutes);
				            $updateList = "update $dataTable set view_time = '$result' WHERE id = $contentId and status = 1 and content_type = '".$carr['type']."' 
							and find_in_set($portalid,`portal_ids`)";
				            wh_log("Total hh:mm:ss : ".$result. " | Update Query - ".$updateList);
							$updateList_rs = mysqli_query($link,$updateList);
							$count = mysqli_affected_rows($link);
							wh_log("Update Content Query - ".$updateList);
							if($count > 0)
							{
							  $data = array();  
							  $response['status']=true;
							  $response['message']="View Time Increased by ".$view_time. " seconds.";
							  $response['data']= $data;
							}
							else
							{
							  $data = array();
							  $response['status']=false;
							  $response['message']= "Invalid Content Id";
							  $response['data']= $data;
							} 
			            } 

					}
					else
					{
						$data = array();
						$response['status']=false;
						$response['message']="Invalid Content Id";
						$response['data']= $data;
					}
				}
				else
				{
					$data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']= $data;
				}
				//Ends
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