<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']) && isset($_GET['ref']))
    odownloadXLS($_GET['file'], BASH_WRITER_FILE_PATH . "/" . $refs[$_GET['ref']-1], "index.php") ;

if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ref = $_REQUEST['index_reference'];
    $writexls = ($_REQUEST['op']=='xls') ? 'oWriteXLS' : 'writeXlsxBASH' ;

        $ext = $file1['extension'];

        if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
        {
            if($file1['extension'] == 'xlsx')
            {
                $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
                $results  = process($xls1Arr[0], 2, 0, $ref) ;
            }
            else
            {
                $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
                $results  = process($xls1Arr[0], 2, 1, $ref) ;
            }
            
            
            
            echo "<pre>";print_r ($results);
            
            //exit;
            
            
            $file_name = "Bash_" . date('YmdHis') . "." . $_REQUEST['op'] ;
            $file_path = BASH_WRITER_FILE_PATH . "/" . $refs[$ref-1] . "/" . $file_name ;

            if ($writexls($results[0], $file_path))
                header("Location:index.php?msg=success&file=".$file_name."&ref=".$ref);
            else
                header("Location:index.php?msg=error");
        }
}
else
    header("Location:index.php");

    function process($data, $start, $xls, $ref)
    {
        global $global_parents_data,$global_parents_data_file;

	// array for reference directories
        $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

	// Writer file reference directory        
	$refdir = BASH_WRITER_FILE_PATH."/".$refs[$ref-1]."/";
        if (!is_dir($refdir))
        {
            mkdir($refdir);
            chmod($refdir, 0777);
        }
        
	// Collecting references from writer files
        getAllParentsFromAllExcel($refs[$ref-1], 1, $ref+1, BASH_CLIENT_URL, BASH_WRITER_FILE_PATH);
        

        $products =   array();

	// Updating data array keys
        $sheetcount = 1;
        foreach ($data as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $datas[$sheetcount-1][$key][0] = '' ;
                foreach ( $dataArr as $idx=>$col ) :
                    $datas[$sheetcount-1][$key][$idx] = $col ;
                endforeach ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
        $sheetcount = 1;
        foreach ($datas as $xlsArr1) {
            $key = $xls ;
            foreach ( $xlsArr1 as $col ) :
		// processing rows - excluding header
                if($key>$xls)
                {
                    $col_ref = trim($col[$ref]);
                    $pattern = BASH_IMAGE_PATH.'/*/'.$col_ref.'*.*';

                    $arraySource = glob($pattern,GLOB_BRACE);
                    sort($arraySource);
                    //$arraySource = str_replace(GALERIES_LAFAYETTE_IMAGE_PATH, GALERIES_LAFAYETTE_CLIENT_URL, $arraySource) ;
                    
                    if(count($arraySource)>0)
                    {
                        $path = pathinfo($arraySource[0]) ;//echo $col_ref;print_r($path);
                        if(!in_array($col_ref, $global_parents_data))
                        {
                            $datas[$sheetcount-1][$key][0] = BASH_CLIENT_REF_URL . $col_ref ;
                        }
                        elseif(in_array($col_ref, $global_parents_data))
                        {
                            $datas[$sheetcount-1][$key][0] = 'Doublon' ;
                        }
                        $pdct_id = $key;
                    }
                    elseif($pdct_id && empty($col_ref))
                        $datas[$sheetcount-1][$key][0] = 'Doublon-'.($pdct_id + 1);
                    else
                    {
                        $datas[$sheetcount-1][$key][0] = 'NA';
                        $pdct_id = '';
                    }
                    //$datas[$sheetcount-1][$key][12] = '' ;
                    $datas[$sheetcount-1][$key][12] = "#Formula";
                }
                else {
                    $datas[$sheetcount-1][$key][0] = 'Url' ;
                    //$datas[$sheetcount-1][$key][12] = 'Descriptif produit' ;
                    $datas[$sheetcount-1][$key][12] = utf8_decode('Nombre de caractères') ;
                }
                $key++;
                
                unset($datas[$sheetcount-1][$key][6]);
                unset($datas[$sheetcount-1][0][6]);
                
            endforeach ;
            
            
            
            $sheetcount++;
        }//exit;
        //echo '<pre>'; print_r($datas);exit;
        
        return $datas ;
        
/*
	// Updating data array keys again to add url in header (Not recommended :D )
        $sheetcount = 1;
        foreach ($datas as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $results[$sheetcount-1][$key] = $dataArr ;
                if($key == $xls)
                    $results[$sheetcount-1][$key][0] = 'Url';
                $key++;
            endforeach ;
            $sheetcount++;
        }  
        echo '<pre>'; print_r($results);exit; 
        return $results ;*/
    }
    
    
    
    /* writeXlsxBASH function
     *
     * will writer finaly $data array in xslx file. 
     * @param $data - array contain data 
     * @param $file_path - file path for XSLX
     * @param  $anchor_cols - default value as null
     * 
     */ 
    
    function writeXlsxBASH($data,$file_path, $anchor_cols=null)
    {
        $anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");
        
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        
        foreach ($data as $row)
        {
            $col = 'A';
            $old_col="";            
            foreach ($row as $key => $value)
            {                
				
				if($value == "#Formula" && $rowCount > 0){ echo $old_col.($rowCount+1);
				   $formula = "=LEN(".$old_col.($rowCount+1).")";
				   $value = $formula;
     			 }   
				
                $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                //if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                if((strstr($value, "http://clients.edit-place.com/excel-devs/")) || (in_array($col, $anchorCols) && !empty($value) && ($rowCount>0)))
                {

					   
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $old_col = $col;
                $col++;
            }
            $rowCount++;
        }
             // echo "<pre>";print_r($data); exit;
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;  //echo "<pre>";print_r($data);exit($file_path);
        
        if(file_exists($file_path))
            return true ;
    }
    
?>
