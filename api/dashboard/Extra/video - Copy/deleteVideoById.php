<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$id = mysqli_real_escape_string($link,trim(isset($req_json['videoId']))) ? mysqli_real_escape_string($link,trim($req_json['videoId'])) :'';
	wh_log("video ID - ".$id);
		
	if((empty($id) || $id == null))
	{
		$arr = array();
		$response['status']=false;
		$response['message']="Video Id Parameter is Missing.";
		$response['data']=$arr;
	}
	elseif(!is_numeric($id))
	{
		$arr = array();
		$response['status']=false;
		$response['message']="Allowed only numbers in Video Id parameter";
		$response['data']=$arr;
	}
	else
	{
		// Check video id is present or not
		$check_query = "select * from contents where id=$id and content_type ='video'";
		$check_query_rs = mysqli_query($link,$check_query);
		wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
		if(mysqli_num_rows($check_query_rs) > 0)
		{
			//Delete Video Details
			$edit_query = "update contents set status = '0' where id = '$id'";
			$edit_query_rs = mysqli_query($link,$edit_query);
			
			wh_log("Update Video Query - ".$edit_query_rs);
			if($edit_query_rs)
			{
				$arr = array();
				$response['status']=true;
				$response['message']="Successfully Deleted.";
				$response['data']=$arr;
			}
			else
			{
				$arr = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data']=$arr;
			}
		}
		else
		{
			$arr = array();
			$response['status']=false;
			$response['message']="Invalid video id";
			$response['data']=$arr;
		}
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


