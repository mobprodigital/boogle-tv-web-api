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
		$req_json = json_decode(file_get_contents("php://input"), true);
		wh_log("Json In Request : ".str_replace("\n"," ", print_r($req_json, true)));
		
		$categoryId = mysqli_real_escape_string($link,trim(isset($req_json['categoryId']))) ? mysqli_real_escape_string($link,trim($req_json['categoryId'])) :'0';
		
		if(empty($categoryId) || $categoryId == null)
		{
			$cat_array = array();
			$response['status']=false;
			$response['message']="categoryId is mandatory.";
			$response['data'] = $cat_array;
		}
		elseif(!is_numeric($categoryId))
		{
			$cat_array = array();
			$response['status']=false;
			$response['message']="Allowed only numbers in category Id parameter";
			$response['data']=$cat_array;
		}
		else
		{
			// Get category Array By category ID
			$query = "select * from category where id = $categoryId and status =1 and content_type_id = $content_type_id";
			wh_log("Query - ".$query);
			$query_rs = mysqli_query($link,$query);
			if($query_rs)
			{
				if(mysqli_num_rows($query_rs) > 0)
				{
					while($row  = mysqli_fetch_assoc($query_rs))
					{  
						$cat_array = singleCategoryArray($row,$link);
					}
					if(!empty($cat_array))
					{
					$response['status']=true;
					$response['message']="Success";
					$response['data'] = $cat_array;	
					} else {
					$cat_array = array();
					$response['status']=false;
					$response['message']="No Data Found For This category Id ".$categoryId;
					$response['data'] = $cat_array;	
					}
				}
				else
				{
					$cat_array = array();
					$response['status']=false;
					$response['message']="No Data Found For This category Id ".$categoryId;
					$response['data'] = $cat_array;
				}
			}
			else
			{
				$cat_array = array();
				$response['status']=false;
				$response['message']=mysqli_error($link);
				$response['data'] = $cat_array;
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


