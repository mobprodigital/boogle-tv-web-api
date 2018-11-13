<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	//$check = getCookie();
	/* if ($check) 
	{ */
		$clientid = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'';
		$type = mysqli_real_escape_string($link,trim(isset($req_json['type']))) ? mysqli_real_escape_string($link,trim($req_json['type'])) :'';
		$title = mysqli_real_escape_string($link,trim(isset($req_json['title']))) ? mysqli_real_escape_string($link,trim($req_json['title'])) :'';
		$desc = mysqli_real_escape_string($link,trim(isset($req_json['desc']))) ? mysqli_real_escape_string($link,trim($req_json['desc'])) :'';
		$language = mysqli_real_escape_string($link,trim(isset($req_json['language']))) ? mysqli_real_escape_string($link,trim($req_json['language'])) :'';
		
		wh_log("clientID - ".$clientid." | Title - ".$title." | Language - ".$language." | Description -".$desc);
		
		if((empty($clientid) || $clientid == null) || (empty($title) || $title == null) || (empty($language) || $language == null) || (empty($req_json['catID'])) || (empty($req_json['assignedPortals'])) || (empty($req_json['tag'])) || (empty($desc) || $desc == null) || (empty($type) || $type == null))
		{
			$response['status']=false;
			$response['message']="Some Parameter Missing.";
		}
		elseif(!is_numeric($clientid))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in clientid parameter";
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $language))
		{
			$response['status']=false;
			$response['message']="Only letters and white space allowed in language parameter.";
		}
		elseif(!preg_match("/^[a-zA-Z]+$/", $type))
		{
			$response['status']=false;
			$response['message']="Only letters allowed in type parameter.";
		}
		else
		{
			// Convert array in comma seperate string
			$pids  = implode(",",$req_json['assignedPortals']);
			$tags  = implode(",",$req_json['tag']);
			$catid = implode(",",$req_json['catID']);
			// Ends
			
			// Title and description encoding
			$find= array("’","‘",'“','”');
			$replace = array("'","'",'"','"');
			
			//Title
			$title1 = str_replace($find, $replace,$title);
			$final_title = addslashes($title1);
			//Ends
			
			//Description
			$description1 = str_replace($find, $replace,$desc);
			$final_description = addslashes($description1);
			//Ends
			
			//Add New Video
			
			// When content type is text/multimedia html script
			if($type == 'text') { $video_url == 'NULL'; }
			// Ends
			$add_video = "INSERT INTO videos (cat_id,client_id,portal_ids,title,video_url,video_tags,language,description,status,insertion_time) VALUES ('$catid','$clientid','$pids','$final_title','$video_url','$tags','$language','$final_description','1',NOW())";
			$add_video_rs = mysqli_query($link,$add_video);
			$last_insert_id = mysqli_insert_id($link);
			
			wh_log("Add Video Query - ".$add_video." | Last Insert_id - ".$last_insert_id);
			if($add_video_rs)
			{
				$response['status']=true;
				$response['message']="Successfully Inserted.";
			}
			else
			{
				$response['status']=false;
				$response['message']=mysqli_error($link);
			}
		}
	/* }
	else
	{
		header('HTTP/1.1 401 Unauthorized', true, 401);
		die;
	}  */
}
else
{
	header("HTTP/1.0 404 Not Found");
	die;
}

wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));
echo json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>


