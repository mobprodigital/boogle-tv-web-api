<?php
resize($thisImage, 70, $thisPath.$thisName."-thumb1.jpg");
resize($thisImage, 150, $thisPath.$thisName."-thumb2.jpg");
resize($thisImage, 750, $thisPath.$thisName."-thumb3.jpg");

function resize($imageName, $thumbImageWidth, $thumbImageTarget)
{
    if(file_exists($imageName)) {

        $imageInfo = finfo_open(FILEINFO_MIME_TYPE);
        $imageType = finfo_file($imageInfo, $imageName);
        finfo_close($imageInfo);


        if($imageType == 'image/pjeg' || $imageType == 'image/jpeg' || $imageType == 'image/jpg') {
            $imgSource = imagecreatefromjpeg($imageName);
        } elseif ($imageType == 'image/png') {
            $imgSource = imagecreatefrompng($imageName);
        } elseif ($imageType == 'image/gif') {
            $imgSource = imagecreatefromgif($imageName);
        } else {
            $imgSource = false;
            return false;
        }

        if($imgSource) {
            list($width,$height)=getimagesize($imageName);

            $thumbImageHeight = ($height/$width)*$thumbImageWidth;
            $tempThumbImage = imagecreatetruecolor($thumbImageWidth,$thumbImageHeight);

            if(!imagecopyresampled($tempThumbImage,$imgSource,0,0,0,0,$thumbImageWidth,$thumbImageHeight,$width,$height)) return false;


            if(!imagejpeg($tempThumbImage,$thumbImageTarget,100)) return false;

            if(!imagedestroy($imgSource)) return false;

            if(!imagedestroy($tempThumbImage)) return false;

            if(!unlink($imageName)) return false;

            return true;
        }
    } else {
        return false;
    }
}
?>