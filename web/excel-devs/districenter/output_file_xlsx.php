<?php
/**
 * Districenter Doublon Dev to check images and doublon.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Apr 23, 2015
 */
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH.'/html_to_doc.inc.php');
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");



if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext=$file1['extension'];
    
  
    if($ext == 'xlsx' || $ext == 'xls')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   DISTRICENTER_PATH."/tempfiles/".$_FILES['userfile1']['name'] ;
		  move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;
		  chmod($srcFile, 0777);

         $image_array = array();

          $l=0;
          //$path = DISTRICENTER_IMAGE_PATH;
          
/* Bring Image from FTP
 * and explode them with -
 */
          
          if ($handle = opendir(DISTRICENTER_IMAGE_PATH."/")) {
            while (false !== ($entry = readdir($handle))) {
	            if($entry != "." && $entry != ".." ){
										
					if ($handle_i = opendir(DISTRICENTER_IMAGE_PATH."/".$entry)) {
					    while (false !== ($entry_i = readdir($handle_i))) {
					        if($entry_i != "." && $entry_i != ".." ){
							     //echo $entry_i;
								 //$image_ref1 = explode(".",$entry_i);
							     $image_ref  =   explode("-",$entry_i);
					             $image_array[$l] = $image_ref[0]; 
					             $l++; 
					        }
					       					
					    }closedir($handle_i);
				    }
                }
            }closedir($handle);		
	    }
		
		 //print_r ($image_array);
		 
/* Reading Data from EXCELs file */		 
		
	require_once (INCLUDE_PATH."/PHPExcel/IOFactory.php");
	
	$objPHPExcel = PHPExcel_IOFactory::load($srcFile);
    $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
    foreach ($cell_collection as $cell) {
    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
 
    //header will/should be in row 1 only. of course this can be modified to suit your need.

        $arr_data[$row][$column] = $data_value;
    }
    
    /* echo "<pre>";
    print_r($arr_data);
    echo "</pre>"; */
	

/* Specify the formula for excel sheet, 
 * create url for images at column A if not found NA
 * Swaping column values as per writer files.
 */

$new_arr_data=array();
$i=1;
foreach($arr_data as $key=>$d){
  if($i == 1){ // specify additional header for writer files.
	$dc = "Max 40 car";
    $de = "Entre 300 et 370 car";	
    $da = "URL"; 	
  }else{
	$dc = "=LEN(C$i)";
    $de	= "=LEN(E$i)";
	                   if(in_array($d['A'],$image_array)){
						   $da = DISTRICENTER_CLIENT_REF_URL.$d['A'];
					   }else{
						   $da = "NA";
					   }
  }
  
  // Associate Array for exchanging the value between columns
   $new_arr_data[] = array(
        'A' => $da,
		'B' => $d['A'],
        'C' => $d['I'],
        'D' => $dc,
        'E' => $d['J'],
        'F' => $de,
        'G' => $d['B'],
        'H' => $d['C'],
        'I' => $d['D'], 
        'J' => $d['E'], 
        'K' => $d['F'],
        'L' => $d['G'],  
        'M' => $d['H'],
        'N' => $d['K'],   
    );
  $i++;	
}

//echo "<pre>"; //print_r($header); print_r($new_arr_data); echo "</pre>"; exit;
	    		 

/* Bring old reference in array 
 * Specify Doubloon if found at 1st column
 * set header as URL
 */

