<?php
/**
 * Hotels.com Writer file Creates Multiple doc files from single xlsx file
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Jun 9,10 2015
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
		odownloadZIP($_GET['file'], HOTELS_WRITER_FILE_PATH2."/dev2/", "dev2.php") ;
	}else
		odownloadXLS($_GET['file'], HOTELS_WRITER_FILE_PATH2."/dev2/".$_GET['folder']."/", "dev2.php") ;

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
		
		//echo "<pre>"; print_r ($fill_colors); echo "</pre>"; //exit;
		
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="HotelsTG-trad-writers-file-".date('d-m-y')."-".uniqid();
    	    $srcPath=HOTELS_WRITER_FILE_PATH2."/dev2/".$rand."/";
			$srcFile=HOTELS_WRITER_FILE_PATH2."/dev2/".$rand."/".$rand.".xlsx";
//			$srcFileZip=HOTELS_WRITER_FILE_PATH2."/dev2/".$rand.".zip";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	
            $info = pathinfo($_FILES['userfile1']['name']);
    	  
    	  $top_header = array();
    	  $header = array();
    	  $header_2 = array();
    	  $data = array();
    	  
    	  $l=0;
    	   foreach($final_array as $key=>$arr){
			  //echo $key;
			  //$color = $fill_colors[$key]; 
			  if($key == 1){
				  $top_header = $arr;
				  //echo "<pre>"; print_r ($top_header); "</pre>"; exit;
				  array_unshift($final_array[$key], $l);
			  }else if($key == 2){
			       $header = $arr;
				   array_unshift($final_array[$key], "Url");
			  }else if($key == 3){
			       $header_2 = $arr;			  
			       array_unshift($final_array[$key], " ");
			  }else{
				  $data = $arr;
				  $docxfile = create_docx_file ($top_header,$header,$header_2,$data,$srcPath,$fill_colors,$key); 
				  array_unshift($final_array[$key], "http://clients.edit-place.com/excel-devs/hotels2/refdocs2.php?client=HOTELS.COM_TRAVEL_GUIDE&folder=".$rand."&file=".$docxfile);
				  //echo "<pre>"; print_r ($header);print_r ($header_2);print_r ($data); echo "</pre>"; exit;
			  }
			  $l++;
		   } 
    	   
    	   			
    	    /*Processing of Array Starts with this call to function */
    	    
            //writeMultiDocs($final_array,$ref_columns,$srcPath,$srcFile,$srcFileZip,$rand,$lang);
		
		   writeXlsxHotels($final_array,$srcFile,$fill_colors);
		
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
}
else
    header("Location:dev2.php");

   /* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $top_header - top header content in array, 
   *  @param $header - top 2nd row header content in array,
   *  @param $header_2 - top 3rd row header content in array,
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $color - color of full xlsx docuement,
   *  @param $key - $data is of which row(index) in $final_array 
   *  
   */	
	
	
	 function create_docx_file($top_header,$header,$header_2,$data,$path,$color,$key){
	
	  //echo sizeof($header)."/".sizeof($path);
	  
	    $color_header_1 = $color[1];
	    $color_header_2 = $color[2];
	    $color_header_3 = $color[3];
	    
	    $color_data = $color[$key];
	
	    //echo "<pre>$key"; print_r ($color_data); echo "</pre>"; exit;
	    
	
        $PHPWord = new PHPWord();

        $sectionStyle = array('orientation' => 'landscape', 'marginLeft'=>400, 'marginRight'=>400, 'marginTop'=>400, 'marginBottom'=>400, 'colsNum' => 2);
        $section = $PHPWord->createSection($sectionStyle);

		// Define table style arrays
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80, 'width'=>100);

		// Define font style for first row
		$fontStyle1 = array('bold'=>true, 'color'=>'ff0000');
		$fontStyle2 = array('bold'=>true, 'color'=>'00ec00');
        $fontStyle = array('bold'=>true);
        
        $tagsFontStyle = array('bold'=>false, 'color'=>'0000FF');

		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		// Add table
		$table = $section->addTable('myOwnTableStyle');

       //$styleCellB2J = array('bgColor'=>'a8a8a8');
       $styleCell3RowData = array('align'=>'center');
       $styleCellRemaining = array('bgColor'=>'0070c0');


       //echo "<pre>"; print_r ($data);echo "</pre>";
       
		$i=0;
		for($r = 1; $r <= sizeof($header); $r++) { // Loop through rows
			// Add row
			$table->addRow();
			
			
			$data[$i] = str_replace("€","\xE2\x82\xAc",$data[$i]); // currency converter
			$data[$i] = str_replace("$","USD",$data[$i]); // currency converter
			$data[$i] = str_replace("¥","JPY",$data[$i]); // currency converter
			
			
			//echo "assa".$data[12];
			$top_header[$i] = $top_header[$i]; 
			$header[$i] = special_character_function($header[$i]);
			$header_2[$i] = utf8_decode(special_character_function($header_2[$i]));
			$data[$i] = utf8_decode(special_character_function($data[$i]));
		  
		    
		    //exit;
						
			for($c = 1; $c <= 5; $c++) { // Loop through cells
				// Add Cell
				if($c == 1){
					//echo str_replace("000000","FFFFFF",substr($color_header_1[$i+1], 2)); exit;
					$styleCellB2J = array('bgColor'=>str_replace("000000","FFFFFF",substr($color_header_1[$i+1], 2)));
				   if(($r >= 2 && $r <= 10) || $r == 14 || ($r >= 18 && $r <= 23)){ $table->addCell(300, $styleCellB2J)->addText("$top_header[$i]");  }else{ $table->addCell(300)->addText("$top_header[$i]"); }
				}else if($c == 2){
					$styleCellB2J = array('bgColor'=>str_replace("000000","FFFFFF",substr($color_header_2[$i+1], 2)));
				    if(($r >= 2 && $r <= 10) || $r == 14 || ($r >= 18 && $r <= 23)){ $table->addCell(1050,$styleCellB2J)->addText("$header[$i]", $fontStyle); }else { $table->addCell(1050)->addText("$header[$i]", $fontStyle); }
				}else if($c == 3){
					$styleCellB2J = array('bgColor'=>str_replace("000000","FFFFFF",substr($color_header_3[$i+1], 2)));
					if(trim($header_2[$i]) == "Localise"){
						$table->addCell(1060)->addText("$header_2[$i]", $fontStyle2);  
				    }else{
						$table->addCell(1060, $styleCellB2J)->addText("$header_2[$i]", $fontStyle1);  
					}     
				}else if($c == 4){
					$styleCellB2J = array('bgColor'=>str_replace("000000","FFFFFF",substr($color_data[$i+1], 2)));
					if(($r >= 2 && $r <= 10) || $r == 14 || ($r >= 18 && $r <= 23)){ $table->addCell(6900, $styleCellB2J)->addText("$data[$i]");  }else{
										
						if($r == 12 || $r == 13 || $r == 11){
							$data[$i] = (str_replace("> ",">", preg_replace('/\s+/', ' ', $data[$i])));
							//echo $new_data = get_data_string_2($data[$i])."<br />";
							$new_data = get_data_string_2($data[$i]);
							//echo htmlspecialchars($data[$i])."<br />";
							//echo htmlspecialchars($new_data)."<br />";
							//echo "<br />";
							$new_tags_data = explode("~",$new_data);
							//echo count($new_tags_data);
							$cell = $table->addCell(6900);
							if(count($new_tags_data) > 1){
							for($l=1;$l<count($new_tags_data)-1;$l++){
							    //echo htmlspecialchars($alltags[$l]);
							    if($new_tags_data[$l][0] == "<")
							      $cell->addText("$new_tags_data[$l]", $tagsFontStyle);
							    else
							      $cell->addText("$new_tags_data[$l]");     	 
							    $cell->addTextBreak(1);   
							      	   
						    }} else { echo "Without taggs";
								$cell->addText("$new_data");     	 
							}		
							
						  //$table->addCell(5000)->addText("$new_data"); 
						}else{
						  $table->addCell(6900)->addText("$data[$i]"); 	
						}	 
						 
				   }
				}else{
					if(($r == 12 || $r == 13 || $r == 11)){
						$tags = get_tags_string($data[$i]);
						//echo htmlspecialchars($tags)."<br /><br />";
						$alltags = explode("|",$tags);
						
						//echo "<pre>"; print_r ($alltags); 
																	
						if($r >= 2 && $r <= 10 ){ $table->addCell(6900, $styleCellB2J)->addText("$tags"); }else{ 
							
							$cell = $table->addCell(6900);
							for($l=0;$l<count($alltags);$l++){
							    //echo htmlspecialchars($alltags[$l]);
							    $cell->addText("$alltags[$l]", $tagsFontStyle);     	 
							    $cell->addTextBreak(1);   
							      	   
						    }							
							//echo "<br /><br />";
							
							 //$table->addCell(5000)->addText("$tags"); 
					  }
						
						
						
					}else{
						if(($r >= 2 && $r <= 10)|| $r == 14 || ($r >= 18 && $r <= 23)){  $table->addCell(6900, $styleCellB2J)->addText("$data[$i]"); }else{ $table->addCell(6900)->addText(" "); }						
					}		
					
				}			  				
			 
			} $i++; //echo "<br />";	
		}
		//exit;
		
      
      $symbols = array("(", ")", " : ", "&", ".", ",", "«", ";", "»", ".", "!", "/","‘", "\\");
      $Destination = explode(" (",$data[7]);
      
      $file_name_docx = $Destination[0]." ".$data[6]." ".$data[2];
      $file_name_docx = str_replace($symbols, "", $file_name_docx);
      $file_name_docx = str_replace(" ", "-", $file_name_docx);
      $file_name_docx = str_replace("--", "-", $file_name_docx);
      
       echo "$file_name_docx"; 
     
		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		 //exit;
		 return $file_name_docx.".docx";
	 }	 
	 
	 
   /* writeXlsxHotels function
   * 
   *  This will write data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  @param $colors color in array of each cell as per uploaded file,
   *  
   */
	 
	 
	  function writeXlsxHotels($data,$file_path,$colors,$anchor_cols=null)
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

        /*$stylArr1 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'a8a8a8')));
        $stylArr2 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'a8a8a8')));*/

        $rowCount=0;
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            
             $color_data = $colors[$k];
            
            foreach ($row as $key => $value)
            {	/* Based on OS Apply Encoding */
				if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
				{     
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("&nbsp;", "", $value) ;
					
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);
					$value = str_replace("’", "'", $value) ;
					$value = str_replace("‘", "'", $value) ;
					
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}else{
				       $value = str_replace("’", "'", $value);
				       $value = str_replace("‘", "'", $value) ;					
				}
				//$value=str_replace("_x0019_","'",$value);
				
				
				//echo $value."-".$col."-".$rowCount;
				
				if($color_data[$key+1] == ""){
						$color_data[$key+1]=$color_data[$key-1];
			    }
				
				if($rowCount == 0 || $rowCount == 1 || $rowCount == 2){
					$stylArr2 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => str_replace("000000","FFFFFF",substr($color_data[$key+1], 2)))));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr2);
					
				}else{
					$stylArr1 = array('fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => str_replace("000000","FFFFFF",substr($color_data[$key+1], 2)))));
					$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);	
				}	
				
				//exit;
				
				if($rowCount == 0)
				    $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), intval($value)+1);
				else 
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

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
       
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('3')->getFont()->setBold(true);
        
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
         
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
	
	/*function get_data_string($text){
	   $alltags="";
       for($i=0;$i<strlen($text);$i++){
        if($text[$i] == "<"){
		  $alltags .= $text[$i];
		  for($j=$i+1;$j<strlen($text);$j++){
		  $alltags .= $text[$j]; 
		  if($text[$j] == ">"){
			  $t1="~";
			 for($z=$j+1;$z<strlen($text);$z++){ 
			   if($text[$z] == "<"){
			      break;
			   }
			     $t1.= $text[$z];
			 } 
			 if($j < strlen($text)-1) 
		      $alltags .= $t1."~";
		     break;
	        }	  
	      }
	    
	   }		
     }
     //echo $alltags;exit;
     return (($alltags));	
	}*/

   /* get_data_string_2 function
   * 
   *  Html content in specific manner. Each tags should come in a row.
   *  <p> hello </p> should convert to
   *  <p>
   *   hello
   *  </p>
   *  @param $text - html content of input xlsx file cell, 
   *  @return $alltags - generate content tag of specific type 
   *  
   */	 
	
	function get_data_string_2($text){
		$alltags="";
		for($i=0;$i<strlen($text);$i++){
		   if($text[$i] == ">" && $text[$i+1] == " " && $text[$i+2] == "<")
		     $alltags .= ">"; 
		   elseif($text[$i] == "<")
		     $alltags .= "~".$text[$i];
		   elseif($text[$i] == ">")
		     $alltags .= $text[$i]."~";
		   else    
		     $alltags .= $text[$i];	
		}
		return trim(str_replace("~~","~",$alltags));
	}	
	 
	 
   /* get_tags_string function
   * 
   *  Extract only tags and remove all the content form html content.
   *  @param $text - html content of input xlsx file cell, 
   *  @return $alltags - content html tags without content on it,
   *  
   */	 
	 
	function get_tags_string($text){
	   $alltags="";
       for($i=0;$i<strlen($text);$i++){
		  //if($text[$i] == "|")
           //  $alltags .= $text[$i];		   
         if($text[$i] == "<"){
		    $alltags .= $text[$i];
		    for($j=$i+1;$j<strlen($text);$j++){
		       $alltags .= $text[$j]; 
		       if($text[$j] == ">"){
			     if($j < strlen($text)-1) 
		            $alltags .= "|";
		         break;
	           }	  
	       }
	    }		
      }	
     return trim($alltags); 
   }	 


   /* special_character_function function
   * 
   *  take care of special characters
   * 
   *  @param $value - string 
   *  @return $values - string,
   *  
   */	
	 
	function special_character_function($value){
	
	      if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                {     
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("&nbsp;", "", $value) ;
					
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);
					$value = str_replace("’", "'", $value) ;
					$value = str_replace("‘", "'", $value) ;
					
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}else{
				       $value = str_replace("’", "'", $value);
				       $value = str_replace("‘", "'", $value) ;					
				}		
			return $value;	
		
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
 		   //$fill=str_replace("C", "0",$fill) ;
    	   //header will/should be in row 1 only. of course this can be modified to suit your need.
           $arr_color_fill[$row][$column] = $fill;
          	       
        }
        return $arr_color_fill;
        
	} 
	
?>
