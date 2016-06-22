<?php
/**
 * M6-WEB Delivery file Creation merging of Multiple doc files to single xlsx file. each docx = 1 row of xlsx
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 July 9,10,11 2015
 */

 
ob_start();

//mb_internal_encoding('UTF-8');
//ini_set('default_charset', 'UTF-8');

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
		odownloadXLS($_GET['file'], M6_WEB_WRITER_FILE_PATH."/dev2/", "dev2.php") ;
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
		  $srcFile    =   M6_WEB_WRITER_FILE_PATH."/dev2/"."M6_WEB-" . date('d-m-y-H-i')."-".uniqid().".".$ext ;
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
			$j=2; // 1st column will be filled by the 1st DOCX file and remaning will other docx files 2nd column
			$check_empty = "";
			foreach($docx_files as $docx){
				//echo "<pre>"; print_r (process_xmlData((readDocx($docx)))); exit;
				$dummy_array = process_xmlData(readDocx($docx));
                $header_i=0;
                $sheet1_column2 = "";
				for($i=0;$i<sizeof($dummy_array);$i++){
					if($j==2){
						//echo $dummy_array[$i+1][0];
					   if($i < 2 || $i==sizeof($dummy_array)-1){	
				          $final_array[0][$j-2][$header_i] = ($header_i == 2) ? "Contenu (à renseigner)" : $dummy_array[$i+1][0];
				          $sheet1_column2 .= ($i > 1) ? modifiy_text($dummy_array[$i+1][1], $i, $dummy_array) : "";
				          $final_array[0][$j-1][$header_i] = ($header_i == 2) ? $sheet1_column2 : $dummy_array[$i+1][1];
				          $header_i++;				  
				       }				       
				        $final_array[1][$j-2][$i] = ($i == sizeof($dummy_array)-1) ? "Contenu" : $dummy_array[$i+1][0];
				        
				         //strong tag for introduction columns and h3 for « intertitre » 3 4 5
				        $final_array[1][$j][$i] = modifiy_text($dummy_array[$i+1][1], $i, $dummy_array); 
	                       	
				    }else{ 
					  if(is_array_empty($dummy_array)){
					     $final_array[0][$j][$i] = "Reading Error ".pathinfo("$docx", PATHINFO_BASENAME);  
					     $final_array[1][$j][$i] = "Reading Error ".pathinfo("$docx", PATHINFO_BASENAME);   					
					     break;
					  }else{  
						if($i < 2 || $i==sizeof($dummy_array)-1){	
				          $final_array[0][$j][$header_i] = $dummy_array[$i+1][1];
				          $sheet1_column2 .= ($i > 1) ? modifiy_text($dummy_array[$i+1][1], $i, $dummy_array) : "";
				          $final_array[0][$j][$header_i] = ($header_i == 2) ? $sheet1_column2 : $dummy_array[$i+1][1];
				          $header_i++;
				        }  
				         
				        //strong tag for introduction columns and h3 for « intertitre » 3 4 5 
				           $final_array[1][$j][$i] = modifiy_text($dummy_array[$i+1][1], $i, $dummy_array);
					         
					           
					  }    
					}	
					  $sheet1_column2 .= ($i > 1) ? $final_array[1][$j][$i] : "";
					
					
				}
				$j++;
			}	
					
			//echo "<pre>";print_r($final_array); exit;
           

			$rand="M6-WEB-delivery-file-".date('d-m-y-H-i')."-".uniqid();
    	    $srcPath=M6_WEB_WRITER_FILE_PATH."/dev2/".$rand."/";
			$srcFile=M6_WEB_WRITER_FILE_PATH."/dev2/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxM6Dev2($final_array,$srcFile,$category);
         
		    
            if(file_exists($srcFile)) {
			  header("Location:dev2.php?msg=success&folder=".$rand."&file=".$rand.".xlsx");
			} else {
				  
				  header("Location:dev2.php?msg=error");
			}
		
            
        }
        else
        {
            header("Location:dev2.php?msg=file_error");
        }
    	
}
else
    header("Location:dev2.php");


 

   /* modifiy_text function
   * 
   *  Add HTML Tags as per column (strong tag for introduction columns and h3 for « intertitre » 3 4 5) 
   *  @param $text - cell text 
   *  @param $i - column number
   *  @param $dummy_array - data array to find size of that array
   *  @param $sheet - sheet0 for 1st sheet & sheet1 for 2nd sheet
   *  @return - html tag string if $text is not empty
   */	

