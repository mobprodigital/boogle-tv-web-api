<?php
include "../../../includes/config.php";
include "../../../includes/functions.php";
$response = array();
$content_type_id = 4;
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$check = getClientData();
	if ($check) 
	{ 
		//$client_id = $check['loginClientId'];
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
		$categoryName = mysqli_real_escape_string($link,trim(isset($req_json['categoryName']))) ? mysqli_real_escape_string($link,trim($req_json['categoryName'])) :'';
		//$clientId = mysqli_real_escape_string($link,trim(isset($client_id))) ? mysqli_real_escape_string($link,trim($client_id)) :'0';
		
		
		wh_log("categoryName - ".$categoryName." | Parent Category ID - ".$content_type_id." | Cookie Client id -".$clientId);
		
		if((empty($categoryName) || $categoryName == null))
		{
			$cat_data = array();
			$response['status']=false;
			$response['message']="categoryName Parameter is Missing.";
			$response['data']= $cat_data;
		}
		elseif(!preg_match("/^[a-zA-Z ]+$/", $categoryName))
		{
			$cat_data = array();
			$response['status']=false;
			$response['message']="Only letters and white space allowed in categoryName parameter.";
			$response['data']= $cat_data;
		}
		/* elseif(empty($clientId) || $clientId == null)
		{
			$portal_data = array();
			$response['status']=false;
			$response['message']="clientId is mandatory.";
			$response['data'] = $portal_data;
		}
		elseif(!is_numeric($clientId))
		{
			$response['status']=false;
			$response['message']="Allowed only in clientId parameter";
		}  */
		else
		{
			//Add New Category
			$add_query = "INSERT INTO category (cat_name,content_type_id,status,insertion_time) VALUES ('$categoryName',$content_type_id,'1',NOW())";
			$add_query_rs = mysqli_query($link,$add_query);
			$last_insert_id = mysqli_insert_id($link);
			wh_log("Add Category Query - ".$add_query." | Last Insert_id - ".$last_insert_id);
			if($add_query_rs)
			{
				// Get Category Array
				$cat_data = getCategoryData($last_insert_id,$link);
				//Ends
				
				$response['status']=true;
				$response['message']="Successfully Added.";
				$response['data']= $cat_data;
			}
			else
			{
				$cat_data = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data']= $cat_data;
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


