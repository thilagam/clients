<?php
/**
 * Decathlon Creates Multiple doc files from single xlsx file
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 DEC 9, 2015
 */
ob_start();
//header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(INCLUDE_PATH."/PHPWord.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['t'])){
	zip_creation(DECATHLON_XML_FILE_PATH3."/",$_GET['t'],1);
}
if(isset($_POST['submit']))
{
	
	/*Create basic lib instance*/
    $basiclib=new basiclib();

    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ; 
    $ext=$file1['extension'];
    
    if(($ext == 'xlsx') || ($ext == 'xls'))
    {
        
		$final_array='';
		/**
		 *	if Code block to extract xls/Xlsx Containt in Array 
		 * */
        if($file1['extension'] == 'xls')
        {
        $data2 = new Spreadsheet_Excel_Reader();
		$data2->setOutputEncoding('Windows-1252');
		$data2->read($_FILES['userfile1']['tmp_name']);
		$columns=$data2->sheets[0]['numCols'];

		$x=1;           
		while($x<=$data2->sheets[0]['numRows']) {
			$y=1;               
			while($y<=$data2->sheets[0]['numCols']) {

				$data2->sheets[0]['cells'][$x][$y]=str_replace("�","'",$data2->sheets[0]['cells'][$x][$y]);
				$final_array[$x][$y-1]=isset($data2->sheets[0]['cells'][$x][$y]) ? $data2->sheets[0]['cells'][$x][$y] : '';        
			    $y++;
			}
			$final_array[$x]=array_values($final_array[$x]);
			$x++;
		}

        }
        else
        {
            $xls1Arr  = $basiclib->xlsx_read($_FILES['userfile1']['tmp_name']) ;
			$x=0;           
			while($x<sizeof($xls1Arr[0][0])) {
					$y=1;  
					while($y<=sizeof($xls1Arr[0][0][$x])) {

						$xls1Arr[0][0][$x][$y]=str_replace("�","'",$xls1Arr[0][0][$x][$y]);
						$final_array[$x+1][$y-1]=isset($xls1Arr[0][0][$x][$y]) ? $xls1Arr[0][0][$x][$y] : '';
						$y++;
				} 
				$final_array[$x+1]=array_values($final_array[$x+1]);
				$x++;			
			}
	
	
        }
        
      
       //echo "<pre>";print_r ($final_array); echo "</pre>";exit;
        
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="decathlon_".date('d-m-y')."_".uniqid();
    	    $srcPath=DECATHLON_XML_FILE_PATH3."/".$rand."/";
						
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	
   	  
    	  $header = array();
    	  $data = array();
          $l=1;
   	   
    	 // pushing Url at 1st column and creating docx file for each row  
    	   
    	   foreach($final_array as $key=>$arr){
	          //if($key < 5){
			  if($key == 1){
				   $header = $arr;
				   print_r ($header);
			  }else{
				  $data = $arr;
				  $docxfile = create_docx_file ($header,$data,$srcPath,$l++); 
			  }
			  
		     //}
		   }
		    	   
    	       	  
            //echo "<pre>"; print_r ($final_array); echo "</pre>";exit;

		
            if(!empty($final_array)) {
			    
			   header("Location:dev3.php?msg=success&t=".$rand); 
			   
			   //zip_creation(DECATHLON_XML_FILE_PATH3."/",$rand,1);
			    
			} else {
				  
				  header("Location:dev3.php?msg=error");
			}
            
        }
        else
        {
            header("Location:dev3.php?msg=file_error");
        }
    }	
}
else
    header("Location:dev3.php");

   /* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $header - top header content in array, 
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $l - row index values,
   *  
   */
	
	 function create_docx_file($header,$data,$path,$l){
	
  
	    // New Word Document
		$PHPWord = new PHPWord();

		// New portrait section
		$section = $PHPWord->createSection();
		
		$PHPWord->addLinkStyle('NLink', array('color'=>'0000FF', 'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE)); // link style

		// Add text elements
		$section->addText(take_care_of_special_character($header[0]),array('fgColor'=>'yellow'));
		$section->addTextBreak(0);
		$section->addText(take_care_of_special_character($data[0]));		
		$section->addTextBreak(2);

		$section->addText(take_care_of_special_character($header[2]),array('fgColor'=>'yellow'));
		$section->addTextBreak(0);
		$section->addText(take_care_of_special_character($data[2]));		
		$section->addTextBreak(2);
		
		$section->addText(take_care_of_special_character($header[3]),array('fgColor'=>'yellow'));
		$section->addTextBreak(0);
		$section->addText(take_care_of_special_character($data[3]));		
		$section->addTextBreak(2);
		
		$section->addText(take_care_of_special_character($header[4]),array('fgColor'=>'yellow'));
		$section->addTextBreak(0);
		$section->addText(take_care_of_special_character($data[4]));		
		$section->addTextBreak(2);
	   
	   
	   // Meta Description for Docx file
	   
		$section->addText(take_care_of_special_character($header[1]),array('fgColor'=>'yellow'));
		$section->addTextBreak(0);
		$modified_text = modifyCellContent($data[1]);
		
		$textrun = $section->createTextRun();
		$flag = 0;
		$flag1 =0;
		$link_string = "";
	    for ($i=0; $i<strlen($modified_text); $i++){
			
			
/* Start - Code is only used when we have to create link and bold tags in Docx file using php word library */	
		
		    if(strcmp($modified_text[$i],"[") == 0){
				$flag = 1;
			}elseif(strcmp($modified_text[$i],"]") == 0){
				$flag = 0;
			}
			
			if(strcmp($modified_text[$i],"{") == 0){
				$flag1 = 1;
			}elseif(strcmp($modified_text[$i],"}") == 0){
				$flag1 = 0;
			}
			
			if($flag1 == 1){
			   $link_string.=$modified_text[$i];
			   continue;
			}elseif($flag1 == 0 && strcmp($modified_text[$i],"}") == 0){
				//echo $link_string."<br />";
			   $link_string = explode("~",str_replace("{","",$link_string));
			   $textrun->addLink("$link_string[0]", "$link_string[1]", 'NLink');	
			   $link_string = "";
			   continue;	
			}			
			
		    
			if($flag == 1 && strcmp($modified_text[$i],"[") != 0 && strcmp($modified_text[$i],"]") != 0)
			  	$textrun->addText($modified_text[$i],array('bold'=>true));		    
			elseif($modified_text[$i] != '[' && $modified_text[$i] != ']' && $flag == 0)
				$textrun->addText($modified_text[$i],array('bold'=>false));	
				
/* End - Code is only used when we have to create link and bold tags in Docx file using php word library */			
  	

		    		
		}
		   
		//exit;
		//$section->addText(modifyCellContent($data[1]));		
		$section->addTextBreak(2);
       
      $file_name_docx = "decathlon_".date('d-m-y')."_".$l;
      
     //echo "Debug $file_name_docx"; 
     
		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		 
		 return $file_name_docx.".docx";
	 }	 
	 
   /* modifyCellContent function
   * 
   *  This function will modify tags 
   *  @param $string 
   *  
   */
function modifyCellContent($string)
{
    $content = "";
	$return = "";

    // Apply Paraghraph Tags 
	$string = "<p>".$string."</p>";
	$string = str_replace('<h2>','</p><h2>',$string);
	$string = str_replace('</h2>','</h2><p style="font-size:10px;">',$string);

    // Applying link + label
    
    $string = take_care_of_special_character($string);
    
    //echo $string; exit;
	preg_match_all('/\[([^\]]+)\]/', $string, $matches);

	if(sizeof($matches[0]) > 0){
	for($i=0;$i<sizeof($matches[0]);$i++){
		//echo "matched: " . $matches[0][$i]. "\n";
		$modified = explode("*",$matches[1][$i]);
		$modified_link_label = '<a href="'.trim($modified[0]).'">'.trim($modified[1]).'</a>';
		//$modified_link_label = '{'.trim($modified[0]).'~'.trim($modified[1]).'}'; old requirement to create link with doc format
		$string = str_replace($matches[0][$i],$modified_link_label,$string);	
	  }	
    } 
    //echo $string;
    //exit;
    
	// Adding strong tag
	$string_new = "";
	$count_astric=1;
	for ($i=0; $i<strlen($string); $i++){
        if ($string[$i] == "*"){
            $string_new.= (($count_astric%2!=0) ? '<strong>' : '</strong>');
           //$string_new.= (($count_astric%2!=0) ? '[' : ']');
           $count_astric++;
        }else
           $string_new.= $string[$i];
    }
	
	//echo $string_new;
	//exit;
	return str_replace("<p></p>","",$string_new);

}

   /* take_care_of_special_character
   * 
   *  will take care of special characters while writing content in docx
   *  @param $string 
   *  
   */
	function take_care_of_special_character($string){
        $string = str_replace("’","'",$string);
        $string = str_replace("&rsquo;","'",$string);
        $string = str_replace("&oelig;","oe",$string);
        $string = str_replace("&hellip;","...",$string);
        return $string;
	}	

   /* zip_creation
   * 
   *  it will bring folder and files to a zip file and download it
   *  @param $path : path of the folder
   *  @param $path : name of zip file
   *  @param $ow : optional value
   */
    function zip_creation($path, $filename,$ow){ 
		$zip_file = $path.$filename.".zip";
		if ($handle = opendir($path.$filename."/")) {
		   $zip = new ZipArchive(); 
		    if($zip->open($path.$filename.".zip",$ow?ZIPARCHIVE::OVERWRITE:ZIPARCHIVE::CREATE)===TRUE)
            {
			     while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != ".." && $entry != "__MACOSX") {
						echo $path.$filename."/".$entry."<br />";
						$zip->addFile($path.$filename."/".$entry,$entry);
					}
				}
				   $zip->close();
		    }
		  }
		  
		  header('Content-type: application/zip');
          header('Content-Disposition: attachment; filename="'.basename($zip_file).'"');
          header("Content-length: " . filesize($zip_file));
          header("Pragma: no-cache");
          header("Expires: 0");
          ob_clean();
          flush(); 
          readfile($zip_file);
          unlink($zip_file);
          exit;
		}
?>
