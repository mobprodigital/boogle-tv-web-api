<?php
/************************************ Function Most Viewed Videos **************************/
function getAllMostViewedVideosArray($start,$count)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `view` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = @mysql_query($videoList);

	wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
	if(mysql_num_rows($videoList_rs) > 0)
	{
		while($row  = mysql_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getMostViewedVideosByCategoryID($values,$start,$count)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `view` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysql_query($videoList);
		wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
    return $video_array;
} 
function sortByView($a, $b)
{
    $a = $a['viewsCount'];
    $b = $b['viewsCount'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}
/*********************************  Most Viewed Videos Functions Ends ***********************************/

/************************** Common Function For Video Array *********************************************/
function videoArray($row)
{
	$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
	$url = 'videos'.$video_name;
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	$cat_ids = comma_separated_to_array($row['cat_id']);
	$tags = comma_separated_to_array($row['video_tags']);
	
	$video_temp = array("id"=>$row['id'],"title"=>$row['title'],"description"=>$row['description'],
	"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
	"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
	"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
    return $video_temp;
} 
/**************************************** Ends **********************************************************/





/************************************ Function For Most Liked Videos **************************/
function getAllMostLikedVideosArray($start,$count)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `like` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = @mysql_query($videoList);

	wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
	if(mysql_num_rows($videoList_rs) > 0)
	{
		while($row  = mysql_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("Liked Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getMostLikedVideosByCategoryID($values,$start,$count)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `like` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysql_query($videoList);
		wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
    return $video_array;
} 
function sortByLike($a, $b)
{
    $a = $a['likesCount'];
    $b = $b['likesCount'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}
/*********************************  Most Liked Videos Functions Ends ***********************************/
 
 
 

/************************************ Function For Most Recent/Latest Videos **************************/
function getAllLatestVideos($start,$count)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `insertion_time` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = @mysql_query($videoList);

	wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
	if(mysql_num_rows($videoList_rs) > 0)
	{
		while($row  = mysql_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("All Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getMostLatestVideosByCategoryID($values,$start,$count)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `insertion_time` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysql_query($videoList);
		wh_log("Rows Found for video -- ".mysql_num_rows($videoList_rs));
		if(mysql_num_rows($videoList_rs) > 0)
		{
			while($row  = mysql_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
	wh_log("Categorywise Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
    return $video_array;
} 
function sortByRecent($a, $b)
{
    $a = $a['createDate'];
    $b = $b['createDate'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}
/*********************************  Ends Most Recent/Latest Videos Functions  ***********************************/
?>