<?php
/**
 * TUI  xlsx contain all data of docx files data with cout validation.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Vinayak Kadolkar
 * @copyright  Edit-Place
 * @version    1.0 
 * @since      1.0 July 08 , 2016
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
include_once(TUI_PATH."/tui.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], TUI_WRITER_FILE_PATH."/dev1/", "index.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();
    $tui=new tui();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];
    $type= $_POST['type'];
    
    if($ext == 'zip' || $ext == 'rar')
    { 
       
		$file1  =   pathinfo($_FILES['userfile1']['name']) ;
		$tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		$ext	=	$file1['extension'] ;
		$srcFile    =   TUI_WRITER_FILE_PATH."/dev1/"."tui-" . date('ymd')."-".uniqid().".".$ext ;
		move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;

		if($ext=='rar')
		{
			$zip_file=pathinfo($srcFile);//exit($zip_file['filename']);
			$zip_file['filename']   =   str_replace(" ","-",$zip_file['filename']) ;
			$path   =   $zip_file['dirname']."/".$zip_file['filename'].".rar" ;
			$rar_file = rar_open($path);
			$list = rar_list($rar_file);
			
			foreach($list as $file) 
			{       
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
            
		$xlsxHeader=array();
		switch ($type) {
				case 1:
					$xlsxHeader=array('Article ID','Domain','Page','Chemin','Paramètres/Sujet','Bloc | Titre','Bloc | Position','Bloc | Nb de colonnes','Bloc | Nb de lignes','Sous-Bloc | index Colonne','Sous-bloc | Index Ligne','Sous-Bloc | Titre','Bloc MOBILE','NBCAR','Bloc SUITE','NBCAR','Sous-Bloc | Contenu, mis au format HTML','Type sous-bloc');
					break;
				case 2:
					$xlsxHeader=array('Article ID','Domain','Page','Chemin','Paramètres/Sujet','Bloc | Titre','Bloc | Position','Bloc | Nb de colonnes','Bloc | Nb de lignes','Sous-Bloc | index Colonne','Sous-bloc | Index Ligne','Sous-Bloc | Titre','Bloc SOUS-TITRE','NBCAR','Bloc MOBILE','NBCAR','Bloc SUITE','NBCAR','Sous-Bloc | Content in HTML Format','Type sous-bloc');
					break;
				case 3:
					$xlsxHeader=array('Article ID','Domain','Page','Chemin','Paramètres/Sujet','Bloc | Titre','Bloc | Position','Bloc | Nb de colonnes','Bloc | Nb de lignes','Sous-Bloc | index Colonne','Sous-bloc | Index Ligne','Sous-Bloc | Titre','Bloc SOUS-TITRE','NBCAR','Bloc MOBILE','NBCAR','Bloc SUITE','NBCAR','Sous-Bloc | Contenu, mis au format HTML','Type sous-bloc');
					break;


			}	
		$final_array = array($xlsxHeader);
		$index=1; // 1st 5 columns will be filled by the 1st DOCX file and remaning will other docx files 3rd & 4th Column
		$check_empty = "";
		$headers=array();
		$validations=array();
		
		foreach($docx_files as $lkey=>$docx){
			$fileInfo=pathinfo($docx);
			//echo "<pre>"; print_r($fileInfo);
			if($fileInfo['extension']=='docx'){
				$dummy_array = process_xmlData(readDocx($docx));
				if($index==1){
					$headers=$tui->getHeaders($dummy_array);
					//echo "<pre>"; print_r($headers);
					$validations=$tui->getValidations($headers);
					//echo "<pre>"; print_r($validations);
				}
				$rows=$tui->segregateRow($dummy_array,$validations,$type);
				
				foreach ($rows as $key => $value) {
					$final_array[]=$value;
				}
			
			}
		    $index++;
		}
			//exit;
					
		//echo "<pre>";print_r($final_array); exit;

        $rand="Mui-validated-".uniqid()."-".date('d-m-y-h-m');
	    $srcPath=TUI_WRITER_FILE_PATH."/dev1/".$rand."/";
		$srcFile=TUI_WRITER_FILE_PATH."/dev1/".$rand.".xlsx";
		
		//exit;
		
		writeXlsxTuiDev1($final_array,$srcFile);
     
	    
        if(file_exists($srcFile)) 
        {
			header("Location:index.php?msg=success&file=".$rand.".xlsx");
		} 
		else 
		{
			header("Location:index.php?msg=error");
		}
		
            
    }
    else
    {
        header("Location:index.php?msg=file_error");
    }
    	
}
else
    header("Location:index.php");

 

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
$text = str_replace("[","||||",$text);
$text = str_replace("]","|||||",$text);

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
		
		
		$arrData[]=read_docx_special_character_function(trim($v));
		//$arrData[]=trim($v);
		//$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
	$arrData = str_replace("||||","[",$arrData);
	$arrData = str_replace("|||||","]",$arrData);
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

	 
   /* writeXlsxVenereDev2 function 
   * 
   *  Read a docx file and return the string
   *  @param $data - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $anchor_cols - default null 
   */ 
 
	function writeXlsxTuiDev1($data,$file_path,$anchor_cols=null)
    {
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
            //echo "<pre>"; print_r ($row); echo "</pre>"; exit;
            $language ='fr_FR'; //exit;
            //echo "<br />";
            $col_k_value = ""; echo $k;
            $style_no = array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID ,'color'=>array('rgb'=>'FF0000')));
            foreach ($row as $key => $value)
            {	
				if(preg_match("/^(ERR-)/", $value)){
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount+1))->getFill()->applyFromArray(array(
				        'type' => PHPExcel_Style_Fill::FILL_SOLID,
				        'startcolor' => array(
				             'rgb' => 'F28A8C'
				        )
				    ));
				    $value =ltrim($value,'ERR-');	
				}
					
				$value = write_docx_special_character_function($value,trim($language));
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                $col++;
            }
             //echo $col_k_value."<br />";
            $rowCount++;
        }

        //exit;

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle("sheet1");
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
         
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
       
        @chmod($file_path, 0777) ; 
        
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
	    }else if($language == "de_DE"){ // German language
			   $value = mb_convert_encoding($value, mb_internal_encoding());
			   //$value = str_replace("é","",$value);
			   
			return $value;
		}else if($language == "en_UK"){ // Norwegian language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "es_ES"){ // Spanish language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "it_IT"){ // italic language
				$value = mb_convert_encoding($value, mb_internal_encoding());
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
    
    
?>
