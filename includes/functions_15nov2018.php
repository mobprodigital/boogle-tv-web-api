<?php
/*************************************** Common Function For Portal And Dashboard ****************************************************************/
/************************************************************************************************************************************************/

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
function check_array_values($arr)
{
	foreach ($arr as $a => $b) {
    if (!is_numeric($b)) {
		return false;
    }
}
return true;
}

/* Convert Seconds To Minutes */
function secondsToMinutes($time,$view_time_in_hour_minutes)
{
	$secs = strtotime($view_time_in_hour_minutes)-strtotime("00:00:00");
	$res = date("H:i:s",strtotime($time)+$secs);
	return $res;
}

/* Remove Duplicate Values From Array List*/
function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 
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
function getContentTypeData($contentType,$videoBaseURL,$imageBaseURL)
{
    $array = array();
    if($contentType == 1) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'audio'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 2) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'video'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 3) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'image'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 4)
    { 
        $array['dataTable'] = 'news_metadata'; 
        $array['type'] = 'text'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    return $array;
}
/****************************************************** Ends *************************************************************************************/
/************************************************************************************************************************************************/

function videoArray($row,$imageBaseURL,$videoBaseURL,$link,$source)
{
	if(!empty($row['video_url'])) { $videoUrl = $videoBaseURL.'/'.$row['video_url']; }
	if(!empty($row['cover_image_url'])) { $imageUrl = $imageBaseURL.'/'.$row['cover_image_url']; } else { $imageUrl = $imageBaseURL.'/default.jpg';}
	
	$video_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($video_date[0]));
			
	// Convert comma seperated strings to array
	if(!empty($row['tags'])) { $tags = comma_separated_to_array($row['tags']); } else { $tags = array();}
	if(!empty($row['country'])) { $country = comma_separated_to_array($row['country']); }

    // Get Portal Names
	if(!empty($row['portal_ids']))
	{ 
		$portalids = $row['portal_ids'];
		$portal_array = getPortalArrayByIds($portalids,$link);
	}
	if(empty($portal_array)) { $portal_array = array(); }
    // Ends
	
	// Fetch Category Array
	if(!empty($row['cat_id']))
	{ 
		$cat_id = $row['cat_id'];
		$cat_array = getCategoryArrayByIds($cat_id,$link);
	}
    if(empty($cat_array)) { $cat_array = array(); }
	//Ends

	if($source == 'portal') 
	{
		$video_temp_array = array("videoId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"videoUrl"=>$videoUrl,"videoTags"=>$tags,"videoDate"=>$insertion_time,"language"=>$row['language'],"description"=>$row['description'],"coverImage"=>array("original"=>$imageUrl,"large"=>"","medium"=>"","small"=>""),"videoLength"=>$row['content_length'],"extension"=>$row['extension'],"videoMime"=>$row['mime'],"minAgeReq"=>$row['min_age_req'],"broadcasterName"=>$row['broadcaster_name'],"type"=>$row['type'],"currentAvailability"=>$row['content_availability'],"platform"=>$row['platform'],"adult"=>$row['adult'],"downloadRights"=>$row['download_rights'],"internationalRights"=>$row['intrernational_rights'],"genere"=>$row['genre'],"director"=>$row['director'],"producer"=>$row['producer'],"writer"=>$row['writer'],"musicDirector"=>$row['music_director'],"productionHouse"=>$row['production_house'],"actor"=>$row['actor'],"singer"=>$row['singer'],"country"=>$country,"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike']);
	}
	else
	{
		$video_temp_array = array("videoId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"videoUrl"=>$videoUrl,"videoTags"=>$tags,"videoDate"=>$insertion_time,"language"=>$row['language'],"description"=>$row['description'],"coverImage"=>array("original"=>$imageUrl,"large"=>"","medium"=>"","small"=>""),"videoLength"=>$row['content_length'],"extension"=>$row['extension'],"videoMime"=>$row['mime'],"minAgeReq"=>$row['min_age_req'],"broadcasterName"=>$row['broadcaster_name'],"type"=>$row['type'],"currentAvailability"=>$row['content_availability'],"platform"=>$row['platform'],"adult"=>$row['adult'],"downloadRights"=>$row['download_rights'],"internationalRights"=>$row['intrernational_rights'],"genere"=>$row['genre'],"director"=>$row['director'],"producer"=>$row['producer'],"writer"=>$row['writer'],"musicDirector"=>$row['music_director'],"productionHouse"=>$row['production_house'],"actor"=>$row['actor'],"singer"=>$row['singer'],"country"=>$country);
	}
	wh_log("Video Content Array : ".str_replace("\n"," ", print_r($video_temp_array, true)));
    return $video_temp_array;
} 

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







/******************************************************** Panel functions ************************************************************************/
/******************************************************** Panel functions ************************************************************************/
/******************************************************** Panel functions ************************************************************************/
/******************************************************** Panel functions ************************************************************************/


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
function singleUserArray($row,$link)
{
	// Fetching Roles Details
	if(!empty($row['role']))
	{ 
		// Get Role Names
		$roleid = $row['role'];
		wh_log("Role ids - ".$roleid);
		$get_roles = "select * from roles where id = $roleid and status = 1";
		$get_roles_rs = mysqli_query($link,$get_roles);
		wh_log("Roles Query - ".$get_roles." | Rows Found for video -- ".mysqli_num_rows($get_roles_rs));
		if(mysqli_num_rows($get_roles_rs) > 0)
		{  
			if($role_row  = mysqli_fetch_assoc($get_roles_rs))
			{  
				$role_array = singleRoleArray($role_row,$link);
			}
			wh_log("Role Array : ".str_replace("\n"," ", print_r($role_array, true))); 
		}
	}
	if(empty($role_array)) { $role_array = array(); } 
	//Ends
	
	//Fetching Portal Details
	// Make Poratlids in array format
	if(empty($row['portal_ids'])) { $pids = array();}
	else { $pids = comma_separated_to_array($row['portal_ids']); }
	//Ends
	
	// Fetch Client Details
	$array = getSingleClientData($pids,$link);
	//Ends
	
	// Fetch Portal Details
	$parray = getPortalArrayByIds($row['portal_ids'],$link);
	//print_r($parray);
	//Ends
	
	$user_temp = array("userId"=>$row['uid'],"firstName"=>$row['first_name'],"lastName"=>$row['last_name'],"email"=>$row['email'],"phone"=>$row['mobile'],"role"=>$role_array,"clientId"=>$row['client_id'],"assignedPortals"=>$parray,"clientInfo"=>$array);
	wh_log("User Array : ".str_replace("\n"," ", print_r($user_temp, true)));
    return $user_temp;
}
function getSingleUserData($id,$link)
{
	$userList = "SELECT * FROM `users` where uid =$id and status =1";
	$userList_rs = mysqli_query($link,$userList);

	wh_log("User Query - ".$userList." | Rows Found for video -- ".mysqli_num_rows($userList_rs));
	if(mysqli_num_rows($userList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($userList_rs))
		{  
			$user_array = singleUserArray($row,$link);
		}
	}
	wh_log("User Array : ".str_replace("\n"," ", print_r($user_array, true)));
	return $user_array;
}
/****************************** Ends **********************************************************************/

/******************************** Portal Details **********************************************************/
function singleContentTypeArray($row,$link)
{
	$content_temp= array("contentTypeId"=>$row['id'],"contentTypeName"=>$row['content_name']);
	wh_log("content_temp Array : ".str_replace("\n"," ", print_r($content_temp, true)));
    return $content_temp;
}
function getPortalArrayByIds($portal_ids,$link)
{ 
	$query1 = "SELECT * FROM portals WHERE portal_id IN ($portal_ids) and status = '1'";
	wh_log("Select Poratl Query - ".$query1);
	$query_rs1 = mysqli_query($link,$query1);
	if($query_rs1)
	{
		if(mysqli_num_rows($query_rs1) > 0)
		{
			while($row1  = mysqli_fetch_assoc($query_rs1))
			{ 
				// Get Porat Details
				$portal_array[] = singlePortalArray($row1,$link);
				//Ends
			}
			wh_log("Portal Array : ".str_replace("\n"," ", print_r($portal_array, true)));
			return $portal_array;
		}
	}
}
function singlePortalArray($row,$link)
{
	// Fetch content type details
	if(!empty($row['content_type']))
	{ 
		$content_type_ids = $row['content_type'];
		wh_log("Content Type ids - ".$content_type_ids);
		$get_content = "select * from content_type where id in ($content_type_ids) and status = 1";
		$get_content_rs = mysqli_query($link,$get_content);
		wh_log("Content type Query - ".$get_content." | Rows Found for video -- ".mysqli_num_rows($get_content_rs));
		if(mysqli_num_rows($get_content_rs) > 0)
		{ 
			while($con_row  = mysqli_fetch_assoc($get_content_rs))
			{ 
				$con_array[] = singleContentTypeArray($con_row,$link);
			}
			wh_log("Contemnt Array : ".str_replace("\n"," ", print_r($con_array, true))); 
		}
		//Ends
	}
	if(empty($con_array)) { $con_array = array(); } 
	//Ends
	//$portalKey = encodeId($row['portal_id']);
	$portal_temp = array("portalId"=>$row['portal_id'],"portalName"=>$row['name'],"url"=>$row['url'],"email"=>$row['email'],"agreementTenure"=>$row['agreement_tenure'],"contentType"=>$con_array);
	wh_log("Portal Temp Array : ".str_replace("\n"," ", print_r($portal_temp, true)));
    return $portal_temp;
}

function getPortalDataById($id,$link)
{
	$portalList = "SELECT * FROM `portals` where portal_id = '$id' and status =1";
	$portalList_rs = mysqli_query($link,$portalList);

	wh_log("Portal Query - ".$portalList." | Rows Found for video -- ".mysqli_num_rows($portalList_rs));
	if(mysqli_num_rows($portalList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($portalList_rs))
		{  
			$portal_array = singlePortalArray($row,$link);
		}
		
	}
	wh_log("Portal Array : ".str_replace("\n"," ", print_r($portal_array, true)));
	return $portal_array;
}
function portalExist($portal,$link,$contentType)
{
	$portalCheck = "SELECT * FROM `portals` WHERE status =1 and `name` ='$portal' and find_in_set($contentType,`content_type`)";
	$portalCheck_rs = mysqli_query($link,$portalCheck);
	wh_log("Portal Check Query Executed : ".$portalCheck);
	if(mysqli_num_rows($portalCheck_rs) > 0)
	{
        //Get Portal ID
		if($portalrow = mysqli_fetch_assoc($portalCheck_rs))
		{ $portalid = $portalrow['portal_id']; } else { $portalid = "";} 
	}
	return $portalid;
}
/********************************** Ends ***************************************************************************/


/******************************** Role Details **********************************************************/
 function singleRoleArray($row,$link)
{
	$role_temp = array("roleId"=>$row['id'],"roleName"=>$row['name']);
	wh_log("Role Temp Array : ".str_replace("\n"," ", print_r($role_temp, true)));
    return $role_temp;
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
function getCategoryArrayByIds($cat_id,$link)
{ 
	$get_cats = "select * from category where id in ($cat_id) and status = 1";
	$get_cats_rs = mysqli_query($link,$get_cats);
	wh_log("Category Query - ".$get_cats." | Rows Found -- ".mysqli_num_rows($get_cats_rs)." | Category Ids -".$cat_id);
	if($get_cats_rs)
	{
		if(mysqli_num_rows($get_cats_rs) > 0)
		{
			while($cat_row  = mysqli_fetch_assoc($get_cats_rs))
			{ 
				$cat_array[] = singleCategoryArray($cat_row,$link);
			}
		}
		wh_log("Category Array : ".str_replace("\n"," ", print_r($cat_array, true))); 
		return $cat_array;
	}
}
/********************************** Ends ***************************************************************************/









/*************************************** Portal Fiunctions ***************************************************************************************/
/*************************************** Portal Fiunctions ******************************************************************************************/
/*************************************** Portal Fiunctions ******************************************************************************************/
/*************************************** Portal Fiunctions ******************************************************************************************/
/*************************************** Portal Fiunctions ******************************************************************************************/

/************************************ Text Details Section **********************************************************/
function textArray($row,$imageBaseURL,$link)
{
	if(!empty($row['cover_image_url'])) { $imageUrl = $imageBaseURL.'/'.$row['cover_image_url']; } else { $imageUrl = $imageBaseURL.'/default.jpg';}
	
	$news_date = explode(' ',$row['insertion_time']);
	$insertion_time = date("d/m/Y", strtotime($news_date[0]));
	
	$post_time = explode(' ',$row['post_time']);
	$news_post_time = date("Y/m/d", strtotime($post_time[0]));
			
	// Convert comma seperated strings to array
	if(!empty($row['tags'])) { $tags = comma_separated_to_array($row['tags']); } else { $tags = array();}
	if(!empty($row['country'])) { $country = comma_separated_to_array($row['country']); } else { $country = array();}
	
    // Get Portal Names
	if(!empty($row['portal_ids']))
	{ 
		$portalids = $row['portal_ids'];
		$portal_array = getPortalArrayByIds($portalids,$link);
	} else { $portal_array = array(); }
	if(empty($portal_array)) { $portal_array = array(); }
	// Ends
	
	// Fetch Category Array
	if(!empty($row['cat_id']))
	{ 
		$cat_id = $row['cat_id'];
		$cat_array = getCategoryArrayByIds($cat_id,$link);
	} else { $cat_ids = array();}
    if(empty($cat_array)) { $cat_array = array(); }
	//Ends

	$text_temp_array = array("textId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"newsDate"=>$insertion_time,"postTime"=>$news_post_time,"language"=>$row['language'],"description"=>$row['description'],"tags"=>$row['tags'],"country"=>$country,"city"=>$row['city'],"author"=>$row['author'],"thumbnail"=>$imageUrl,);
	wh_log("Text Content Array : ".str_replace("\n"," ", print_r($text_temp_array, true)));
    return $text_temp_array;
} 

/********************************** Ends ***************************************************************************/

/*************************************** Function - Get All Text Content *************************************************/
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


/*************************************** Function - Get All Videos Content *************************************************/
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
function getVideoByID($portalid,$videoId,$link,$imageBaseURL,$videoBaseURL)
{
	$videoList = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.id = $videoId and t1.content_type ='video' and t1.status = 1";
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Video Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$vid_array = videoArray($row,$imageBaseURL,$videoBaseURL,$link);
		}
		
	}
	wh_log("All Video Array : ".str_replace("\n"," ", print_r($vid_array, true)));
	return $vid_array;
	
}
/*************************************** Endsssssssssssssssssssssssssssss *************************************************/

function getContentByCategoryID($contentType,$videoBaseURL,$imageBaseURL,$portalid,$values,$start,$count,$link)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
	
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
    $dataTable = $carr['dataTable'];

	// Get Content By Category Id
	foreach ($values as $value)
	{
		$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) and find_in_set($value,t1.`cat_id`) and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`insertion_time` desc limit $start,$count";
		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Content Query Executed : ".$getData." | Rows Found for category -- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				$source = 'portal';
				if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link);  $data = unique_multidim_array($data1,'textId'); }
			}
		}
	}
	
	wh_log("Content By Category Id : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
	//Ends
}


