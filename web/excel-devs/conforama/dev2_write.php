<?php
/**
 * Conforama Xlsx to template docx files.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 March 18 2016
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
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(INCLUDE_PATH."/PHPWord.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']) && isset($_GET['folder']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
//		echo CONFORAMA_WRITER_FILE_PATH."/dev2/".$_GET['folder']."/";
		odownloadXLS($_GET['file'], CONFORAMA_WRITER_FILE_PATH."/dev2/".$_GET['folder']."/", "dev2.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
         
    $file2  =   pathinfo($_FILES['userfile2']['name']) ;
    $ext2 = $file2['extension'];
    
    if($ext2 == 'xlsx')
    {
	
		/* XSLX READ */
		   $xls1Arr  = $basiclib->xlsx_read($_FILES['userfile2']['tmp_name']) ;
			$x=0;      // read data from row 4 above will be igoned
			while($x<sizeof($xls1Arr[0][0])) {
					$y=1;  
					while($y<=sizeof($xls1Arr[0][0][$x])) {
                        if(1){ // $y == 1 || $y == 2 || $y == 4 || $y == 8 || $y == 11 || $y == 13 || $y == 18 
							$xls1Arr[0][0][$x][$y]=str_replace("�","'",$xls1Arr[0][0][$x][$y]);
							$final_array[$x+1][$y]=isset($xls1Arr[0][0][$x][$y]) ? $xls1Arr[0][0][$x][$y] : '';
						}
						$y++;
				} 
				$final_array[$x+1]=array_values($final_array[$x+1]);
				$x++;			
			}
		/* CLOSE XSLX READ */
		
		//echo "<pre>";print_r ($final_array); echo "</pre>"; exit;
       
               /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="conforama-url-template".uniqid();
    	    $srcPath=CONFORAMA_WRITER_FILE_PATH."/dev2/".$rand."/";
			$srcFile=CONFORAMA_WRITER_FILE_PATH."/dev2/".$rand."/".$rand.".xlsx";
