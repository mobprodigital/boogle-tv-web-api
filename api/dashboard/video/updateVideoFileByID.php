<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($_POST, true)));

		$videoId     = mysqli_real_escape_string($link,trim(isset($_POST['videoId']))) ? mysqli_real_escape_string($link,trim($_POST['videoId'])) : '';
		$videoLength = mysqli_real_escape_string($link,trim(isset($_POST['videoLength']))) ? mysqli_real_escape_string($link,trim($_POST['videoLength'])) : '';
		$extension   = mysqli_real_escape_string($link,trim(isset($_POST['extension']))) ? mysqli_real_escape_string($link,trim($_POST['extension'])) : '';
		$videoMime         = mysqli_real_escape_string($link,trim(isset($_POST['videoMime']))) ? mysqli_real_escape_string($link,trim($_POST['videoMime'])) : '';

		if(empty($videoId) || $videoId == null)
		{
			$response['status']=false;
			$response['message']="videoId is mandatory.";
			$response['data']="";
		}
		elseif($_FILES['videoFile']['size'] == 0 && $_FILES['videoFile']['error'] == 0)
		{
			$response['status']=false;
			$response['message']="videoFile is mandatory.";
			$response['data']="";
		} 
		/* elseif($_FILES['videoThumbnail']['size'] == 0 && $_FILES['videoThumbnail']['error'] == 0)
		{
			$response['status']=false;
			$response['message']="thumbnail is mandatory.";
			$response['data']="";
		} */
		else
		{
			// Check Video Id is present or not 
			$check_query = "select * from content_metadata where id=$videoId and content_type='video'";
			$check_query_rs = mysqli_query($link,$check_query);
			wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
			if(mysqli_num_rows($check_query_rs) > 0)
			{
				if($_FILES['videoFile']['size'] > 0 && $_FILES['videoFile']['error'] == 0)
				{
					/* Video details */
					$vidfilename = $_FILES["videoFile"]["name"];
					$vidfile_ext = substr($vidfilename, strripos($vidfilename, '.')); // get file name
					$allowed_video_file_types = array('.mp4','.mp3');
					$video_status = false;
					wh_log("filename ".$vidfilename." file_ext - ".$vidfile_ext);
					//Ends
					
					// Upload Video File
					if (in_array($vidfile_ext,$allowed_video_file_types))
					{
						// delete old video file
						$check_query1 = "select * from content_multimedia where content_id=$videoId";
						$check_query_rs1 = mysqli_query($link,$check_query1);
						wh_log("Check Query ".$check_query1." count rows - ".mysqli_num_rows($check_query_rs1));
						if(mysqli_num_rows($check_query_rs1) > 0)
						{
							if($row = mysqli_fetch_assoc($check_query_rs1))
							{
								$myvideofile = $videoBaseDirURL.$row['video_url'];
								wh_log("File from databadse ".$row['video_url']." | Full file path -".$myvideofile);
								if (is_file($myvideofile)) {
									unlink($myvideofile);
								}
							}
						}
						//ends
						if(move_uploaded_file($_FILES["videoFile"]["tmp_name"],$videoBaseDirURL . $vidfilename))
						{ $video_status = true; }
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
					// Ends
				}
				if($_FILES['videoThumbnail']['size'] > 0 && $_FILES['videoThumbnail']['error'] == 0)
				{
					/* Image details */
					$filename = $_FILES["videoThumbnail"]["name"];
					$file_ext = substr($filename, strripos($filename, '.')); // get file name
					$allowed_file_types = array('.png','.jpg','.jpeg','.JPG','.JPEG','.PNG');
					wh_log("filename ".$filename." file_ext - ".$file_ext);
					$image_status = false;
					//Ends
					
					// Upload Cover Image
					if(in_array($file_ext,$allowed_file_types)) 
					{
						$myimagefile = $imageBaseDirURL.$row['cover_image_url'];
						wh_log("Image File from databadse ".$row['cover_image_url']." | Full file path -".$myimagefile);
						if (is_file($myimagefile)) {
							unlink($myimagefile);
						}
						if(move_uploaded_file($_FILES["videoThumbnail"]["tmp_name"],$imageBaseDirURL . $filename))
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
				// Insert Multimedia Files Of Particular StotyId
				if($image_status || $video_status)
				{
					if(empty($filename)) { $filename = $row['cover_image_url']; }
					if(empty($vidfilename)) { $vidfilename = $row['video_url']; }
					$updatedata = "update content_multimedia set video_url='$vidfilename',cover_image_url='$filename',content_length='$videoLength',extension='$extension',mime='$videoMime' where content_id='$videoId'";
					wh_log("Query executed = ".$updatedata." | Image File - ".$filename." | Video File -".$vidfilename);
					$updatedata_rs = mysqli_query($link,$updatedata);
					if($updatedata_rs)
					{
						$response['status']=true;
						$response['message']="Successfully Uploaded Files";
						$response['data']="";
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
				$response['message']="No data found regarding this video id".$videoId;
				$response['data']="";
			}
			//Ends
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