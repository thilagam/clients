<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if($_GET['action']=='download' && isset($_GET['file']))
    odownloadXLS($_GET['file'], MISC_TAGVALIDATION_FILE_PATH, "tagvalidation.php") ;

if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $_REQUEST['op'] = 'xlsx';
    $writexls = 'writeXlsx' ;
    $reference = $_REQUEST['reference'];
    
	// tags array
    foreach(explode(",",$_REQUEST['tags']) as $tag) $tags[]=strtolower(trim($tag));
    $tags = array_unique(array_filter($tags));

    if(($file1['extension']=='xls' || $file1['extension']=='xlsx'))
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
            $xlsE1 = 0;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
            $xlsE1 = 1;
        }

	// Getting color codes from the source uploaded
        $bgColors = getMainExcelBgColor($_FILES['userfile1']['tmp_name'], array('A', 'B','C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'), sizeof($xls1Arr[0][0]), $kw_reference) ;

        $results  = process($xls1Arr[0], 2, $xlsE1, $bgColors, $reference, $tags) ;
        
        $file_name = uniqid() . "_tag." .$_REQUEST['op'] ;
        $file_path = MISC_TAGVALIDATION_FILE_PATH . "/" . $file_name ;

        if ($writexls($results, $file_path, $xls1Arr[1], $bgColors))
            header("Location:tagvalidation.php?msg=success&file=".$file_name."&client=MISC");
        else
            header("Location:tagvalidation.php?msg=error");
    }
}
else
    header("Location:tagvalidation.php");

    function process($data1, $start, $xls1, $bgColors, $reference, $tags)
    {
        $sheetcount = 1;
        foreach ($data1 as $data_)
        {
            if($sheetcount==1)
            {
                $key = 1 ;
                foreach ( $data_ as $dataArr ) :
                    foreach ( $dataArr as $idx=>$col ) :
                        $datas[$sheetcount-1][$key][$idx-1] = trim($col) ;
                    endforeach ;
                    if($key>$xls1)
                    {
			foreach($reference as $refrnce)
                        	if(!checkTagValid($tags, $datas[$sheetcount-1][$key][$refrnce-1]))
                                	$err[$key."|".($refrnce-1)] = 1;
                    }
                    $key++;
                endforeach ;
                $sheetcount++;
            }
        };
        
        return array($datas, $err) ;
    }

	// Checking all the tags present in the content
    function checkTagValid($tags, $string)
    {
	$valid=1;
	foreach($tags as $tag)
	{
		// Not considering anchor tag
	    if((substr_count($string, '<'.$tag) != substr_count($string, '</'.$tag.'>') && $tag=='a') || ((substr_count($string, '<'.$tag.'>') != substr_count($string, '</'.$tag.'>')) && $tag!='a'))
	    {
		$valid=0;
	    }
	}
	return $valid;
    }

    function getMainExcelBgColor($file, $cols, $rowCount, $kw_reference)
    {
        foreach ($kw_reference as $kwd=>$kw_ref)
            $kwrefs[] = $kw_ref ;
        
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(false);
        $objPHPExcel = $objReader->load($file);
        
        for ($i=1; $i <= $rowCount; $i++) {
            foreach ($cols as $col) {
                $bgColors[$col.$i] = $objPHPExcel->getActiveSheet()->getStyle($col.$i)->getFill()->getStartColor()->getRGB() ;
            }
        }
        return $bgColors ;
    }
    
    function oxlsxRead($file)
    {
        require_once INCLUDE_PATH . '/PHPExcel.php';
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
        
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
                        $xls_array[$i][$x][$y] = cleanString($xlsArr1[$i][$x][$y-1]) ;
                        
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname);
    }

    function writeXlsx($datas1,$file_path,$sheetnames,$bgColors)
    {
        $datas = $datas1[0];
        $err = $datas1[1];
        
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php'; 
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");

        $sheetIndex = 0 ;
        foreach ($datas as  $sheet_cnt=>$data)
        {
            $objPHPExcel->createSheet();
            // Add some data
            $objPHPExcel->setActiveSheetIndex($sheetIndex) ;
            $sheet_name=$sheetnames[$sheet_cnt];
            
            $rowCount=0;
            
            foreach ($data as $row)
            {
                $colIdx = 1;
                $col = 'A';
                foreach ($row as $key => $value)
                {
                    $value= (str_replace("’", "'", $value)) ;
                    $value = str_replace("", "œ", $value) ;
                    $value = str_replace("", "'", $value) ;
                    $value = str_replace("", "'", $value) ;
                    $value_l = strtolower($value) ;

		    $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);

                    if( $bgColors[$col.($rowCount+1)] && ($bgColors[$col.($rowCount+1)] != '000000') )
                    {
                        $objPHPExcel->getActiveSheet()->getStyle($col.($rowCount+1))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' => $bgColors[$col.($rowCount+1)])));
                    }
                    
                    if($err[($rowCount+1)."|".$key])
                    {
                        $objPHPExcel->getActiveSheet()->getStyle($col.($rowCount+1))->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'FFA500'))) ;
                    }
                        
                    $col++;
                    $colIdx++;
                }
                $rowCount++;
            }//exit;
            $sheetIndex++ ;
            // Rename sheet
            $objPHPExcel->getActiveSheet()->setTitle($sheet_name);
            $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true)->setSize(11);
        }
        //echo "<pre>";print_r($error_cells);exit;
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }
?>
