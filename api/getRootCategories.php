<?php
include "../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$getRootCatList = "SELECT * FROM category where parent =0 and status = 1 ORDER BY insertion_time desc";
	wh_log("Root category Query Executed : ".$getRootCatList);
	$getRootCatList_rs = @mysql_query($getRootCatList);
	if(mysql_num_rows($getRootCatList_rs) > 0)
	{
		wh_log("Rows Found for category -- ".mysql_num_rows($getRootCatList_rs));
		while($row  = mysql_fetch_assoc($getRootCatList_rs))
		{
			// Root Categories
			$rootcat_data[] = array("id"=>$row['id'],"name"=>$row['cat_name']);
		}
	}
	//print_r($rootcat_data);
	//die;
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
	/* $response['status']=false;
	$response['message']="No Page Found."; */
	header("HTTP/1.0 404 Not Found");
	die;
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,true);
//print_r($response);
//die;

?>


