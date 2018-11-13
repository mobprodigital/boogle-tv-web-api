<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		//$client_id = $check['loginClientId'];
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
		$clientId = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'0';
		
		if(empty($clientId) || $clientId == null)
		{
			$text_array = array();
			$response['status']=false;
			$response['message']="clientId is mandatory.";
			$response['data'] = $text_array;
		}
		elseif(!is_numeric($clientId))
		{
			$text_array = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in clientId parameter";
			$response['data']=$text_array;
		}
		else
		{
			// Text Listing By Client ID
			$query = "SELECT t1.*,t2.content_id,t2.cover_image_url FROM news_metadata as t1 LEFT JOIN 
			content_multimedia as t2 ON t1.id = t2.content_id where t1.`client_id`= $clientId and t1.content_type ='text' and t1.status = 1";
			
			wh_log("Text Meta Data And Multimedia Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					while($row  = mysqli_fetch_assoc($query_rs))
					{  
						$text_array[] = textArray($row,$imageBaseURL,$link);
					}
					if(!empty($text_array))
					{
					$response['status']=true;
					$response['message']="Success";
					$response['data'] = $text_array;	
					} else {
					$text_array = array();
					$response['status']=false;
					$response['message']="No Texts Found For This Client Id.";
					$response['data'] = $text_array;	
					} 
				}
				else
				{ 
					$text_array = array();
					$response['status']=false;
					$response['message']="No Text Found For This Client Id.";
					$response['data'] = $text_array;
				}
			}
			else
			{
				$text_array = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data'] = $text_array;
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


