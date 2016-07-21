<?php
/**
 * Bash Writer file Creates Multiple XSLX files from single xlsx file based on languages array("ANGLAIS","WEB","WFLAM");
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 FEB 3 2016
 */
ob_start();
//header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");

include_once("titles.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file'])){
		zip_creation(BASH_WRITER_FILE_PATH_NEW."/",$_GET['folder'],1);
}

if(isset($_POST['submit']))
{
	
	$languages = array("ANGLAIS","WEB","WFLAM"); // languages predefine
	
    $writexls = ($_REQUEST['op']=='xls') ? 'WriteXLS' : 'writeXlsx' ;

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
        
              
        //echo "<pre>";print_r ($final_array); exit;
      
		
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="bash-writer-".uniqid()."-".date('d-m-y-h-m');
    	    $srcPath=BASH_WRITER_FILE_PATH_NEW."/".$rand."/";
    	    mkdir($srcPath) ;
			chmod($srcPath,0777) ;
    	    
			foreach($languages as $lang){
				    $randf="bash-writer-".$lang."-".uniqid()."-".date('d-m-y-h-m');
    	    		$srcFile=$srcPath.$randf.".xlsx";
    	    		writeXlsxBashNewDev1($final_array,$srcFile,$lang); // call writer function here
    	    		//exit;
    	    }	
          	
		    
            if(file_exists($srcFile)) {
			  header("Location:dev1.php?msg=success&folder=".$rand."&file=zip");
			} else {
				  
				  header("Location:dev1.php?msg=error");
			}
            
        }
        else
        {
            header("Location:dev1.php?msg=file_error");
        }
    }	
}
else
    header("Location:dev1.php");

 
	 
   /* writeXlsxBashNewDev1 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  @param $lang languages in array,
   *  
   */
	 
	 
	  function writeXlsxBashNewDev1($data,$file_path,$lang)
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
            $col = 'B'; // writing start from 2 column
                                          
            
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
				
				echo $key ;
				echo "<pre>"; print_r ($row); 				
		
				
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
				
				$objPHPExcel->getActiveSheet()->setCellValue('A'.($rowCount+1), $lang); // Language Name except 1st row
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($rowCount+1), check_in_Ftp($row[0])); // Check Ftp
								
				$objPHPExcel->getActiveSheet()->setCellValue('A1', "Langue"); 	// Heading for A1 cell
				$objPHPExcel->getActiveSheet()->setCellValue('C1', "URL of IMAGES"); 	//  Heading for C1 cell
				$objPHPExcel->getActiveSheet()->setCellValue('J1', "Descriptif"); 	// Heading for J1 cell
				
                                   
                if($col == 'B' || $col == 'I')   // insert empty columns for $col == C & J
					$col++;
					
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
	 
	/**
	  * Function create Zip with predefined Extension 
	  * 
	  * @package Bash New DEV1
	  * @author  Lavanya
	  * @param   string $path  path at which file to be created with file name
	  * @param   string  $filename of the file 
	  * @param   string $ow as always 1
	  * @return  nill
	  */
    function zip_creation($path, $filename,$ow){ 
		$zip_file = $path.$filename.".zip";
		if ($handle = opendir($path.$filename."/")) {
		   $zip = new ZipArchive(); 
		    if($zip->open($path.$filename.".zip",$ow?ZIPARCHIVE::OVERWRITE:ZIPARCHIVE::CREATE)===TRUE)
            {
			     while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != ".." && $entry != "__MACOSX") {
						//echo $path.$filename."/".$entry."<br />";
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
	
	/**
	  * Function check in FTP
	  * 
	  * @package Bash New DEV1
	  * @author  Lavanya
	  * @param   string $value  reference id
	  * @return  Url if image is present and NA if not present
	  */
		
		function check_in_Ftp($value){
		
			$col_ref = $value;
            $pattern = BASH_IMAGE_PATH.'/*/'.$col_ref.'*.*';
            $arraySource = glob($pattern,GLOB_BRACE);
            sort($arraySource);
            //$arraySource = str_replace(GALERIES_LAFAYETTE_IMAGE_PATH, GALERIES_LAFAYETTE_CLIENT_URL, $arraySource) ;
                    
            if(count($arraySource)>0)
            {
                 return BASH_CLIENT_REF_URL . $col_ref;
            }
            else
            {
                 return 'NA';
             }			
			
		}	
?>
