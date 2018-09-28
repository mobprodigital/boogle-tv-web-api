<?php
include "../includes/config.php";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_data, true)));
	$req_data = json_decode(file_get_contents("php://input"), true);
	//print_r($req_data);
	$catId = mysqli_real_escape_string($link,isset($req_data['id'])) ? mysqli_real_escape_string($link,trim($req_data['id'])) :'';
	$start = mysqli_real_escape_string($link,isset($req_data['start'])) ? mysqli_real_escape_string($link,trim($req_data['start'])) :'0';
	$count = mysqli_real_escape_string($link,isset($req_data['count'])) ? mysqli_real_escape_string($link,trim($req_data['count'])) :'9';
	
	$response = array();

	if(empty($catId) || $catId == null)
	{
	$final_array = array();
	$response['status']=false;
	$response['message']="catId Parameter Missing.";
	$response['data']= $final_array;	
	}
	elseif (!is_numeric($catId))
	{
	$final_array = array();
	$response['status']=false;
	$response['message']="Allowed only numbers in catId Parameter";
	$response['data']= $final_array;
	} 
	else
	{
		$sql = "SELECT id,cat_name FROM category where parent =? and status = ? ORDER BY insertion_time desc limit ?,?";
		if($stmt = mysqli_prepare($link, $sql))
		{
			$parent = $catId;
			$status = 1;
			$start1 = $start;
			$count1 = $count;
			mysqli_stmt_bind_param($stmt,'iiii', $parent, $status,$start1,$count1);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt,$id,$catname);
			mysqli_stmt_store_result($stmt);
			$count = mysqli_stmt_num_rows($stmt);
			if($count > 0)
			{ 
				while (mysqli_stmt_fetch($stmt)) 
				{
				$final_array[] = array("id"=>$id,"name"=>$catname);
				}
			}
		}
		wh_log("Total Rows ".$count." | Final Array : ".str_replace("\n"," ", print_r($final_array, true)));
		if(!empty($final_array))
		{
		$response['status']=true;
		$response['message']="Success";
		$response['data'] = $final_array;
		} else {
		$final_array = array();
		$response['status']=false;
		$response['message']="No sub Categories Found in given Category.";
		$response['data'][] = $final_array;
		}
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response);
?>


