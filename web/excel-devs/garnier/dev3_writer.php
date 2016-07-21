<?php 

/**
 * Gariner Template Creation.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 May 25 2016
 */
 

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(GARNIER_PATH."/dbfunctions.php");
include_once(INCLUDE_PATH."/PHPWord.php");
 
$refs_num =   array(7,8,11,14,16,18); // column which need to translate
//$refs_col =   array('E','F','I','L','N','P');
$refs_col =   array('G','H','K','N','P','R'); // column which need to translate

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['f'])){
	//echo $_GET['f']; exit;
	if($_GET['bool']=='zip'){
		zip_creation(GARNIER_WRITER_FILE_PATH."/dev3/",$_GET['f'],1);
	}else
		odownloadXLS($_GET['f'].".xlsx", GARNIER_WRITER_FILE_PATH."/dev3/".$_GET['f']."/", "dev3.php") ;
}



$basiclib=new basiclib();
$dbfunctions=new dbfunctions();
if(isset($_POST['submit']))
{ 
    $file1  =   pathinfo($_FILES['userfile1']['name']);
    $language = $_POST['lang'];
   
    if($file1['extension']=='XLSX' || $file1['extension']=='xlsx')
    {
		if($file1['extension'] == 'xlsx')
		{
			$xls1Arr  = $basiclib->xlsx_optimised_read($_FILES['userfile1']['tmp_name'],20) ;
			
		}
		
			$headers =  $xls1Arr[0][0][0];
			array_unshift($headers,"URL");
			//echo "<pre>"; print_r($headers); exit;
			
		    $rand="garnier-dev3-".strtolower($language)."-".uniqid();
    	    $srcPath=GARNIER_WRITER_FILE_PATH."/dev3/".$rand."/";
    	    $srcFile=GARNIER_WRITER_FILE_PATH."/dev3/".$rand."/".$rand.".xlsx";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
		
		//echo "<pre>"; print_r($xls1Arr[0][0]);
		$insertArray = array();
		
		foreach($xls1Arr[0][0] as $rkey=>$row){
			if($rkey == 0)
				continue;	
				
			//echo "<pre>"; print_r ($row);
										
			$other_data = json_encode(array_map("utf8_encode", $row));

			$insertArray[] = "('".escapeString($language)."','".escapeString($row[7])."','".escapeString($row[8])."','".escapeString($row[11])."','".escapeString($row[14])."','".escapeString($row[16])."','".escapeString($row[18])."','".escapeString($other_data)."')";  // crete array for insertion	
			
		}	
		
		$sql = "INSERT INTO `cl_garnier_keywords`(`gkeyword_language`, `gkeyword_column_e`, `gkeyword_column_f`, `gkeyword_column_i`, `gkeyword_column_l`, `gkeyword_column_n`, `gkeyword_column_p`, `gkeyword_other_data`) VALUES"	.implode(',',$insertArray);
		
		$bool = false;
		
		$keywordIdArray = array();
		
		$final_array = array();
		$final_array[0] = $headers;
		
		if($dbfunctions->mysql_qry($sql,0))
		{
			$keywordsSql="SELECT `gkeyword_id`, `gkeyword_language`, `gkeyword_column_e`, `gkeyword_column_f`, `gkeyword_column_i`, `gkeyword_column_l`, `gkeyword_column_n`, `gkeyword_column_p`,`gkeyword_other_data` FROM `cl_garnier_keywords` WHERE `gkeyword_template_status`=0 AND `gkeyword_status`=1";
			$data=$dbfunctions->mysql_qry($keywordsSql,1);	
			
			$i=1;		      	
			
			while ($row = mysql_fetch_array($data, MYSQL_NUM)){	
				
				$keywordIdArray[] = "('".$row[0]."')";				 
				$docx_file_name = create_docx_file($row,$language,$srcPath);
				
                $xslx_array = json_decode($row[8],true);
                array_unshift($xslx_array, "http://clients.edit-place.com/excel-devs/garnier/refdocs.php?client=GARNIER&folder=".$rand."&file=".$docx_file_name);
                $xslx_array[1] = $row[0];
                $final_array[$i++] = array_map("utf8_decode", $xslx_array); //$xslx_array;
			}	
			   $bool = true;
			
		}else{
			$bool = false;
		}
		
		/* Update status of keywrods */	
		$updateQyr="UPDATE `cl_garnier_keywords`
					SET `gkeyword_template_status` = 1
					WHERE gkeyword_id IN(".implode(',',$keywordIdArray) .")";
	
	   //echo "<pre>"; print_r($final_array); exit;

		//$tempaltes=true;
	    if ($dbfunctions->mysql_qry($updateQyr,0) && $bool && writeXlsxGarnierDev3($final_array,$srcFile))
	    	 	header("Location:dev3.php?client=GARNIER&msg=success&f=".$rand."&excel=".$rand.".xlsx");
	    	else
	    	 	header("Location:dev3.php?client=GARNIER&msg=error");
    }
}
else
    header("Location:dev3.php?client=GARNIER");
    
	/**
	 * Function json_parsing
	 *
	 * @param $value = string
	 * @return
	 */	

	function json_parsing($value){
	    $value=json_encode(utf8_encode($value));
		$value=str_replace('\u00e2\u0080\"', '-',$value);
		$value=utf8_decode(json_decode($value));
        return $value;
    }	
   
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
		$str=addslashes($str);
		$basiclib=new basiclib();
		if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
			$str = str_replace("—","&mdash;",$str);
			$str = str_replace("…","&hellip;",$str);
			$str = str_replace("Œ","&OElig;",$str);
			$str = str_replace("œ","&oelig;",$str);
			$str = str_replace("€","&euro;",$str);
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
		 
		// echo "<pre>"; print_r($data); //exit;			
		 
	   $docx_header = array('Article ID','Language','TITLE to translate','TITLE translation','TEXT to translate','TEXT translation','Balise ALT to translate','Balise ALT translation','URL Last Level to translate','URL Last Level translation','METATITLE to Translate','METATITLE Translation', 'METADESCRIPTION to Translate','METADESCRIPTION Translation');		 
	   
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
       	$styleCell3Row = array('bgColor'=>'deeaf6');
       	
       	$paragraphStyle = array('lineHeight' => '1.0','spaceAfter'=>'.5');

        
        $i=1;
                       
		for($r = 0; $r < sizeof($data)-1; $r++) { // Loop through rows
			// Add row
                $data[$r] = str_replace("’","'", $data[$r]); // replace ’ with '
                //$data[$r] = str_replace("\n","\n\n", $data[$r]);
                
			$table->addRow();
			$table->addCell(200,$styleCell2Row)->addText($i,$fontStyle1);
			$table->addCell(1000,$styleCell2Row)->addText($docx_header[$i-1],$fontStyle1);
			$cell = $table->addCell(14600);
			$tdata = explode("\n",$data[$r]); 
			foreach($tdata as $td)			    
				$cell->addText($td,$paragraphStyle);   
			  
			  //echo $data[$r]."<br />";
			  
			$i++;
			if($r >= 2){  
				$table->addRow();
				$table->addCell(200,$styleCell3Row)->addText($i,$fontStyle1);
				$table->addCell(1000,$styleCell3Row)->addText($docx_header[$i-1],$fontStyle1); 				    
				$table->addCell(14600)->addText("");
			    $i++;	    				
			}
		}
		
		//exit;
        $file_name_1 = "garnier-".strtolower($lang)."-".$data[0].".docx";	
       	$file_name_docx = $path."garnier-".strtolower($lang)."-".$data[0].".docx"; //exit;

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
        	utf8_decode($value);	
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

  /* writeXlsxGarnierDev3 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */
	 
	 
	  function writeXlsxGarnierDev3($data,$file_path)
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
