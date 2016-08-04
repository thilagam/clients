<?php
/**
 * La Redoute delivery file xlsx contain all data of docx files data.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 March 16
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

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], LA_REDOUTE_WRITER_FILE_PATH."/dev1/", "dev1.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];
    
    $file2  =   pathinfo($_FILES['userfile2']['name']) ;
    $ext2 = $file2['extension'];
    
    if(($ext == 'zip' || $ext == 'rar') && $ext2 == 'xlsx')
    {
		
		/* XSLX READ */
		   $xls1Arr  = $basiclib->xlsx_read($_FILES['userfile2']['tmp_name']) ;
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
			//echo "<pre>";print_r($final_array);exit;
		/* CLOSE XSLX READ */
		
       /* ZIP & RAR READ */
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   LA_REDOUTE_WRITER_FILE_PATH."/dev1/"."laredoute-".uniqid().".".$ext ;
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
		/* CLOSE ZIP & RAR READ */	
		
			//echo $unzip_dir;
            //$docx_files = all_docx_files($unzip_dir);
            //echo "<pre>";print_r ($final_array); echo "</pre>";     exit;			
			
			
			foreach($final_array as $key=>$fa){
				if($key > 1){					
					if($ext == "rar")
						$path = $unzip_dir."/".str_replace(" ","_",trim($fa[1])).".docx"; //exit;
					else
						$path = $unzip_dir."/".str_replace(" ","_",trim($basiclib->normaliseUrlString($fa[1]))).".docx"; //exit;
					//echo "<br />";	
					if(file_exists($path)){
						//echo $path;
						$column_C_D = process_xmlData(readDocx($path),$fa[1]);
						//$cols = explode("&lt;/h2&gt;",$column_C_D);
						$cols = explode("</h2>",$column_C_D);
						//echo "<pre>";print_r($cols);
						//echo "<pre>";print_r($cols);
						//$final_array[$key][3] = trim($column_C_D); // H2 tag values + Content tag values
						//$final_array[$key][3] = trim($column_C_D[1]); // Content tag values
						if($cols[0]){
							$final_array[$key][3]="<h2>".trim(str_replace("<h2>","",$cols[0]))."</h2>";
						}
						else{
							$final_array[$key][3]=" ";
						}
						$final_array[$key][4]=trim($cols[1]);
					}	
				}	
			}
			//exit;
			//echo "<pre>";print_r($final_array);exit;
			 $rand="laredoute-delivery-".uniqid();
    	    $srcPath=LA_REDOUTE_WRITER_FILE_PATH."/dev1/".$rand."/";
			$srcFile=LA_REDOUTE_WRITER_FILE_PATH."/dev1/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxLaRedouteNewDev1($final_array,$srcFile);
         
		    
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


   /* process_xmlData function 
   * 
   *  Read data from xml file generate by function readDocx
   *  @param $text - xml file input as string.
   *  @param $newArr - return data as array data of column C & D
   */	

function process_xmlData($text,$keyword){
	//echo $keyword;
	$data_return = array();
	$data = read_docx_special_character_function(trim(strip_tags($text),"<h2></h2>"));
	//echo $data;
	if (preg_match("/$keyword/i", $data)) {
		$data = preg_replace("/\*\*$keyword\*\*/i","",$data);
		//$data = str_replace("&lt;h2&gt;","",$data); // replace <h2>
		//$data = str_replace("&lt;/h2&gt;","###",$data); // replace </h2> with ###
		return $data;				
	}else{
		return "";
	}
	
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
		//echo $filePath; exit;
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

	 
/* writeXlsxLaRedouteNewDev1 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */
	 
	 
	  function writeXlsxLaRedouteNewDev1($data,$file_path)
    {
		
		//echo "<pre>";print_r($data);exit;
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
				
				//echo $value;
							
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
				
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
	 
   
    
?>
