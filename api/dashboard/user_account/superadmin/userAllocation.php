<?php
include "../../../../includes/config.php";
include "../../../../includes/functions.php";
$response = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{
        $login_uid = $check['loginUserId'];
		$login_clientid = $check['loginClientId'];
        wh_log("ClientId - ".$login_clientid." | Login UId -".$login_uid);
        
        $req_json = json_decode(file_get_contents("php://input"), true);
        wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
        
        $allocatedFrom = mysqli_real_escape_string($link,trim(isset($req_json['allocatedFrom']))) ? mysqli_real_escape_string($link,trim($req_json['allocatedFrom'])) :'';
        $allocatedTo = mysqli_real_escape_string($link,trim(isset($req_json['allocatedTo']))) ? mysqli_real_escape_string($link,trim($req_json['allocatedTo'])) :'';
        //$allocatedUserID = mysqli_real_escape_string($link,trim(isset($req_json['allocatedUserID']))) ? mysqli_real_escape_string($link,trim($req_json['allocatedUserID'])) :'';

        if(! empty($req_json['allocatedUserID'])) { $userid_status = check_array_values($req_json['allocatedUserID']); }

        if(empty($allocatedFrom) || $allocatedFrom == null)
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allocated From Id is mandatory.";
            $response['data'] = $Client_data;
        }
        elseif(!is_numeric($allocatedFrom))
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allowed only numbers in Allocated From Id parameter";
            $response['data']=$Client_data;
        }
        elseif(empty($allocatedTo) || $allocatedTo == null)
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allocated To Id is mandatory.";
            $response['data'] = $Client_data;
        }
        elseif(!is_numeric($allocatedTo))
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allowed only numbers in allocated To Id parameter";
            $response['data']=$Client_data;
        }
        elseif(empty($req_json['allocatedUserID']))
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allocated UserID are mandatory.";
            $response['data']= $Client_data;
        }
        elseif(!$userid_status)
        {
            $Client_data = array();
            $response['status']=false;
            $response['message']="Allocated User ID parameter should be numeric.";
            $response['data']= $Client_data;
        }
        else
        {
            // check Allocated To Super Admin Id is exist or not in database
            $check_query = "select * from users WHERE uid = $allocatedTo and client_id = $login_clientid and reports_to =$login_uid and status = 1";
			$check_query_rs = mysqli_query($link,$check_query);
			wh_log("Check Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
            if(mysqli_num_rows($check_query_rs) > 0)
            {
                if($row  = mysqli_fetch_assoc($check_query_rs))
                {  
                    $portal_ids = $row['portal_ids'];
                }
                foreach($req_json['allocatedUserID'] as $value)
                {
                    $query = "update users set `reports_to`= $allocatedTo,portal_ids = '$portal_ids' where uid = $value and `reports_to`= $allocatedFrom and status =1 and client_id = $login_clientid";
                    wh_log("User allotment update Query - ".$query);
                    $query_rs = mysqli_query($link,$query);
                    $status = true;
                }
                if($status)
                {
                    $Client_data = array();
                    $response['status']=true;
                    $response['message']="User Successfully Allotted.";
                    $response['data']= $Client_data; 
                }
                else
                {
                    $Client_data = array();
                    $response['status']=false;
                    $response['message']=mysqli_error($link);
                    $response['data'] = $Client_data;
                }
                
            }
            else
            {
                $Client_data = array();
                $response['status']=false;
                $response['message']="Invalid Id";
                $response['data']= $Client_data;
            }
            // Ends
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