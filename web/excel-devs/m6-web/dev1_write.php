<?php
/**
 * M6-WEB Writer file Creates Multiple doc files from single xlsx row
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Jun 8,9 2015
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
include_once(INCLUDE_PATH."/PHPWord.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='zip'){
		odownloadZIP($_GET['file'], M6_WEB_WRITER_FILE_PATH."/dev1/", "dev1.php") ;
	}else
		odownloadXLS($_GET['file'], M6_WEB_WRITER_FILE_PATH."/dev1/".$_GET['folder']."/", "dev1.php") ;

if(isset($_POST['submit']))
{
	
    $writexls = ($_REQUEST['op']=='xls') ? 'WriteXLS' : 'writeXlsx' ;
    $columns[]="1";
    //$combiner=$_POST['comb'];
    $lang=($_POST['lang']!='')? $_POST['lang']:'FR';
    //echo $lang;
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
        
        $fill_colors = get_color_from_excel($_FILES['userfile1']['tmp_name']);
        
        //echo "<pre>";print_r ($fill_colors); echo "</pre>"; exit;
        
       		
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="M6-WEB-writer-file-".date('d-m-y-H-i')."-".uniqid();
    	    $srcPath=M6_WEB_WRITER_FILE_PATH."/dev1/".$rand."/";
			$srcFile=M6_WEB_WRITER_FILE_PATH."/dev1/".$rand."/".$rand.".xlsx";
//			$srcFileZip=HOTELS_WRITER_FILE_PATH2."/dev1/".$rand.".zip";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	
            $info = pathinfo($_FILES['userfile1']['name']);
    	  
    	  $header = array();
    	  $data = array();
    	  
    	  $l=0;

   	   
    	 // pushing Url at 1st column and creating docx file for each row  
    	   
    	   foreach($final_array as $key=>$arr){
	          if($key == 1){
				   $header = $arr;
				   array_unshift($final_array[$key], "URL");				    				  
			 }else{
				  $final_array[$key][3] = $final_array[$key][0]; 
				  $data = $arr;
				  //echo "<pre>Debug 2"; print_r ($header);print_r ($data); echo "</pre>";exit;
				  $docxfile = create_docx_file ($header,$data,$srcPath,$basiclib,$fill_colors); 
				  array_unshift($final_array[$key], "http://clients.edit-place.com/excel-devs/m6-web/refdocs.php?client=M6_WEB&folder=".$rand."&file=".$docxfile);
			  }
			  $l++;
		   }
		   
    	   
    	   //echo "<pre>"; print_r ($final_array); echo "</pre>";exit;
    	  
    	   
			
    	    /*Processing of Array Starts with this call to function */
    	    
           writeXlsxM6Dev1($final_array,$srcFile,$fill_colors);
		
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
}
else
    header("Location:dev1.php");

   /* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $header - top header content in array, 
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @pram $basiclib - custom library for converting special characters to normal $basiclib->normaliseUrlString
   *  @param $fill_colors - array containing colors for headers
   */
	
	 function create_docx_file($header,$data,$path,$basiclib,$fill_colors){
	
	   //echo "<pre>"; print_r ($fill_colors);
	   $header_color = $fill_colors[1];	
	
	   $PHPWord = new PHPWord();

        $sectionStyle = array('orientation' => 'portrait', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>600, 'marginBottom'=>600, 'colsNum' => 2);
        $section = $PHPWord->createSection($sectionStyle);

		// Define table style arrays
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80, 'width'=>100);

		// Define font style for first row
		$fontStyle = array('bold'=>true, 'align'=>'center');

		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		// Add table
		$table = $section->addTable('myOwnTableStyle');

       $styleCell2Row = array('bgColor'=>'ffff66');
       $styleCell3RowData = array('align'=>'center');
       $styleCellRemaining = array('bgColor'=>'0070c0');

       
		$i=3; //remove 1st 2nd column from docx  
		for($r = 0; $r <= sizeof($header)-4; $r++) { // Loop through rows
			// Add row
			$table->addRow();

			for($c = 1; $c <= 2; $c++) { // Loop through cells
				// Add Cell 
				if($c == 1){
					$col_bg_1 = array('bgColor'=> substr($header_color[$i+1], 2) == "000000" ? "FFFFFF" : substr($header_color[$i+1], 2));
					$table->addCell(1500,$col_bg_1)->addText(write_to_docx($header[$i])); 
				}else if($c == 2){
					$data_value = ($i == 3 ? $data[$i-3] : $data[$i]);
					$table->addCell(9500)->addText(write_to_docx($data_value));      
			    }		
			 
			}  $i++; //echo "<br />";	
		}
      
      //exit;
      
      $symbols = array("(", ")", " : ", "&", ".", ",", "«", ";", "»", ".", "!", "/","‘", "\\");
      $file_name_docx = $basiclib->normaliseUrlString($data[0]);
      $file_name_docx = str_replace($symbols, "", $file_name_docx);
      $file_name_docx = str_replace(" ", "-", $file_name_docx);
      $file_name_docx = str_replace("--", "-", $file_name_docx);
      
     //echo "Debug $file_name_docx"; 
     
		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		 
		 return $file_name_docx.".docx";
	 }
	 
	 	 
   /* write_to_docx function
   * 
   *  This will encode character before writing in XSLX
   *  @param $value - final array data for writing in XLSX, 
   *  @return $$value - double decode it and then pass to writerXlsxM6Dev1 function
   */
	  
	 function write_to_docx($value){
		        $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8')); 
                return utf8_decode(utf8_decode($value));		 
	 }
	 	 
	 
   /* writeXlsxM6Dev1 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  @param $colors color in array of each cell as per uploaded file,
   *  
   */
	 
	  function writeXlsxM6Dev1($data,$file_path,$colors)
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
            $header_color=array();
            
            if($rowCount == 0){
				   $header_color = $colors[1];
			 }
            
                       
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
				  
				if($rowCount == 0 && $col != 'A' ){
					
					echo substr($header_color[$key], 2) == "000000" ? "FFFFFF" : substr($header_color[$key], 2);
					
					$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => substr($header_color[$key], 2) == "000000" ? "FFFFFF" : substr($header_color[$key], 2))));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				}
				
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
        //$objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
	 
   /* get_color_from_excel function
   * 
   *  Fetch color as array from input XLSX file.
   *  @param $srcFile - $path of input file uploaded  
   *  @return $arr_color_fill- array containing of each cell color
   */ 
	 
	function get_color_from_excel($srcFile){
	
	   require_once (INCLUDE_PATH."/PHPExcel/IOFactory.php");
	   $reader = new PHPExcel_Reader_Excel5();
	   $reader->setReadDataOnly(false);
       $objPHPExcel = PHPExcel_IOFactory::load($srcFile);
	   $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
       foreach ($cell_collection as $key=>$cell) {
    	   $column = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getCell($cell)->getColumn());;
    	   $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
    	   if($row == 2)
	         break;
    	   $fill=$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->getARGB();
 		   //$fill=str_replace("C", "0",$fill) ;
    	   //header will/should be in row 1 only. of course this can be modified to suit your need.
           $arr_color_fill[$row][$column] = $fill;
          	       
        }
        return $arr_color_fill;
        
	} 
?>
