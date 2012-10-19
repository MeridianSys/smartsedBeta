<?php
class ImagesComponent extends Object {
    
        /* The Upload Directory @access public * @var string*/
        var $upload_dir = "";

        /* The Upload Directory Path @access public  @var string*/
        var $upload_path = "";

       var $large_image_prefix = "resize_"; 			// The prefix name to large image
       var $thumb_image_prefix = "thumbnail_";			// The prefix name to the thumb image
        /* New name of the large image & New name of the thumbnail image @access public @var string */
        var $large_image_name = "resized_";
        var $thumb_image_name = "thumbnail_";
      

        /**
        * The Max File Size. Approx 1MB @access public @var string */
        var $max_file = "3";

        /* The Max width allowed for the large image @access public @var string */
        var $max_width = "500";

        /* The Width of thumbnail image @access public @var string*/
        var $thumb_width = "200";

        /* The Height of thumbnail image @access public @var string */
        var $thumb_height = "200";

        //Image functions
        //You do not need to alter these functions
        function resizeImage($image,$width,$height,$scale)
        {
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
            $source = imagecreatefromjpeg($image);
            imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
            imagejpeg($newImage,$image,90);
            chmod($image, 0777);
            return $image;
        }

        //You do not need to alter these functions
        function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
            $newImageWidth = ceil($width * $scale);
            $newImageHeight = ceil($height * $scale);
            $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
            $source = imagecreatefromjpeg($image);
            imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
            imagejpeg($newImage,$thumb_image_name,90);
            chmod($thumb_image_name, 0777);
            return $thumb_image_name;
        }

        //You do not need to alter these functions
        function getHeight($image) {
            $sizes = getimagesize($image);
            $height = $sizes[1];
            return $height;
        }

        //You do not need to alter these functions
        function getWidth($image)
        {
            $sizes = getimagesize($image);
            $width = $sizes[0];
            return $width;
        }
}
?>