//			$srcFileZip=HOTELS_WRITER_FILE_PATH2."/dev2/".$rand.".zip";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	
    	  $header = array();
    	  $data = array();
    	  
    	  $l=0;
   	   
    	   foreach($final_array as $key=>$arr){
			  if($key < 4){
				array_push($final_array[$key], "");  
				continue;
			  }	
	          if($key == 4){
				   $header = columns_included_for_docx($arr,0);
				   array_unshift($final_array[$key],"URL TEMPLATE");
		      }else{
				  //echo "<pre>"; print_r ($arr[9]); exit;
				  $data = columns_included_for_docx($arr,$key);
				  $docxfile = create_docx_file ($header,$data,$srcPath,$key);  // create docx file using create_docx_file function
				  array_unshift($final_array[$key],"http://clients.edit-place.com/excel-devs/conforama/refdocs.php?client=CONFORAMA&folder=".$rand."&file=".$docxfile);
			  }
			  $l++;
		   }
		   
		   //echo "<pre>";print_r ($final_array); echo "</pre>"; exit;
		    
		   writeXlsxConforamaNewDev2($final_array,$srcFile); // call writer function here
		
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
        {
            header("Location:dev2.php?msg=file_error");
        }
    	
}
else
    header("Location:dev2.php");


 
   /* columns_included_for_docx function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $array_data - top header content in array,
   *  @param $hd - value will seperate data among header header2 and data row,
   *  @return $array_data - color for all cell in array
   *  
   */

   function columns_included_for_docx($array_data,$hd){
	   
	   $array_data1=array();
	   
	   $array_data1[0]= $array_data[0];
	   $array_data1[1]= $array_data[1];
	   $array_data1[2]= $array_data[2];
	   $array_data1[3]= $array_data[3];
	   $array_data1[4]= $array_data[4];
	   $array_data1[5]= $array_data[5];
	   $array_data1[6]= $array_data[6];
	   $array_data1[7]= ($hd == 0) ? "Conducteur SEO" : $array_data[7];
	   $array_data1[8]= ($hd == 0) ? "Texte Marketing collection/série" : '';
	   $array_data1[9]= ($hd == 0) ? "Informations Produits" : $array_data[9];
	   $array_data1[10]= $array_data[10];
   
	   return $array_data1;
   }
	
   /* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $header - top header content in array, 
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $key - row number of Array
   *  
   */
	
	 function create_docx_file($header,$data,$path,$key){
	
	   //echo "<pre>";print_r ($data); echo "</pre>"; exit;
	   //echo "<pre>";print_r ($header); echo "</pre>"; exit;
	
	    $color_array = array('ff66cc','ff66cc','ff66cc','ff66cc','ff66cc','ff66cc','ff66cc','66ff99','3399ff','3399ff','3399ff');
	    
	    $PHPWord = new PHPWord();
        // document style orientation and margin 
        $sectionStyle = array('orientation' => 'landscape', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>600, 'marginBottom'=>600, 'colsNum' => 2);
        $section = $PHPWord->createSection($sectionStyle);

		// Define table style arrays
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80, 'width'=>100);

		// Define font style for first row
		$fontStyle = array('bold'=>true, 'align'=>'center');
		
		$paragraphStyle = array('lineHeight' => '1.0','spaceAfter'=>'0');

		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		// Add table
		$table = $section->addTable('myOwnTableStyle');

       $styleCell2Row = array('bgColor'=>'ffff66');
       $styleCell3RowData = array('align'=>'center');
       $styleCellRemaining = array('bgColor'=>'0070c0');

       
       $i=0;
		foreach($header as $key=>$hd) { // Loop through rows
			$table->addRow();
            for($c = 1; $c <= 3; $c++) { // Loop through cells
				if($c == 1){
					$col_bg_1 = array('bgColor'=>$color_array[$i]);
					$table->addCell(500,$col_bg_1)->addText(($i), $fontStyle); 
				}else if($c == 2){
				    $col_bg_2 = array('bgColor'=>$color_array[$i]);
				    //echo $hd;
					$table->addCell(2000,$col_bg_2)->addText(write_to_docx($hd), $fontStyle); 
			    }else{
					$cell = $table->addCell(13300);
					if($i == 10){
						$tdata_i = explode("\n",$data[$i]);
					     foreach($tdata_i as $td_i){
							 if((strstr(rtrim($td_i), "-")))							 
								$td_i = write_to_docx(str_replace("-","#-",$td_i)); 
							 else
								$td_i = write_to_docx("<strong>".$td_i."</strong>");
							 $td_i = str_replace("<strong></strong>","",$td_i);	
							 $cell->addText($td_i,null,$paragraphStyle);
						 }	
					}elseif($i == 9){
						 $tdata = preg_replace("/-+\n/","",$data[$i]);   // replace --- \n of line one from string
					     $tdata = explode("\n",$tdata);
					     foreach($tdata as $td){
							 if((strstr(rtrim($td), "  ")))							 
								$td = write_to_docx(str_replace("  ","#- ",$td)); 
							 else
								$td = write_to_docx("<strong>".$td."</strong>");
							 $td = str_replace("<strong></strong>","",$td);	
							 $cell->addText($td,null,$paragraphStyle);
						 }	
					}else{
					   if($i == 1 || $i == 6)
							$cell->addLink(write_to_docx($data[$i]), null, 'NLink');
					   else	
							$cell->addText(write_to_docx($data[$i]));    						
				    } 
				}			
			}
			$i++;       
	    }

      //exit;
      $file_name_docx = $string = preg_replace('/\s+/', '', $data[0]);
      

		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		//exit; 
		 return $file_name_docx.".docx";
	 }	 
	 
	 
  /* write_to_docx function
   * 
   *  This will encode character before writing in XSLX
   *  @param $value - final array data for writing in XLSX, 
   *  @return $value - double decode it and then pass to writer function
   */
	  
	 function write_to_docx($value){
		       $value = str_replace("’","'", $value) ; // please dnt decode and put after iconv
		       
		        $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("’","'", $value) ;
                $value = str_replace("&rsquo;","'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8')); 
                //echo $value,"<br />";
                return utf8_decode(utf8_decode($value));			 
	 } 
   
     /* writeXlsxConforamaDev2 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */
	 
	 
	  function writeXlsxConforamaNewDev2($data,$file_path)
    {
		
	
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
				if($rowCount < 4){
				if(($col == 'A' || $col == 'B' || $col == 'C' || $col == 'D' || $col == 'E' || $col == 'F' || $col == 'G' || $col == 'H')){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'fbe5d6')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}				
				elseif(($col == 'I')){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'e2f0d9')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
				elseif($col == 'J' || $col == 'K'){
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'bdd7ee')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
				else{
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'deebf7')));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
			    }else{
					if($value == "NA"){
						  $stylArr1 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ffc7ce')));
						  $objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
						}  
				}	
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
				
				if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) &&  $rowCount >= 4)
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
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('4')->getFont()->setBold(true);
        
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
    
?>
