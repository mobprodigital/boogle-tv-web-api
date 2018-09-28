<?php
include "../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$sql = "SELECT id,cat_name FROM category WHERE parent =? and status = ? ORDER BY insertion_time desc";
	if($stmt = mysqli_prepare($link, $sql))
	{
		$parent = 0;
		$status = 1;
		mysqli_stmt_bind_param($stmt,'ii', $parent, $status);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt,$id,$catname);
		mysqli_stmt_store_result($stmt);
		$count = mysqli_stmt_num_rows($stmt);
		if($count > 0)
		{ 
			while (mysqli_stmt_fetch($stmt)) 
			{
			$rootcat_data[] = array("id"=>$id,"name"=>$catname);
			}
	
			
		}
	}
	wh_log("Total Rows ".$count." | Final Array : ".str_replace("\n"," ", print_r($rootcat_data, true)));
	if(!empty($rootcat_data))
	{
	$response['status']=true;
	$response['message']="Success";
	$response['data'] = $rootcat_data;
	} else {
	$rootcat_data = array();
	$response['status']=false;
	$response['message']="No Root Categories Found.";
	$response['data'] = $rootcat_data;
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


