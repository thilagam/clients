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
 * @since      1.0 Apr 13, 2015
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


function delete_col(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
        array_splice($v, $offset, 1);
    });
}

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

         $image_array = array();

          $l=0;
          //$path = DISTRICENTER_IMAGE_PATH;
          if ($handle = opendir(DISTRICENTER_IMAGE_PATH."/")) {
            while (false !== ($entry = readdir($handle))) {
	            if($entry != "." && $entry != ".." ){
										
					if ($handle_i = opendir(DISTRICENTER_IMAGE_PATH."/".$entry)) {
					    while (false !== ($entry_i = readdir($handle_i))) {
					        if($entry_i != "." && $entry_i != ".." ){
							     //echo $entry_i;
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

			      /*echo "<pre>";
			      print_r ($basiclib->xlsx_read($srcFile));	
			      echo "</pre>";*/
	
	              $arr =  $basiclib->xlsx_read($srcFile);
	                
	              for($i=0;$i<sizeof($arr[0][0]);$i++){
					  if($i == 0){	
						   array_unshift($arr[0][0][$i],"URL");					  
					   }else{					 	  			    
					    if(in_array($arr[0][0][$i][1],$image_array)){					    
						   array_unshift($arr[0][0][$i],DISTRICENTER_CLIENT_REF_URL.$arr[0][0][$i][1]);
						}else{
						   array_unshift($arr[0][0][$i],"NA");
						}
					   }	
					   
					   unset($arr[0][0][$i][2]);					
					   unset($arr[0][0][$i][3]);					
					   unset($arr[0][0][$i][4]);					
					   unset($arr[0][0][$i][6]);					
					   unset($arr[0][0][$i][8]);					
					   unset($arr[0][0][$i][10]);					
					   unset($arr[0][0][$i][11]);
					   unset($arr[0][0][$i][13]);
					   unset($arr[0][0][$i][14]);
					   unset($arr[0][0][$i][19]);					
					}
			        
			        
			      /*echo "<pre>";
                  print_r ($arr);
                  echo "</pre>";*/
                  
                  
                  $arraySource = glob(DISTRICENTER_WRITER_FILE_PATH."/*.xlsx");
                  
                   usort($arraySource, function($a, $b) {
                      return filemtime($a) > filemtime($b);
                   });
                  
                  //print_r ($arraySource);
                  $arr_wt = array();
                  
                  foreach ($arraySource as $excelFile) {
					
					   $arr_w =  $basiclib->xlsx_read($excelFile);
					   
					   
					   
					   for($i=0;$i<sizeof($arr_w[0][0]);$i++){
						   
						  if($i>0){ 					   
					      
					        $arr_wt[$i] = $arr_w[0][0][$i][2];
					      
					        					      
					      }
					   }
									  
						//print_r ($arr_wt);
						
						for($i=0;$i<sizeof($arr[0][0]);$i++){
							 if($i>0){ 						
						        if(in_array($arr[0][0][$i][1],$arr_wt)){
									//if($arr[0][0][$i][0] != "NA"){
									      $arr[0][0][$i][0] = "Doublon:- ".pathinfo($excelFile,PATHINFO_FILENAME)." ".$arr[0][0][$i][0];
									//}
						           						        
						        }else{
								   	
								}
						        
						     }
					    }
									  
				    }
                  
                  echo "<pre>";
                  print_r ($arr);
                  echo "</pre>";
                  
                  $basiclib->writeXlsx($arr[0][0],DISTRICENTER_PATH."/tempfiles/"."new.xlsx");
					
			//header("Location:index.php?msg=success&file=swapped_".$file1['filename'].".zip");		
    }
    else
    {
            //header("Location:index.php?msg=file_error");
    }
    	
}
else
   // header("Location:index.php");
	
?>
