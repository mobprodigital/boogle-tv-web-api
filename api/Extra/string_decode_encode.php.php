<?php
echo $str = 'Come, play with us: “Chennais” first ‘Animecon’'; echo '<br>'; // original string 
$find= array("’","‘",'“','”');
$replace = array("'","'",'"','"');
echo $str1 = str_replace($find, $replace,$str); echo '<br>';
echo "db".$str2 = addslashes($str1); echo '<br>'; // save in db with addslashes
echo "res".$str3 = stripslashes($str2); echo '<br>'; // add this in video array
echo json_encode($str,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>