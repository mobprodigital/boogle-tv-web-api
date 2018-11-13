<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
	
		$id = mysqli_real_escape_string($link,trim(isset($req_json['clientId']))) ? mysqli_real_escape_string($link,trim($req_json['clientId'])) :'';
		$clientName = mysqli_real_escape_string($link,trim(isset($req_json['clientName']))) ? mysqli_real_escape_string($link,trim($req_json['clientName'])) :'';
		$email = mysqli_real_escape_string($link,trim(isset($req_json['email']))) ? mysqli_real_escape_string($link,trim($req_json['email'])) :'';
		$phone = mysqli_real_escape_string($link,trim(isset($req_json['phone']))) ? mysqli_real_escape_string($link,trim($req_json['phone'])) :'';
		$domain = mysqli_real_escape_string($link,trim(isset($req_json['domain']))) ? mysqli_real_escape_string($link,trim($req_json['domain'])) :'';
		$billingCycle = mysqli_real_escape_string($link,trim(isset($req_json['billingCycle']))) ? mysqli_real_escape_string($link,trim($req_json['billingCycle'])) :'';
		$agreementTenure = mysqli_real_escape_string($link,trim(isset($req_json['agreementTenure']))) ? mysqli_real_escape_string($link,trim($req_json['agreementTenure'])) :'0';
		$address = mysqli_real_escape_string($link,trim(isset($req_json['address']))) ? mysqli_real_escape_string($link,trim($req_json['address'])) :'';
		$skypeId = mysqli_real_escape_string($link,trim(isset($req_json['skypeId']))) ? mysqli_real_escape_string($link,trim($req_json['skypeId'])) :'';
		
		if(! empty($req_json['assignedPortals'])) { $cat_array_status = check_array_values($req_json['assignedPortals']); }
		
		wh_log(" clientid - ".$id." | clientName - ".$clientName." | Email - ".$email." | Phone - ".$phone." | Domain - ".$domain." BillingCycle -".$billingCycle." | AgreementTenure - ".$agreementTenure." | Address - ".$address." | SkypeId - ".$skypeId);
		
		if(empty($id) || $id == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Id Parameter Missing.";
			$response['data']= $Client_data;
		}
		elseif(empty($address) || $address == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="address Parameter Missing.";
			$response['data']= $Client_data;
		}
		elseif(empty($clientName) || $clientName == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="clientName Parameter Missing.";
			$response['data']= $Client_data;
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $clientName))
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Only letters and white space allowed in name parameter.";
			$response['data']= $Client_data;
		}
		elseif(empty($phone) || $phone == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="phone Parameter Missing.";
			$response['data']= $Client_data;
		}
		elseif(!is_numeric($phone))
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in mobile parameter";
			$response['data']= $Client_data;
		}
		elseif(empty($email) || $email == null)
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="email Parameter Missing.";
			$response['data']= $Client_data;
		}
		elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$Client_data = array();
			$response['status']=false;
			$response['message']="Invalid email format";
			$response['data']= $Client_data;
		}
		elseif(empty($req_json['assignedPortals']))
		{
			$response['status']=false;
			$response['message']="assignedPortals are mandatory.";
			$response['data']="";
		}
		elseif(!$cat_array_status)
		{
			$response['status']=false;
			$response['message']="assignedPortals parameter should be numeric.";
			$response['data']="";
		}
		else
		{
			// Make Poratlids array in comma seperate string
			$pids = implode(",",$req_json['assignedPortals']);
			// Ends
			
			// Check client/partner is exist in client table
			$check_user = "SELECT * FROM clients WHERE client_id = $id and status =1";
			wh_log("Query - ".$check_user);
			$check_user_rs = mysqli_query($link,$check_user);
			if($check_user_rs)
			{
				if(mysqli_num_rows($check_user_rs) > 0)
				{
					//update case
					$updatedata = "update clients set name='$clientName',email='$email',mobile=$phone,address='$address',skype_id='$skypeId',billing_cycle='$billingCycle',url='$domain',agreement_tenure='$agreementTenure',portal_ids='$pids' where client_id='$id'";
					wh_log("Query executed = ".$updatedata);
					$updatedata_rs = mysqli_query($link,$updatedata);
					if($updatedata_rs)
					{
						// Get Client Details
						$Client_data = getSingleClientData($id,$link);
						//Ends
						$response['status']=true;
						$response['message']="Success";
						$response['data']= $Client_data;
					}
					else
					{
						$Client_data = array();
						$response['status']=false;
						$response['message']=mysqli_error($link);
						$response['data']= $Client_data;
					}
				}
				else
				{
					$Client_data = array();
					$response['status']=false;
					$response['message']="Invalid Client Id.".$clientid;
					$response['data']= $Client_data;
				}
			}
			else
			{
				$Client_data = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data']= $Client_data;
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


