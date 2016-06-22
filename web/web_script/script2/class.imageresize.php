<?php
/** 
*  Class WebImageCompress
*  Compress image to there Lower Resolution Like HD, Full HD. 
*  Image with Half HD and less Resoultion will compressed only when size goes to 400KB.
*  @author Lavanya Pant
*  @copyright  Edit-Place
*  @version    1.0
*  @since      3 Aug 2015
*/	

class WebImageCompress{
	
	private $inFile;
	private $outFile;
	private $imageInFile;
	private $imageOutFile;
	
	/** Constructor Call 
	 *  @author Lavanya Pant
	 *  @param 1 argument as source file
     *  @param 2 argument as output file
	 *  Define Input Output Image with Imagick Objects 
	 */	
	public function __construct($inFile,$outFile){
		$this->inFile = $inFile;
		$this->outFile = $outFile;		
		$this->imageInFile = new Imagick($inFile);
		$this->imageInFile->writeImage($outFile);
		$this->imageOutFile = new Imagick($outFile);
	}
	
	/** sizeOfImage Call Function
	 *  @author Lavanya Pant
	 *  @param 1 argument as source file
     *  returns the size of image in byte
	 */	
	public function sizeOfImage(){
		return $this->imageInFile->getImageLength();
	}
	
	/** reduceDpi Call Function
	 *  @author Lavanya Pant
     *  Reduce the Dpi to 72 
     *  call to Recursive compressNow Function
	 */
	public function reduceDpi(){
	    $wh = $this->imageInFile->getImageResolution();
	    if(intval($wh['x']) > 72){
		    $this->imageInFile->setImageCompressionQuality(80);
		    $this->imageInFile->setImageResolution(72, 72);
	        $this->imageInFile->writeImage($this->imageOutFile);
	    }
	}	
	
	/** checkWh Call Function
	 *  @author Lavanya Pant
	 *  Check Width Height and Make Image Compress till full HD, HD & below HD
	 */
	public function checkWH(){
		
		//echo $this->imageInFile->getImageWidth();
		//echo $this->imageInFile->getImageHeight()."<br />";
		//$this->imageInFile->setImageAttribute("signature","Compressed12345671251");
		//echo "<pre>"; print_r ($this->imageInFile->getImageSignature());
		//exit;
		
		if(intval($this->sizeOfImage()/1000) > 700){
			
			//$this->reduceDpi();
		    if((intval($this->imageInFile->getImageWidth()) >= 1920) || (intval($this->imageInFile->getImageHeight()) >= 1080)){
		        $this->fhdCompress();  
		    }else if(((intval($this->imageInFile->getImageWidth()) < 1920) && (intval($this->imageInFile->getImageWidth()) > 1280)) || ((intval($this->imageInFile->getImageHeight()) <= 1080) && (intval($this->imageInFile->getImageHeight()) >= 720))){
		        $this->hdCompress();	
		    }else{
				 $this->imageInFile->setImageCompressionQuality(90);
				 unlink($this->inFile);
                 $this->imageInFile->writeImage($this->outFile); 
                 chmod($this->outFile, 0777); 				
			}	
				
	
	    }else{
		    //echo "Image Size is Already Less then 400 Kb";	
		}
		//exit;
	}
	
	/** hdCompress Call Function
	 *  @author Lavanya Pant
	 *  Compress Image to HD Resolution 1280x720
	 */	
	public function hdCompress(){
		//$this->imageInFile->setImageCompressionQuality(100);
		if((intval($this->imageInFile->getImageWidth()) > intval($this->imageInFile->getImageHeight()))){
		    $wpercent = (1280 / floatval($this->imageInFile->getImageWidth()));
		    $hpercent = intval((floatval($this->imageInFile->getImageHeight()) * floatval($wpercent)));
            $this->imageInFile->thumbnailImage(1280, $hpercent);	
		}else{
		    $hpercent = (720 / floatval($this->imageInFile->getImageHeight()));
		    $wpercent = intval((floatval($this->imageInFile->getImageWidth()) * floatval($hpercent)));
            $this->imageInFile->thumbnailImage($wpercent, 720);	
		}
		unlink($this->inFile);
		$this->imageInFile->writeImage($this->outFile);
		chmod($this->outFile, 0777);
	}
	
	
	/** hdCompress Call Function
	 *  @author Lavanya Pant
	 *  Compress Image to Full HD Resolution 1920x1080
	 */	
	public function fhdCompress(){
		//$this->imageInFile->setImageCompressionQuality(100);
		if((intval($this->imageInFile->getImageWidth()) > intval($this->imageInFile->getImageHeight()))){
		    $wpercent = (1920 / floatval($this->imageInFile->getImageWidth()));
		    $hpercent = intval((floatval($this->imageInFile->getImageHeight()) * floatval($wpercent)));
            $this->imageInFile->thumbnailImage(1920, $hpercent);	
		}else{
		    $hpercent = (1080 / floatval($this->imageInFile->getImageHeight()));
		    $wpercent = intval((floatval($this->imageInFile->getImageWidth()) * floatval($hpercent)));
            $this->imageInFile->thumbnailImage($wpercent, 1080);	
		}
		unlink($this->inFile);
		$this->imageInFile->writeImage($this->outFile);
		chmod($this->outFile, 0777);
	}
	
}


/** Create Object of Image Compress Class 
 *  @author Lavanya Pant
 *  @params 1 argument as source file
 *  @params 2 argument as output file
 *  Call compressNow function to Compress Image File
 *  *Note:- Take The Copy of Source File Before Runing the Script
 */
//$lv = new WebImageCompress("world-map.jpg","cmprsd_world-map.jpg");
//$lv->checkWH();

?>
