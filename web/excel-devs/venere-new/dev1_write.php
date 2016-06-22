<?php
/**
 * Venere Writer file Creates Multiple doc files from single xlsx file
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 July 21,22 2015
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
		odownloadZIP($_GET['file'], VENERE_NEW_WRITER_FILE_PATH."/dev1/", "dev1.php") ;
	}else
		odownloadXLS($_GET['file'], VENERE_NEW_WRITER_FILE_PATH."/dev1/".$_GET['folder']."/", "dev1.php") ;

if(isset($_POST['submit']))
{
	
    $writexls = ($_REQUEST['op']=='xls') ? 'WriteXLS' : 'writeXlsx' ;
    $columns[]="1";
    //$combiner=$_POST['comb'];
    $lang=$_POST['lang'];
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
        
        //echo "<pre>";print_r ($final_array); echo "</pre>";
        //echo "<pre>";print_r ($fill_colors); echo "</pre>";
        

		
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="Venere-".uniqid()."-".date('d-m-y-h-m');
    	    $srcPath=VENERE_NEW_WRITER_FILE_PATH."/dev1/".$rand."/";
			$srcFile=VENERE_NEW_WRITER_FILE_PATH."/dev1/".$rand."/".$rand.".xlsx";
//			$srcFileZip=HOTELS_WRITER_FILE_PATH2."/dev1/".$rand.".zip";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	
            $info = pathinfo($_FILES['userfile1']['name']);
    	  
    	  $header = array();
    	  $header_2 = array();
    	  $data = array();
    	  
    	  $l=0;

   	   
    	   foreach($final_array as $key=>$arr){
	            
			  if($key == 1){
				   $header = columns_included_for_docx($arr,1);
			  }else if($key == 2){
				   $header_2 = columns_included_for_docx($arr,2);
		      }else{
				  $data = columns_included_for_docx($arr,3);
				  $docxfile = create_docx_file ($header,$header_2,$data,$srcPath,$fill_colors,$key);  // create docx file using create_docx_file function
				  $final_array[$key][0] = "http://clients.edit-place.com/excel-devs/venere-new/refdocs.php?client=VENERE_NEW&folder=".$rand."&file=".$docxfile;
			  }
			  $l++;
		   }
		   
		   //echo "<pre>";print_r ($final_array); echo "</pre>"; exit;
		    
		   writeXlsxVenereNewDev1($final_array,$srcFile,$fill_colors); // call writer function here
		
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
	   
	   $array_data1[1]= $array_data[2];
	   $array_data1[2]= $array_data[4];
	   $array_data1[3]= $array_data[5];
	   $array_data1[4]= $array_data[14];
	   $array_data1[5]= $array_data[15];
	   $array_data1[6]= $array_data[11];
	   
	   if($hd == 1) { $array_data1[7]=7; } else if($hd == 2) { $array_data1[7]="Paragraph 1"; } else { $array_data1[7]=""; }
	    
	   $array_data1[8]= $array_data[12];
	   
	   if($hd == 1) { $array_data1[9]=9; } else if($hd == 2) { $array_data1[9]="Paragraph 2"; } else { $array_data1[9]=""; }
	     
	   $array_data1[10]= $array_data[13];
	   
	   if($hd == 1) { $array_data1[11]=11; } else if($hd == 2) { $array_data1[11]="Paragraph 3"; } else { $array_data1[11]=""; }
	   
	   return $array_data1;
   }
   
   /* columns_color_included_for_docx function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $array_data - top header content in array, 
   *  @return $array_data - color for all cell in array
   *  
   */

   function columns_color_included_for_docx($array_data){
	   
	   $array_data1=array();
	   
	   $array_data1[1]= $array_data[3];
	   $array_data1[2]= $array_data[5];
	   $array_data1[3]= $array_data[6];
	   $array_data1[4]= $array_data[15];
	   $array_data1[5]= $array_data[16];
	   $array_data1[6]= $array_data[12];
	   $array_data1[7]= $array_data[12];
	   $array_data1[8]= $array_data[13];
	   $array_data1[9]= $array_data[13];
	   $array_data1[10]= $array_data[14];
	   $array_data1[11]= $array_data[14];
	   
	   return $array_data1;
   } 
   
   /* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $header - top header content in array, 
   *  @param $header_2 - top 2nd row header content in array,
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $fill_colors - color for all cell in array
   *  @param $key - row number of Array
   *  
   */
	
	 function create_docx_file($header,$header_2,$data,$path,$fill_colors,$key){
	
	   $header_color= array();
	   $header_2_color= array();
	   $data_color= array();
	   
	   $header_color = columns_color_included_for_docx($fill_colors[1]);
	   $header_2_color = columns_color_included_for_docx($fill_colors[2]);
	   $data_color = columns_color_included_for_docx($fill_colors[$key]);
	   
	  //echo sizeof($header)."/".sizeof($path);
	  //echo $key;
	  //echo "Debug 3<pre>"; print_r ($header_color); echo "</pre>";
	  //echo "Debug 3<pre>"; print_r ($header_color); echo "</pre>";
	  //echo "Debug 3<pre>"; print_r ($header_2_color); echo "</pre>";
	  //echo "Debug 3<pre>"; print_r ($data); echo "</pre>"; 	//exit;	
	  
	
	    $PHPWord = new PHPWord();
        // document style orientation and margin 
        $sectionStyle = array('orientation' => 'landscape', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>600, 'marginBottom'=>600, 'colsNum' => 2);
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

       
       
		$i=1;
		for($r = 1; $r <= sizeof($header); $r++) { // Loop through rows
			// Add row
			$table->addRow();
                $data[$i] = str_replace("’","'", $data[$i]); // replace ’ woth '
			for($c = 1; $c <= 3; $c++) { // Loop through cells
				// Add Cell  
				if($c == 1){
					$col_bg_1 = array('bgColor'=>substr($header_color[$i], 2));
					$table->addCell(500,$col_bg_1)->addText(write_to_docx($header[$i]), $fontStyle); 				    
				}else if($c == 2){
					if(strstr($header_2[$i],"Title 1")) { $header_2[$i] = "Title 1 (change if wrong)"; } // search Title 1 in string and replace it
					if(strstr($header_2[$i],"Title 2")) { $header_2[$i] = "Title 2 (change if wrong)"; } // search Title 1 in string and replace it
					if(strstr($header_2[$i],"Title 3")) { $header_2[$i] = "Title 3 (change if wrong)"; } // search Title 1 in string and replace it
					
					$col_bg_2 = array('bgColor'=>substr($header_2_color[$i], 2));
					$table->addCell(2000,$col_bg_2)->addText(write_to_docx($header_2[$i]), $fontStyle); 				    
			    }else{
					 $col_bg_3 = array('bgColor'=>substr($data_color[$i], 2));
				     $table->addCell(13300,$data_color)->addText(write_to_docx($data[$i]));    		
				}
			 
			}  $i++; //echo "<br />";	
		}
      //exit;
      $symbols = array("(", ")", " : ", "&", ".", ",", "«", ";", "»", ".", "!", "/","‘", "\\"); // array of special chracter to remove
      
      $file_name_docx = $data[3]." ".$data[1]." ".$data[5];
      $file_name_docx = str_replace($symbols, "", $file_name_docx);
      $file_name_docx = str_replace(" ", "-", $file_name_docx);
      $file_name_docx = str_replace("--", "-", $file_name_docx);
      
      $bl = new Basiclib();
      $file_name_docx = $bl->normaliseUrlString($file_name_docx); // remove french character from filename
      //echo "$file_name_docx"; 

		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		//exit; 
		 return $file_name_docx.".docx";
	 }	 
	 
   /* writeXlsxVenereNewDev1 function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  @param $colors color in array of each cell as per uploaded file,
   *  
   */
	 
	 
	  function writeXlsxVenereNewDev1($data,$file_path,$colors)
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
				

			    if(substr($header_colors[$key+1],2) == "000000")
			       $cell_color = "FFFFFF";
			    elseif(empty($header_colors[$key+1]))
			       $cell_color = "FFFFFF";
			    else
			       $cell_color = substr($header_colors[$key+1],2);    
			      
				$stylArr1 = array('fill' =>array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $cell_color)));
				
				$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
				
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                   
                if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
                
                /* if($col == "A" || $col == "B") // Show only A B columns hide other columns
                   $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setVisible(true); 
                else
                   $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setVisible(false); */   
            }
            $rowCount++;
        }
        //exit;

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
       
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        
        
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
   *  @return - array containing of each cell color
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
    	   $fill=$objPHPExcel->getActiveSheet()->getStyle($cell)->getFill()->getStartColor()->getARGB();
           $arr_color_fill[$row][$column] = empty($fill) ? "FFFFFFFF" : $fill;          	       
        }
        return $arr_color_fill;
        
	} 
	
  /* write_to_docx function
   * 
   *  This will encode character before writing in XSLX
   *  @param $value - final array data for writing in XLSX, 
   *  @return $value - double decode it and then pass to writerXlsxM6Dev1 function
   */
	  
	 function write_to_docx($value){
		        $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("’","'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8')); 
                return utf8_decode(utf8_decode($value));		 
	 }
	
?>