/******************************************************************* Function For Most Liked Content *********************************************/
function getMostLikedContent($start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
    $dataTable = $carr['dataTable'];
	
	$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime 
	FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) 
	and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`like` desc limit $start,$count";

	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Liked Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			$source = 'portal';
			if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
		}
	}
	wh_log("Most Liked Content Array : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
	//Ends
}
function getMostLikedContentByCategoryID($values,$start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */

	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
	$dataTable = $carr['dataTable'];
	
	foreach ($values as $value)
	{
		$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM $dataTable as t1 
		LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) and 
		find_in_set($value,t1.`cat_id`) and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`like` desc limit $start,$count";
		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Liked Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				$source = 'portal';
				if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
			}
		}
	}
	wh_log("Most Liked Content By Category Id Array : ".str_replace("\n"," ", print_r($data, true)));
    return array_slice($data,$start,$count);
} 

function sortByLike($a, $b)
{
    $a = $a['likesCount'];
    $b = $b['likesCount'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}

/**********************************************************  Most Liked Content Functions Ends **************************************************/

/********************************************************** Function For Most Viewed Content *****************************************************/
function getMostViewedContent($start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
	
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
	$dataTable = $carr['dataTable'];
	
	$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime 
	FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) 
	and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`view` desc limit $start,$count";
	wh_log("Most Viewed Content Query Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Viewed Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			$source = 'portal';
			if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId');  }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
		}
	}
	wh_log("Most Viewed Content Array : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
}

