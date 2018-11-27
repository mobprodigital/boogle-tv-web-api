<?php
/************************************ Text Details Section **********************************************************/
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

function portalVideoArray($row,$imageBaseURL,$videoBaseURL,$link)
{
	$conid = $row['id'];
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
	
	// get multimedia array
	$multimedia_array = MultimediaContentarray($conid,$imageBaseURL,$videoBaseURL,$link);
	if(empty($multimedia_array)) { $multimedia_array = array(); }
	// ends

	$video_temp_array = array("videoId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,
	"title"=>$row['title'],"videoTags"=>$tags,"videoDate"=>$insertion_time,"language"=>$row['language'],
	"description"=>$row['description'],"minAgeReq"=>$row['min_age_req'],"broadcasterName"=>$row['broadcaster_name'],
	"type"=>$row['type'],"currentAvailability"=>$row['content_availability'],"platform"=>$row['platform'],"adult"=>$row['adult'],"downloadRights"=>$row['download_rights'],
	"internationalRights"=>$row['intrernational_rights'],"genere"=>$row['genre'],"director"=>$row['director'],"producer"=>$row['producer'],
	"writer"=>$row['writer'],"musicDirector"=>$row['music_director'],"productionHouse"=>$row['production_house'],"actor"=>$row['actor'],
	"singer"=>$row['singer'],"country"=>$country,"viewsCount"=>$row['view'],"likesCount"=>$row['like'],"dislikesCount"=>$row['dislike'],"multimedia"=>$multimedia_array);
	wh_log("Video Content Array : ".str_replace("\n"," ", print_r($video_temp_array, true)));
    return $video_temp_array;
} 

