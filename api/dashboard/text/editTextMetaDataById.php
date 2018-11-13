<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

// Update
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		$client_id = $check['loginClientId'];
		$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :'';
		
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($_POST, true)));
		
		$textId = mysqli_real_escape_string($link,trim(isset($_POST['textId']))) ? mysqli_real_escape_string($link,trim($_POST['textId'])) :'';
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
		
		wh_log("textId - ".$textId." | Title - ".$title." | Language - ".$language." | Description -".$description." | city -".$city." | country -".$country." | author -".$author." | newsTime -".$postTime." | portalId - ".$portalId." | categoryId - ".$categoryId." | tags -".$tags);
		
		if(empty($title) || $title == null)
		{
			$response['status']=false;
			$response['message']="title is mandatory.";
			$response['data']="";
		}
		elseif(empty($textId) || $textId == null)
		{
			$response['status']=false;
			$response['message']="textId is mandatory.";
			$response['data']="";
		}
		elseif(empty($description) || $description == null)
		{
			$response['status']=false;
			$response['message']="description is mandatory.";
			$response['data']="";
		}
		elseif(empty($tags) || $tags == null)
		{
			$response['status']=false;
			$response['message']="tags are mandatory.";
			$response['data']="";
		}
		elseif(empty($categoryId) || $categoryId == null)
		{
			$response['status']=false;
			$response['message']="category id is mandatory.";
			$response['data']="";
		}
		elseif(empty($portalId) || $portalId == null)
		{
			$response['status']=false;
			$response['message']="portal id is mandatory.";
			$response['data']="";
		}
		/* elseif($_FILES['thumbnail']['size'] == 0 && $_FILES['thumbnail']['error'] == 0)
		{
			$response['status']=false;
			$response['message']="thumbnail is mandatory.";
			$response['data']="";
		} */ 
		else
		{
			$query_status = false;
			$image_status = false;
			// Check Text Id is present or not 
			$check_query = "select * from news_metadata where id=$textId and content_type='text' and client_id = $clientId";
			$check_query_rs = mysqli_query($link,$check_query);
			wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
			if(mysqli_num_rows($check_query_rs) > 0)
			{
				// Image file is missing
				if($_FILES['thumbnail']['size'] == 0 && $_FILES['thumbnail']['error'] == 0)
				{ 
					$query_status = true;
					$check_query1 = "select * from content_multimedia where content_id=$textId";
					$check_query_rs1 = mysqli_query($link,$check_query1);
					wh_log("Check Query ".$check_query1." count rows - ".mysqli_num_rows($check_query_rs1));
					if(mysqli_num_rows($check_query_rs1) > 0)
					{
						if($row = mysqli_fetch_assoc($check_query_rs1))
						{
							$filename = $row['cover_image_url'];
							wh_log("File from databadse ".$filename);
						}
					}
				}
				else
				{
					// Image file is not missing
					// Uplaod Image
					if($_FILES['thumbnail']['size'] > 0 && $_FILES['thumbnail']['error'] == 0)
					{
						/* Image details */
						$filename = $_FILES["thumbnail"]["name"];
						$file_ext = substr($filename, strripos($filename, '.')); // get file name
						$allowed_file_types = array('.png','.jpg','.jpeg','.JPG','.JPEG','.PNG');
						wh_log("filename ".$filename." file_ext - ".$file_ext);
						//Ends
						
						// Upload Cover Image
						if(in_array($file_ext,$allowed_file_types)) 
						{
							// delete old cover image file
							$check_query1 = "select * from content_multimedia where content_id=$textId";
							$check_query_rs1 = mysqli_query($link,$check_query1);
							wh_log("Check Query ".$check_query1." count rows - ".mysqli_num_rows($check_query_rs1));
							if(mysqli_num_rows($check_query_rs1) > 0)
							{
								if($row = mysqli_fetch_assoc($check_query_rs1))
								{
									$myimagefile = $imageBaseDirURL.$row['cover_image_url'];
									wh_log("File from databadse ".$row['cover_image_url']." | Full file path -".$myimagefile);
									if (is_file($myimagefile)) {
										unlink($myimagefile);
									}
								}
							}
							else
							{
								$response['status']=false;
								$response['message']="No data found regarding this text id".$textId;
								$response['data']="";
							}
							if(move_uploaded_file($_FILES["thumbnail"]["tmp_name"],$imageBaseDirURL . $filename))
							{ $image_status = true; }
							else
							{
								$response['status']=false;
								$response['message']="Some Error Occured in uploading.";
								$response['data']="";
							}
						}
						else
						{
							$response['status']=false;
							$response['message']="Not A Valid Format.";
							$response['data']="";
						}
						//Ends
					}
				}
			// Update data in table
			if($image_status || $query_status)
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
				
				
				if(empty($filename)) { $filename = $row['cover_image_url']; }
								
				$insert_data = "update news_metadata set cat_id='$categoryId',portal_ids='$portalId',title='$title',post_time='$postTime',language='$language',description='$description',tags='$tags',country='$country',city='$city',author ='$author' where id = $textId";
				$insert_data_rs = mysqli_query($link,$insert_data);
				wh_log("Update Text Data Query - ".$insert_data);
				
				if($insert_data_rs)
				{
					$updatedata = "update content_multimedia set cover_image_url='$filename' where content_id='$textId'";
					wh_log("Query executed = ".$updatedata." | Image File - ".$filename);
					$updatedata_rs = mysqli_query($link,$updatedata);
					wh_log("Update content_multimedia Data Query - ".$updatedata);
					if($updatedata_rs)
					{
						$response['status']=true;
						$response['message']="Success.";
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
			}
			else
			{
				$response['status']=false;
				$response['message']="No data found regarding this text id".$textId;
				$response['data']="";
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