function getMostViewedContentByCategoryID($values,$start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */

	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
    $dataTable = $carr['dataTable'];

	foreach ($values as $value)
	{
		$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM $dataTable as t1 
		LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) and 
		find_in_set($value,t1.`cat_id`) and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`view` desc limit $start,$count";
		wh_log("Most Viewed Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));

		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Viewed Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				$source = 'portal';
				if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
			}
		}
	}
	
	wh_log("Most Viewed Content By Category Id Array : ".str_replace("\n"," ", print_r($data, true)));
    return array_slice($data,$start,$count);
} 
function sortByView($a, $b)
{
    $a = $a['viewsCount'];
    $b = $b['viewsCount'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}
/**************************************************  Most Viewed Content Functions Ends ******************************************/

/***************************************************** Function For Most Recent/Latest Contents ***************************************************/
function getLatestContent($start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
	$dataTable = $carr['dataTable'];
	
	$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime 
	FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) 
	and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`insertion_time` desc limit $start,$count";
	wh_log("Most Latest Content Query Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Latest Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			$source = 'portal';
			if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
		}
	}
	wh_log("Most Latest Content Array : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
}

function getMostLatestContentByCategoryID($values,$start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */

	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
	$dataTable = $carr['dataTable'];
	
	foreach ($values as $value)
	{
		$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM $dataTable as t1 
		LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) and 
		find_in_set($value,t1.`cat_id`) and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY t1.`insertion_time` desc limit $start,$count";
		wh_log("Most Latest Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));

		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Latest Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				$source = 'portal';
				if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
			}
		}
	}
	
	wh_log("Most Viewed Content By Category Id Array : ".str_replace("\n"," ", print_r($data, true)));
    return array_slice($data,$start,$count);
} 
function sortByRecent($a, $b)
{
    $a = $a['createDate'];
    $b = $b['createDate'];

    if ($a == $b) return 0;
    return ($a > $b) ? -1 : 1;
}

