<?php
/**
 * Hotels.com Writer file to create delivery file xlsx contain all data of docx files.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 July 2,3,6
 */
 
//mbstring.language		= Neutral	; Set default language to Neutral(UTF-8) (default)
//mbstring.internal_encoding	= UTF-8		; Set default internal encoding to UTF-8
//mbstring.encoding_translation	= On		;  HTTP input encoding translation is enabled
//mbstring.http_input		= auto		; Set HTTP input character set dectection to auto
//mbstring.http_output		= UTF-8		; Set HTTP output encoding to UTF-8
//mbstring.detect_order		= auto		; Set default character encoding detection order to auto
//mbstring.substitute_character	= none		; Do not print invalid characters
//default_charset			= UTF-8		; Default character set for auto content type header 
 
 
ob_start();

mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH.'/html_to_doc.inc.php');
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], HOTELS_WRITER_FILE_PATH2."/dev4/", "dev4.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];
    
    if($ext == 'zip' || $ext == 'rar')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   HOTELS_WRITER_FILE_PATH2."/dev4/"."hotels2-" . date('ymd')."-".uniqid().".".$ext ;
		  move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;

		  if($ext=='rar')
			{
				$zip_file=pathinfo($srcFile);//exit($zip_file['filename']);
				$zip_file['filename']   =   str_replace(" ","-",$zip_file['filename']) ;
				$path   =   $zip_file['dirname']."/".$zip_file['filename'].".rar" ;
				$rar_file = rar_open($path);
				$list = rar_list($rar_file);
				
				foreach($list as $file) {       
					preg_match('/RarEntry for file "(.*)"/', $file, $matches) ;
					if(strstr($file, 'RarEntry for file'))
					{
						$entry = rar_entry_get($rar_file, $matches[1]) or die("Failed to find such entry") ;
						$entry->extract(false, $zip_file['dirname']."/".$zip_file['filename']."/".(str_replace(" ","-",$matches[1])));
					}
				}
				rar_close($rar_file);
				$unzip_dir  =   $zip_file['dirname']."/".$zip_file['filename'] ;
				chmod($unzip_dir,0777) ;
			}
			else
			{
				chmod($srcFile, 0777);
				$unzip_dir = unzipfolder($srcFile);
			}
            $docx_files = all_docx_files($unzip_dir);
            
			//echo "<pre>";print_r($docx_files); exit;
			
			$final_array = array();
			$j=4; // 1st 5 columns will be filled by the 1st DOCX file and remaning will other docx files 3rd & 4th Column
			$check_empty = "";
			$z=0;
			
			
			foreach($docx_files as $lkey=>$docx){
				//echo "<pre>"; print_r (process_xmlData(readDocx($docx))); 
				$dummy_array = process_xmlData(readDocx($docx));
				$sheet_3_array = array();
				for($i=0;$i<sizeof($dummy_array);$i++){
					if($j==4){
				       $final_array[$z][$j-4][$i] = $dummy_array[$i+1][0];	
				       $final_array[$z][$j-3][$i] = $dummy_array[$i+1][1];
				       $final_array[$z][$j-2][$i] = $dummy_array[$i+1][2];
				       $final_array[$z][$j-1][$i] = $dummy_array[$i+1][3];
				       
				       $final_array[$z+1][$j-4][$i] = $dummy_array[$i+1][0];	
				       $final_array[$z+1][$j-3][$i] = $dummy_array[$i+1][1];
				       $final_array[$z+1][$j-2][$i] = $dummy_array[$i+1][2];
				       $final_array[$z+1][$j-1][$i] = $dummy_array[$i+1][4];
				       
				    }else{
					  //echo "<br />".mb_detect_encoding($dummy_array[$i+1][1])."-".;	
					  if(is_array_empty($dummy_array)){
					     $final_array[$j][$i] = "Reading Error ".pathinfo("$docx", PATHINFO_BASENAME);   					
					     break;
					  }else{   
					     $final_array[$z][$j][$i] = $dummy_array[$i+1][3];   					
					     $final_array[$z+1][$j][$i] = $dummy_array[$i+1][4];   					
					  }   
					}
				
			     }
				  //echo $dummy_array[8][3]."+".$dummy_array[7][3]."+".$dummy_array[3][3]."<br />";
			
			
			
			//3rd sheet set headers
			$final_array[$z+2][0][0] = "Destination";
		    $final_array[$z+2][0][1] = "Introduction English";
			$final_array[$z+2][0][2] = "Introduction Translated";
			$final_array[$z+2][0][3] = "COUNT TAGS for Introduction English";
			$final_array[$z+2][0][4] = "COUNT TAGS for Introduction Translated";
			$final_array[$z+2][0][5] = "Body English";
			$final_array[$z+2][0][6] =  "Body Translated";
		    $final_array[$z+2][0][7] = "COUNT TAGS for Body English";
			$final_array[$z+2][0][8] = "COUNT TAGS for Body Translated";
			$final_array[$z+2][0][9] = "Body 2 English";
			$final_array[$z+2][0][10] = "Body 2 Translated";
			$final_array[$z+2][0][11] = "COUNT TAGS for Body 2 English";
			$final_array[$z+2][0][12] = "COUNT TAGS for Body 2 Translated";
			
		    
		    if($lkey > 0 ){
			//3rd sheet text assignment	
			//get_tags_string($dummy_array[11][3]); exit;
			$final_array[$z+2][$lkey][0] = $dummy_array[8][3]."-".$dummy_array[7][3]."-".$dummy_array[3][3];
		    $final_array[$z+2][$lkey][1] = $dummy_array[11][3];
			$final_array[$z+2][$lkey][2] = $dummy_array[11][4];
			$final_array[$z+2][$lkey][3] = get_tags_string($dummy_array[11][3]);
			$final_array[$z+2][$lkey][4] = get_tags_string($dummy_array[11][4]);
			$final_array[$z+2][$lkey][5] = $dummy_array[12][3];
			$final_array[$z+2][$lkey][6] =  $dummy_array[12][4];
			$final_array[$z+2][$lkey][7] = get_tags_string($dummy_array[12][3]);
			$final_array[$z+2][$lkey][8] = get_tags_string($dummy_array[12][4]);
			$final_array[$z+2][$lkey][9] = $dummy_array[13][3];
			$final_array[$z+2][$lkey][10] = $dummy_array[13][4];
			$final_array[$z+2][$lkey][11] = get_tags_string($dummy_array[13][3]);
			$final_array[$z+2][$lkey][12] = get_tags_string($dummy_array[13][4]);
		    }
			
				$j++;
			}
			//exit;
					
			//echo "<pre>";print_r($final_array); 

            $rand="hotels-TG-trad-delivery-".date('y-m-d').'-'.time().rand(5, 9);
    	    $srcPath=HOTELS_WRITER_FILE_PATH2."/dev4/".$rand."/";
			$srcFile=HOTELS_WRITER_FILE_PATH2."/dev4/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxHotelsDev4($final_array,$srcFile);
         
		    
            if(file_exists($srcFile)) {
			  header("Location:dev4.php?msg=success&folder=".$rand."&file=".$rand.".xlsx");
			} else {
				  
				  header("Location:dev4.php?msg=error");
			}
		
            
        }
        else
        {
            header("Location:dev4.php?msg=file_error");
        }
    	
}
else
    header("Location:dev4.php");

   /* all_docx_files function
   * 
   *  Read all docx files from unziped folder
   *  @param $path - path of unziped folder
   *  @return $docx_files - return the name + path of docx
   */	

