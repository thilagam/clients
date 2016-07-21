<?php
/**
 * Garnier delivery file xlsx contain all data of docx files data.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 31 MAY  2016
 */
 
 
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
include_once(GARNIER_PATH."/dbfunctions.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], GARNIER_WRITER_FILE_PATH."/dev4/", "dev4.php") ;
	}
		

if(isset($_POST['submit']))
{
    
    $headers = array("ITEM ID", "LANGUAGE", "BRAND", "1ST LEVEL SECTION", "SUB SECTION", "VISUAL TITLE", "TITLE", "TEXT", "VISUAL", "CREDIT", "BALISE ALT", "GARNIER SUGGESTED PRODUCTS", "CONNECTED ARTICLES", "URL dernier niveau", "URL MASTER", "META TITLE", "NUMBER OF CARACTERS", "META DESCRIPTION", "NUMBER OF CARACTERS");
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();
    $dbfunctions=new dbfunctions();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];
    $language = $_POST['lang'];
    
    if($ext == 'zip' || $ext == 'rar')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   GARNIER_WRITER_FILE_PATH."/dev4/"."garnier-" . date('ymd')."-".uniqid().".".$ext ;
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
			
			$insertArray = array();
			
			$final_array[0] = $headers;
			
			$j=1; 
			
			foreach($docx_files as $lkey=>$docx){
				//echo "<pre>"; print_r (process_xmlData(readDocx($docx)));  //exit;
				$dummy_array = process_xmlData(readDocx($docx));
				
				$insertArray[] = "('".escapeString($dummy_array[1][1])."','".escapeString($dummy_array[2][1])."','".escapeString($dummy_array[4][1])."','".escapeString($dummy_array[6][1])."','".escapeString($dummy_array[8][1])."','".escapeString($dummy_array[10][1])."','".escapeString($dummy_array[12][1])."','".escapeString($dummy_array[14][1])."')";  // crete array for insertion					  
			
				$j++;
			}
			
			
			
			$sql = "INSERT INTO `cl_garnier_keywords_translations`(`gkw_trans_keyword_id`, `gkw_trans_language`, `gkw_trans_column_e`, `gkw_trans_column_f`, `gkw_trans_column_i`, `gkw_trans_column_l`, `gkw_trans_column_n`, `gkw_trans_column_p`) VALUES".implode(',',$insertArray);
			
			
			
			$keywordIdArray = array();
			$bool = false;
			
			if($dbfunctions->mysql_qry($sql,0))
			{
				
				//echo "<pre>";print_r($insertArray); exit;
				
				
				$keywordsSql="SELECT clgkt .*, clgk.gkeyword_other_data
							  FROM `cl_garnier_keywords_translations` clgkt
							  LEFT JOIN `cl_garnier_keywords` clgk ON clgk.`gkeyword_id` = clgkt.`gkw_trans_keyword_id`
							  WHERE gkw_template_status = '0' AND `gkw_status`=1";	
				$translated_data = $dbfunctions->mysql_qry($keywordsSql,1);			  
				$i=1;
							  
				while ($row = mysql_fetch_array($translated_data, MYSQL_NUM)){
					
						//echo "<pre>";print_r($row); 
						
						$keywordIdArray[] = "('".$row[0]."')";		
						
						$xslx_array = json_decode($row[12],true);
						
						//echo "<pre>";print_r($xslx_array); //exit;
												
						$final_array[$i] = array_map("utf8_decode", $xslx_array);
						
						$final_array[$i][1] = $row[1];
						$final_array[$i][7] = modify_content(strtoupper($row[3]));
						$final_array[$i][8] = modify_content($row[4]);
						$final_array[$i][11] = modify_content($row[5]);
						$final_array[$i][14] = modify_content($row[6]);
						$final_array[$i][16] = modify_content($row[7]);
						$final_array[$i][18] = modify_content($row[8]);						
						
						
						$final_array[$i][17] = "=LEN(P".($i+1).")";
						$final_array[$i][19] = "=LEN(R".($i+1).")";						
													
						$i++;	
						
						//echo "<pre>";print_r($final_array[$i]); exit;
				}
				
				//exit;
				$bool = true;				
				
			}else{
				$bool = false;
			}		
			
			
					
			//echo "<pre>";print_r($final_array); exit;

            $rand="garnier-delivery-".uniqid();
    	    $srcFile=GARNIER_WRITER_FILE_PATH."/dev4/".$rand.".xlsx";
			
					/* Update status of keywrods */	
		    $updateQyr="UPDATE `cl_garnier_keywords_translations`
					SET `gkw_template_status` = 1
					WHERE `gkw_trans_id` IN(".implode(',',$keywordIdArray) .")";
         
		    
            if ($dbfunctions->mysql_qry($updateQyr,0) && $bool && writeXlsxGarnierDev4($final_array,$srcFile)){
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
    
    
    /**
	 * Function escapeString
	 *
	 * @param $str = string
	 * @return
	 */		
	function escapeString($str)
	{	
		$str = str_replace("&rsquo;", "'", $str) ;
		$str = str_replace("’", "'", $str) ;
		
		$basiclib=new basiclib();
		if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
			//$str = str_replace("…", "...", $str) ;
		//	$str = str_replace("—","-",$str);
			$str = str_replace('”','"',$str);
			$str = str_replace('“','"',$str);
			//$str = str_replace("—","&mdash;",$str);
			$str = str_replace("-","-",$str);
			$str = str_replace("-","-",$str);
			$str = str_replace("—","-",$str);
			$str = str_replace("–","-",$str);
			$str = str_replace("…","&hellip;",$str);
			$str = str_replace("Œ","&OElig;",$str);
			$str = str_replace("œ","&oelig;",$str);
			$str = str_replace("€","&euro;",$str);
			$str=utf8_decode($str);	
        	
        }
        $str=addslashes($str);
		return $str;
	} 

   /* modify_content function
   * 
   *  Modify convert to line break '\r'  
   *  @param $value -  value of column cell
   *  @return $value - return the $value with tags \r
   */	

