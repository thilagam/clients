<?php     /* 
		   * New Option To Read Xlsx, Write Xlsx 
		   * Modified on 5 MAY 2015 by Lavanya Pant
		   */
?>
<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");


if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
    odownloadCSV($_GET['file'], ARMAND_THIERY_WRITER_FILE_PATH, "index.php") ;

if(isset($_POST['submit']))
{
    require_once(INCLUDE_PATH."/reader.php");
    //print_r($_FILES);
    //echo $_FILES['xlsfile']['type'];
    //exit;
    
    if(($_FILES['csvfile']['type']=='text/comma-separated-values')||($_FILES['csvfile']['type']=='application/vnd.ms-excel')||($_FILES['csvfile']['type']=='text/csv'))
    {
        /***********Getting File1 Data**********************/
        $csv_array=array();
        $row = 1;
        if (($handle = fopen($_FILES['csvfile']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                if($row==2)
                    $cnt=count($data);
                    $num = count($data);
                //echo "<p> $num fields in line $row: <br /></p>\n";

                    for ($c=0; $c < $num; $c++) {
                        $csv_array[$row][$c]=$data[$c];
                    }
                    $row++;
                    
            }
            fclose($handle);
            
            $rows=count($csv_array);
            $cols=$cnt;
        }
        
        for($i=1;$i<=$rows;$i++)
        {
            for($j=0;$j<$cols;$j++)
            {
                $csv_final[$i][$j]=$csv_array[$i][$j];
            }
        }   
        
        //echo $rows."--".$cols;
        //echo "<pre>"; print_r($csv_final);echo "</pre>";exit;
        
        
    }
    if(($_FILES['xlsfile']['type']=='application/x-msexcel' || $_FILES['xlsfile']['type']=='application/vnd.ms-excel' || $_FILES['xlsfile']['type']=='application/xls' || $_FILES['xlsfile']['type']=='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ))
    {
		
		//echo $_FILES['xlsfile']['type']; exit;
        
        /***********Getting File1 Data**********************/
       $xls_array = array();
	   if($_FILES['xlsfile']['type']=='application/xls'){
		   
		   echo "xls";
		   
		$data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252');
        $data->read($_FILES['xlsfile']['tmp_name']);
                
        //echo "<pre>"; print_r($data->sheets[0]['cells']);echo "</pre>"; 
                
        if($data->sheets[0]['numRows'])
        {
            $x=1;
            while($x<=$data->sheets[0]['numRows']) {
                $y=1;
                while($y<=$data->sheets[0]['numCols']) {
                
                    $xls_array[$x][$y]=isset($data->sheets[0]['cells'][$x][$y]) ? $data->sheets[0]['cells'][$x][$y] : '';
                    
                                    
                    $y++;
                }
                $x++;
            }
			
			//echo "<pre>"; print_r ($xls_array);  echo "</pre>"; exit;
			 
            
        }
        else
        {
            header("Location:excel_upload.php?msg=file_error");
        }       
        //echo "<pre>"; print_r($xls_array);echo "</pre>";exit;
		   
		   
		   
	   }else{
		   
		   $xls_array_copy = oxlsxRead($_FILES['xlsfile']['tmp_name']);
		   //$xls_array = $xls_array_copy[0][0];
		   
		   // Create Array Similar to $xls_array ARRAY
		   $j=1;
		   for($i=0;$i<=sizeof($xls_array_copy[0][0]);$i++){
			  $xls_array[$j] = $xls_array_copy[0][0][$i];  			   
			  $j++;
		   }
		   //echo "<pre>"; print_r ($xls_array); echo "</pre>"; exit;
	   }
		
       
    }
    if(count($csv_final)>1 && count($xls_array)>1)  
    {
        foreach($csv_final as $key=>$records)
        {
            $xls_ids[$key]=trim($records[1]);
        }       
        
        foreach($xls_array as $xls_records)
        {
            
                $id=trim($xls_records[4]);
                                
                if(in_array($id,$xls_ids))
                {
                    $csv_key=array_search($id,$xls_ids);
                    /**gettng data of F to M and adding in csv in D to K**/
                    for($k=6;$k<=13;$k++)
                    {
                        $csv_final[$csv_key][$k-3]=str_replace(",",' ',$xls_records[$k]);
                        $csv_final[1][$k-3]=$xls_array[1][$k];
                        
                    }
                    
                    
                    
                }
            
        }
        
        
        if($_POST["op"] == "csv"){ 
           $info = pathinfo($_FILES['csvfile']['name']);
           WriteCSV($csv_final,$info['filename']);
        }else{ 
		   $info = ARMAND_THIERY_WRITER_FILE_PATH1."/".$_FILES['xlsfile']['name'];
           $info=str_replace(" ","_",$info);
           writeXlsx($csv_final,$info);
           //echo "<pre>"; print_r ($csv_final_copy); echo "</pre>"; exit;	
           
           $info1=str_replace("&","%26",$_FILES['xlsfile']['name']); // URL encoding of & to %26
           header("Location:excel_upload.php?msg=success&file=".$info1);
		}
		
		//echo "<pre>"; print_r($csv_final);echo "</pre>";exit;
    
    }
    else
    {
            header("Location:excel_upload.php?msg=file_error");
    }   
    
}
else
    header("Location:excel_upload.php");

/**Create XLS File**/

function WriteCSV($data,$name)
{
        
    $filename=str_replace(" ","_",$name);
    
	
	$path=ARMAND_THIERY_WRITER_FILE_PATH1."/".$filename.".csv";
    
    $fp = fopen($path, 'w+');

    foreach ($data as $fields) {
        fputcsv($fp, $fields,";");
    }
    fclose($fp);
    
    // save file to disk
    if (file_exists($path)) {
		$filename=str_replace("&","%26",$name); // URL encoding of & to %26
      //echo 'Spreadsheet successfully saved! <a href="?action=download&file='.$filename.'">Cick here to download</a>';
      header("Location:excel_upload.php?msg=success&file=".$filename.".csv");
    } else {
      //echo 'ERROR: Could not save spreadsheet.';
      header("Location:excel_upload.php?msg=error");
    }

}
?>