function all_docx_files($path){
   $docx_files = array();
   $docx_files_data = array();
   $i=1;	
   if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "__MACOSX") {
            $docx_files[$i] = $path."/".$entry;
            $i++;
        }
        
    }
    closedir($handle);
  }
  return $docx_files;	
}	

   /* process_xmlData function 
   * 
   *  Read data from xml file generate by function readDocx
   *  @param $text - xml file input as string.
   *  @param $newArr - return data as array
   */	

function process_xmlData($text){
	/*things need to change end*/
//$text=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>',$text);
$text = str_replace("[","####",$text);
$text = str_replace("]","#####",$text);

$text = str_replace("</w:tr>","]",$text);
$text = str_replace("<w:tr>","[",$text);
//echo $text;exit;
$matches = array();

//$pattern = '/\tblrowstart([^\]]*)\tblrowend/';

//$pattern = '/\[(tblrowstart^\tblrowend]*)\]/';
//$pattern='/\[([^\]]*)\]/';
//preg_match_all($pattern, $text, $matches);
	//echo "<pre>";print_r($matches);exit;
$matches=explode(']',$text);

$ind=1;
$newArr=array();
foreach($matches as $key => $value){
	$value = str_replace("</w:tc>","]",$value);
	$value = str_replace("<w:tc>","[",$value);
	//echo $text2;
	$pattern='/\[([^\]]*)\]/';
	//$htmldata.="<tr id='row_".$key."'>";
	preg_match_all($pattern, $value, $matches2);
	$arrData='';
	//$htmldata.="<td >".$ind."</td>";
	foreach($matches2[1] as $k=>$v){
		$arrData[]=read_docx_special_character_function(trim(strip_tags($v)));
		//$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
	$arrData = str_replace("####","[",$arrData);
	$arrData = str_replace("#####","]",$arrData);
	$newArr[$ind]=$arrData;
	//$htmldata.="</tr>";
//	echo "<pre>";print_r($matches2[0]);
	$ind++;
}
//$htmldata.="</table>";
//echo "<pre>"; print_r($matches);

//$htmldata = str_replace("####","[",$htmldata);
//$htmldata = str_replace("#####","]",$htmldata);

return $newArr;
}

   /* readDocx function 
   * 
   *  Read a docx file and return the string
   *  @param $filepath - path of the docx file 
   *  @param $newArr - xml data output as string
   */

function readDocx($filePath) {
    // Create new ZIP archive
    $zip = new ZipArchive;
    $dataFile = 'word/document.xml';
    // Open received archive file
    if (true === $zip->open($filePath)) {
        // If done, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // If found, read it to the string
            $data = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Return data without XML formatting tags
			//echo $xml->saveXML();exit;
            $contents = explode('\n',strip_tags($xml->saveXML()));
            $text = '';
            foreach($contents as $i=>$content) {
                $text .= $contents[$i];
            }
            return  $xml->saveXML();
        }
        $zip->close();
    }
    // In case of failure return empty string
    return "";
}


  /* read_docx_special_character_function function 
   * 
   *  Return value as per encoding of data based on OS
   *  @param $value - data from docx read
   *  @return $value - data from docx read
   */ 

	function read_docx_special_character_function($value){
	 
	    if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
							$value = isset($value) ? html_entity_decode($value,ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $value=html_entity_decode(htmlentities($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
                             //$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
							//$value = isset($value) ? html_entity_decode($value,ENT_QUOTES,"UTF-8") : '';
                        }
						$value =str_replace("\0", "", $value);
		 return $value;
	 }	

	 
   /* writeXlsxHotelsDev4 function 
   * 
   *  Read a docx file and return the string
   *  @param $data - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $anchor_cols - default null 
   */ 
 
	function writeXlsxHotelsDev4($datas,$file_path,$anchor_cols=null)
    {
		$sheetnames = array("english_text","translate_text","checking_tags");
		/** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        //echo $file_path;
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        //include_once INCLUDE_PATH.'/PHPExcel/RichText.php';
        //echo "<pre>"; var_dump($datas);exit;
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
            
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Edit-place");
        
        $sheetCount = 0 ;
        foreach($datas as $idx => $data)
        {
            // Rename sheet
            $sheet_name=$sheetnames[$idx];
           // echo $sheetname;
            $objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
            $objPHPExcel->addSheet($objWorksheet);
            $objWorksheet->setTitle($sheet_name);

            //$objPHPExcel->setActiveSheetIndex($idx);
            $rowCount=0;
            foreach ($data as $k=>$row)
            {
                $col = 'A';
                
                
                               
                $language = $row[2];
                
                foreach ($row as $key => $value)
                {	
					$value = write_docx_special_character_function($value,trim($language));
                
                   if($k == 2 && $idx < 2){
					  $stylArr2 = array();
					   if(strcmp(trim($value),"Localise"))
					      $stylArr2 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '00ec00'), 'bold' => true));
					   else   
					      $stylArr2 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => 'ff0000'), 'bold' => true));
					  $objWorksheet->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr2);
				   }
				   
				    if($idx == 2 && $k > 0){
					  	
					  	//echo "$key<pre>";print_r($row);
					  					  
					  	//echo $row[$key+2].$row[$key+3];exit;	
				
		//3rd sheet for coloring if Tags are not same in columns 3,4,7,8,11,12 with column A Column per row.	  	
					  	if($col == "A"){
						
					  if((strcmp($row[$key+3],$row[$key+4]) == 0) &&  (strcmp($row[$key+7],$row[$key+8]) == 0) && (strcmp($row[$key+11],$row[$key+12]) == 0)){
						
						//echo $key."same"."<br />";
										  
					   $stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffffff'))); 
					        $objWorksheet->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3); 
					        		
				      }else{
						 //echo $key."diff"."<br />"; 
						  $stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ff4b4b'))); 
					        $objWorksheet->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3);
					   }	
					  }else{			
		//3rd sheet for coloring if Tags are not same in columns 3,4,6,7,9,10 with column B,C,D,E Column per row.			   
					  if(($col == "B" && (strcmp($row[$key+2],$row[$key+3]) == 0) ) || ($col == "C" && (strcmp($row[$key+1],$row[$key+2]) == 0) ) || ($col == "D" && (strcmp($row[$key],$row[$key+1]) == 0) ) || ($col == "E" && (strcmp($row[$key-1],$row[$key]) ==0) ) || ($col == "F" && (strcmp($row[$key+2],$row[$key+3]) ==0) ) || ($col == "G" && (strcmp($row[$key+1],$row[$key+2]) ==0) ) || ($col == "H" && (strcmp($row[$key],$row[$key+1]) ==0) ) || ($col == "I" && (strcmp($row[$key-1],$row[$key]) ==0) || ($col == "J" && (strcmp($row[$key+2],$row[$key+3]) ==0) ) || ($col == "K" && (strcmp($row[$key+1],$row[$key+2]) ==0) ) || ($col == "L" && (strcmp($row[$key],$row[$key+1]) ==0) ) || ($col == "M" && (strcmp($row[$key-1],$row[$key]) ==0)) )){
						    //echo "Same".$key."$col<br />";
						    $stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffffff'))); 
					        $objWorksheet->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3); 	
					  }else{
						    //echo "not same".$key."$col<br />";
						    $stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ff4b4b'))); 
					        $objWorksheet->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3);	 					  	
					  }	
						//exit;
					  }	
					}	
				   	   
				   
                   if($idx < 2 && strstr($value,"\n")){
					    $objWorksheet->getStyle($col.($rowCount+1))->getAlignment()->setWrapText(true);							   
				   }
				   
				   
				   
                     $objWorksheet->setCellValue($col.($rowCount+1),$value);
                    

                    $col++;
                } 
                $rowCount++;
            } 
            
            if($idx < 2){
            $objWorksheet->getStyle('1')->getFont()->setBold(true);
            $objWorksheet->getStyle('2')->getFont()->setBold(true);
            $objWorksheet->getStyle('3')->getFont()->setBold(true);
		   }else{
			 $objWorksheet->getStyle('1')->getFont()->setBold(true);  
		   }	   
            
            $sheetCount++ ;
        }
        $objPHPExcel->removeSheetByIndex(0);
       
       //exit;
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
       
        @chmod($file_path, 0777) ;//echo "<pre>";print_r($data);exit;
         //exit;
        if(file_exists($file_path))
            return true ;
            
    }
    
   /* write_docx_special_character_function function 
   * 
   *  Write XSLX Delivery file with 2 sheet
   *  @param $value - data from $final_array 
   *  @param $language - language of docx file 
   *  @return $value - data from $final_array
   */ 
    
    function write_docx_special_character_function($value,$language){
		//echo $language;
		if($language == "fr_FR"){ // French language
		    if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                {    
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("", "'", $value) ;
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}				
			return $value;	    
	    }else if($language == "ru_RU"){ // Russian language
			   $value = mb_convert_encoding($value, mb_internal_encoding());
			   //$value = str_replace("é","",$value);
			   
			return $value;
		}else if($language == "no_No"){ // Norwegian language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "kr_KR"){ // Korean language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "tr_TR"){ // Marmaris language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "pt_BR"){ // San Francisco language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "zh_HK"){ // Chinese language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "ja_JP"){ // Japanese language
				$value = mb_convert_encoding($value, mb_internal_encoding());
				//$value = str_replace("·","&#183;",$value);
			return $value;	
		}else{
		   return $value;	
		}		
		
	 }
	 
  /* is_array_empty function 
   * 
   *  Check the array is empty or not
   *  @param $a - data in array
   *  @return true if no data & false if data is there
   */ 
	 
	 
	 function is_array_empty($a){
       foreach($a as $elm)
          if(!empty($elm)) return false;
            return true;
     }
    
   	 
   /* get_tags_string function
   * 
   *  Extract only tags and remove all the content form html content.
   *  @param $text - html content of input xlsx file cell, 
   *  @return $alltags - unique html tags without content on it & count tags occurance
   *  
   */	 
	 
	function get_tags_string($text){
		$text = html_entity_decode($text);
		//echo htmlspecialchars($text)."<br />";
	   $alltags="";
       for($i=0;$i<strlen($text);$i++){
         if($text[$i] == "<" && $text[$i-1] != "<"){
		    $alltags .= $text[$i];
		    for($j=$i+1;$j<strlen($text);$j++){
		      $alltags .= $text[$j]; 
		       if($text[$j] == ">"){
			     if($j < strlen($text)-1) 
		            $alltags .= "########";
		         break;
	           }	  
	       }
	    }		
      }	
     "tags :-".htmlspecialchars($alltags)."<br />";
     $tags = explode("########",$alltags);
     $i=0;
     //echo count($tags);
     //print_r ($tags);
     if(count($tags) > 0){
     foreach(array_count_values($tags) as $key=>$t){
		 //echo htmlspecialchars($t);
		 /*if(!strstr($alltags_new,$t))
		   $alltags_new.= $t."=".substr_count($alltags, $t)."\n"; */
		    if(!empty($key))
		     $alltags_new.= $key."=".$t."\n";		   
	 }}else{
		$alltags_new="0"; 
	 } 	 
	 //echo htmlspecialchars($alltags_new); echo "<br />"; //exit;
	 return trim($alltags_new); 
   }	 
   
    
    
?>