function portalTextArray($row,$imageBaseURL,$videoBaseURL,$link)
{
	$conid = $row['id'];
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
	
	// get multimedia array
	$multimedia_array = MultimediaContentarray($conid,$imageBaseURL,$videoBaseURL,$link);
	if(empty($multimedia_array)) { $multimedia_array = array(); }
	// ends

	$text_temp_array = array("textId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"newsDate"=>$insertion_time,"postTime"=>$news_post_time,"language"=>$row['language'],"description"=>$row['description'],"tags"=>$row['tags'],"country"=>$country,"city"=>$row['city'],"author"=>$row['author'],"thumbnail"=>$imageUrl,"multimedia"=>$multimedia_array);
	wh_log("Text Content Array : ".str_replace("\n"," ", print_r($text_temp_array, true)));
    return $text_temp_array;
} 

/********************************** Ends ***************************************************************************/

/*************************************** Function - Get All Text Content *************************************************/
/* function getAllText($portalid,$start,$count,$link,$imageBaseURL)
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
} */

function getTextByID($portalid,$textId,$link,$imageBaseURL)
{
	$TextList = "SELECT t1.*,t2.content_id,t2.cover_image_url FROM news_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.id = $textId and t1.content_type ='text' and t1.status = 1";
	$TextList_rs = mysqli_query($link,$TextList);

	wh_log("Text Query Executed : ".$TextList." | Rows Found for video -- ".mysqli_num_rows($TextList_rs));
	if(mysqli_num_rows($TextList_rs) > 0)
	{
		while($row  = mysqli_fetch_assoc($TextList_rs))
		{  
			$text_array = textArray($row,$imageBaseURL,$link,$videoBaseURL);
		}
		
	}
	wh_log("All Text Array : ".str_replace("\n"," ", print_r($text_array, true)));
	return $text_array;
	
}
/****************************************************** Ends ********************************************************/


/*************************************** Function - Get All Videos Content *************************************************/
function getAllVideos($portalid,$start,$count,$link,$imageBaseURL,$videoBaseURL)
{
	//$videoList = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.content_type ='video' and t1.status = 1 order by insertion_time desc limit $start,$count";
	$videoList = "SELECT * from  `content_metadata` where `portal_ids` IN ($portalid) and content_type ='video' and status = 1 order by insertion_time desc limit $start,$count";
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($con_row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$video_array[] = videoArray($con_row,$link,$imageBaseURL,$videoBaseURL);
		}
		
	}
	wh_log("All Video Array : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;
}
function getVideoByID($portalid,$videoId,$link,$imageBaseURL,$videoBaseURL)
{
	//$videoList = "SELECT t1.*,t2.content_id,t2.video_url,t2.cover_image_url,t2.content_length,t2.extension,t2.mime FROM content_metadata as t1 LEFT JOIN content_multimedia as t2 ON t1.id = t2.content_id where t1.`portal_ids` IN ($portalid) and t1.id = $videoId and t1.content_type ='video' and t1.status = 1";
	$videoList = "SELECT * from  `content_metadata` where `portal_ids` IN ($portalid) and id = $videoId and content_type ='video' and status = 1";
	
	$videoList_rs = mysqli_query($link,$videoList);

	wh_log("Video Query Executed : ".$videoList." | Rows Found for video -- ".mysqli_num_rows($videoList_rs));
	if(mysqli_num_rows($videoList_rs) > 0)
	{
		while($con_row  = mysqli_fetch_assoc($videoList_rs))
		{  
			$vid_array = videoArray($con_row,$link,$imageBaseURL,$videoBaseURL);
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
		$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) and find_in_set($value,`cat_id`) and content_type ='".$carr['type']."' and status = 1 ORDER BY `insertion_time` desc limit $start,$count";
		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Content Query Executed : ".$getData." | Rows Found for category -- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				//$source = 'portal';
				if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']);  $data = unique_multidim_array($data1,'textId'); }
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
	
	$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) 
	and content_type ='".$carr['type']."' and status = 1 ORDER BY `like` desc limit $start,$count";

	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Liked Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			//$source = 'portal';
			if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
		$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) and 
		find_in_set($value,`cat_id`) and content_type ='".$carr['type']."' and status = 1 ORDER BY `like` desc limit $start,$count";
		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Liked Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				//$source = 'portal';
				if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
	
	$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) 
	and content_type ='".$carr['type']."' and status = 1 ORDER BY `view` desc limit $start,$count";
	wh_log("Most Viewed Content Query Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Viewed Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			//$source = 'portal';
			if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId');  }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
		$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) and 
		find_in_set($value,`cat_id`) and content_type ='".$carr['type']."' and status = 1 ORDER BY `view` desc limit $start,$count";
		wh_log("Most Viewed Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));

		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Viewed Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				//$source = 'portal';
				if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
	
	$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) 
	and content_type ='".$carr['type']."' and status = 1 ORDER BY `insertion_time` desc limit $start,$count";
	wh_log("Most Latest Content Query Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Most Latest Content Query Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			//$source = 'portal';
			if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
		$getData = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) and 
		find_in_set($value,`cat_id`) and content_type ='".$carr['type']."' and status = 1 ORDER BY `insertion_time` desc limit $start,$count";
		wh_log("Most Latest Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));

		$getData_rs = mysqli_query($link, $getData);
		if(mysqli_num_rows($getData_rs) > 0)
		{
			wh_log("Most Latest Content By Category Id Query Executed : ".$getData." | Rows Found-- ".mysqli_num_rows($getData_rs));
			while($row  = mysqli_fetch_assoc($getData_rs))
			{  
				//$source = 'portal';
				if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoId'); }
				elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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

	$getData = "SELECT * FROM $dataTable where 
	tags like '%$tag%' and find_in_set($portalid,`portal_ids`) and content_type ='".$carr['type']."' and status = 1 ORDER BY `id` desc limit $start,$count";
	wh_log("Content Query By Tags Executed : ".$getData);
	$getData_rs = mysqli_query($link, $getData);
	if(mysqli_num_rows($getData_rs) > 0)
	{
		wh_log("Content Query By Tags Executed : ".$getData." | Rows Found -- ".mysqli_num_rows($getData_rs));
		while($row  = mysqli_fetch_assoc($getData_rs))
		{  
			//$source = 'portal';
		    if($contentType == 2) { $data1[] = portalVideoArray($row,$carr['ipath'],$carr['vpath'],$link); $data = unique_multidim_array($data1,'videoUrl'); }
			elseif($contentType == 4) { $data1[] = textArray($row,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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

		$getList1 = "SELECT * FROM $dataTable where 
		(tags like '%$search_tag%' or tags like '%$search_tag1%') and find_in_set($portalid,`portal_ids`) and content_type ='".$carr['type']."' and status = 1 limit 0,10"; 
		 
		wh_log("search_tag - ".$search_tag." | search_tag1 - ".$search_tag1."getList1 Query Executed : ".$getList1);
		$getList_rs1 = mysqli_query($link, $getList1);
		wh_log("Rows count : ".mysqli_num_rows($getList_rs1));
		if(mysqli_num_rows($getList_rs1) > 0)
		{ 
			while($row1  = mysqli_fetch_assoc($getList_rs1))
			{ 
				//$source = 'portal';
			    if($contentType == 2) { $data[] = portalVideoArray($row1,$carr['ipath'],$carr['vpath'],$link); /* $data = unique_multidim_array($data1,'videoUrl'); */ }
			    elseif($contentType == 4) { $data1[] = textArray($row1,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
		$videoList = "SELECT * FROM $dataTable where find_in_set($portalid,`portal_ids`) and find_in_set($id,`cat_id`) and content_type ='".$carr['type']."' and status = 1
		limit $start,$count"; 
		$videoList_rs = mysqli_query($link,$videoList);
		wh_log("Query Executed : ".$videoList." | Rows Found-- ".mysqli_num_rows($videoList_rs));
		if(mysqli_num_rows($videoList_rs) > 0)
		{
			while($row1  = mysqli_fetch_assoc($videoList_rs))
			{ 
				$vids[] = $row1['id'];
				//$source = 'portal';
			    if($contentType == 2) { $data[] = portalVideoArray($row1,$carr['ipath'],$carr['vpath'],$link); /* $data = unique_multidim_array($data1,'videoUrl'); */ }
			    elseif($contentType == 4) { $data1[] = textArray($row1,$carr['ipath'],$link,$carr['vpath']); $data = unique_multidim_array($data1,'textId'); }
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
			//$source = 'portal';
			if($contentType == 2) { $vdata[] = portalVideoArray($row2,$carr['ipath'],$carr['vpath'],$link); /* $vdata = unique_multidim_array($data1,'videoUrl'); */ }
			elseif($contentType == 4) { $vdata1[] = textArray($row2,$carr['ipath'],$link,$carr['vpath']); $vdata = unique_multidim_array($vdata1,'textId'); }
		}
	}
	//print_r($vdata);
	if(!empty($vids)) { return array_slice(array_merge($data,$vdata),$start,$count); }
	else { return array_slice($vdata,$start,$count);}
} 

/******************************************** Endssss ***********************************************************/

?>