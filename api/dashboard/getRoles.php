<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();


if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$query = "SELECT * FROM roles WHERE status = '1'";
		wh_log("Roles Query - ".$query);
		$query_rs = mysqli_query($link,$query);
		if($query_rs)
		{
			if(mysqli_num_rows($query_rs) > 0)
			{
				while($row = mysqli_fetch_assoc($query_rs))
				{ 
					// Get Role Details
					$role_array[] = singleRoleArray($row);
					//Ends
				}
			}
		}
		if(empty($role_array))
		{
			$role_array = array();
			$response['status']=false;
			$response['message']= "No roles available";
			$response['data'] = $role_array;
		}
		else
		{
			$response['status']=true;
			$response['message']= "Success";
			$response['data'] = $role_array;
		}
	}
	else
	{
		header('HTTP/1.1 401 Unauthorized', true, 401);
		die;
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


