<?php
/**
 * Swap Dev :- Enterchange Colums value as per the input Ex: B to A
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
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
error_reporting(1);
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
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext=$file1['extension'];
    
  
    if($ext == 'xlsx')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   DISTRICENTER_PATH."/tempfiles/".$_FILES['userfile1']['name'] ;
		  move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;
		  chmod($srcFile, 0777);

        
 /* Reading Data from EXCELs file */       
         
		 require_once (INCLUDE_PATH."/PHPExcel/IOFactory.php");
	
         $objPHPExcel = PHPExcel_IOFactory::load($srcFile);
         $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
         foreach ($cell_collection as $cell) {
         $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
         $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
         $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
         $arr_data[$row][$column] = $data_value;
        }

         

		// echo "<pre>";
		// print_r ($arr_data);		
		// echo "</pre>";
				  
		$new_arr_data=array();
        $i=1;
        foreach($arr_data as $key=>$d){
   
            /*if($i > 1){
				$dc = "=LEN(B$i)";
                $de = "=LEN(D$i)";
			}*/
 
 
   // Associate Array for exchanging the value between columns   
           $new_arr_data[] = array(
		         'A' => $d['B'],
                 'B' => $d['C'],
                 'C' => $d['E'],
                 'D' => $d['G'],
                 //'E' => $de,
                 //'F' => $d['G'],
            );
           $i++;	
        } 
          
	
        		//  echo "<pre>"; print_r ($new_arr_data); echo "</pre>";
				  
				  
				include_once INCLUDE_PATH.'/PHPExcel.php';
				  include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';  
				  
$date = date("Y-m-d-H-i-s",time());
$newfileName = "Delivery_Districenter_".$date.".xlsx";
		
/* Writing data in EXCEL */		
		  
$excel = new PHPExcel; 
$list = $excel->setActiveSheetIndex(0);
$rowcounter = 1; 
foreach($new_arr_data as $key=>$d){
  $chr = "A";
  foreach($d as $d1){
     //echo $key."-".$chr."-".$d1."#";
     //echo $chr.$rowcounter;
      //echo $d1;	 
	  $list->setCellValue($chr.$rowcounter,$d1);
	$chr++;
  }
  $rowcounter++;
  echo "<br />"; 
}
$writer = new PHPExcel_Writer_Excel2007($excel);
$writer->save(DISTRICENTER_PATH."/tempfiles/".$newfileName); 

	
            /*    $arr_copy =  $basiclib->xlsx_read($srcFile);
	                
	              for($i=0;$i<sizeof($arr_copy[0][0]);$i++){
					  	
					     if($i > 0){
	                     $arr_copy[0][0][$i][4] = '=LEN(C3)';
						 $arr_copy[0][0][$i][6] = '=LEN(E5)';
                        }
						unset($arr_copy[0][0][$i][1]);
						unset($arr_copy[0][0][$i][8]);
					  	unset($arr_copy[0][0][$i][9]);
					  	unset($arr_copy[0][0][$i][10]);
					  	unset($arr_copy[0][0][$i][11]);
					  	unset($arr_copy[0][0][$i][12]);
					  	unset($arr_copy[0][0][$i][13]);
					  	
					  				       
					}
			 */       
			     unlink($srcFile);   
                  
                /*				  
			      echo "<pre>";
			      print_r ($arr_copy);	
			      echo "</pre>";
			       $date = date("Y-m-d-H-i-s",time());
			       $newfileName = "Delivery_Districenter_".$date.".xlsx";
			       $basiclib->writeXlsx($arr_copy[0][0],DISTRICENTER_PATH."/tempfiles/".$newfileName);
				 */
				 /*
                  if($_POST["op"] == "xlsx"){ 				 
                       $newfileName = "Delivery_Districenter_".$date.".xlsx";
			           $basiclib->writeXlsx($arr_copy[0][0],DISTRICENTER_PATH."/tempfiles/".$newfileName);
	               }elseif($_POST["op"] == "xls"){
					   $newfileName = "Delivery_Districenter_".$date.".xls";
			           write_xls($arr_copy[0][0],DISTRICENTER_PATH."/tempfiles/".$newfileName);
				   }else{
					   header("Location:generate_delivery_file.php?msg=file_error");
				   }
				  */ 
			   header("Location:generate_delivery_file.php?msg=success&file=".$newfileName);		
    }
    else
    {
            header("Location:generate_delivery_file.php?msg=file_error");
    }
    	
}
else
    header("Location:generate_delivery_file.php");
	
?>

