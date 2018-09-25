<?php
include "../includes/config.php";
wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	//print_r($req_data);
	//die;
	//echo $data1['videoId'];
	$catId = isset($req_data['catId']) ? trim($req_data['catId']) :'';
	$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	//die;
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
		$getCatList = "SELECT * FROM category where parent =$catId and status = 1 ORDER BY insertion_time desc limit $start,$count";
		wh_log("Subcategory Query Executed : ".$getCatList);
		$getCatList_rs = @mysql_query($getCatList);
		if(mysql_num_rows($getCatList_rs) > 0)
		{
			wh_log("Rows Found for category -- ".mysql_num_rows($getCatList_rs));
			while($row  = mysql_fetch_assoc($getCatList_rs))
			{  
				
				// Sub Categories
				$subcatid = $row['id'];
				$sub_cat_data = array();
				$data = array();
				//if($row['status'] == 0){ $status = false;} else { $status = true; }
				$sub_cat_data = array("id"=>$row['id'],"name"=>$row['cat_name']);
				// Sub Sub Categories
				/* $getSubCatList = "SELECT * FROM category where status =1 and parent =$subcatid ORDER BY insertion_time desc limit $start,$count";
				wh_log("Sub Sub Category Query Executed : ".$getSubCatList);
				$getSubCatList_rs = @mysql_query($getSubCatList);
				if(mysql_num_rows($getSubCatList_rs) > 0)
				{
				   wh_log("Rows Found for sub sub category -- ".mysql_num_rows($getSubCatList_rs));
				   while($row1  = mysql_fetch_assoc($getSubCatList_rs))
				   {
					   $data[]= array("id"=>$row1['id'],"name"=>$row1['cat_name']);
				   }
				   wh_log("Data Sub Sub Cats : ".str_replace("\n"," ", print_r($data, true)));
				   
				  //$sub_cat_data['categories']=  $data;
				} */
			    /*if(count($sub_cat_data['categories'])) { $sub_cat_data['categories']= ""; } */
				$final_array[] =  $sub_cat_data;
			}
		}
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
	/* $response['status']=false;
	$response['message']="No Page Found."; */
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response);
?>


