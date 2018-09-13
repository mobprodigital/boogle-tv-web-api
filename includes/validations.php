<?php
if (!is_numeric($video_id))
{
$response['status']=false;
$response['message']="Allowed only numbers in VideoID Parameter";
echo json_encode($response);
die();
} 

?>