<?php
include "../includes/config.php";
wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));

$video_id = isset($_REQUEST['video_id']) ? trim($_REQUEST['video_id']) :'';
$response = array();

if(empty($video_id) || $video_id == null)
{
$final_array = array();
$response['status']=false;
$response['message']="All Parameters are needed.";
$response['data']= $final_array;	
}
else
{
	// Check Sub category of video_id is present or not in database
	$getvideoList = "SELECT * FROM category where status =1 and parent =$video_id ORDER BY insertion_time desc";
	wh_log("Subcategory Query Executed : ".$getvideoList);
	$getvideoList_rs = @mysql_query($getvideoList);
	if(mysql_num_rows($getvideoList_rs) > 0)
	{
		echo "sub category video list";
		wh_log("Rows Found for category -- ".mysql_num_rows($getvideoList_rs));
		while($row  = mysql_fetch_assoc($getvideoList_rs))
		{  
			
			// Sub Categories
			$subcatid = $row['id'];
			$sub_cat_data = array();
			$data = array();
			$sub_cat_data = array("id"=>$row['id'],"cat_name"=>$row['cat_name']);
			// Sub Sub Categories
			$getSubCatList = "SELECT * FROM category where status =1 and parent =$subcatid ORDER BY insertion_time desc";
			wh_log("Sub Sub Category Query Executed : ".$getSubCatList);
			$getSubCatList_rs = @mysql_query($getSubCatList);
			if(mysql_num_rows($getSubCatList_rs) > 0)
			{
			   wh_log("Rows Found for sub sub category -- ".mysql_num_rows($getSubCatList_rs));
			   while($row1  = mysql_fetch_assoc($getSubCatList_rs))
			   {
				   $data[]= array("id"=>$row1['id'],"cat_name"=>$row1['cat_name']);
			   }
			   wh_log("Data Sub Sub Cats : ".str_replace("\n"," ", print_r($data, true)));
			   $sub_cat_data['child']=  $data;
			}
		   
			$final_array['parent'][] =  $sub_cat_data;
		}
	}
	else
	{
		echo "parent category video list";
		$getvideoList = "select * from videos where find_in_set($video_id,`cat_id`) ORDER BY insertion_time desc";
		wh_log("getvideoList Query Executed : ".$getvideoList);
		$getvideoList_rs = @mysql_query($getvideoList);
		if(mysql_num_rows($getvideoList_rs) > 0)
		{
			wh_log("Rows Found for category -- ".mysql_num_rows($getvideoList_rs));
			while($row  = mysql_fetch_assoc($getvideoList_rs))
			{ 
				$data[] = array("cat_id"=>$row['cat_id'],"video_url"=>$row['video_url'],"status"=>$row['status'],"language"=>$row['language'],
			"description"=>$row['description'],"like"=>$row['like'],"view"=>$row['view'],"dislike"=>$row['dislike'],"cover_image"=>$row['cover_image'],
			"video_length"=>$row['video_length'],"video_mime"=>$row['video_mime'],"min_age_req"=>$row['min_age_req']);
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
		$response['message']="No Videos Found For This Video Id.";
		$response['data'] = $data;	
		}
	}
}
	
	
	
	
	
	/* if(mysql_num_rows($getCatList_rs) > 0)
	{
		wh_log("Rows Found for category -- ".mysql_num_rows($getCatList_rs));
		while($row  = mysql_fetch_assoc($getCatList_rs))
		{  
			
			// Sub Categories
			$subcatid = $row['id'];
			$sub_cat_data = array();
			$data = array();
			$sub_cat_data = array("id"=>$row['id'],"cat_name"=>$row['cat_name']);
			// Sub Sub Categories
			$getSubCatList = "SELECT * FROM category where status =1 and parent =$subcatid ORDER BY insertion_time desc";
			wh_log("Sub Sub Category Query Executed : ".$getSubCatList);
			$getSubCatList_rs = @mysql_query($getSubCatList);
			if(mysql_num_rows($getSubCatList_rs) > 0)
			{
			   wh_log("Rows Found for sub sub category -- ".mysql_num_rows($getSubCatList_rs));
			   while($row1  = mysql_fetch_assoc($getSubCatList_rs))
			   {
				   $data[]= array("id"=>$row1['id'],"cat_name"=>$row1['cat_name']);
			   }
			   wh_log("Data Sub Sub Cats : ".str_replace("\n"," ", print_r($data, true)));
			   $sub_cat_data['child']=  $data;
			}
		   
			$final_array['parent'][] =  $sub_cat_data;
		}
		//print_r($final_array);
		//echo '<br>';
		//print_r($data);
		//die;
		
	}
	if(!empty($final_array))
	{
	$response['status']=true;
	$response['message']="Success";
	$response['data'][] = $final_array;
	} else {
	$final_array = array();
	$response['status']=false;
	$response['message']="No sub Categories Found in given Category.";
	$response['data'][] = $final_array;
	} */


wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response);
?>


