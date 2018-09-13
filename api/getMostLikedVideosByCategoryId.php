<?php
include "../includes/config.php";

wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	//print_r($req_data);
	//die;
	
	$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	if(empty($req_data['id'])) 
	{
		$videoList = "SELECT * FROM `videos` where status =1 order by `like` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = @mysql_query($videoList);

		wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{  
				$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
				$url = 'videos'.$video_name;
				$video_date = explode(' ',$row['insertion_time']);
				$insertion_time = date("d/m/Y", strtotime($video_date[0]));
						
				$cat_ids = comma_separated_to_array($row['cat_id']);
				$tags = comma_separated_to_array($row['video_tags']);
				
				$data[] = array("id"=>$row['id'],"title"=>$row['title'],"description"=>$row['description'],
				"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
				"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
				"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
			}
			
		}
	}
	else
	{
		$value = check_array_values($req_data['id']);
		if($value)
		{
			// Array have all integers value.
			foreach ($req_data['id'] as $value)
			{
				//echo  "$value<br />";
				$getvideoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `like` desc limit $start,$count";
				wh_log("getvideoList Query Executed : ".$getvideoList);
				$getvideoList_rs = @mysql_query($getvideoList);
				if(mysql_num_rows($getvideoList_rs) > 0)
				{
					wh_log("Rows Found for category -- ".mysql_num_rows($getvideoList_rs));
					while($row  = mysql_fetch_assoc($getvideoList_rs))
					{ 
						$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
						$url = 'videos'.$video_name;
						$video_date = explode(' ',$row['insertion_time']);
						$insertion_time = date("d/m/Y", strtotime($video_date[0]));
						
						$cat_ids = comma_separated_to_array($row['cat_id']);
						$tags = comma_separated_to_array($row['video_tags']);
						
						$data[] = array("id"=>$row['id'],"title"=>$row['title'],"description"=>$row['description'],
						"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
						"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
						"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
					}
				}
			}
			//print_r($data);
			//die;
		}
		else
		{
			$data = array();
			$response['status']=false;
			$response['message']="Id parameter should be numeric.";
			$response['data'] = $data;
		}
	}
	usort($data, 'sortByLike');
	if(!empty($data))
	{
	$response['status']=true;
	$response['message']="Success";
	$response['data'] = $data;	
	} else {
	$data = array();
	$response['status']=false;
	$response['message']="No Videos Found For This Category.";
	$response['data'] = $data;	
	}
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_NUMERIC_CHECK);
?>


