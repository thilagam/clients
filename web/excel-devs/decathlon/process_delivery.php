<?php
/**
 * Decathlon Writer file Creates Multiple doc files from single xlsx file
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Vinayak Kadolkar
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
include_once(DECATHLON_PATH."/decathlon.php");

include_once("titles.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='zip'){
		odownloadZIP($_GET['file'], DECATHLON_WRITER_FILE_PATH."/dev3/", "dev1.php") ;
	}else
		odownloadXLS($_GET['file'], DECATHLON_WRITER_FILE_PATH."/dev3/".$_GET['folder']."/", "dev1.php") ;

if(isset($_POST['submit']))
{
	
    $writexls = ($_REQUEST['op']=='xls') ? 'WriteXLS' : 'writeXlsx' ;
    $columns[]="1";
    //$combiner=$_POST['comb'];
    $lang=($_POST['lang']!='')? $_POST['lang']: 'FR';
    //echo $lang;
	/*Create basic lib instance*/
    $basiclib=new basiclib();
    $decathlon=new decathlon();

    
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
        
       // echo "<pre>";print_r ($final_array); echo "</pre>"; exit;
      	//echo "<pre>";print_r ($fill_colors[1]); echo "</pre>";exit;
        

		
        /**
         * Check if array is not empty and process
         * */
        if(sizeof($final_array)>0)    
        {
          	
          	$rand="decathlon-".$lang."-".uniqid()."-".date('d-m-y-h-m');
    	    $srcPath=DECATHLON_WRITER_FILE_PATH."/dev3/".$rand."/";
			$srcFile=DECATHLON_WRITER_FILE_PATH."/dev3/".$rand.".zip";
//			$srcFileZip=HOTELS_WRITER_FILE_PATH2."/dev1/".$rand.".zip";
			
			mkdir($srcPath) ;
			chmod($srcPath,0777) ;
          	//echo $srcPath;exit;
            $info = pathinfo($_FILES['userfile1']['name']);
    	  
    	  	$header = array();
    	  	$header_2 = array();
    	  	$data = array();
    	  
    	  	$l=0;

			//echo "<pre>";print_r($final_array);exit;
   	   
    	   foreach($final_array as $key=>$arr){
	          //echo "<pre>";  
			 if($key>1){
	      	  //echo "<pre>";print_r($data);exit;
    	   	  $file_name=$srcPath.$arr[0]."-".$arr[1]."-".$arr[4].".txt";
    	   	  //echo $file_name."<br>";
    	   	  $text=$arr[17];
			  $textfile = create_textFile ($text,$file_name);  // create docx file using create_docx_file 
			  //echo $textfile."<br>";
			  //function
			 // $final_array[$key][0] = "http://clients.edit-place.com/excel-devs/decathlon/refdocs.php?client=decathlon&folder=".$rand."&file=".$docxfile;
			 // array_unshift($final_array[$key], "http://clients.edit-place.com/excel-devs/decathlon/refText.php?client=decathlon&folder=".$rand."&file=".$textfile);
			 
			  }
			  $l++;
		   }
		   $basiclib->zip_creation($srcPath, $srcFile,'txt');
		 //echo "<pre>";
		// print_r($titles);
		//	print_r ($final_array); echo "</pre>"; exit;
		    
		 // writeXlsxVenereNewDev1($final_array,$srcFile,$fill_colors); // call writer function here
		  // writeMultiSheets(array($final_array), $srcFile,array('sheet1') ,$sheetnames,$colors); 
            if(file_exists($srcFile)) {
			  header("Location:delivery.php?msg=success&folder=".$rand."&file=".$rand.".zip");
			} else {
				  header("Location:delivery.php?msg=error");
			}
            
        }
        else
        {
            header("Location:delivery.php?msg=file_error");
        }
    }	
}
else
    header("Location:delivery.php");

  
   
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
            if($k>1)
            {
	            $col = 'A';
	            $header_colors=array();
	            

			   //echo "Debug 4 $k<pre>"; print_r ($colors[$k]); echo "</pre>";	exit;
		       $header_colors = $colors[$k];
		      // echo "<pre>"; print_r($header_colors);
		       array_unshift($header_colors,'FFFFFFFF');
	              //echo "<pre>"; print_r($header_colors);exit;
	            
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
					
					//$value =str_replace("=","",$value);
				    if(substr($header_colors[$key],2) == "000000")
				       $cell_color = "FFFFFF";
				    elseif(empty($header_colors[$key]))
				       $cell_color = "FFFFFF";
				    else
				       $cell_color = substr($header_colors[$key],2);    
				      
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
	

	/**
	 * Function create_textFile
	 *
	 * @param
	 * @return
	 */		
	function create_textFile($str,$text_file)
	{
		$fh = fopen($text_file, 'w+');
        fwrite($fh, $str);
        fclose($fh);

        if (file_exists($text_file))
            return $text_file ;
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
	
	
?>
