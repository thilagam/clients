<?php
ini_set('max_execution_time', 0);
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(-1);

$path=getcwd();
$directory_path = $path."/CLIENTS/BASH/";//"2015_02_05_CAROLL_P30_BD_JPEG/";
//$array=array("Truffaut Avril 2016","Suite Avril Truffaut 19042016","Visuels Truffaut sem 19. 6mai 2016","Visuels Truffaut 12 mai","Truffaut 23 mai 144 codes","Visuels Truffaut 07062016","63 visuels de complement Truffaut 09062016");
$array=array("PRECOH16");



//,"2015_07_10_CAROLL_JPEG_BD","2015_07_13_CAROLL_JPEG_BD","2015_07_16_CAROLL_JPEG_BD");

foreach($array as $k=>$v)
{
	$directory=$directory_path.$v."/";	
	//$directory = $path."/CLIENTS/".$_REQUEST["folder"];
	chmod($directory,777);
	$images = glob($directory . "*.{jpg,png,gif,JPG,PNG,GIF}",GLOB_BRACE);
	//$images = glob($directory . "*.".$_REQUEST["ext"],GLOB_BRACE);
	//print_r($images);exit;
	$ii=0;
	foreach($images as $i)
	{
		/*echo $i;
		echo substr(sprintf('%o', fileperms($i)), -4);
		chmod($i,777);
		echo substr(sprintf('%o', fileperms($i)), -4);
		exit;*/		
		$s= getimagesize($i);
		$width=$s[0];
		$height=$s[1];
		$new_width=400;
		
		if(filesize($i)/(1024) > 200 && $width>$new_width)
		{
			//echo $i."::".(filesize($i)/(1024))." KB<br/>";
			/*if($ii==200)
				exit;*/
			//echo $ii.": ".$i."<br/>";			
			$new_height=($height/$width)*$new_width;			
			smart_resize_image($i,$string             = null,
								  $width              = $new_width,
								  $height             = $new_height,
								  $proportional       = false,
								  $output             = 'file',
								  $delete_original    = true,
								  $use_linux_commands = false,
								  $quality = 100   );
			 //$ii++;
			 //chmod($i,777);
		 
	
	   }
	  
	}
}
function smart_resize_image($file,
                              $string             = null,
                              $width              = 0,
                              $height             = 0,
                              $proportional       = false,
                              $output             = 'file',
                              $delete_original    = true,
                              $use_linux_commands = false,
   							  $quality = 100  ) 
{
      
    if ( $height <= 0 && $width <= 0 ) return false;
    if ( $file === null && $string === null ) return false;
 
    # Setting defaults and meta
    $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
    $image                        = '';
    $final_width                  = 0;
    $final_height                 = 0;
    list($width_old, $height_old) = $info;
	$cropHeight = $cropWidth = 0;
 
    # Calculating proportionality
    if ($proportional) {
      if      ($width  == 0)  $factor = $height/$height_old;
      elseif  ($height == 0)  $factor = $width/$width_old;
      else                    $factor = min( $width / $width_old, $height / $height_old );
 
      $final_width  = round( $width_old * $factor );
      $final_height = round( $height_old * $factor );
    }
    else {
      $final_width = ( $width <= 0 ) ? $width_old : $width;
      $final_height = ( $height <= 0 ) ? $height_old : $height;
  $widthX = $width_old / $width;
  $heightX = $height_old / $height;
  
  $x = min($widthX, $heightX);
  $cropWidth = ($width_old - $width * $x) / 2;
  $cropHeight = ($height_old - $height * $x) / 2;
    }
 
    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
      case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
      case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
      default: return false;
    }
    
    
    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = imagecolortransparent($image);
      $palletsize = imagecolorstotal($image);
 
      if ($transparency >= 0 && $transparency < $palletsize) {
        $transparent_color  = imagecolorsforindex($image, $transparency);
        $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($image_resized, 0, 0, $transparency);
        imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        imagealphablending($image_resized, false);
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        imagefill($image_resized, 0, 0, $color);
        imagesavealpha($image_resized, true);
      }
    }
    imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);
    # Taking care of original, if needed
    if ( $delete_original ) {
      if ( $use_linux_commands ) exec('rm '.$file);
      else @unlink($file);
    }
 
    # Preparing a method of providing result
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $file;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
    
    # Writing image according to type to the output destination and image quality
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
      case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
      case IMAGETYPE_PNG:
        $quality = 9 - (int)((0.9*$quality)/10.0);
        imagepng($image_resized, $output, $quality);
        break;
      default: return false;
    }
 
    return true;
  }
?>