//echo "Next Array";

                  $new_arr_data1=array();				  
			        
                  $arraySource = glob(DISTRICENTER_WRITER_FILE_PATH."/*.xlsx");
                  
                  //print_r ($arraySource);
                  
                   usort($arraySource, function($a, $b) {  // short old writer file array with name.
                      return filemtime($a) > filemtime($b);
                   });
			        
			        $arr_wt = array();
			        foreach ($arraySource as $excelFile) {
					
					   $arr_w =  $basiclib->xlsx_read($excelFile);
					   
					   
					   
					   for($i=1;$i<sizeof($arr_w[0][0]);$i++){
						  $arr_wt[$i] = $arr_w[0][0][$i][2];
					   }
							
					   }	
						//echo count($new_arr_data);exit;	
									  
						//exit;
						$j=1;
						$da="";
						foreach($new_arr_data as $key=>$d){
							$da="";
							//echo $d['A']; 
							if(in_array($d['B'],$arr_wt)){
								
								
								if($d['A'] != "NA" && $d['A'] != "URL"){
									    $da = "Doublon:- ".pathinfo($excelFile,PATHINFO_FILENAME); 	 // Bring file name where doubloon is found.     
								}
								//else{
								//	echo $d['A'] = $d['A'];
							//	}
								
							}else{
							  $da = $d['A'];	
							}	
							
							 if($j == 1){
	                            $da = "URL";
                              }
							  
							  // Associate Array for exchanging the value between columns
                            $new_arr_data1[] = array(
                                'A' => $da,
		                        'B' => $d['B'],
                                'C' => $d['C'],
                                'D' => $d['D'],
                                'E' => $d['E'],
                                'F' => $d['F'],
                                'G' => $d['G'],
                                'H' => $d['H'],
                                'I' => $d['I'], 
                                'J' => $d['J'], 
                                'K' => $d['K'],
                                'L' => $d['L'],  
                                'M' => $d['M'],
                                'N' => $d['N'],   
                            ); 
                            $j++;	
						}	
						
			       
			       
			       unlink($srcFile);
			      			        
//echo "hello <pre>"; print_r ($new_arr_data1); echo "</pre>"; exit; 
	
				  
			      $date = date("Y-m-d-H-i-s",time());
			      $newfileName = "Writers_Districenter_".$date.".xlsx";
			      
			   
/* Writing data in EXCEL */			     
			      
				  include_once INCLUDE_PATH.'/PHPExcel.php';
				  include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
				  
				  $excel = new PHPExcel; 
                  $list = $excel->setActiveSheetIndex(0);
                   $rowcounter = 1; 
                  
				  echo sizeof($new_arr_data1); 
				  
                   if(sizeof($new_arr_data1) == 0){
   				    foreach($new_arr_data as $key=>$d){
                        $chr = "A";
                        foreach($d as $d1){
                           //echo $key."-".$chr."-".$d1."#";
                           //echo $chr.$rowcounter; 
						   
                        
							    $list->setCellValue($chr.$rowcounter,$d1); 
						  						   
						   
						   //echo $d1;
						   if(strstr($d1,"http://clients.edit-place.com/excel-devs/") && $chr = 'A'){
							   //echo "set".$d1;
							   $list->getCell($chr.$rowcounter)->getHyperlink()->setUrl($d1); // Creating link for column A 
						   }
                          $chr++;
                        }
                     $rowcounter++;
                    }
				   }else{
					foreach($new_arr_data1 as $key=>$d){
                        $chr = "A";
                        foreach($d as $d1){
                           //echo $key."-".$chr."-".$d1."#";
                           //echo $chr.$rowcounter; 
                           
						   $list->setCellValue($chr.$rowcounter,$d1); 
						   
						   
						   if(strstr($d1,"http://clients.edit-place.com/excel-devs/") && $chr = 'A'){ 
							    //echo "set".$d1;
							   $list->getCell($chr.$rowcounter)->getHyperlink()->setUrl($d1); // Creating link for column A 
						   }
                          $chr++;
                        }
                     $rowcounter++;
                    }   
					   
				   }	

                    $writer = new PHPExcel_Writer_Excel2007($excel);
                    $writer->save(DISTRICENTER_WRITER_FILE_PATH."/".$newfileName); 
		      
			        header("Location:index.php?msg=success&file=".$newfileName);	
    }
    else
    {
            header("Location:index.php?msg=file_error");
    }
    	
}
else
    header("Location:index.php");
	
?>
