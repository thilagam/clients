<?php 

/**
 * Gariner Writer Template Creation.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 JUNE 2 2016
 */

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(GARNIER_PATH."/dbfunctions.php");
include_once(INCLUDE_PATH."/PHPWord.php");
 


if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['f'])){
	//echo $_GET['f']; exit;
	if($_GET['bool']=='zip'){
		zip_creation(GARNIER_WRITER_FILE_PATH."/dev5/",$_GET['f'],1);
	}else
		odownloadXLS($_GET['f'].".xlsx", GARNIER_WRITER_FILE_PATH."/dev5/".$_GET['f']."/", "dev5.php") ;
}



$basiclib=new basiclib();
$dbfunctions=new dbfunctions();
if(isset($_POST['submit']))
{ 
    $file1  =   pathinfo($_FILES['userfile1']['name']);
    //$language = $_POST['lang'];
   
    if($file1['extension']=='XLSX' || $file1['extension']=='xlsx')
    {
		if($file1['extension'] == 'xlsx')
		{
			$xls1Arr  = $basiclib->xlsx_optimised_read($_FILES['userfile1']['tmp_name'],20) ;
			
		}
		
			$headers =  $xls1Arr[0][0][0];
			array_unshift($headers,"URL");
			//echo "<pre>"; print_r($headers); exit;
			
		    $rand="garnier-dev5-".uniqid();
    	    $srcPath=GARNIER_WRITER_FILE_PATH."/dev5/".$rand."/";
    	    $srcFile=GARNIER_WRITER_FILE_PATH."/dev5/".$rand."/".$rand.".xlsx";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
			
		$data = $dbfunctions->mysql_qry("SELECT MAX(`gw_id`) FROM `cl_garnier_writer`",1);
		$autoID = mysql_fetch_row($data);
		$autoID = ((int)$autoID[0] == 0 || (int)$autoID[0] < 20001) ? 20001 : (int)$autoID[0]; 
		
		//echo "<pre>"; print_r($xls1Arr[0][0]); //exit;
		
		$insertArray = array();
		$final_array = array();
		
		foreach($xls1Arr[0][0] as $rkey=>$row){
			if($rkey == 0){
				array_unshift($row, "URL");
				$final_array[] = $row;
				continue;	
			}
			
			$input_xlsx_data = json_encode(array_map("utf8_encode", $row));
			$row[1] = $autoID++; 
			$docx_file_name = "http://clients.edit-place.com/excel-devs/garnier/refdocs_dev5.php?client=GARNIER&action=download&folder=".$rand."&file=".create_docx_file($row,$row[2],$srcPath);			
			array_unshift($row, $docx_file_name);
			$insertArray[] = "('".escapeString($autoID)."','".escapeString($input_xlsx_data)."','".escapeString($docx_file_name)."',1)";  // crete array for insertion			
			$final_array[] = $row;
			
		}	
		
		
		//echo "<pre>"; print_r($insertArray);
		//echo "<pre>"; print_r($final_array);
		
		//exit;
		
		$sql = "INSERT INTO `cl_garnier_writer`(`gw_id`,`gw_input_file_data`,`gw_template_file_name`, `gw_template_file_status`) VALUES".implode(',',$insertArray);
		

		//$tempaltes=true;
	    if ($dbfunctions->mysql_qry($sql,0) && writeXlsxGarnierDev5($final_array,$srcFile))
	    	 	header("Location:dev5.php?client=GARNIER&msg=success&f=".$rand."&excel=".$rand.".xlsx");
	    	else
	    	 	header("Location:dev5.php?client=GARNIER&msg=error");
    }
}
else
    header("Location:dev5.php?client=GARNIER");

   
	/**
	 * Function escapeString
	 *
	 * @param $str = string
	 * @return $str
	 */		
	function escapeString($str)
	{	
		$str = str_replace("&rsquo;", "'", $str) ;
		$str = str_replace("’", "'", $str) ;
		$str=addslashes($str);
		$basiclib=new basiclib();
		if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
        	$str=utf8_decode($str);	
        }
		return $str;
	}   


/* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $lang - language which we will select from dropdown
   *  
   */
   
	
	 function create_docx_file($data,$lang,$path){
		 
		//echo "<pre>"; print_r($data);// exit;			
		 
	   $docx_header = array('ITEM ID','LANGUAGE','BRAND','1ST LEVEL SECTION','SUBSECTION','TITLE','TEXT','BALISE ALT','GARNIER SUGGESTED PRODUCT','CONNECTED ARTICLES','METATITLE','METADESCRIPTION');	
	   
	   $refs_num =   array(1,2,3,4,5,7,8,11,12,13,16,18);	 
	   
	    $PHPWord = new PHPWord();
        // document style orientation and margin 
        $sectionStyle = array('orientation' => 'landscape', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>600, 'marginBottom'=>600, 'colsNum' => 2);
        $section = $PHPWord->createSection($sectionStyle);

		// Define table style arrays
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80, 'width'=>100);

		// Define font style for first row
		$fontStyle1 = array('bold'=>true, 'align'=>'center');
		$fontStyle2 = array('bold'=>false, 'align'=>'center');


		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		// Add table
		$table = $section->addTable('myOwnTableStyle');

       	$styleCell2Row = array('bgColor'=>'ff3399');
       	$styleCell3Row = array('bgColor'=>'daeef3');
       	
       	$paragraphStyle = array('lineHeight' => '1.0','spaceAfter'=>'.5');

        
        $i=1;
                       
		for($r = 1; $r < sizeof($data); $r++) { // Loop through rows
			
		   if(in_array($r,$refs_num)){
			   
			    $color_style  = ($i == 1 || $i == 2 || $i == 3 || $i == 4 || $i == 9) ?  array('bgColor'=>'ff3399') : array('bgColor'=>'daeef3');	   
			    $data[$r] = str_replace("’","'", $data[$r]); // replace ’ with '                
				$table->addRow();
				$table->addCell(200,$color_style)->addText($i,$fontStyle1);
				$table->addCell(1000,$color_style)->addText($docx_header[$i-1],$fontStyle1);
				$cell = $table->addCell(14600);
				$tdata = explode("\n",write_to_docx($data[$r])); 
				foreach($tdata as $td)			    
					$cell->addText($td,$paragraphStyle);   
		  		$i++;
		   }	
			
		}
		
		//exit;
        $file_name_1 = "garnier-writer-".strtolower($lang)."-".$data[1].".docx";	
       	$file_name_docx = $path."garnier-writer-".strtolower($lang)."-".$data[1].".docx"; //exit;

		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($file_name_docx);
		//exit; 
		 return $file_name_1;
	 }	 

	  /* write_to_docx function
   * 
   *  This will encode character before writing in XSLX
   *  @param $value - final array data for writing in XLSX, 
   *  @return $value - double decode it and then pass to writer function
   */
	  
	 function write_to_docx($value){
        //$value = iconv("ISO-8859-1", "UTF-8", $value);
       	//$value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8')); 
       	$value = str_replace("&rsquo;", "'", $value) ;
       	$basiclib=new basiclib();
       	if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
        	$value = utf8_decode($value);	
        }	
        return $value;	 
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

  /* writeXlsxGarnierDev5 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */
	 
	 
	  function writeXlsxGarnierDev5($data,$file_path)
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
				

				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                   
                if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
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
