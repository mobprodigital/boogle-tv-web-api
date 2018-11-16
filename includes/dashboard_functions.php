<?php
/************************** Common Function For Client Array *********************************************/
function videoArray($row,$imageBaseURL,$videoBaseURL,$link)
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

	$video_temp_array = array("videoId"=>$row['id'],"categoryId"=>$cat_array,"clientId"=>$row['client_id'],"portalId"=>$portal_array,"title"=>$row['title'],"videoUrl"=>$videoUrl,"videoTags"=>$tags,"videoDate"=>$insertion_time,"language"=>$row['language'],"description"=>$row['description'],"coverImage"=>array("original"=>$imageUrl,"large"=>"","medium"=>"","small"=>""),"videoLength"=>$row['content_length'],"extension"=>$row['extension'],"videoMime"=>$row['mime'],"minAgeReq"=>$row['min_age_req'],"broadcasterName"=>$row['broadcaster_name'],"type"=>$row['type'],"currentAvailability"=>$row['content_availability'],"platform"=>$row['platform'],"adult"=>$row['adult'],"downloadRights"=>$row['download_rights'],"internationalRights"=>$row['intrernational_rights'],"genere"=>$row['genre'],"director"=>$row['director'],"producer"=>$row['producer'],"writer"=>$row['writer'],"musicDirector"=>$row['music_director'],"productionHouse"=>$row['production_house'],"actor"=>$row['actor'],"singer"=>$row['singer'],"country"=>$country);
	
	wh_log("Video Content Array : ".str_replace("\n"," ", print_r($video_temp_array, true)));
    return $video_temp_array;
} 


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

?>