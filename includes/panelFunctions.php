<?php
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
	$portal_temp = array("portalId"=>$row['portal_id'],"PoratlName"=>$row['name'],"url"=>$row['url'],"email"=>$row['email']);
	wh_log("Portal Temp Array : ".str_replace("\n"," ", print_r($portal_temp, true)));
    return $portal_temp;
}
function getPortalData($id,$link)
{
	$portalList = "SELECT * FROM `portals` where portal_id = '$id'";
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

/********************************** Ends *****************************************************************/
?>