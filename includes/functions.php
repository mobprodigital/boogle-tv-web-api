<?php
include_once ('portel_functions.php');
include_once ('dashboard_functions.php');

/* Convert comma seperated strings to array*/
function comma_separated_to_array($string, $separator = ',')
{
  $vals = explode($separator, $string);
  foreach($vals as $key => $val) {
    $vals[$key] = trim($val);
  }
  return array_diff($vals, array(""));
}

/* Convert array to strings */
function array_to_comma_separated($array)
{
  $data = implode(",",$array);
  return $data;
}

/* Check array contains integer values or not */
function check_array_values($arr)
{
	foreach ($arr as $a => $b) {
    if (!is_numeric($b)) {
		return false;
    }
}
return true;
}

/* Convert Seconds To Minutes */
function secondsToMinutes($time,$view_time_in_hour_minutes)
{
	$secs = strtotime($view_time_in_hour_minutes)-strtotime("00:00:00");
	$res = date("H:i:s",strtotime($time)+$secs);
	return $res;
}

/* Remove Duplicate Values From Array List*/
function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 
function encodeId($id)
{
	$str1 = decbin($id);
	//$str1 = base64_encode($id);
	return $str1;
}
function decodeId($id)
{
	$str1 = bindec($id);
	//$str1 = base64_decode($id);
	return $str1;
}
function getContentTypeData($contentType,$videoBaseURL,$imageBaseURL)
{
    $array = array();
    if($contentType == 1) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'audio'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 2) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'video'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 3) 
    { 
        $array['dataTable'] = 'content_metadata'; 
        $array['type'] = 'image'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    elseif($contentType == 4)
    { 
        $array['dataTable'] = 'news_metadata'; 
        $array['type'] = 'text'; 
        $array['vpath'] = $videoBaseURL; 
        $array['ipath'] = $imageBaseURL;
    }
    return $array;
}
?>