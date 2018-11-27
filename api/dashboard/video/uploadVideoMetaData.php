<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	//print_r($check);
	if ($check) 
	{
		$client_id = $check['loginClientId'];
		$uploaded_By = $check['loginUserId'];
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
		// For update case
		$videoId = mysqli_real_escape_string($link,trim(isset($req_json['videoId']))) ? mysqli_real_escape_string($link,trim($req_json['videoId'])) :'';
		
		$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :' ';
		$uploadedBy = mysqli_real_escape_string($link,trim(isset($uploaded_By))) ? mysqli_real_escape_string($link,trim($uploaded_By)) :'0';
		$title = mysqli_real_escape_string($link,trim(isset($req_json['title']))) ? mysqli_real_escape_string($link,trim($req_json['title'])) :'';
		$language = mysqli_real_escape_string($link,trim(isset($req_json['language']))) ? mysqli_real_escape_string($link,trim($req_json['language'])) :'';
		$description = mysqli_real_escape_string($link,trim(isset($req_json['description']))) ? mysqli_real_escape_string($link,trim($req_json['description'])) :'';
		$minAgeReq = mysqli_real_escape_string($link,trim(isset($req_json['minAgeReq']))) ? mysqli_real_escape_string($link,trim($req_json['minAgeReq'])) :'0';
		$broadcasterName = mysqli_real_escape_string($link,trim(isset($req_json['broadcasterName']))) ? mysqli_real_escape_string($link,trim($req_json['broadcasterName'])) :'';
		$type = mysqli_real_escape_string($link,trim(isset($req_json['type']))) ? mysqli_real_escape_string($link,trim($req_json['type'])) :'';
		$currentAvailability = mysqli_real_escape_string($link,trim(isset($req_json['currentAvailability']))) ? mysqli_real_escape_string($link,trim($req_json['currentAvailability'])) :'';
		$platform = mysqli_real_escape_string($link,trim(isset($req_json['platform']))) ? mysqli_real_escape_string($link,trim($req_json['platform'])) :'';
		$adult = mysqli_real_escape_string($link,trim(isset($req_json['adult']))) ? mysqli_real_escape_string($link,trim($req_json['adult'])) :'false';
		$downloadRights = mysqli_real_escape_string($link,trim(isset($req_json['downloadRights']))) ? mysqli_real_escape_string($link,trim($req_json['downloadRights'])) :'No';
		$internationalRights = mysqli_real_escape_string($link,trim(isset($req_json['internationalRights']))) ? mysqli_real_escape_string($link,trim($req_json['internationalRights'])) :'No';
		$genere = mysqli_real_escape_string($link,trim(isset($req_json['genere']))) ? mysqli_real_escape_string($link,trim($req_json['genere'])) :'';
		$director = mysqli_real_escape_string($link,trim(isset($req_json['director']))) ? mysqli_real_escape_string($link,trim($req_json['director'])) :'';
		$producer = mysqli_real_escape_string($link,trim(isset($req_json['producer']))) ? mysqli_real_escape_string($link,trim($req_json['producer'])) :'';
		$writer = mysqli_real_escape_string($link,trim(isset($req_json['writer']))) ? mysqli_real_escape_string($link,trim($req_json['writer'])) :'';
		$musicDirector = mysqli_real_escape_string($link,trim(isset($req_json['musicDirector']))) ? mysqli_real_escape_string($link,trim($req_json['musicDirector'])) :'';
		$productionHouse = mysqli_real_escape_string($link,trim(isset($req_json['productionHouse']))) ? mysqli_real_escape_string($link,trim($req_json['productionHouse'])) :'';
		$actor = mysqli_real_escape_string($link,trim(isset($req_json['actor']))) ? mysqli_real_escape_string($link,trim($req_json['actor'])) :'';
		$singer = mysqli_real_escape_string($link,trim(isset($req_json['singer']))) ? mysqli_real_escape_string($link,trim($req_json['singer'])) :'';
		
		if(! empty($req_json['categoryId'])) { $cat_array_status = check_array_values($req_json['categoryId']); }
		if(! empty($req_json['portalId'])) { $portal_array_status = check_array_values($req_json['portalId']); }
		wh_log("clientID - ".$clientId." | Uploaded By - ".$uploadedBy." | Title - ".$title." | Language - ".$language." | Description -".$description." | minAgeReq -".$minAgeReq." | broadcasterName -".$broadcasterName." | type -".$type." | currentAvailability -".$currentAvailability." | platform -".$platform." | adult -".$adult." | downloadRights -".$downloadRights." | internationalRights -".$internationalRights." | genere -".$genere." | director -".$director." | producer -".$producer." | writer -".$writer." | musicDirector -".$musicDirector." | productionHouse -".$productionHouse." | actor -".$actor." | singer -".$singer);
		
		if(empty($clientId) || $clientId == null)
		{
			$response['status']=false;
			$response['message']="clientId is mandatory.";
			$response['data']="";
		}
		elseif(!is_numeric($clientId))
		{
			$arr = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in clientId parameter";
			$response['data']=$arr;
		}
		elseif(empty($uploadedBy) || $uploadedBy == null)
		{
			$response['status']=false;
			$response['message']="Uploaded By is mandatory.";
			$response['data']="";
		}
		elseif(empty($title) || $title == null)
		{
			$response['status']=false;
			$response['message']="title is mandatory.";
			$response['data']="";
		}
		elseif(empty($req_json['categoryId']))
		{
			$response['status']=false;
			$response['message']="category id is mandatory.";
			$response['data']="";
		}
		elseif(!$cat_array_status)
		{
			$response['status']=false;
			$response['message']="Category Id parameter should be numeric.";
			$response['data']="";
		}
		elseif(empty($req_json['portalId']))
		{
			$response['status']=false;
			$response['message']="portal id is mandatory.";
			$response['data']="";
		}
		elseif(!$portal_array_status)
		{
			$response['status']=false;
			$response['message']="Portal Id parameter should be numeric.";
			$response['data']="";
		}
		
		else
		{
		// Update portal ids in category table
		foreach ($req_json['categoryId'] as $value) 
			{
				$check_cat = "select portal_ids from category where id = $value and status =1";
				wh_log("Query - ".$query);
				$check_cat_rs = mysqli_query($link,$check_cat);
				if($check_cat_rs)
				{
					if(mysqli_num_rows($check_cat_rs) > 0)
					{
						if($row  = mysqli_fetch_assoc($check_cat_rs))
						{
							$a = comma_separated_to_array($row['portal_ids']);
							$array = array_unique (array_merge($a, $req_json['portalId']));
							
							if ($array) 
							{ 
								//echo "first";
								$portal_selected = $array;
							}
							elseif(empty($a))
							{ 
								//echo "second";
								$portal_selected = $req_json['portalId'];
							}
							else
							{
							}
						}
						//print_r($portal_selected);
						$portal_selected1 = array_to_comma_separated($portal_selected);
						//update portal ids in category table
						$update_data = "update category set portal_ids = '$portal_selected1' where id = $value and status =1";
						$update_data_rs = mysqli_query($link,$update_data);
					}
				}
			}
			//die;
			//Ends
			
			
			// Convert Array To Comma Seperated Strings
			$catid = array_to_comma_separated($req_json['categoryId']);
			$pids = array_to_comma_separated($req_json['portalId']);
			if(!empty($req_json['videoTags'])) { $tags = array_to_comma_separated($req_json['videoTags']); }
			if(!empty($req_json['country'])) { $country = array_to_comma_separated($req_json['country']); }
			//Ends
			
			if($videoId == null || $videoId == 0)
			{
				//echo "Insert case";
				$insert_data = "insert into content_metadata (`cat_id`,`client_id`,`uploaded_by`,`portal_ids`,`title`,`content_type`,`tags`, `status`,
				`insertion_time`,`language`, `description`,`min_age_req`,`broadcaster_name`,`type`,`content_availability`,`platform`, `adult`,`download_rights`,
				`intrernational_rights`,`country`,`genre`,`director`,`producer`,`writer`, `music_director`,`production_house`,`actors`,`singers`) values ('$catid',
				'$clientId','$uploadedBy','$pids','$title','video','$tags','1', NOW(),'$language','$description','$minAgeReq','$broadcasterName','$type','$currentAvailability',
				'$platform', '$adult','$downloadRights','$internationalRights','$country','$genere','$director','$producer','$writer', '$musicDirector',
				'$productionHouse','$actor','$singer')";
				$insert_data_rs = mysqli_query($link,$insert_data);
				$last_insert_id = mysqli_insert_id($link);
				wh_log("Insert Video Data Query - ".$insert_data." | Last Insert_id - ".$last_insert_id);
				if($insert_data_rs)
				{
					$response['status']=true;
					$response['message']="Successfully Inserted"; 
					$response['data']=$last_insert_id; 
				}
				else
				{
					$response['status']=false;
					$response['message']=mysqli_error($link);
					$response['data']="";
				}
			}
			else
			{
				//echo "Update case";
				$check_query = "select * from content_metadata where id=$videoId";
				$check_query_rs = mysqli_query($link,$check_query);
				wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
				if(mysqli_num_rows($check_query_rs) > 0)
				{
					$insert_data = "update content_metadata set cat_id='$catid',client_id='$clientId',uploaded_by='$uploadedBy',portal_ids='$pids',title='$title',tags='$tags',
					language='$language',description='$description',min_age_req='$minAgeReq',broadcaster_name='$broadcasterName',type='$type',
					content_availability='$currentAvailability',platform='$platform', adult='$adult',download_rights='$downloadRights',
					intrernational_rights='$internationalRights',country='$country',genre='$genere',director='$director',producer='$producer',writer='$writer', 
					music_director='$musicDirector',production_house='$productionHouse',actors='$actor',singers='$singer' where id = $videoId";
					$insert_data_rs = mysqli_query($link,$insert_data);
					wh_log("Update Video Data Query - ".$insert_data);
					if($insert_data_rs)
					{
						$response['status']=true;
						$response['message']="Successfully Updated"; 
						$response['data']=$videoId;
						
					}
					else
					{
						$response['status']=false;
						$response['message']=mysqli_error($link);
						$response['data']="";
					}
				}
				else
				{ 
					$response['status']=false;
					$response['message']="Invalid Video Id";
					$response['data']="";
				}
			}
		}
	}
	else
	{
		header('HTTP/1.1 401 Unauthorized', true, 401);
		die;
	}  
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>