function modify_content($value){
	   $value = str_replace("####","\r \r",$value);
	   $value = str_replace("##","\r",$value);
	   return utf8_encode($value);
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

function process_xmlData($text){
	/*things need to change end*/
//$text=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>',$text);
$text = str_replace("[","^^^^",$text);
$text = str_replace("]","^^^^^",$text);

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
	/* if($key != 1){ */ // Dont include second row of DOCX file as per specification
	$value = str_replace("</w:tc>","]",$value);
	$value = str_replace("<w:tc>","[",$value);
	//echo $text2;
	$pattern='/\[([^\]]*)\]/';
	//$htmldata.="<tr id='row_".$key."'>";
	preg_match_all($pattern, $value, $matches2);
	$arrData='';
	//$htmldata.="<td >".$ind."</td>";
	foreach($matches2[1] as $k=>$v){
		
		if($k > 0)
		$arrData[]=read_docx_special_character_function(trim(strip_tags($v)));
		//$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
	$arrData = str_replace("^^^^","[",$arrData);
	$arrData = str_replace("^^^^^","]",$arrData);
	$newArr[$ind]=$arrData;
	//$htmldata.="</tr>";
//	echo "<pre>";print_r($matches2[0]);
	$ind++;
   /* }	*/
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
   *  @return - xml data output as string
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

	 
   /* writeXlsxGarnierDev4 function 
   * 
   *  Read a docx file and return the string
   *  @param $data - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   */ 
 
	 function writeXlsxGarnierDev4($data,$file_path)
    {
		
		//echo "Debug 3<pre>"; print_r ($colors); echo "</pre>";	
		
		$anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        /* Create new PHPExcel object*/
        $objPHPExcel = new PHPExcel();
        
        /* Set properties*/
        $objPHPExcel->getProperties()->setCreator("Edit-Place");
        
        /* Add some data */
        $objPHPExcel->setActiveSheetIndex(0);


        $rowCount=0;
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            $header_colors=array();
            

		   //echo "Debug 4 $k<pre>"; print_r ($colors[$k]); echo "</pre>";	exit;
	       $header_colors = $colors[$k];
            
            
            foreach ($row as $key => $value)
            {	/* Based on OS Apply Encoding */
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
				//$value=str_replace("_x0019_","'",$value);
								
				//echo $value."-".$col."-".$rowCount;
				
				if (strpos($value, "#") !== false) {
					$stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'e34d4d'))); 
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3); 
			    }

				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), html_entity_decode($value));
                   
                $col++;
                
   
            }
            $rowCount++;
        }
        //exit;

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
       
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
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
