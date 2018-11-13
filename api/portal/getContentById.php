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
	elseif(!is_numeric($contentId))
	{
		$data = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in content id parameter";
		$response['data']= $data;
	}
	elseif(empty($contentId) || $contentId == null)
	{
		$data = array();
		$response['status']=false;
		$response['message']="Content Id Parameter Missing";
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
				if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
				
				// Get Content By Id
				$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where find_in_set($portalid,t1.`portal_ids`) and t1.id = $contentId and t1.content_type ='$type' and t1.status = 1";
				wh_log("Get Content Portal wise Query Executed : ".$getData);
				$getData_rs = mysqli_query($link,$getData);
				if($getData_rs)
				{
					if(mysqli_num_rows($getData_rs) > 0)
					{ 
						while($row  = mysqli_fetch_assoc($getData_rs))
						{  
							$source = 'portal'; 
							if($contentType == 2) { $data[] = videoArray($row,$ipath,$vpath,$link,$source);}
							elseif($contentType == 4) { $data[] = textArray($row,$ipath,$link); }
						}
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
						$response['message']="No Content Found For This Portal ".$portal;
						$response['data'] = $data;
					}
				}
				else
				{
					$data = array();
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data'] = $data;
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


