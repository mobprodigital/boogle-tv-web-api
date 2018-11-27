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
        $clientId = $check['loginClientId'];
        wh_log("Login UId -".$login_uid." | Client Id -".$login_clientid);

		$req_json = json_decode(file_get_contents("php://input"), true);
        wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
        
        $action = mysqli_real_escape_string($link,trim(isset($req_json['action']))) ? mysqli_real_escape_string($link,trim($req_json['action'])) :'';
        $id = mysqli_real_escape_string($link,trim(isset($req_json['id']))) ? mysqli_real_escape_string($link,trim($req_json['id'])) :'';

        if(empty($action) || $action == null)
        {
            $user_array = array();
            $response['status']=false;
            $response['message']="Action is mandatory.";
            $response['data'] = $user_array;
        }
        elseif(empty($id) || $id == null)
        {
            $user_array = array();
            $response['status']=false;
            $response['message']="Id is mandatory.";
            $response['data'] = $user_array;
        }
        elseif(!is_numeric($id))
        {
            $user_array = array();
            $response['status']=false;
            $response['message']="Allowed only numbers in Id parameter";
            $response['data']=$user_array;
        }
        else
        {
            if($action == 'delete by id')
            {
                $roleId = mysqli_real_escape_string($link,trim(isset($req_json['roleId']))) ? mysqli_real_escape_string($link,trim($req_json['roleId'])) :'';
                
                if(empty($roleId) || $roleId == null)
                {
                    $user_array = array();
                    $response['status']=false;
                    $response['message']="roleId is mandatory.";
                    $response['data'] = $user_array;
                }
                elseif(!is_numeric($roleId))
                {
                    $user_array = array();
                    $response['status']=false;
                    $response['message']="Allowed only numbers in roleId parameter";
                    $response['data']=$user_array;
                }
                else
                {
                    // Check id is present or not in users table
                    $check_query = "select * from users WHERE uid = $id and role = $roleId and status = 1 and reports_to = $login_uid and client_id =$clientId";
                    $check_query_rs = mysqli_query($link,$check_query);
                    wh_log(" Action -".$action." | Check Id is present or not Query ".$check_query." count rows - ".mysqli_num_rows($check_query_rs));
                    if(mysqli_num_rows($check_query_rs) > 0)
                    {
                        $check_query1 = "select * from users WHERE status = 1 and reports_to = $id and client_id =$clientId";
                        $check_query_rs1 = mysqli_query($link,$check_query1);
                        wh_log(" Action -".$action." | Check Users associated with admin Query ".$check_query1." count rows - ".mysqli_num_rows($check_query_rs1));
                        if(mysqli_num_rows($check_query_rs1) > 0)
                        {
                            $user_array = array();
                            $response['status']=true;
                            $response['message']="Some users are associated with this id. Kindly move them and then delete this user";
                            $response['data']=$user_array;
                        }
                        else
                        {
                             // Insert Into Logs
                             if($row  = mysqli_fetch_assoc($check_query_rs))
                             {
                                 $name = $row['first_name'].' '.$row['last_name'];
                                 $email = $row['email'];
                                 $mobile = $row['mobile'];
                                 $role = $row['role'];
                                 $create_time = $row['insertion_time'];
                                 echo $insert_logs = "Insert into logs (name,email,mobile,role,'type',created_on) values ('$name','$email',$mobile,$role,'admin','$create_time')";
                                 $insert_logs_rs = mysqli_query($link,$insert_logs);
                                 //print_r($row);
                             }
                             // Ends

                            //echo "Delete Particular Admin by clientid";
                            $query = "delete from users where uid = $id and reports_to = $login_uid and client_id = $clientId and status = 1";
                            //$query = "update users set status =0 WHERE uid = $id and reports_to = $login_uid and client_id = $clientId and status = 1 ";
                            wh_log(" Action -".$action." | Delete Particular Admin by clientid Query - ".$query);
                            $query_rs = mysqli_query($link,$query);
                            if($query_rs)
                            {
                                $user_array = array();
                                $response['status']=true;
                                $response['message']="Admin Successfully Deleted.";
                                $response['data']= $user_array; 
                            }
                            else
                            {
                                $user_array = array();
                                $response['status']=false;
                                $response['message']=mysqli_error($link);
                                $response['data'] = $user_array;
                            }
                        }
                    }
                    else
                    {
                        $user_array = array();
                        $response['status']=false;
                        $response['message']="Invalid id";
                        $response['data']=$user_array;
                    }
                    // Ends

                }
            }
            elseif($action == 'delete all')
            {
                //Insert into logs
                $check_query = "select * from users WHERE uid = $id and reports_to = $login_uid and client_id =$clientId and status = 1";
                $check_query_rs = mysqli_query($link,$check_query);
                wh_log("Action - ".$action." | Check Query - ".$check_query." | count rows - ".mysqli_num_rows($check_query_rs));
                if(mysqli_num_rows($check_query_rs) > 0)
                {
                    if($row  = mysqli_fetch_assoc($check_query_rs))
                    {
                        $name = $row['first_name'].' '.$row['last_name'];
                        $email = $row['email'];
                        $mobile = $row['mobile'];
                        $role = $row['role'];
                        $create_time = $row['insertion_time'];
                        $insert_logs = "Insert into logs (name,email,mobile,role,'type',created_on) values ('$name','$email',$mobile,$role,'admin','$create_time')";
                        $insert_logs_rs = mysqli_query($link,$insert_logs);
                        //print_r($row);
                    }
                }
                //Ends

                //echo "Delete admin and its hierarchy by clientid";
                $query = "delete from users WHERE uid = $id and reports_to = $login_uid and client_id = $clientId and status = 1 ";
                wh_log(" Action -".$action." | Delete admin and its hierarchy by clientid Query - ".$query);
                $query_rs = mysqli_query($link,$query);
                if($query_rs)
                {
                    $query1 = "delete from users WHERE reports_to = $id and client_id =$clientId and status = 1";
                    wh_log("update Query1 - ".$query1);
                    $query_rs1 = mysqli_query($link,$query1);
                    if($query_rs1)
                    {
                        $user_array = array();
                        $response['status']=true;
                        $response['message']="Super admin and its associated user are Successfully Deleted.";
                        $response['data']= $user_array; 
                    }
                    else
                    {
                        $user_array = array();
                        $response['status']=false;
                        $response['message']=mysqli_error($link);
                        $response['data'] = $user_array;
                    }
                }
                else
                {
                    $user_array = array();
                    $response['status']=false;
                    $response['message']=mysqli_error($link);
                    $response['data'] = $user_array;
                }

            }
            else
            {
                $user_array = array();
                $response['status']=false;
                $response['message']="Invalid Action";
                $response['data'] = $user_array;
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