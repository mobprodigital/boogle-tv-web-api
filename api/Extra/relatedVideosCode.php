function getRelatedVideosByCategoryID($cat_id,$video_id,$link)
{
	// Get 5 videos from category id.
	$getvideoList = "select * from videos where find_in_set($cat_id,cat_id) limit 0,5";
	wh_log("getvideoList Query Executed : ".$getvideoList);
	$getvideoList_rs = mysqli_query($link, $getvideoList);
	$count = mysqli_num_rows($getvideoList_rs);
	if($count > 0)
	{ 
		$remaining_count = 10 - $count;
		wh_log("Rows Found for category -- ".mysqli_num_rows($getvideoList_rs));
		while($row  = mysqli_fetch_assoc($getvideoList_rs))
		{ 
			$ids[]= $row['id'];
			$video_array[] = videoArray($row);
		}
		$ids_used = implode(',',$ids);
	}
	// if videos < 5 then find videos from video_tags of particupar video id
	if(($count == 0) || (!empty($remaining_count)))
	{
		$getvideoList1 = "select video_tags from videos where id = $video_id";
		wh_log("getvideoList Query Executed : ".$getvideoList1);
		$getvideoList_rs1 = mysqli_query($link, $getvideoList1);
		if($row1  = mysqli_fetch_assoc($getvideoList_rs1))
		{
			$tags = explode(',',$row1['video_tags']);
			$search_tag = $tags['0'];
			$search_tag1 = $tags['1'];
			
			// Search videos by first tag
			$getvideoList2 = "select * from videos where video_tags like '%$search_tag%' and id not in ($ids_used) limit 0,$remaining_count";
			wh_log("getvideoList Query Executed : ".$getvideoList2);
			$getvideoList_rs2 = mysqli_query($link, $getvideoList2);
			$count1 = mysqli_num_rows($getvideoList_rs2);
			$remaining_count1 = $remaining_count - $count1; 
			if($count1 > 0)
			{
				wh_log("Rows Found for category -- ".mysqli_num_rows($getvideoList_rs2));
				while($row2  = mysqli_fetch_assoc($getvideoList_rs2))
				{ 
					$ids1[]= $row2['id'];
					$video_array1[] = videoArray($row2);
				}
				$ids_used1= implode(',',$ids1);
				$ids_used_str = $ids_used.','.implode(',',$ids1);
				$video_array = array_merge($video_array,$video_array1);
			}
			if(($count1 == 0) || (!empty($remaining_count1)))
			{
				// Search videos by second tag
				$getvideoList3 = "select * from videos where video_tags like '%$search_tag1%' and id not in ($ids_used_str) limit 0,$remaining_count1";
				wh_log("getvideoList Query Executed : ".$getvideoList3);
				$getvideoList_rs3 = mysqli_query($link, $getvideoList3);
				$count3 = mysqli_num_rows($getvideoList_rs3); 
				if($count3 > 0)
				{
					$remaining_count2 = $remaining_count1 - $count3;
					wh_log("Rows Found for category -- ".mysqli_num_rows($getvideoList_rs3));
					while($row3  = mysqli_fetch_assoc($getvideoList_rs3))
					{ 
						$ids3[]= $row3['id'];
						$video_array3[] = videoArray($row3);
					}
					$ids_used3= implode(',',$ids3);
					$ids_used_str_final = $ids_used_str.','.$ids_used3;
					$video_array = array_merge($video_array,$video_array3);
				}	
			}	
		}
	}
	wh_log("Videos By Category Id : ".str_replace("\n"," ", print_r($video_array, true)));
	return $video_array;	
}

$query = "(SELECT content, title, 'msg' as type FROM messages WHERE content LIKE '%" . 
           $keyword . "%' OR title LIKE '%" . $keyword ."%') 
           UNION
           (SELECT content, title, 'topic' as type FROM topics WHERE content LIKE '%" . 
           $keyword . "%' OR title LIKE '%" . $keyword ."%') 
           UNION
           (SELECT content, title, 'comment' as type FROM comments WHERE content LIKE '%" . 
           $keyword . "%' OR title LIKE '%" . $keyword ."%')";

mysql_query($query);