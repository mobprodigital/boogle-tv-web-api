<?php
include "../../includes/config.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "GET")
{
	print_r($_COOKIE);
	die;
	//$req_json = json_decode(file_get_contents("php://input"), true);
	//wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
	//$check = getCookie();
	//if ($check) 
	if (isset($_COOKIE['authentication_key']))
	{ 
		$clientName = mysqli_real_escape_string($link,trim(isset($req_json['clientName']))) ? mysqli_real_escape_string($link,trim($req_json['clientName'])) :'';
		$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		$phone = mysqli_real_escape_string($link,trim(isset($req_json['phone']))) ? mysqli_real_escape_string($link,trim($req_json['phone'])) :'';
		$domain = mysqli_real_escape_string($link,trim(isset($req_json['domain']))) ? mysqli_real_escape_string($link,trim($req_json['domain'])) :'';
		$billingCycle = mysqli_real_escape_string($link,trim(isset($req_json['billingCycle']))) ? mysqli_real_escape_string($link,trim($req_json['billingCycle'])) :'';
		$agreementTenure = mysqli_real_escape_string($link,trim(isset($req_json['agreementTenure']))) ? mysqli_real_escape_string($link,trim($req_json['agreementTenure'])) :'0';
		$address = mysqli_real_escape_string($link,trim(isset($req_json['address']))) ? mysqli_real_escape_string($link,trim($req_json['address'])) :'';
		$skypeId = mysqli_real_escape_string($link,trim(isset($req_json['skypeId']))) ? mysqli_real_escape_string($link,trim($req_json['skypeId'])) :'';
		
		wh_log("clientName - ".$clientName." | Email - ".$email." | Phone - ".$phone." | Domain - ".$domain." BillingCycle -".$billingCycle." | AgreementTenure - ".$agreementTenure." | Address - ".$address." | SkypeId - ".$skypeId);
		
		if((empty($email) || $email == null) || (empty($address) || $address == null) || (empty($clientName) || $clientName == null) || (empty($phone) || $phone == null) || (empty($req_json['assignedPortals'])))
		{
			$response['status']=false;
			$response['message']="Some Parameter Missing.";
		}
		elseif(!is_numeric($phone))
		{
			$response['status']=false;
			$response['message']="Allowed only numbers in mobile parameter";
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$response['status']=false;
			$response['message']="Invalid email format";
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $clientName))
		{
			$response['status']=false;
			$response['message']="Only letters and white space allowed in name parameter.";
		}
		else
		{
			// Make Poratlids array in comma seperate string
			$pids = implode(",",$req_json['assignedPortals']);
			// Ends
			
			// Check client/partner is exist in client table with email id and mobile
			$check_user = "SELECT * FROM clients WHERE email = '$email' and mobile='$phone'";
			wh_log("Query - ".$check_user);
			$check_user_rs = mysqli_query($link,$check_user);
			if($check_user_rs)
			{
				if(mysqli_num_rows($check_user_rs) > 0)
				{
					wh_log("Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs));
					$response['status']=false;
					$response['message']="This client is already exist.";
				}
				else
				{
					wh_log("Query - ".$check_user." | Rows Count - ".mysqli_num_rows($check_user_rs)." | Poratl Ids -".$pids);
					
					//Add New Partner
					$billingCycle = !empty($billingCycle) ? "$billingCycle" : "NULL";
					$content_type = !empty($content_type) ? "$content_type" : "NULL";
					$add_client = "INSERT INTO clients (name,email,mobile,address,skype_id,content_type,billing_cycle,url,agreement_tenure,portal_ids,status,insertion_time) VALUES ('$clientName','$email','$phone','$address','$skypeId','$content_type','$billingCycle','$domain','$agreementTenure','$pids','1',NOW())";
					$add_client_rs = mysqli_query($link,$add_client);
					$last_insert_id = mysqli_insert_id($link);
					wh_log("Add Client Query - ".$add_client." | Last Insert_id - ".$last_insert_id);
					if($add_client_rs)
					{
						$response['status']=true;
						$response['message']="Successfully Registered.";
						$response['clientId']= $last_insert_id;
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


