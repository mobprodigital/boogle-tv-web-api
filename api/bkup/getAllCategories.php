<?php
include "../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	
	//$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	//$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	
	$getRootCatList = "SELECT * FROM category where status =1";
	wh_log("Root category Query Executed : ".$getRootCatList);
	$getRootCatList_rs = @mysql_query($getRootCatList);
	if(mysql_num_rows($getRootCatList_rs) > 0)
	{
		wh_log("Rows Found for category -- ".mysql_num_rows($getRootCatList_rs));
		while($row  = mysql_fetch_assoc($getRootCatList_rs))
		{
			//if($row['status'] == 0){ $status = false;} else { $status = true; }
			$allcat_data[] = array("id"=>$row['id'],"name"=>$row['cat_name']);
		}
	}
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


