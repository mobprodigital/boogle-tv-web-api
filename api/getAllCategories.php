<?php
include "../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	
	//$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	//$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	
	$getRootCatList = "SELECT id,cat_name FROM category where status =?";
	if($stmt = mysqli_prepare($link, $getRootCatList))
	{
		$status = 1;
		mysqli_stmt_bind_param($stmt,'i',$status);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt,$id,$catname);
		mysqli_stmt_store_result($stmt);
		$count = mysqli_stmt_num_rows($stmt);
		if($count > 0)
		{ 
			while (mysqli_stmt_fetch($stmt)) 
			{
			$allcat_data[] = array("id"=>$id,"name"=>$catname);
			}
		}
	}
	wh_log("Total Rows ".$count." | Final Array : ".str_replace("\n"," ", print_r($allcat_data, true)));
	if(!empty($allcat_data))
	{
	$response['status']=true;
	$response['message']="Success";
	$response['data'] = $allcat_data;
	} else {
	$allcat_data = array();
	$response['status']=false;
	$response['message']="No Root Categories Found.";
	$response['data'] = $allcat_data;
	}
}
else
{
	/* $response['status']=false;
	$response['message']="No Page Found."; */
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,true);


?>


