<?php
/**
 * SEO-SALOMON Writer file to create delivery file xlsx contain all data of docx files.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 AUG 19 2015
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
session_start();

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
		odownloadXLS($_GET['file'], SEO_SALOMON_WRITER_FILE_PATH."/dev1/", "dev1.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $lang  =   explode("-",trim($_POST['language'])) ;
    $language = $lang[1];
    $language_code = $lang[0];
    
    $ext = $file1['extension'];
    
    $_SESSION['words_translated'] = 0;
    $_SESSION['words_not_translated'] = 0;
    
    if(1)
    {
       
		    $english_input_file_array = readExcel($_FILES['userfile2']['tmp_name']);
            
            //echo "<pre>";print_r($english_input_file_array); exit;
            
    		$other_input_file_array = readExcelMultiSheet($_FILES['userfile1']['tmp_name']);
			
			//echo "<pre>";print_r($other_input_file_array);
			
			$final_other_input_file_array = create3ColumnArray4AllSheet($other_input_file_array);
			
			//echo "<pre>";print_r($final_other_input_file_array);
			
            $final_array = process_data($english_input_file_array, $final_other_input_file_array,$language);

			//echo "<pre>";print_r($final_array); 
			
			//exit;
			
            

			$rand="seo-salomon-".date('y-m-d').'-'.time().rand(5, 9);
    	    $srcPath=SEO_SALOMON_WRITER_FILE_PATH."/dev1/".$rand."/";
			$srcFile=SEO_SALOMON_WRITER_FILE_PATH."/dev1/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxSeoSalomondev1($final_array,$srcFile,$language,$language_code);
         
  		    
            if(file_exists($srcFile)) {
			  header("Location:dev1.php?msg=success&folder=".$rand."&file=".$rand.".xlsx");
			} else {
				  
				  header("Location:dev1.php?msg=error");
			}
		
            
        }
        else
        {
            header("Location:dev1.php?msg=file_error");
        }
    	
}
else
    header("Location:dev1.php");

   /* equivalent_translate_string function 
   * 
   *  Comparision of 1st sheet word to 2nd sheet 
   *  @param $other_language_array - data in array
   *  @param $search_value -  word in 1st sheet
   *  @return false if no data & word if translation is found
   */
  function equivalent_translate_string($other_language_array,$search_value){
	  foreach($other_language_array as $d){
		foreach($d as $key=>$d1){ 
		  if(!empty($d1) || $d1 != ""){   
			 if(strcasecmp(trim($d1), $search_value) == 0 && $key < 4)
			     //return $d[$key+3];	
			     return (strcasecmp($d1, $d[$key+3]) == 0) ? $d[$key+3]."#####" : $d[$key+3] ;
            }
          }    
	    }	    		   
	    return " ";  
  }	  


   /* process_data function 
   * 
   *  Fetch data in sheet 1 and find it equivalent in the 2nd sheet
   *  @param $english_array = data in array of 1st sheet 
   *  @param $other_language_array = data in array of 2st sheet
   *  @param $language = language selected by user from front end 
   *  @param $data - data in array
   *  @return final_array for writer in xlsx
   */
   function process_data($english_array,$other_language_array,$language){ 
	  $finaly_array = array(); 
	  $unique_words_non_transalated = array();
	  $unique_words_transalated = array();
	  $i=1;
	  $j=1;
       foreach($english_array as $row=>$en){ 
		  
		  $finaly_array[$row][1]= ""; // To make 1 column as blank 
		   
		  foreach($en as $col=>$en1){ 
			  $finaly_array[$row][$col] = $en1;
			  
		  } 
		  foreach($en as $col=>$en1){

           //echo "$col"; echo "<pre>"; print_r ($en); 

		   if($row == 1 && $col > 5){
				$finaly_array[$row][$col+5] = !empty($en1) ? $language : "" ;
				
			  }elseif($row == 2 && $col > 5){
				$finaly_array[$row][$col+5] = !empty($en1) ? $finaly_array[$row][$col] : "" ;
				
			  }else{
				   if($col > 6){ //echo $col;
					   if(!empty($finaly_array[$row][$col]) && !is_numeric($finaly_array[$row][$col]) && equivalent_translate_string($other_language_array,trim($finaly_array[$row][$col])) == " "){
						    $unique_words_non_transalated[$i++] = $finaly_array[$row][$col];
						    $finaly_array[$row][$col+5] = " ";
						    //echo "<pre>";  print_r($unique_words); //exit;					        
					   }else{
					     $finaly_array[$row][$col+5] = equivalent_translate_string($other_language_array,trim($finaly_array[$row][$col]));
					     if((!empty($finaly_array[$row][$col]) || $finaly_array[$row][$col] != "") && !is_numeric($finaly_array[$row][$col]))
					        $unique_words_transalated[$j++] = $finaly_array[$row][$col];
					   }  
		   		   }  		   		   
			  }
		  }		  
		  
		 //if($row == 100){ echo "<pre> "; print_r ($unique_words_non_transalated); echo "<br />"; echo "<pre> "; print_r ($unique_words_transalated); echo "<pre> "; print_r ($finaly_array); exit; }
           
       }
     //echo "<pre>"; print_r($unique_words); exit;
     $unique_words_non_transalated = array_intersect_key($unique_words_non_transalated, array_unique(array_map('strtolower', $unique_words_non_transalated)));
     $unique_words_transalated = array_intersect_key($unique_words_transalated, array_unique(array_map('strtolower', $unique_words_transalated)));
     
     //echo "<pre> "; print_r ($unique_words_non_transalated); echo "<br />"; echo "<pre> "; print_r ($unique_words_transalated); echo "<pre> ";
     
     //exit;
     
     $_SESSION['words_not_translated'] = sizeof($unique_words_non_transalated);
     $_SESSION['words_translated'] = sizeof($unique_words_transalated);
     
     
     
     return $finaly_array;   
   }
   
   
   /* create3ColumnArray4AllSheet function 
   * 
   *  Combine all sheet value to 3 columns in sheet
   *  @param $data - data in array of 2nd sheet
   *  @return modified array with 6 colmun 1-3 as word & 4-6 as translation word
   */
   function create3ColumnArray4AllSheet($data){ 
      $data_new_array = array();
      $i=1;
      foreach($data as $key=>$d){
		  $col = 1;
		  foreach($d as $d1){
		     if($col <= 6){
		       $data_new_array[$i] = $d1;
		       $i++; 
		     }
		  }      
 
	  }
	  return $data_new_array;
   }
    
    /**
      * Function readExcelMultiSheet
      * function used to return Multi Excel Sheet in form of Array
      * @param string $file
      * @return string $data
      *
      */
   function readExcelMultiSheet($file){
           $objPHPExcel = PHPExcel_IOFactory::load($file);
           $i=0;
           foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
               $objPHPExcel->setActiveSheetIndex($i);
               $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
               foreach ($cell_collection as $cell) {
                 $column = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getCell($cell)->getColumn());
                 //$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                 $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                 $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
                 $arr_data[$row][$column] = read_special_character_function($data_value);
               }
              $data[$i] = $arr_data;
              $i++;
            }
           unset($objPHPExcel);
           return $data;
    }

 
     
   /* readExcel function 
   * 
   *  Will read all type of XSLX or XLS files.
   *  @param $file - data of input file path
   *  @param $ref -  which columns should be check for geting references
   *  @return $arr_data - contain all the reference in column 
   */ 
 
 function readExcel($file){
	$objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheetname = $objPHPExcel->getSheetNames();
    $arr_data = array();
    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
    foreach ($objPHPExcel->getWorksheetIterator() as $key=>$objWorksheet) { 
		$cell_collection = $objWorksheet->getCellCollection(); 
	 if($key == 0){
    foreach ($cell_collection as $cell) {
        $column_alpha = $objWorksheet->getCell($cell)->getColumn();
        $column = PHPExcel_Cell::columnIndexFromString($column_alpha);
        $row = $objWorksheet->getCell($cell)->getRow();
        if($column >= 12)
		  continue; 
        $data_value = $objWorksheet->getCell($cell)->getValue();
        $arr_data[$row][$column] = read_special_character_function($data_value);
	  }
	 }  
	}
	unset($objPHPExcel);
    return $arr_data;
  }   
 
  /* read_docx_special_character_function function 
   * 
   *  Return value as per encoding of data based on OS
   *  @param $value - data from docx read
   *  @return $value - data from docx read
   */ 

	function read_special_character_function($value){
	 
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
 
   
  /* writeXlsxSeoSalomondev1 function 
   * 
   *  This will write data array to XLSX file.
   *  @param $data - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $language - as $_POST["language"] ("DE-Detuch") value assigned Detuch
   *  @param $language_code - as $_POST["language"] as DE-Detuch value assigned DE
   *  @param $anchor_cols - default null 
   */ 			 
	 
	function writeXlsxSeoSalomondev1($datas,$file_path,$language,$language_code,$anchor_cols=null)
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
        foreach ($datas as $k=>$row)
        {
            $col = 'A';
            //echo "<pre>"; print_r ($row); echo "</pre>"; exit;
            //echo "<br />";
            foreach ($row as $key => $value)
            {	
				
						
				$value = write_docx_special_character_function($value,trim($language_code));
				if($value1 = strstr($value, "#####", true)){
				   $styleArray = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffff00'))); 
				   $objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($styleArray); 
                   $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value1);
                }else   
                   $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                   
                $col++;
            }
            $rowCount++;
        }

        

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle($language);
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
		
    }
    
   /* write_docx_special_character_function function 
   * 
   *  Return value as per encoding of data
   *  @param $value - data from $final_array 
   *  @param $language - language of docx file 
   *  @return $value - data from $final_array
   */ 
    
    function write_docx_special_character_function($value,$language){
		//echo $language;
		if($language == "FR"){ // French language
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
	    }else if($language == "DE"){ // DE-Deutsch language
			   $value = mb_convert_encoding($value, mb_internal_encoding());
			   //$value = str_replace("é","",$value);
			   
			return $value;
		}else if($language == "ES"){ // ES-Spanisch language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
			
		}else if($language == "IT"){ // IT-Italic language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;
				
		}else if($language == "CS"){ // CS-Czech language
			   $value = mb_convert_encoding($value, mb_internal_encoding());
			   //$value = str_replace("é","",$value);
			   
			return $value;
		}else if($language == "SV"){ // SV-Swedish language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
			
		}else if($language == "PL"){ // PL-Polich language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;
				
		}else if($language == "NO"){ // Norwegian language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;
				
		}else if($language == "JP"){ // JP-Japanese language
				$value = mb_convert_encoding($value, mb_internal_encoding());
				//$value = str_replace("·","&#183;",$value);
			return $value;	
		}else{
		   return $value;	
		}		
		 
	 }
    
?>
