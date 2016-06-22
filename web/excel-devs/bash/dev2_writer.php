<?php
/**
 * Delivery file Creation merging of Multiple xlsx files to single xlsx file. 
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 2 FEB 2016
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
		odownloadXLS($_GET['file'], BASH_DELIVERY_FILE_PATH_NEW."/", "dev2.php") ;
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
		  $srcFile    =   BASH_DELIVERY_FILE_PATH_NEW."/" . date('d-m-y-H-i')."-".uniqid().".".$ext ;
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
            $xlsx_files = all_xlsx_files($unzip_dir);
            

			
			/*  Modify array and swap index 0 value and index 1 value */
			
			$final_array = array();
			$final_array_header = array();
			
			$z=1;
			foreach($xlsx_files as $key=>$xfl){
				$xls1Arr  = $basiclib->xlsx_read($xfl);
				$final_array_header = $xls1Arr[0][0][0];
				$x=($key == 1) ? 1 : 1;
				while($x<sizeof($xls1Arr[0][0])) {
						$y=1;
						$temp;  
						while($y<=sizeof($xls1Arr[0][0][$x])) {
							$xls1Arr[0][0][$x][$y]=str_replace("�","'",$xls1Arr[0][0][$x][$y]);
							if($y != 3){ // not consider column 3
								if($y == 1)
									$final_array[$z+1][$y-1]=isset($xls1Arr[0][0][$x][$y+1]) ? $xls1Arr[0][0][$x][$y+1] : '';	
								else if($y == 2)
									$final_array[$z+1][$y-1]=isset($xls1Arr[0][0][$x][$y-1]) ? $xls1Arr[0][0][$x][$y-1] : '';	
								else
									$final_array[$z+1][$y-1]=isset($xls1Arr[0][0][$x][$y]) ? $xls1Arr[0][0][$x][$y] : '';
							}
							$y++;
						} 
						$final_array[$z+1]=array_values($final_array[$z+1]);
						$x++;
						$z++;			
				}	
				
			}

			  sort($final_array);
			  unset($final_array_header[3]); // unset column C
			  //print_r ($final_array_header); exit;
			  $final_array_i = array_merge(array($final_array_header), $final_array);
			  //echo "<pre>"; print_r ($final_array_i);
			  //exit;	
			
			/* Close Modify Array */	
			
			$rand="bash-delivery-file-".date('d-m-y-H-i')."-".uniqid();
    	   	$srcFile=BASH_DELIVERY_FILE_PATH_NEW."/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxBashDev2($final_array_i,$srcFile,$category);
         
		    
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

  /* all_docx_files function
   * 
   *  Read all docx files from unziped folder
   *  @param $path - path of unziped folder
   *  @return $docx_files - return the name + path of docx
   */	
function all_xlsx_files($path){
   $xslx_files = array();
   $xslx_files_data = array();
   $i=1;	
   if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "__MACOSX") {
            $xlsx_files[$i] = $path."/".$entry;
            $i++;
        }
        
    }
    closedir($handle);
  }
  return $xlsx_files;	
}	

   /* writeXlsxBashDev2 function 
   * 
   *  Write XSLX Delivery file
   *  @param $datas - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $anchor_cols - default null 
   */ 
 
	function writeXlsxBashDev2($data,$file_path,$anchor_cols=null)
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
        //$columns_required = array('G','H','I','J','K','L');
        $rowCount=0;
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            //echo "<pre>"; print_r ($row); echo "</pre>"; exit;
            $language = $row[0]; //exit;
            //echo "<br />";
            $col_k_value = ""; //echo $k;
            foreach ($row as $k1 => $value)
            {	
								
				/* start swap value for 1nd column and 2nd column */
				if($k > 0){
				if($k1 == 0)
				  $value = $row[$k1+1];
				elseif($k1 == 1)
				  $value = $row[$k1-1];
				else  	
				   $value = $value;
				}
				/* close swap value */   
				
				//$value = write_docx_special_character_function($value,trim($language));
				
				//echo $k;
				if($col  == 'I' && $k > 0 && (strpos($value, "#") == true)){
					//echo $value;
					
//					$stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffffff'))); 
//					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3);
					
					if (check_hash_pattern($value) == false) {
						  //echo "true"."<br />";
							$stylArr3 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ff4b4b'))); 
							$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr3);	 
					}							
					
				}
               
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

  /* check_hash_pattern function 
   * 
   *  Check hash sequence is correct
   *  @param mystring - string of hash sequence
   *  @return true & false
   */ 
     
    function check_hash_pattern($mystring){
		
	$bool = false;
	for($i=0;$i<strlen($mystring);$i++){
		if($mystring[$i] == "#" && ($i+2)<strlen($mystring)){
			//echo $mystring[$i];
			if(($mystring[$i+1] == "#") && ($mystring[$i+2] != "#")){
				//echo $mystring[$i+1];
				$bool = true;
				$i = $i+1;
			}else{
				$bool = false;	
				break;
			}			
		}				
	}
	
	if($bool)
		return true; //echo "correct";
	else
		return false; //echo "incorrect";		 
		
		 
     }
?>
