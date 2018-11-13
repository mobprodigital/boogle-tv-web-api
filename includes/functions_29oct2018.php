<?php
/************************** Common Function For Video Array *********************************************/
function videoArray($row,$videoBaseURL,$link)
{
	if(!empty($row['video_url'])) { $videoUrl = $videoBaseURL.'/'.$row['video_url']; }
	if(!empty($row['cover_image_url'])) { $imageUrl = $imageBaseURL.'/'.$row['cover_image_url']; }
	
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	// Convert comma seperated strings to array
	if(!empty($row['cat_id'])) { $cat_ids = comma_separated_to_array($row['cat_id']); }
	if(!empty($row['tags'])) { $tags = comma_separated_to_array($row['tags']); } else { $tags = array();}
	if(!empty($row['country'])) { $country = comma_separated_to_array($row['country']); }
	if(!empty($row['portal_ids']))
	{ 
		$pids = comma_separated_to_array($row['portal_ids']); 
		// Get Portal Names
		$portalids = $row['portal_ids'];
		wh_log("Portal ids - ".$portalids);
		$get_portals = "select * from portals where portal_id in ($portalids) and status = 1";
		$get_portals_rs = mysqli_query($link,$get_portals);
		wh_log("Portal Query - ".$get_portals." | Rows Found for video -- ".mysqli_num_rows($get_portals_rs));
		if(mysqli_num_rows($get_portals_rs) > 0)
		{
			while($portal_row  = mysqli_fetch_assoc($get_portals_rs))
			{  
				$portal_array[] = singlePortalArray($portal_row);
				
			}
			wh_log("Poratl Array : ".str_replace("\n"," ", print_r($portal_array, true))); 
		}
		//Ends
	}
	// Ends
	if(empty($portal_array)) { $portal_array = array(); }
	$video_temp_array = array("videoId"=>$row['id'],"categoryId"=>$cat_ids,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"videoUrl"=>$videoUrl,"videoTags"=>$tags,"videoDate"=>$insertion_time,"language"=>$row['language'],"description"=>$row['description'],"cover_image"=>$imageUrl,"videoLength"=>$row['content_length'],"extension"=>$row['extension'],"videoMime"=>$row['mime'],"minAgeReq"=>$row['min_age_req'],"broadcasterName"=>$row['broadcaster_name'],"type"=>$row['type'],"currentAvailability"=>$row['content_availability'],"platform"=>$row['platform'],"adult"=>$row['adult'],"downloadRights"=>$row['download_rights'],"internationalRights"=>$row['intrernational_rights'],"genere"=>$row['genre'],"director"=>$row['director'],"producer"=>$row['producer'],"writer"=>$row['writer'],"musicDirector"=>$row['music_director'],"productionHouse"=>$row['production_house'],"actor"=>$row['actor'],"singer"=>$row['singer'],"country"=>$country);
	wh_log("Video Content Array : ".str_replace("\n"," ", print_r($video_temp_array, true)));
    return $video_temp_array;
} 
/* function videoArray($row)
{
	$local_base_url = 'http://192.168.0.7/boogletv/';
	$localhost_base_url = 'http://localhost/boogletv/';
	
	$video_name = substr($row['video_url'], strripos($row['video_url'], '/'));
	$url = $local_base_url.'videos'.$video_name;
	
	$image_name = substr($row['cover_image'], strripos($row['cover_image'], '/'));
	$image_url = 'image'.$image_name;
	
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	$cat_ids = comma_separated_to_array($row['cat_id']);
	$tags = comma_separated_to_array($row['video_tags']);
	//echo $row['title'];
	$video_temp = array("id"=>$row['id'],"title"=>stripslashes($row['title']),"description"=>$row['description'],
	"categories"=>$cat_ids,"tags"=>$tags,"videoUrl"=>$url,
	"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],
	"createDate"=>$insertion_time,"minAgeReq"=>$row['min_age_req'],"thumbnails"=>array("large"=>$image_url,"medium"=>"","small"=>""));
	wh_log("Video Array : ".str_replace("\n"," ", print_r($video_temp, true)));
    return $video_temp;
} */
/* function videoArray1($row)
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
} */ 

/* Convert comma seperated strings to array*/
function comma_separated_to_array($string, $separator = ',')
{
  $vals = explode($separator, $string);
  foreach($vals as $key => $val) {
    $vals[$key] = trim($val);
  }
  return array_diff($vals, array(""));
}

