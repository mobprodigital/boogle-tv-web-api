<?php
/************************** Common Function For Video Array *********************************************/
function videoArray($row)
{
	$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
	$url = 'videos'.$video_name;
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	$cat_ids = comma_separated_to_array($row['cat_id']);
	$tags = comma_separated_to_array($row['video_tags']);
	//echo $row['title'];
	$video_temp = array("id"=>$row['id'],"title"=>stripslashes($row['title']),"description"=>$row['description'],
	"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
	"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
	"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
	//print_r($video_temp);
    return $video_temp;
}

function comma_separated_to_array($string, $separator = ',')
{
  $vals = explode($separator, $string);
  foreach($vals as $key => $val) {
    $vals[$key] = trim($val);
  }
  return array_diff($vals, array(""));
}

/* Check array contains integer values or not */
function check_array_values($array)
{
	foreach ($array as $a => $b) {
    if (!is_int($b)) {
		return false;
    }
}
return true;
}

/**************************************** Ends **********************************************************/

/************************************ Function For Most Viewed Videos **************************/
function getAllMostViewedVideosArray($start,$count,$link)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `view` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("Viewed Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getMostViewedVideosByCategoryID($values,$start,$count,$link)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `view` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
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

 
/************************************ Function For Most Liked Videos **************************/
function getAllMostLikedVideosArray($start,$count,$link)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `like` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("Liked Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getMostLikedVideosByCategoryID($values,$start,$count,$link)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `like` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
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
 function getAllLatestVideos($start,$count,$link)
{
	$get_total = "SELECT * FROM `videos` where status =1 order by `insertion_time` desc";
	wh_log("Query Executed : ".$get_total);
	$get_total_rs = mysqli_query($link,$get_total);
	$total = mysqli_num_rows($get_total_rs);
	if($count < $total) { $hasmore = true; } else { $hasmore = false; }
	
	$videoList = "SELECT * FROM `videos` where status =1 order by `insertion_time` desc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray1($row);
		}
		 
		$response = array("hasMore"=>$hasmore,"videos"=>$video_array);
	}
	
	wh_log("All Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $response;
} 
function videoArray1($row)
{
	$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
	$url = 'videos'.$video_name;
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	$cat_ids = comma_separated_to_array($row['cat_id']);
	$tags = comma_separated_to_array($row['video_tags']);
	
	
	$video_temp = array("id"=>$row['id'],"title"=>stripslashes($row['title']),"description"=>$row['description'],
	"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
	"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
	"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>"images/aa.jpg","medium"=>"","small"=>""));
	//print_r($video_temp);
    return $video_temp;
}


function getMostLatestVideosByCategoryID($values,$start,$count,$link)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `insertion_time` desc limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
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

/*************************************** Function - Video By Category Id ****************************************/
function getVideosByCategoryID($values,$start,$count,$link)
{
	$video_array = array();
	foreach ($values as $value)
	{
		$getvideoList = "select * from videos where find_in_set($value,`cat_id`) ORDER BY insertion_time desc limit $start,$count";
		wh_log("getvideoList Query Executed : ".$getvideoList);
		$getvideoList_rs = mysqli_query($link, $getvideoList);
		if(mysqli_num_rows($getvideoList_rs) > 0)
		{
			wh_log("Rows Found for category -- ".mysqli_num_rows($getvideoList_rs));
			while($row  = mysqli_fetch_assoc($getvideoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		}
	}
	wh_log("Videos By Category Id : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By Tag ****************************************/
function getVideosByTag($tag,$start,$count,$link)
{
	$videoList = "select DISTINCT(`video_url`),`id`,`cat_id`,`title`,`video_tags`,`insertion_time`,`description`,`view`,
	`like`,`dislike` from videos where video_tags like '%$tag%' order by id asc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);
	wh_log("Rows Found for video Tag List -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
	}
	wh_log("Videos By Tag Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
	
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By ID ****************************************/
function getVideosByID($video_id,$link)
{
	$videoList = "select * from videos where status =1 and id = $video_id";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);
	wh_log("Rows Found for video ID List -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array = videoArray($row);
		}
	}
	wh_log("Videos By ID Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
	
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Get All Videos ****************************************/
function getAllVideos($start,$count,$link)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `id` asc limit $start,$count";
	wh_log("Query Executed : ".$videoList);
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		
	}
	wh_log("All Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By Search ****************************************/
function getVideosBySearch($term,$start,$count,$link)
{
	$search_term = trim($term);
	// Get category id of matched search term
	$getvideoList = "select * from category where cat_name like '%$search_term%' and status =1 limit $start,$count";
	wh_log("getvideoList Query Executed : ".$getvideoList);
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	if(mysqli_num_rows($getvideoList_rs) > 0)
	{ 
		while($row = mysqli_fetch_assoc($getvideoList_rs))
		{ 
			 $ids[] = $row['id'];
		}
	}
	// Get videos of marched category ids with a search term
	foreach ($ids as $id)
	{
		$videoList = "select * from videos where find_in_set($id,`cat_id`) and status =1 limit $start,$count";
		wh_log("Query Executed : ".$videoList);
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row1  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$vids[] = $row1['id'];
				$video_array[] = videoArray($row1);
			}
		} 
	}
	// Get videos by tag and titles with a search term
	$id_str = implode(',',$vids);
	$getvideoListbyTag = "select * from ((select * from videos where title like '%$search_term%') 
						  UNION (SELECT * FROM videos WHERE `video_tags` LIKE '%$search_term%')) as u where id NOT IN ($id_str) limit $start,$count";
	wh_log("getvideoList Query Executed : ".$getvideoListbyTag);
	$getvideoListbyTag_rs = mysqli_query($link, $getvideoListbyTag);
	if(mysqli_num_rows($getvideoListbyTag_rs) > 0)
	{ 
		while($row2  = mysqli_fetch_assoc($getvideoListbyTag_rs))
		{ 
			$video_array1[] = videoArray($row2);
		}
	}
	return array_slice(array_merge($video_array,$video_array1),$start,$count);
	

}

/******************************************** Endssss ***********************************************************/
?>