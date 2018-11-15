<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
    
    $start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	$portal = mysqli_real_escape_string($link,isset($req_data['portalName'])) ? mysqli_real_escape_string($link,trim($req_data['portalName'])) :'';
	$contentType = mysqli_real_escape_string($link,isset($req_data['contentType'])) ? mysqli_real_escape_string($link,trim($req_data['contentType'])) :'';
	$tag = mysqli_real_escape_string($link,isset($req_data['tag'])) ? mysqli_real_escape_string($link,trim($req_data['tag'])) :'';
    
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
    elseif(empty($tag) || $tag == null)
	{
	$data = array();
	$response['status']=false;
	$response['message']="tag parameter is missing.";
	$response['data'] = $data;
	}
	elseif(!preg_match("/^[a-zA-Z]+$/", $tag))
	{
	$data = array();
	$response['status']=false;
	$response['message']="Allowed only alphabets in Tag Field";
	$response['data'] = $data;	
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
                $data = getContentByTag(trim($tag),$start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL);
		        wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
		
		        if(!empty($data))
                {
                $response['status']=true;
                $response['message']="Success";
                $response['data'] = $data;	
                } else {
                $data = array();
                $response['status']=false;
                $response['message']="No Content Found For This Tag";
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