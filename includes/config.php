<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Credentials: true");

ob_start();
error_reporting(0); //E_ALL ^ E_NOTICE ^ E_DEPRECATED
ini_set('display_errors', 0);

static $link;
$link = mysqli_connect("localhost", "root", "") or print(mysqli_error()."error\n");
mysqli_select_db($link,"boogletv") or die(mysqli_error()."\n"); 

/* $link = mysqli_connect("50.62.209.160", "dev_boogletv", "Riccha@123") or print(mysqli_error()."error\n");
mysqli_select_db($link,"dev_boogletv") or die(mysqli_error()."\n"); */

$imageBaseDirURL = 'C:/xampp/htdocs/boogletv/images/';
$imageBaseURL = "http://192.168.0.8/boogletv/images";

$videoBaseDirURL = 'C:/xampp/htdocs/boogletv/videos/';
$videoBaseURL = "http://192.168.0.8/boogletv/videos";

$localhost_base_url = "http://localhost/boogletv/";
$base_url = "http://192.168.0.8/boogletv/";

$panel_url = 'http://192.168.0.8/boogletv/';

/* Set authentication_key in Cookie */
function random_num($size) {
	$alpha_key = '';
	$keys = range('A', 'Z');

	for ($i = 0; $i < 2; $i++) {
		$alpha_key .= $keys[array_rand($keys)];
	}

	$length = $size - 2;

	$key = '';
	$keys = range(0, 9);

	for ($i = 0; $i < $length; $i++) {
		$key .= $keys[array_rand($keys)];
	}

	return $alpha_key . $key;
}
function setcookies($clientid,$role,$uid)
{
//$cookie_name = "authentication_key";
$cookie_value = random_num(10);

// For Other Domain Access
setcookie('loginClientId',$clientid, time() + (86400 * 7), "/","http://localhost",false); // 86400 = 1 day
setcookie('loginRoleId',$role, time() + (86400 * 7), "/","http://localhost",false); // 86400 = 1 day
setcookie('loginUserId',$uid, time() + (86400 * 7), "/","http://localhost",false); // 86400 = 1 day
setcookie('authentication_key',$cookie_value, time() + (86400 * 7), "/","http://localhost",false); // 86400 = 1 day

// For Local System Access
setcookie('loginClientId',$clientid, time() + (86400 * 7), "/"); // 86400 = 1 day
setcookie('loginRoleId',$role, time() + (86400 * 7), "/"); // 86400 = 1 day
setcookie('loginUserId',$uid, time() + (86400 * 7), "/"); // 86400 = 1 day
setcookie('authentication_key',$cookie_value, time() + (86400 * 7), "/"); // 86400 = 1 day

}
function getClientData(){
	return getClientCookie();
	//return getJsonData();
}
function getJsonData(){
	$req_json = json_decode(file_get_contents("php://input"), true);
	
	$array['loginClientId'] = $req_json['loginClientId'];
	$array['loginRoleId'] = $req_json['loginRoleId'];
	$array['authentication_key'] = $req_json['authentication_key'];
	$array['loginUserId'] = $req_json['loginUserId'];
	
	if((empty($array['loginClientId'])) || (empty($array['loginRoleId'])) || (empty($array['authentication_key'])) ||  (empty($array['loginUserId']))) { return false;} else { return $array ; }
}
function getClientCookie()
{
$array['loginClientId'] = $_COOKIE['loginClientId'];
$array['loginRoleId'] = $_COOKIE['loginRoleId'];
$array['authentication_key'] = $_COOKIE['authentication_key'];
$array['loginUserId'] = $_COOKIE['loginUserId']; 

if($array['loginRoleId'] == 1) { if((empty($array['loginRoleId'])) || (empty($array['authentication_key'])) ||  (empty($array['loginUserId']))) { return false;} else { return $array ; } }
else { if((empty($array['loginClientId'])) || (empty($array['loginRoleId'])) || (empty($array['authentication_key'])) ||  (empty($array['loginUserId']))) { return false;} else { return $array ; }}


}
function deleteCookie()
{
	//setcookie($_COOKIE['authentication_key'],'', time()-300, "/"); 
	//setcookie($_COOKIE['loginClientId'],'', time()-300, "/"); 
	//setcookie($_COOKIE['loginRoleId'],'', time()-300, "/"); 
	//setcookie($_COOKIE['loginUserId'],'', time()-300, "/"); 
	
	if (isset($_COOKIE['authentication_key'])) 
	{
    unset($_COOKIE['authentication_key']);
    unset($_COOKIE['loginClientId']);
	unset($_COOKIE['loginRoleId']);
	unset($_COOKIE['loginUserId']);
	setcookie('authentication_key', null, -1, '/');
	setcookie('loginClientId', null, -1, '/');
	setcookie('loginRoleId', null, -1, '/');
	setcookie('loginUserId', null, -1, '/');
    return true;
	} 
	else 
	{
    return false;
	}
}
/* function getKeyCookie()
{
	if (isset($_COOKIE['authentication_key']))
	{ return true; }
	else { return false ; }
} */
/* Ends */



function wh_log($log_msg){
		$log_filename = "C:/xampp/htdocs/boogletv/logs";
        //$log_filename = "logs";
        if (!file_exists($log_filename))
        {
                mkdir($log_filename, 0777, true);
        }
        $log_file_data = $log_filename.'/boogletv' . date('Y-m-d') . '.log';
        $uri_parts = explode('?', $_SERVER[REQUEST_URI], 2);
        file_put_contents($log_file_data, date("Y-m-d H:i:s.U")." - ".get_client_ip()." - ".$uri_parts[0]." - ".$log_msg . "\n", FILE_APPEND);
}
function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
        else
        $ipaddress = 'UNKNOWN';
        return $ipaddress;
}

$domain_array = array("localhost","192.168.0.7");
function validate_domain($domain)
{
	if (in_array($domain, $domain_array))
	{ return 1; }
	else { return 0; }
	/* if(($domain == 'localhost') || ($domain == '192.168.0.7'))
	{ return 1; }
	else { return 0; } */
}

$roles = array("owner","superadmin","admin","user");

function send_email($email,$msg,$subject)
{
	$adminEmail = 'riccha.rastogi@gmail.com';
	$to = $email;
	$headers = "From: info@boogletv.in". "\r\n";
	$headers.= "bcc: ".$adminEmail."". "\r\n";
	$headers .= "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" ;
	wh_log("adminEmail -".$adminEmail." | to - ".$to." | Subject - ".$subject."| Message - ".$msg);
	if(mail($to,$subject,$msg,$headers)){
		return 'success';
	}
	else{
		return 'fail';
	}
}



?>
