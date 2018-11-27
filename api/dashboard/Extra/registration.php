<?php
include "../../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$req_json = json_decode(file_get_contents("php://input"), true);
	wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	$key = mysqli_real_escape_string($link,trim(isset($req_json['authentication_key']))) ? mysqli_real_escape_string($link,trim($req_json['authentication_key'])) :'0';
	
	if($key == $auth_key)
	{
		$uname = mysqli_real_escape_string($link,trim(isset($req_json['name']))) ? mysqli_real_escape_string($link,trim($req_json['name'])) :'';
		$uemail = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		$upassword = mysqli_real_escape_string($link,trim(isset($req_json['password']))) ? mysqli_real_escape_string($link,trim($req_json['password'])) :'';
		$umobile = mysqli_real_escape_string($link,trim(isset($req_json['mobile']))) ? mysqli_real_escape_string($link,trim($req_json['mobile'])) :'';
		$utenure = mysqli_real_escape_string($link,trim(isset($req_json['tenure']))) ? mysqli_real_escape_string($link,trim($req_json['tenure'])) :'0';
		$utype = mysqli_real_escape_string($link,trim(isset($req_json['type']))) ? mysqli_real_escape_string($link,trim($req_json['type'])) :'';
		$uaddress = mysqli_real_escape_string($link,trim(isset($req_json['address']))) ? mysqli_real_escape_string($link,trim($req_json['address'])) :'';
		
		wh_log("Authentication Key -".$key." | uname - ".$uname." | uemail - ".$uemail." | upassword - ".$upassword." | umobile - ".$umobile." | utenure - ".$utenure." | utype - ".$utype." | uaddress - ".$uaddress);
		
		if((empty($uname) || $uname == null) || (empty($uemail) || $uemail == null) || (empty($upassword) || $upassword == null) || (empty($umobile) || $umobile == null) || (empty($utenure) || $utenure == null) || (empty($utype) || $utype == null) || (empty($uaddress) || $uaddress == null))
		{
			$response['status']=false;
			$response['message']="Some Parameter Missing.";
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $uname))
		{
			$response['status']=false;
			$response['message']="Only letters and white space allowed in name parameter.";
		}
		elseif(!filter_var($uemail, FILTER_VALIDATE_EMAIL))
		{
			$response['status']=false;
			$response['message']="Invalid email format";
		}
		elseif(!is_numeric($umobile))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in mobile parameter";
		}
		/* elseif(!is_numeric($utenure))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in tenure parameter";
		} */
		elseif(!preg_match("/^[a-zA-Z ]+$/", $utype))
		{
			$response['status']=false;
			$response['message']="Only letters allowed in type parameter.";
		}
		else
		{
			// Check Email Duplicasy
			$query = "SELECT * FROM users WHERE email = '$uemail'";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					wh_log("Query - ".$query." | Rows Count - ".mysqli_num_rows($query_rs));
					$response['status']=false;
					$response['message']="Already Exist Email id";
				}
				else
				{
					wh_log("Query - ".$query." | Rows Count - ".mysqli_num_rows($query_rs));
					//echo "Add New User";
					$sql = "INSERT INTO users (name,email,password,mobile,address,agreement_tenure,type,insertion_time) VALUES ('$uname','$uemail','$upassword','$umobile','$uaddress',$utenure,'$utype',NOW())";
					$sql_rs = mysqli_query($link,$sql);
					if($query_rs)
					{
						$response['status']=true;
						$response['message']="Successfully Registered.";
					}
					else
					{
						$response['status']=false;
						$response['message']=mysqli_error($link);
					}
				}
			}
			else
			{
				$response['status']=false;
				$response['message']=mysqli_error($link);
			}
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


