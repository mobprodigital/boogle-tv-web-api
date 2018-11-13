<?php
include "../../includes/config.php";
$response = array();

$query1 ="select * from videos order by id desc limit 1";
$query_rs1 = mysqli_query($link,$query1);
if($query_rs1)
{
	if(mysqli_num_rows($query_rs1) > 0)
	{
		//echo mysqli_num_rows($query_rs1);
		while($row = mysqli_fetch_assoc($query_rs1))
		{
			$array[] = array("id"=>$row['id'],"Title"=>$row['title'],"Description"=>str_replace("\\'", "'", $row['description'] ),"Language"=>$row['language']);
			
		}
		if(!empty($array))
		{
			$response['status']=true;
			$response['message']="Success";
			$response['data']=$array;
		}
		else
		{
			$array = array();
			$response['status']=false;
			$response['message']="Failure";
			$response['data']=$array;
		}
	}
	else
	{
		$array = array();
		$response['status']=false;
		$response['message']="No Data Found";
		$response['data']=$array;
	}
}
wh_log("Response : ".str_replace("\n"," ", print_r($response, true)));

$rplc_arr = array("u2013", "u00a0");
$find= array("’", '“', '”',"u00a0","u2013","u2019","u2026","…","u201c","u2019d","u201d",",","–","u2013","u2014","u2019s","u2018","‘","u2009","u00e9","u20b");
$replace = array("'",'"','"',"","","","...","...","","","",",","-","-","-","'","'","'","","","");

echo str_replace($find, $replace, json_encode($response,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

?>