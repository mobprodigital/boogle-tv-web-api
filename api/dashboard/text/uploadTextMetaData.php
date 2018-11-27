<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

/* Validate Api */
$apiKey = "UplText";
/* Ends */

// Insert
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		$roleid = $check['loginRoleId'];
		$res = validateApi($apiKey,$roleid);
		if($res)
		{
			$client_id = $check['loginClientId'];
			$uploaded_By = $check['loginUserId'];

			$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :'0';
			$uploadedBy = mysqli_real_escape_string($link,trim(isset($uploaded_By))) ? mysqli_real_escape_string($link,trim($uploaded_By)) :'0';

			wh_log("Json In Request : ".str_replace("\n"," ", print_r($_POST, true)));
			
			$title = mysqli_real_escape_string($link,trim(isset($_POST['title']))) ? mysqli_real_escape_string($link,trim($_POST['title'])) :'';
			$language = mysqli_real_escape_string($link,trim(isset($_POST['language']))) ? mysqli_real_escape_string($link,trim($_POST['language'])) :'';
			$description = mysqli_real_escape_string($link,trim(isset($_POST['description']))) ? mysqli_real_escape_string($link,trim($_POST['description'])) :'';
			$city = mysqli_real_escape_string($link,trim(isset($_POST['city']))) ? mysqli_real_escape_string($link,trim($_POST['city'])) :'';
			$country = mysqli_real_escape_string($link,trim(isset($_POST['country']))) ? mysqli_real_escape_string($link,trim($_POST['country'])) :'';
			$author = mysqli_real_escape_string($link,trim(isset($_POST['author']))) ? mysqli_real_escape_string($link,trim($_POST['author'])) :'';
			$postTime = mysqli_real_escape_string($link,trim(isset($_POST['postTime']))) ? mysqli_real_escape_string($link,trim($_POST['postTime'])) :'';
			$categoryId = mysqli_real_escape_string($link,trim(isset($_POST['categoryId']))) ? mysqli_real_escape_string($link,trim($_POST['categoryId'])) :'';
			$portalId = mysqli_real_escape_string($link,trim(isset($_POST['portalId']))) ? mysqli_real_escape_string($link,trim($_POST['portalId'])) :'';
			$tags = mysqli_real_escape_string($link,trim(isset($_POST['tags']))) ? mysqli_real_escape_string($link,trim($_POST['tags'])) :'';
			
			wh_log("clientID - ".$clientId." |  Uploaded By - ".$uploadedBy. " | Title - ".$title." | Language - ".$language." | Description -".$description." | city -".$city." | country -".$country." | author -".$author." | newsTime -".$postTime." | portalId - ".$portalId." | categoryId - ".$categoryId." | tags -".$tags);
			
			if(empty($title) || $title == null)
			{
				$response['status']=false;
				$response['message']="title is mandatory.";
				$response['data']="";
			}
			elseif(empty($uploadedBy) || $uploadedBy == null)
			{
				$response['status']=false;
				$response['message']="Uploaded By is mandatory.";
				$response['data']="";
			}
			elseif(empty($description) || $description == null)
			{
				$response['status']=false;
				$response['message']="description is mandatory.";
				$response['data']="";
			}
			elseif(empty($categoryId) || $categoryId == null)
			{
				$response['status']=false;
				$response['message']="category id is mandatory.";
				$response['data']="";
			}
			elseif(empty($tags) || $tags == null)
			{
				$response['status']=false;
				$response['message']="tags are mandatory.";
				$response['data']="";
			}
			elseif(empty($portalId) || $portalId == null)
			{
				$response['status']=false;
				$response['message']="portal id is mandatory.";
				$response['data']="";
			}
			elseif($_FILES['thumbnail']['size'] == 0 && $_FILES['thumbnail']['error'] == 0)
			{
				$response['status']=false;
				$response['message']="thumbnail is mandatory.";
				$response['data']="";
			} 
			elseif(empty($clientId) || $clientId == null)
			{
				$response['status']=false;
				$response['message']="clientId is mandatory.";
				$response['data']="";
			}
			else
			{
				// Update/Insert Portal ids in category table
				$cids = comma_separated_to_array($categoryId);
				$pids = comma_separated_to_array($portalId);
				
				foreach ($cids as $value) 
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
								$array = array_unique (array_merge($a, $pids));
								
								if ($array) 
								{ 
									//echo "first";
									$portal_selected = $array;
								}
								elseif(empty($a))
								{ 
									//echo "second";
									$portal_selected = $pids;
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
				// Uplaod Image
				$image_flag = uploadImage($_FILES,$imageBaseDirURL);
				if($image_flag['status'] == true)
				{  
					$Cover_image = $image_flag['file'];
					// Convert Array To Comma Seperated Strings
					wh_log("categoryId via post - ".$categoryId." | portalId via post - ".$portalId);
					//Ends
					
					//echo "Insert case";
					$insert_data = "insert into news_metadata (`cat_id`,`client_id`,`uploaded_by`,`portal_ids`,`title`,`content_type`,`status`,`post_time`,`insertion_time`,`language`,`description`,`tags`,`country`,`city`,`author`) values ('$categoryId',
					'$clientId','$uploadedBy','$portalId','$title','text','1', '$postTime',NOW(),'$language','$description','$tags','$country','$city','$author')";
					$insert_data_rs = mysqli_query($link,$insert_data);
					$last_insert_id = mysqli_insert_id($link);
					wh_log("Insert Text Data Query - ".$insert_data." | Last Insert_id - ".$last_insert_id);
					if($insert_data_rs)
					{
						$updatedata = "insert into content_multimedia (content_id,cover_image_url,content_type) values ($last_insert_id,'$Cover_image','text')";
						wh_log("Query executed = ".$updatedata." | Image File - ".$Cover_image);
						$updatedata_rs = mysqli_query($link,$updatedata);
						if($updatedata_rs)
						{
							$response['status']=true;
							$response['message']="Successfully Inserted";
							$response['data']="";
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
						$response['message']=mysqli_error($link);
						$response['data']="";
					}
				}
				else
				{ 
					$response = $image_flag;
				}
				//Ends
			}
		}
		else
		{
			header('HTTP/1.1 400 Bad Request', true, 400);
			die;
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


function uploadImage($file,$imageBaseDirURL)
{
	if($file['thumbnail']['size'] > 0 && $file['thumbnail']['error'] == 0)
	{
		/* Image details */
		$filename = $file["thumbnail"]["name"];
		$file_ext = substr($filename, strripos($filename, '.')); // get file name
		$allowed_file_types = array('.png','.jpg','.jpeg','.JPG','.JPEG','.PNG');
		wh_log("filename ".$filename." file_ext - ".$file_ext);
		//Ends
		
		// Upload Cover Image
		if(in_array($file_ext,$allowed_file_types)) 
		{
			if(move_uploaded_file($file["thumbnail"]["tmp_name"],$imageBaseDirURL . $filename))
			{  
				wh_log("filename ".$filename." file_ext - ".$file_ext." | imageBaseDirURL -".$imageBaseDirURL); 
				
				$response['status']=true;
				$response['message']="Success.";
				$response['file']=$filename;
				$response['data']=""; 
				return $response;
			}
			else
			{  
				$response['status']=false;
				$response['message']="Some Error Occured in uploading.";
				$response['data']=""; 
				return $response;
			}
		}
		else
		{
			$response['status']=false;
			$response['message']="Not A Valid Format.";
			$response['data']=""; 
			return $response;
		}
		//Ends
	}
	else
	{ 
		$response['status']=false;
		$response['message']="File Error.";
		$response['data']=""; 
		return $response; 
	}
}


wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


	
?>


