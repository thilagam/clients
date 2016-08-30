<?php
    ob_start();
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
    define("INCLUDE_PATH",ROOT_PATH."/includes");
    
    include_once(INCLUDE_PATH."/config_path.php") ;
    include_once(INCLUDE_PATH."/common_functions.php") ;

    if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
        odownloadXLS($_GET['file'], MISC_COPIERCOLLER_FILE_PATH, "copiercoller.php") ;

    if(isset($_POST['submit']))
    {
        $input_reference = $_REQUEST['input_reference'];
        $output_reference = $_REQUEST['output_reference'];
        $copied_reference = $_REQUEST['copied_reference'];
        $translation_text = $_REQUEST['translation_text'];
        
        $file1  =   pathinfo($_FILES['userfile1']['name']) ;
        $file2  =   pathinfo($_FILES['userfile2']['name']) ;
        $ext1 = $file1['extension'] ;
        $ext2 = $file2['extension'] ;
        $writexls = 'swriteXlsx';

        if(($ext1=='xls' || $ext1=='xlsx') && ($ext2=='xls' || $ext2=='xlsx'))
        {
		if($ext1 == 'xlsx')
		{
			$xls1Arr  = sxlsxRead($_FILES['userfile1']['tmp_name']) ;
			$xls1 = 0;
		}
		else
		{
			$xls1Arr  = sxlsRead($_FILES['userfile1']['tmp_name']) ;
			$xls1 = 1;
		}
		
		if($ext2 == 'xlsx')
		{
			$xls2Arr  = sxlsxRead($_FILES['userfile2']['tmp_name']) ;
			$xls2 = 0;
		}
		else
		{
			$xls2Arr  = sxlsRead($_FILES['userfile2']['tmp_name']) ;
			$xls2 = 1;
		}
			
		$results  = process($xls1Arr[0], $xls2Arr[0], $xls1, $xls2, $input_reference, $output_reference, $copied_reference, $translation_text) ;
		
		$file_name = "copier_coller_" . date('YmdHis') . ".xlsx" ;
		$file_path = MISC_COPIERCOLLER_FILE_PATH . "/" . $file_name ;

		if ($writexls($results[0], $file_path))
			header("Location:copiercoller.php?msg=success&file=".$file_name);
		else
			header("Location:copiercoller.php?msg=error");
        }
    }
    else
        header("Location:copiercoller.php");


    function process($data1, $data2, $xls1, $xls2, $input_reference, $output_reference, $copied_reference, $translation_text)
    {
	// Looping through first file array for all translation text(input reference as key and translation text as value)
        $sheetcount = 1;
        foreach ($data1 as $data_) {
            $key = $xls1 ;
            foreach ( $data_ as $dataArr ) :
                foreach ( $dataArr as $idx=>$col ) :
                    $datas[$sheetcount-1][$key][$idx-1] = trim($col) ;
                endforeach ;
                if($key > $xls1)
                {
                    $transText[$datas[$sheetcount-1][$key][$input_reference-1]] = $datas[$sheetcount-1][$key][$translation_text-1] ;
                    foreach(array_filter(array_map("trim", explode(",", $datas[$sheetcount-1][$key][$copied_reference-1]))) as $cpRef)
						$transText[$cpRef] = $datas[$sheetcount-1][$key][$translation_text-1] ;
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
	// Updating translated text to last column for each reference in ouput file
        $sheetcount = 1;
        foreach ($data2 as $data_) {
            $key = $xls2 ;
            foreach ( $data_ as $dataArr ) :
                foreach ( $dataArr as $idx=>$col ) :
                    $results[$sheetcount-1][$key][$idx-1] = trim($col) ;
                endforeach ;
                if($key > $xls2)
                {
			$results[$sheetcount-1][$key][] = $transText[$results[$sheetcount-1][$key][$output_reference-1]] ;
                }
                else
			$results[$sheetcount-1][$key][] = "Translation text" ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
        return $results ;
    }

    function swriteXlsx($data,$file_path, $anchor_cols=null)
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

		$stylArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '2C2B2B')), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => '000000'))), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CFE7F5', )), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

		$stylHeadArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '8E8A8A', )), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF'))), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $wdth[$col] = 1;
                $col++;
            }
            $rowCount++;
        }

        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                $col++;
            }
            foreach ($wdth as $key => $value)
                $objPHPExcel->getActiveSheet()->getStyle($key . ($rowCount + 1))->applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);

            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }
?>
