<?php
class ImageCompress{
	private $imageInFile;
	private $imageOutFile;
	
	/** Constructor Call 
	 *  @author Lavanya Pant
	 *  @param 1 argument as source file
     *  @param 2 argument as output file
	 *  Define Input Output Image Path 
	 */
	public function __construct($inFilePath){
		$this->imageInFile = $inFilePath;
	}
	
	/** imageSize Call Function
	 *  @author Lavanya Pant
	 *  @param 1 argument as source file
     *  returns the size of image in byte
	 */	
	public function imageSize($filePath){
	    $image = new Imagick($filePath);
        return intval($image->getImageSize())/1000;
 	}
	
	/** reduceDpi Call Function
	 *  @author Lavanya Pant
     *  Reduce the Dpi to 72 & Quality to 70 overright to the same source file
     *  call to Recursive compressNow Function
	 */
	public function reduceDpi(){
		$inFilePath = $this->imageInFile;
    	$image = new Imagick($inFilePath);
		$wh = $image->getImageResolution();
		if(intval($wh['x']) > 72){
		    $image->setImageResolution(72, 72);
	    }
	    if(intval($image->getImageWidth()) > 3000){
		   $image->minifyImage();
		}
		$image->setImageCompressionQuality(70);
		$image->writeImage($inFilePath);
		$image->getImageWidth();
		$this->compressNow($inFilePath,100);
	}
	
	/** Recursive compressNow Function
	 *  @author Lavanya Pant
	 *  @params 1 argument as source file
     *  @params 2 argument as output file
	 *  Comparess Image While Maintaining the Aspect Ratio Between width and height 
	 */
    public function compressNow($inFilePath,$width_reduce){
		$image = new Imagick($inFilePath);
        $new_size = intval($this->imageSize($inFilePath));    
        if($new_size > 400){ 
		    $basewidth = intval($image->getImageWidth())-$width_reduce;   
			$wpercent = ($basewidth / floatval($image->getImageWidth()));
			$hpercent = intval((floatval($image->getImageHeight()) * floatval($wpercent)));
            $image->thumbnailImage($basewidth, $hpercent);
            $image->writeImage($inFilePath);
            $width_reduce=$width_reduce;          				
           	$this->compressNow($inFilePath,$width_reduce);	
		}else{
			unlink($inFilePath);
			$image->writeImage($inFilePath);
		}	
    }	    	
}

/** Create Object of Image Compress Class 
 *  @author Lavanya Pant
 *  @params 1 argument as source file
 *  @params 2 argument as output file
 *  Call compressNow function to Compress Image File
 *  *Note:- Take The Copy of Source File Before Runing the Script
 */
//$lv = new ImageCompress("world-map.jpg");
//$lv->reduceDpi();

?>
