<?php
include "../../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$key = mysqli_real_escape_string($link,trim(isset($req_json['authentication_key']))) ? mysqli_real_escape_string($link,trim($req_json['authentication_key'])) :'0';
	
	if($key == $auth_key)
	{
		foreach($roles as $role)	
		{
			  $arr[] = $role;
		}
		if(empty($arr))
		{
			$arr = array();
			$response['status']=false;
			$response['message']= "No roles available";
			$response['data'] = $arr;
		}
		else
		{
			$response['status']=true;
			$response['message']= "Success";
			$response['data'] = $arr;
		}
	}
	else
	{
		$arr = array();
		$response['status']=false;
		$response['message']= "Authentication Failed";
		$response['data'] = $arr;
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