/* Convert array to strings */
function array_to_comma_separated($array)
{
  $data = implode(",",$array);
  return $data;
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

/* Check array contains string value */
function check_strings_in_array($arr) 
{
    return array_sum(array_map('is_string', $arr)) == count($arr);
}

function secondsToMinutes($time,$view_time_in_hour_minutes)
{
	$secs = strtotime($view_time_in_hour_minutes)-strtotime("00:00:00");
	$res = date("H:i:s",strtotime($time)+$secs);
	return $res;
}
/**************************************** Ends **********************************************************/

/************************************ Function For Most Viewed Videos **************************/
function getAllMostViewedVideosArray($start,$count,$link)
{
	$videoList = "SELECT * FROM `videos` where status =1 order by `view` desc limit $start,$count";
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Query - ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
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
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query - ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
    return array_slice($video_array,$start,$count);
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
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
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
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
    return array_slice($video_array,$start,$count);
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
/* function getAllLatestVideos($start,$count,$link)
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
		 
		$response[] = array("hasMore"=>$hasmore,"videos"=>$video_array);
	}
	
	wh_log("All Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $response;
} */

function getAllLatestVideos($start,$count,$link)
{
	$get_total = "SELECT * FROM `videos` where status =1 order by `insertion_time` desc";
	$get_total_rs = mysqli_query($link,$get_total);
	$total = mysqli_num_rows($get_total_rs);
	if($count < $total) { $hasmore = true; } else { $hasmore = false; }
	wh_log("Query Executed : ".$get_total." | Rows count - ".$total." | hasmore - ".$hasmore);
	
	$videoList = "SELECT * FROM `videos` where status =1 order by `insertion_time` desc limit $start,$count";
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		 
		//$response[] = array("hasMore"=>$hasmore,"videos"=>$video_array);
	}
	wh_log("All Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}

function getMostLatestVideosByCategoryID($values,$start,$count,$link)
{
	foreach ($values as $value)
	{
		$videoList = "select * from videos where find_in_set($value,`cat_id`) and status =1 ORDER BY `insertion_time` desc limit $start,$count";
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		} 
	}
	wh_log("Categorywise Recent/Latest Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
    //return $video_array;
	return array_slice($video_array,$start,$count);
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
		$getvideoList = "select * from videos where find_in_set($value,`cat_id`) ORDER BY id desc limit $start,$count";
		$getvideoList_rs = mysqli_query($link, $getvideoList);
		if(mysqli_num_rows($getvideoList_rs) > 0)
		{
			wh_log("Query Executed : ".$getvideoList." | Rows Found for category -- ".mysqli_num_rows($getvideoList_rs));
			while($row  = mysqli_fetch_assoc($getvideoList_rs))
			{ 
				$video_array[] = videoArray($row);
			}
		}
	}
	wh_log("Videos By Category Id : ".str_replace("\n"," ", print_r($video_array, true)));
	return array_slice($video_array,$start,$count);
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By Tag ****************************************/
function getVideosByTag($tag,$start,$count,$link)
{
	//$get_total = "select * from videos where video_tags like '%$tag%' order by id asc";
	//wh_log("Query Executed : ".$get_total);
	//$get_total_rs = mysqli_query($link,$get_total);
	//$total = mysqli_num_rows($get_total_rs);
	//if(($start+$count) < $total) { $hasmore = true; } else { $hasmore = false; }
	//echo $count; echo $total;
	$videoList = "select DISTINCT(`video_url`),`id`,`cat_id`,`title`,`video_tags`,`insertion_time`,`description`,`view`,
	`like`,`dislike` from videos where video_tags like '%$tag%' order by id asc limit $start,$count";
	$videoList_rs = mysqli_query($link,$videoList);
	wh_log("Query Executed : ".$videoList." | Rows Found for video Tag List -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row);
		}
		//$response = array("hasMore"=>$hasmore,"videos"=>$video_array);
	}
	wh_log("Videos By Tag Array : ".str_replace("\n"," ", print_r($video_array, true)));
	//return $response;
	return $video_array;
	//return array_slice($video_array,$start,$count);
	
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By ID ****************************************/
function getVideosByID($video_id,$link)
{
	$videoList = "select * from videos where status =1 and id = $video_id";
	$videoList_rs = mysqli_query($link,$videoList);
	wh_log("Query Executed : ".$videoList." | Rows Found for video ID List -- ".mysqli_num_rows($videoList_rs));
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
function getAllVideos($portalid,$start,$count,$link,$imageBaseURL,$videoBaseURL)
{
	$videoList = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.content_type ='video' and t1.status = 1 order by insertion_time desc limit $start,$count";
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($row,$imageBaseURL,$videoBaseURL,$link);
		}
		
	}
	wh_log("All Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By Client Id ****************************************/
function getVideosByClientID($client_id,$start,$count,$link)
{
	//$video_array = array();
	$getvideoList = "select * from videos where `client_id` IN ($client_id) ORDER BY id desc limit $start,$count";
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	wh_log("Query Executed : ".$getvideoList." |Rows Found for category -- ".mysqli_num_rows($getvideoList_rs));
	if(mysqli_num_rows($getvideoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($getvideoList_rs))
		{ 
			$video_array[] = videoArray($row);
		} 
	}
	wh_log("Videos By Category Id : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Related Videos By Video Id ****************************************/
function getRelatedVideosByCategoryID($cat_id,$video_id,$link)
{
	$getvideoList = "select video_tags from videos where id = $video_id";
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	wh_log("getvideoList Query Executed : ".$getvideoList);
	if($row  = mysqli_fetch_assoc($getvideoList_rs))
	{
		$tags = explode(',',$row['video_tags']);
		//print_r($tags);
		 
		$search_tag = trim($tags['0']);
		$search_tag1 = trim($tags['1']);
		$getvideoList1 = "(select * from videos where find_in_set($cat_id,cat_id)) 
						  UNION (SELECT * FROM videos WHERE `video_tags` LIKE '%$search_tag%') 
						  UNION (SELECT * FROM videos WHERE `video_tags` LIKE '%$search_tag1%') limit 0,10"; 
		 
		wh_log("search_tag - ".$search_tag." | search_tag1 - ".$search_tag1."getvideoList Query Executed : ".$getvideoList1);
		$getvideoList_rs1 = mysqli_query($link, $getvideoList1);
		wh_log("Rows count : ".mysqli_num_rows($getvideoList_rs1));
		if(mysqli_num_rows($getvideoList_rs1) > 0)
		{ 
			while($row1  = mysqli_fetch_assoc($getvideoList_rs1))
			{ 
				$video_array[] = videoArray($row1);
			}
			//$output = array_slice($video_array, 0, 5); 
			//print_r($output);
		}
		
		
	}
	wh_log("Videos By Category Id : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Video By Search ****************************************/
function getVideosBySearch($term,$start,$count,$link)
{
	$search_term = trim($term);
	// Get category id of matched search term
	$getvideoList = "select * from category where cat_name like '%$search_term%' and status =1 limit $start,$count";
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	wh_log("getvideoList Query Executed : ".$getvideoList." | Rows - ".mysqli_num_rows($getvideoList_rs));
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
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
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
	if(!empty($vids)) { $id_str = implode(',',$vids); $cond = "where id NOT IN ($id_str) order by id desc"; }
	else { $cond = "order by id desc"; }
	
	$getvideoListbyTag = "select * from ((select * from videos where title like '%$search_term%') UNION (SELECT * FROM videos WHERE `video_tags` LIKE '%$search_term%')) as u $cond limit $start,$count";
	$getvideoListbyTag_rs = mysqli_query($link, $getvideoListbyTag);
	wh_log("Query Executed : ".$getvideoListbyTag." | Rows Found for video -- ".mysqli_num_rows($getvideoListbyTag_rs));
	if(mysqli_num_rows($getvideoListbyTag_rs) > 0)
	{ 
		while($row2  = mysqli_fetch_assoc($getvideoListbyTag_rs))
		{ 
			$video_array1[] = videoArray($row2);
		}
	}
	//print_r($video_array1);
	if(!empty($vids)) { return array_slice(array_merge($video_array,$video_array1),$start,$count); }
	else { return array_slice($video_array1,$start,$count);}
	
	

}

/******************************************** Endssss ***********************************************************/





/******************************************** Panel Functions ***********************************************************/
function encodeId($id)
{
	$str1 = decbin($id);
	//$str1 = base64_encode($id);
	return $str1;
}
function decodeId($id)
{
	$str1 = bindec($id);
	//$str1 = base64_decode($id);
	return $str1;
}

/******************************************** Endssss ***********************************************************/



/************************************************ Panel functions ***************************************************/
/************************************************ Panel functions ***************************************************/
/************************************************ Panel functions ***************************************************/
/************************************************ Panel functions ***************************************************/


/************************** Common Function For Client Array *********************************************/
function singleClientArray($row)
{
	// Make Poratlids in array format
	$pids = explode(",",$row['portal_ids']);
	// Ends
	$client_temp= array("clientId"=>$row['client_id'],"clientName"=>$row['name'],"email"=>$row['email'],"phone"=>$row['mobile'],"domain"=>$row['url'],"address"=>$row['address'],"skypeId"=>$row['skype_id'],"assignedPortals"=>$pids,"billingCycle"=>$row['billing_cycle'],"agreementTenure"=>$row['agreement_tenure']);
	wh_log("Client Array : ".str_replace("\n"," ", print_r($client_temp, true)));
    return $client_temp;
}
function getSingleClientData($id,$link)
{
	$clientList = "SELECT * FROM `clients` where client_id ='$id' and status =1";
	$clientList_rs = mysqli_query($link,$clientList);

	wh_log("Client Query - ".$clientList." | Rows Found for video -- ".mysqli_num_rows($clientList_rs));
	if(mysqli_num_rows($clientList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($clientList_rs))
		{  
			$client_array = singleClientArray($row);
		}
		
	}
	wh_log("Client Array : ".str_replace("\n"," ", print_r($client_array, true)));
	return $client_array;
}
/****************************** Ends **********************************************************************/

/************************** Common Function For User Array *********************************************/
function singleUserArray($row)
{
	// Make Poratlids in array format
	$pids = explode(",",$row['portal_ids']);
	// Ends
	$user_temp = array("userId"=>$row['uid'],"firstName"=>$row['first_name'],"lastName"=>$row['last_name'],"email"=>$row['email'],"phone"=>$row['mobile'],"assignedPortals"=>$pids,"role"=>$row['role'],"clientId"=>$row['client_id']);
	wh_log("User Array : ".str_replace("\n"," ", print_r($user_temp, true)));
    return $user_temp;
}
function getSingleUserData($id,$link)
{
	$userList = "SELECT * FROM `users` where uid ='$id' and status =1";
	$userList_rs = mysqli_query($link,$userList);

	wh_log("User Query - ".$userList." | Rows Found for video -- ".mysqli_num_rows($userList_rs));
	if(mysqli_num_rows($userList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($userList_rs))
		{  
			$user_array = singleUserArray($row);
		}
		
	}
	wh_log("User Array : ".str_replace("\n"," ", print_r($user_array, true)));
	return $user_array;
}
/****************************** Ends **********************************************************************/

/******************************** Portal Details **********************************************************/
function singlePortalArray($row)
{
	//$portalKey = encodeId($row['portal_id']);
	$portal_temp = array("portalId"=>$row['portal_id'],"portalName"=>$row['name'],"url"=>$row['url'],"email"=>$row['email'],"agreementTenure"=>$row['agreement_tenure']);
	wh_log("Portal Temp Array : ".str_replace("\n"," ", print_r($portal_temp, true)));
    return $portal_temp;
}
function getPortalData($id,$link)
{
	$portalList = "SELECT * FROM `portals` where portal_id = '$id' and status =1";
	$portalList_rs = mysqli_query($link,$portalList);

	wh_log("Portal Query - ".$portalList." | Rows Found for video -- ".mysqli_num_rows($portalList_rs));
	if(mysqli_num_rows($portalList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($portalList_rs))
		{  
			$portal_array = singlePortalArray($row);
		}
		
	}
	wh_log("Portal Array : ".str_replace("\n"," ", print_r($portal_array, true)));
	return $portal_array;
}

/********************************** Ends ***************************************************************************/


/************************************ Category Details **********************************************************/
function singleCategoryArray($row,$link)
{
	$cat_temp = array("categoryId"=>$row['id'],"categoryName"=>$row['cat_name']);
	wh_log("Category Temp Array : ".str_replace("\n"," ", print_r($cat_temp, true)));
	//print_r($cat_temp);
    return $cat_temp;
}
function getCategoryData($id,$link)
{
	$catList = "SELECT * FROM `category` where id = '$id' and status =1";
	$catList_rs = mysqli_query($link,$catList);

	wh_log("Category Query - ".$catList." | Rows Found for video -- ".mysqli_num_rows($catList_rs));
	if(mysqli_num_rows($catList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($catList_rs))
		{  
			$cat_array = singleCategoryArray($row,$link);
		}
		
	}
	wh_log("Category Array : ".str_replace("\n"," ", print_r($cat_array, true)));
	return $cat_array;
}

/********************************** Ends ***************************************************************************/



/************************************ Text Details Section **********************************************************/
function textArray($row,$imageBaseURL,$link)
{
	if(!empty($row['cover_image_url'])) { $imageUrl = $imageBaseURL.'/'.$row['cover_image_url']; }
	
	$news_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($news_date[0]));
	
	$post_time = explode(' ',$row['post_time']);
	$news_post_time = date("Y/m/d", strtotime($post_time[0]));
			
	// Convert comma seperated strings to array
	if(!empty($row['cat_id'])) { $cat_ids = comma_separated_to_array($row['cat_id']); } else { $cat_ids = array();}
	if(!empty($row['tags'])) { $tags = comma_separated_to_array($row['tags']); } else { $tags = array();}
	if(!empty($row['country'])) { $country = comma_separated_to_array($row['country']); } else { $country = array();}
	if(!empty($row['portal_ids']))
	{ 
		$pids = comma_separated_to_array($row['portal_ids']); 
		// Get Portal Names
		$portalids = $row['portal_ids'];
		wh_log("Portal ids - ".$portalids);
		$get_portals = "select * from portals where portal_id in ($portalids) and status = 1";
		$get_portals_rs = mysqli_query($link,$get_portals);
		wh_log("Portal Query - ".$get_portals." | Rows Found for video -- ".mysqli_num_rows($get_portals_rs));
		if(mysqli_num_rows($get_portals_rs) > 0)
		{
			while($portal_row  = mysqli_fetch_assoc($get_portals_rs))
			{  
				$portal_array[] = singlePortalArray($portal_row);
				
			}
			wh_log("Poratl Array : ".str_replace("\n"," ", print_r($portal_array, true))); 
		}
		//Ends
	} else { $portal_array = array(); }
	// Ends
	if(empty($portal_array)) { $portal_array = array(); }
	
	$text_temp_array = array("textId"=>$row['id'],"categoryId"=>$cat_ids,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"newsDate"=>$insertion_time,"postTime"=>$news_post_time,"language"=>$row['language'],"description"=>$row['description'],"tags"=>$row['tags'],"country"=>$country,"city"=>$row['city'],"author"=>$row['author'],"thumbnail"=>$imageUrl,);
	wh_log("Text Content Array : ".str_replace("\n"," ", print_r($text_temp_array, true)));
    return $text_temp_array;
} 

/********************************** Ends ***************************************************************************/









/*************************************** Portal Fiunctions **************************************************************/
/*************************************** Portal Fiunctions **************************************************************/
/*************************************** Portal Fiunctions **************************************************************/
/*************************************** Portal Fiunctions **************************************************************/
/*************************************** Portal Fiunctions **************************************************************/


/*************************************** Function - Get All Text Content ****************************************/
function getAllText($portalid,$start,$count,$link,$imageBaseURL)
{
	$TextList = "SELECT t1.*,t2.content_id,t2.cover_image_url FROM news_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.content_type ='text' and t1.status = 1 order by insertion_time desc limit $start,$count";
	$TextList_rs = mysqli_query($link,$TextList);

	wh_log("Text Query Executed : ".$TextList." | Rows Found for video -- ".mysqli_num_rows($TextList_rs));
	if(mysqli_num_rows($TextList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($TextList_rs))
		{  
			$text_array[] = textArray($row,$imageBaseURL,$link);
		}
		
	}
	wh_log("All Text Array : ".str_replace("\n"," ", print_r($text_array, true)));
	return $text_array;
}

function getTextByID($portalid,$textId,$link,$imageBaseURL)
{
	$TextList = "SELECT t1.*,t2.content_id,t2.cover_image_url FROM news_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.id = $textId and t1.content_type ='text' and t1.status = 1";
	$TextList_rs = mysqli_query($link,$TextList);

	wh_log("Text Query Executed : ".$TextList." | Rows Found for video -- ".mysqli_num_rows($TextList_rs));
	if(mysqli_num_rows($TextList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($TextList_rs))
		{  
			$text_array = textArray($row,$imageBaseURL,$link);
		}
		
	}
	wh_log("All Text Array : ".str_replace("\n"," ", print_r($text_array, true)));
	return $text_array;
	
}






/****************************************************** Ends ********************************************************/
?>