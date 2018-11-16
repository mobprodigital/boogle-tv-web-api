<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	/* print_r($_CCOKIE);
	die; */
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
		/* elseif($_FILES['thumbnail']['size'] == 0 && $_FILES['thumbnail']['error'] == 0)
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
				//print_r($_FILES);
				//die;
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
					$updatedata = "insert into content_multimedia (content_id,video_url,cover_image_url,content_length,extension,mime,content_type) values ($videoId,'$vidfilename',
					'$filename','$videoLength','$extension','$videoMime','video')";
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