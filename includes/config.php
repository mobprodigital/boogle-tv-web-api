<?php
/* mysql_connect("localhost", "root", "")or die("cannot connect");
mysql_select_db("boogletv")or die("cannot select DB"); */

header("Access-Control-Allow-Origin: *");
ob_start();
error_reporting(0); //E_ALL ^ E_NOTICE ^ E_DEPRECATED
ini_set('display_errors', 0);
$mc = mysql_connect("localhost", "root", "") or print(mysql_error()."error\n");
mysql_select_db("boogletv") or die(mysql_error()."\n");

function wh_log($log_msg){

        $log_filename = "C:/xampp/htdocs/boogletv/logs";
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
function comma_separated_to_array($string, $separator = ',')
{
  $vals = explode($separator, $string);
  foreach($vals as $key => $val) {
    $vals[$key] = trim($val);
  }
  return array_diff($vals, array(""));
}

/* Check array contains integer values or not */
function check_array_values($array)
{
	foreach ($array as $a => $b) {
    if (!is_int($b)) {
		return false;
    }
}
return true;
}
/* Ends */

/* Domain Validations */
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
/* Ends */


?>
