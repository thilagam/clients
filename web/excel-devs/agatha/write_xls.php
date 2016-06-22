<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
    odownloadXLS($_GET['file'], COMPTOIR_WRITER_FILE_PATH, "index.php") ;
else if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['zip']))
    odownloadZIP($_GET['zip'], COMPTOIR_WRITER_FILE_PATH, "index.php");

if(isset($_POST['submit']))
{
    $ref = $_REQUEST['index_reference'];    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];

    if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = xlsx_read($_FILES['userfile1']['tmp_name']) ;
            //echo '<pre>@@';print_r($xls1Arr); exit('*****');
            $results  = process($xls1Arr[0], 3, 0) ;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
            $results  = process($xls1Arr[0], 4, 1) ;
            //echo '<pre>@@';print_r($results); exit('*****');
        }
        //echo '<pre>@@';print_r($xls1Arr); exit('*****');
       
       /* Old Code 
	   $file_name = str_replace(' ', '_', $file1['filename']) . "_" . uniqid() . ".xls" ;
        $file_path = COMPTOIR_WRITER_FILE_PATH . "/" . $file_name ;

        if (write_xls($results[0], $file_path))
            header("Location:index.php?msg=success&file=".$file_name) ;
        else
            header("Location:index.php?msg=error") ;
	   */
	   
	   /* New Code */
		if($_POST["op"] == "xls"){
            $file_name = str_replace(' ', '_', $file1['filename']) . "_" . uniqid() . ".xls" ;
            $file_path = COMPTOIR_WRITER_FILE_PATH . "/" . $file_name ;

            if (write_xls($results[0], $file_path))
                header("Location:index.php?msg=success&file=".$file_name) ;
            else
                header("Location:index.php?msg=error") ;
		}elseif($_POST["op"] == "xlsx"){
			$file_name = str_replace(' ', '_', $file1['filename']) . "_" . uniqid() . ".xlsx" ;
            $file_path = COMPTOIR_WRITER_FILE_PATH . "/" . $file_name ;
			
			if (writeXlsx($results[0], $file_path))
                header("Location:index.php?msg=success&file=".$file_name) ;
            else
                header("Location:index.php?msg=error") ;
		}else{
			    header("Location:index.php?msg=error") ;
		}
		
    }
}
else
    header("Location:index.php");


    function process($fileData, $start, $xls)    {
        
        
        $sheetcount = 0;//echo '<pre>';//print_r($fileData);exit;
        
        //$merge_columns_1=array(10,11,12,13,14,15);
        //$merge_columns_2=array(22,23,24,25,26,27);
        //$merge_columns_3=array(34,35,36,37,38,39);
        
        foreach($fileData as $sheetArray)
        {
            $i=$xls;    
            $key=0;
            foreach($sheetArray as $row)
            {
                $row=array_values($row);    
                //print_r($row);                
                if($i>=$start)
                {
                    $col=0;
                    $merge1=0;
                    foreach($row as $column)
                    {                                   
                        if($col==10)
                        {
                            $data[$sheetcount][$key][10]=trim($row[10].' '.$row[11].' '.$row[12].' '.$row[13].' '.$row[14].' '.$row[15]);
                        }
                        else if($col==22)
                        {
                            $data[$sheetcount][$key][22]=trim($row[22].' '.$row[23].' '.$row[24].' '.$row[25].' '.$row[26].' '.$row[27]);
                        }
                        else if($col==34)
                        {
                            $data[$sheetcount][$key][34]=trim($row[34].' '.$row[35].' '.$row[36].' '.$row[37].' '.$row[38].' '.$row[39]);
                        }
                        else if(($col>10 && $col<16)|| ($col>22 && $col<28)|| ($col>34 && $col<40)) 
                        {                       
                            $col++;
                            continue;
                        }   
                        else                        
                            $data[$sheetcount][$key][$col]=$column; 
                        
                        
                        $col++;
                        
                    }
                    $data[$sheetcount][$key]=array_values($data[$sheetcount][$key]);
                        
                        $key++;
                }
                $i++;
            }
            $sheetcount++;          
            
        }     
        
        
        //print_r($data);exit;
        
        
       
        return $data ;
    }
?>