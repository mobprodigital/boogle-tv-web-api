<?php
include "../includes/config.php";
wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	//print_r($req_data);
	//die;
	$start = isset($req_data['start']) ? trim($req_data['start']) :'0';
	$count = isset($req_data['count']) ? trim($req_data['count']) :'9';
	$video_tag = isset($req_data['tag']) ? trim($req_data['tag']) :'';
	$response = array();

	if(empty($video_tag) || $video_tag == null)
	{
	$data = array();
	$response['status']=false;
	$response['message']="tag parameter is missing.";
	$response['data'] = $data;
	}
	elseif(!preg_match("/^[a-zA-Z]+$/", $video_tag))
	{
	$data = array();
	$response['status']=false;
	$response['message']="Allowed only alphabets in Tag Field";
	$response['data'] = $data;	
	}
	else
	{
		$videoList = "select DISTINCT(`video_url`),`id`,`cat_id`,`title`,`video_tags`,`insertion_time`,`description`,`view`,
		`like`,`dislike` from videos where video_tags like '%$video_tag%' order by id desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = @mysql_query($videoList);
		wh_log("Rows Found for video Tag List -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{  //print_r($row);
				$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
				$url = 'videos'.$video_name;
				$video_date = explode(' ',$row['insertion_time']);
				$insertion_time = date("d/m/Y", strtotime($video_date[0]));
				
				$cat_ids = comma_separated_to_array($row['cat_id']);
				$tags = comma_separated_to_array($row['video_tags']);
				/* print_r($cat_ids);
				print_r($tags);
				die; */
				
				$data[] = array("id"=>$row['id'],"title"=>$row['title'],"description"=>$row['description'],
				"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
				"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
				"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
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
		$response['message']="No Videos Found For This Tag";
		$response['data'] = $data;	
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
echo json_encode($response,JSON_NUMERIC_CHECK);
?>