/*********************************************  Ends Most Recent/Latest Contents Functions  *****************************************************/
/********************************************** Content By Tag *********************************************/
function getContentByTag($tag,$start,$count,$link,$portalid,$contentType,$videoBaseURL,$imageBaseURL)
{ 
    /* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */
	
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
    //print_r($carr);
    $dataTable = $carr['dataTable'];

	$getData = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,
	t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where 
	tags like '%$tag%' and find_in_set($portalid,t1.`portal_ids`) and t1.content_type ='".$carr['type']."' and t1.status = 1 ORDER BY 
	t1.`id` desc limit $start,$count";
	wh_log("Content Query By Tags Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Content Query By Tags Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			$source = 'portal';
		    if($contentType == 2) { $data1[] = videoArray($row,$carr['ipath'],$carr['vpath'],$link,$source); $data = unique_multidim_array($data1,'videoUrl'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
		}
	}
	wh_log("Content By Tags Array : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
}

/******************************************** Endssss ***********************************************************/

/*************************************** Function - Related Content By Content Id ****************************/
function getRelatedContentsByContentID($contentId,$portalid,$contentType,$link,$videoBaseURL,$imageBaseURL)
{
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
	//print_r($carr);
	$dataTable = $carr['dataTable'];
	/* if($contentType == 1) { $dataTable = 'content_metadata'; $type = 'audio'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 2) { $dataTable = 'content_metadata'; $type = 'video'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 3) { $dataTable = 'content_metadata'; $type = 'image'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;}
	elseif($contentType == 4) { $dataTable = 'news_metadata'; $type = 'text'; $vpath = $videoBaseURL; $ipath = $imageBaseURL;} */

	$getList = "SELECT * FROM $dataTable where id= $contentId and find_in_set($portalid,`portal_ids`) and content_type ='".$carr['type']."' and status = 1";
	$getList_rs = mysqli_query($link, $getList);
	wh_log("getList Query Executed : ".$getList);
	if($row  = mysqli_fetch_assoc($getList_rs))
	{
		$tags = explode(',',$row['tags']);
		//print_r($tags);
		 
		$search_tag = trim($tags['0']);
		$search_tag1 = trim($tags['1']);

		echo $getList1 = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,
		t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where 
		(tags like '%$search_tag%' or tags like '%$search_tag1%') and find_in_set($portalid,t1.`portal_ids`) and t1.content_type ='".$carr['type']."' and t1.status = 1 limit 0,10"; 
		 
		wh_log("search_tag - ".$search_tag." | search_tag1 - ".$search_tag1."getList1 Query Executed : ".$getList1);
		$getList_rs1 = mysqli_query($link, $getList1);
		wh_log("Rows count : ".mysqli_num_rows($getList_rs1));
		if(mysqli_num_rows($getList_rs1) > 0)
		{ 
			while($row1  = mysqli_fetch_assoc($getList_rs1))
			{ 
				$source = 'portal';
			    if($contentType == 2) { $data[] = videoArray($row1,$carr['ipath'],$carr['vpath'],$link,$source); /* $data = unique_multidim_array($data1,'videoUrl'); */ }
			    elseif($contentType == 4) { $data1[] = textArray($row1,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
			}
		}
		
		
	}
	wh_log("Related Content By Content Id : ".str_replace("\n"," ", print_r($data, true)));
	return array_slice($data,$start,$count);
}

/******************************************** Endssss ****************************************************/

/*************************************** Function - Content By Search ****************************************/
function getContentBySearch($contentType,$portalid,$term,$start,$count,$link,$videoBaseURL,$imageBaseURL)
{
	$carr = getContentTypeData($contentType,$videoBaseURL,$imageBaseURL);
	//print_r($carr);
	$dataTable = $carr['dataTable'];

	$search_term = trim($term);
	// Get category id of matched search term
	$getvideoList = "select * from category where cat_name like '%$search_term%' and find_in_set($portalid,`portal_ids`)
	 and content_type_id = $contentType and status =1 limit $start,$count";
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	wh_log("getcontentList Query Executed : ".$getvideoList." | Rows - ".mysqli_num_rows($getvideoList_rs));
	if(mysqli_num_rows($getvideoList_rs) > 0)
	{ 
		while($row = mysqli_fetch_assoc($getvideoList_rs))
		{ 
			 $ids[] = $row['id'];
		}
	}
	//print_r($ids);
	// Get content of matched category ids with a search term
	foreach ($ids as $id)
	{
		$videoList = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,
		t2.extension,t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON 
		t1.id= t2.content_id where find_in_set($portalid,t1.`portal_ids`) and find_in_set($id,`cat_id`) and t1.content_type ='".$carr['type']."' and t1.status = 1
		limit $start,$count"; 
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query Executed : ".$videoList." | Rows Found-- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row1  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$vids[] = $row1['id'];
				$source = 'portal';
			    if($contentType == 2) { $data[] = videoArray($row1,$carr['ipath'],$carr['vpath'],$link,$source); /* $data = unique_multidim_array($data1,'videoUrl'); */ }
			    elseif($contentType == 4) { $data1[] = textArray($row1,$carr['ipath'],$link); $data = unique_multidim_array($data1,'textId'); }
            }
		} 
	}
	//print_r($vids);
	//print_r($data);
	//die;
	// Get contents by tag and titles with a search term
	if(!empty($vids)) { $id_str = implode(',',$vids); $cond = "where id NOT IN ($id_str) order by id desc"; }
	else { $cond = "order by id desc"; }
	
	//$getvideoListbyTag = "select * from ((select * from videos where title like '%$search_term%') UNION (SELECT * FROM videos WHERE `video_tags` LIKE '%$search_term%')) as u $cond limit $start,$count";
	$getvideoListbyTag = "select * from((SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,
	t2.mime FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where 
	find_in_set(1,t1.`portal_ids`) and t1.tags like '%$search_term%' and t1.content_type ='video' and t1.status = 1) 
	UNION (SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime
	FROM $dataTable as t1 LEFT JOIN content_multimedia as t2 ON t1.id= t2.content_id where 
	find_in_set(1,t1.`portal_ids`) and t1.title like '%$search_term%' and t1.content_type ='video' and t1.status = 1))
	as u $cond limit 0,10";
	
	$getvideoListbyTag_rs = mysqli_query($link, $getvideoListbyTag);
	wh_log("Query Executed : ".$getvideoListbyTag." | Rows Found -- ".mysqli_num_rows($getvideoListbyTag_rs));
	if(mysqli_num_rows($getvideoListbyTag_rs) > 0)
	{ 
		while($row2  = mysqli_fetch_assoc($getvideoListbyTag_rs))
		{ 
			//$video_array1[] = videoArray($row2);
			$source = 'portal';
			if($contentType == 2) { $vdata[] = videoArray($row2,$carr['ipath'],$carr['vpath'],$link,$source); /* $vdata = unique_multidim_array($data1,'videoUrl'); */ }
			elseif($contentType == 4) { $vdata1[] = textArray($row2,$carr['ipath'],$link); $vdata = unique_multidim_array($vdata1,'textId'); }
		}
	}
	//print_r($vdata);
	if(!empty($vids)) { return array_slice(array_merge($data,$vdata),$start,$count); }
	else { return array_slice($vdata,$start,$count);}
} 

/******************************************** Endssss ***********************************************************/

?>