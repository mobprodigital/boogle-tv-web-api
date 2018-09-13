<?php
include "../includes/config.php";
wh_log("Request Parameters ".str_replace("\n"," ", print_r($_REQUEST, true)));

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_data = json_decode(file_get_contents("php://input"), true);
	//print_r($req_data);
	//die;
	$video_id = isset($req_data['id']) ? trim($req_data['id']) :'';
	$response = array();

	if(empty($video_id) || $video_id == null)
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="id Parameter Missing.";
	$response['data']= $video_data;
	}
	elseif (!is_numeric($video_id))
	{
	$video_data = array();
	$response['status']=false;
	$response['message']="Allowed only numbers in Id Parameter";
	$response['data']= $video_data;
	}
	else
	{
		$getVideo = "select * from videos where status =1 and id = $video_id";
		wh_log("Video Query Executed : ".$getVideo);
		$getVideo_rs = @mysql_query($getVideo);
		if(mysql_num_rows($getVideo_rs) > 0)
		{
			wh_log("Rows Found for video -- ".mysql_num_rows($getVideo_rs));
			while($row  = mysql_fetch_assoc($getVideo_rs))
			{ 
				$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
				$url = 'videos'.$video_name;
				$video_date = explode(' ',$row['insertion_time']);
				$insertion_time = date("d/m/Y", strtotime($video_date[0]));
				$cat_ids = comma_separated_to_array($row['cat_id']);
				$tags = comma_separated_to_array($row['video_tags']);
				
				$video_data = array("id"=>$row['id'],"title"=>$row['title'],"description"=>$row['description'],
				"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
				"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
				"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
			}
		}
		if(!empty($video_data))
		{
		$response['status']=true;
		$response['message']="Success";
		$response['data'] = $video_data;
		} 
		else 
		{
		$video_data = array();
		$response['status']=false;
		$response['message']="No Video Found.";
		$response['data'] = $video_data;
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