function modifiy_text($text,$i,$dummy_array){ $text = preg_replace('/\s+/', " ",trim($text));
	   //$text = modify_url_encoding_characters($text);
	   $modified_text = "";
			if($i == 2) 
			  $modified_text = ($text != " " && !empty($text) && $text != "" && !ctype_space($text)) ? "<strong>".$text."</strong>\n\n" : " ";
			else if($i > 2 && $i%2 != 0 && $i < sizeof($dummy_array)-1){
			  $modified_text = ($text != " " && !empty($text) && $text != "" && !ctype_space($text)) ? "<h2>".$text."</h2>\n\n" : " ";
			  //echo $text."<br />";
			}else if($i > 2 && $i%2 == 0)   
			 $modified_text = $text."\n\n";
			else
			 $modified_text = $text;  
	   //echo $modified_text;
	   return $modified_text;
} 

   /* modify_url_encoding_characters function 
   * 
   *  remove some urlencode characters only work with LINUX
   *  @param $text - $text
   *  @return - urldecode($text)
   */
function modify_url_encoding_characters($text){
     $text = urlencode($text);
     $text = str_replace("%A0","%20",$text);
     $text = str_replace("%C2","",$text);
     return urldecode($text);
}	

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
function process_xmlData($text){ //echo "<pre>".htmlspecialchars($text); exit;
	/*things need to change end*/
//$text=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>',$text);
//$text = preg_replace("","",$text); 
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
		//echo read_docx_special_character_function(trim(strip_tags($v)));
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
//exit;
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
            //echo "<pre>"; print_r ($xml->saveXML());
            return  $xml->saveXML();
        }
        $zip->close();
    }
    // In case of failure return empty string
    return "";
}

 
   /* read_docx_special_character_function function 
   * 
   *  Read string and return utf8 string
   *  @param $value - convert data in utf for windows only
   *  @return $value - return modified data
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
		 
	 
   /* writeXlsxM6Dev2 function 
   * 
   *  Write XSLX Delivery file with 2 sheet
   *  @param $datas - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $anchor_cols - default null 
   */ 
 
	function writeXlsxM6Dev2($datas,$file_path,$anchor_cols=null)
    {
		$sheetnames = array("Delivery","Checking");
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
                 $formula="";             
                $language = $row[2];
                
                foreach ($row as $key => $value)
                {	
					
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
                        
				}else {
				    $value=html_entity_decode($value);	
				}	
					
									   
                   if($idx == 1 && strstr($value,"\n")){
					    $objWorksheet->getStyle($col.($rowCount+1))->getAlignment()->setWrapText(true);							   
				   }
				   
				   if($k > 1 && $idx == 1 && $key > 1 && $key < sizeof($row)-1){
					 $formula.= ($key < sizeof($row)-2) ? $col.($rowCount+1).",CHAR(10)," : $col.($rowCount+1).",CHAR(10)";					   
				   }	   
				   
				   
				   if($key == sizeof($row)-1 && $idx == 1 && $k > 1){ 
				      $value = "=CONCATENATE(".$formula.")"; 
				   }   
				     
				   
                     $objWorksheet->setCellValue($col.($rowCount+1),$value);
                    

                    $col++;
                } 
                $rowCount++;
            } 
            
          
			 $objWorksheet->getStyle('1')->getFont()->setBold(true);  
		 	   
            
            $sheetCount++ ;
        }
        $objPHPExcel->removeSheetByIndex(0);
       
       
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
       
        @chmod($file_path, 0777) ;//echo "<pre>";print_r($data);exit;
         //exit;
        if(file_exists($file_path))
            return true ;
            
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
   
?>
