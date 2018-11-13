<?php
include "../../includes/config.php";
include "../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$key = mysqli_real_escape_string($link,isset($req_json['authentication_key'])) ? mysqli_real_escape_string($link,trim($req_json['authentication_key'])) :'0';
	
	if($key == $auth_key)
	{
		$uemail = mysqli_real_escape_string($link,isset($req_json['email'])) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		wh_log("Authentication Key -".$key." | Email - ".$uemail);
		if(empty($uemail) || $uemail == null)
		{
			$response['status']=false;
			$response['message']="Email Parameter Missing.";
		}
		elseif(!filter_var($uemail, FILTER_VALIDATE_EMAIL))
		{
			$response['status']=false;
			$response['message']="Invalid email format";
		}
		else
		{
			//Get Data from users table on basis of email and status
			$query = "SELECT id FROM users WHERE email = ? and status = ?";
			if ($stmt = mysqli_prepare($link, $query))
			{
				$status = 1;
				mysqli_stmt_bind_param($stmt, "si", $uemail,$status);
				mysqli_stmt_execute($stmt);
				mysqli_stmt_bind_result($stmt,$id);
				mysqli_stmt_store_result($stmt);
				if (mysqli_stmt_num_rows($stmt) > 0) 
				{
					//echo $count = mysqli_stmt_num_rows($stmt);
					wh_log("Query -".$query." | Rows Count - ".mysqli_stmt_num_rows($stmt));
					if(mysqli_stmt_fetch($stmt)) 
					{
						$encode_id = encodeId($id);
						$url = $panel_url.'panel/test.php?id='.$encode_id;
						$subject = 'Boogletv Verification Code';
						$msg = 'Please Click on below link,to change your password.<br>'.$url;
					    wh_log("Userid -".$encode_id." | Url - ".$url." | Subject - ".$subject."| Message - ".$msg." | Uemail -".$uemail);
						$result_email = send_email($uemail,$msg,$subject);
						wh_log("Result Email -".$result_email);
						$response['status']=true;
						$response['message']="Mail Send Successfully On Your Registered Email";
					}
					else
					{
						$response['status']=false;
						$response['message']=mysqli_error($link);
					}
					
				}
				else
				{
					$response['status']=false;
					$response['message']="Not a registered user";
				}
				
			}
			else
			{
				$response['status']=false;
				$response['message']=mysqli_error($link);
			}
			mysqli_stmt_close($stmt);
		}
	}
	else
	{
		$response['status']=false;
		$response['message']= "Authentication Failed";
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


