<?php
include "../includes/config.php";
include "../includes/functions.php";

// Validate domain //
/* $domain = $_SERVER['HTTP_HOST'];
$check = validate_domain($domain);
if(!$check)
{
	$response['status']=false;
	$response['message']="Invalid Request";
	echo json_encode($response);
	die;
} */
// Ends //

$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	if(empty($req_data['id'])) 
	{
		// Get Videos List From All Categories
		$data = getAllVideos($start,$count,$link);
	}
	else
	{
		$value = check_array_values($req_data['id']);
		wh_log("Array contains integer values result : ".$value);
		if($value)
		{
			// Array have all integers value.
			// Get Videos By Category Id
			$data = getVideosByCategoryID($req_data['id'],$start,$count,$link);
			wh_log("Final Array : ".str_replace("\n"," ", print_r($data, true)));
			usort($data, 'sortByRecent');
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Id should be numeric.";
			$response['data'] = $data;
		}
	}
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
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>


