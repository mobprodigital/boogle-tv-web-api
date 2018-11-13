<?php
include "../../includes/config.php";
$response = array();
$edit = @$_POST["Save"];
if(!empty($edit))
{
	if(empty($_POST['title']))
	{
	   print("Invalid details");
	}
	else 
	{
		//$title = str_ireplace("'", "\'", $_POST["title"]); 
		//$description = str_ireplace("'", "\'", $_POST["description"]);
		
		$title = $_POST["title"]; 
		$description = $_POST["description"];
		
		
		$find= array("’","‘",'“','”');
		$replace = array("'","'",'"','"');
		
		//Title
		$title1 = str_replace($find, $replace,$title);
		$final_title = addslashes($title1);
		//Ends
		
		//Description
		$description1 = str_replace($find, $replace,$description);
		$final_description = addslashes($description1);
		//Ends
		
		$language = 'english';
		$query ="insert into content (title,description,language) values ('$final_title','$final_description','$language')";
		$query_rs = mysqli_query($link,$query);
		if($query_rs)
		{
			echo "Success";
		}
		else
		{
			echo "Not Success";
		} 
	}
}
 
?>
	 <html>
<head>
</head>

<body>

<p style="border-bottom: 1px solid grey; padding-bottom: 20px;">Upload Text Content</p>
<div id="errorMessageReg"
 style="color: red; margin: 5px; display: none;">Mandatory Field Can not
be blank and File should be in valid image formate</div>


<form name="edit" id="edit" method="POST" enctype="multipart/form-data">
<table>
	<tr>
		<td>Title :</td>
		<td><input type="text" size="50" name="title" id="title"></input></td>
	</tr>
	<tr>
		<td>Description :</td>
		<td><input type="text" size="50" name="description" id="description" ></input></td>
	</tr>
</table>




<div><input type="submit" value="Save Changes" name="Save" id="edit"></div>


</div>
</form>
</body>
</html>