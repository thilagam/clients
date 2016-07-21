<!--<meta http-equiv="refresh" content="5" />-->
<?php
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH", ROOT_PATH . "/includes");

include_once (INCLUDE_PATH . "/session.php");
include_once (INCLUDE_PATH . "/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php") ;

$wfiles=glob(CAROLL_PATH."/writer-files/*");
//echo "<pre>"; print_r($wfiles);

	$l=1;$mm=1;
	foreach($wfiles as $idx=>$wfile)
	{
		$fileInfo = pathinfo($wfile) ;//&& ($l<501)
		if(($fileInfo['basename']!=".") && ($fileInfo['basename']!="..") && !file_exists(CAROLL_PATH."/writer-filesx/".$fileInfo['basename']."x")  )
		{
			// && (filesize($wfile)<130000)
			/*if($fileInfo['extension']=='xls')
				swriteXlsx(oxlsRead($wfile), CAROLL_PATH."/writer-filesx/".$fileInfo['basename']."x");
			elseif($fileInfo['extension']=='xlsx')
				copy($wfile, CAROLL_PATH."/writer-filesx/".$fileInfo['basename']);*/
				
//if($fileInfo['extension']=='xls'){
			echo $l . ". " . $wfile . "(". filesize($wfile) . ")<br>" ;//exit(CAROLL_PATH."/writer-filesx/".$fileInfo['basename']."x");
			$l++;//exit;
//}
		}else{
			echo $mm . " --> " . $wfile . "(". filesize($wfile) . ")<br>" ;
			$mm++;
		}
	}

    function oxlsRead($file)
    {
        require_once(INCLUDE_PATH."/reader.php");
        
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252') ;
        $data->read($file);
        $bound_sheets=$data->boundsheets;
        $sheets = sizeof($data->sheets);

        for ($i = 0; $i < $sheets; $i++)
        {
            $sheetname[$i]=$bound_sheets[$i]['name'];
            if (sizeof($data->sheets[$i]['cells'])>0)
            {
                $x = 1;
                while ($x <= sizeof($data->sheets[$i]['cells']))
                {
                    $y = 1;
                    while ($y <= $data->sheets[$i]['numCols'])
                    {
                        $data->sheets[$i]['cells'][$x][$y] = convert_smart_quotes($data->sheets[$i]['cells'][$x][$y]) ;
                        $xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';

                        if(strlen($xls_array[$i][$x][$y])>strlen(utf8_decode($xls_array[$i][$x][$y])))
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        else
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? utf8_encode($xls_array[$i][$x][$y]) : '';
                            
                        $xls_array1[$i][$x-1][$y-1] = utf8_decode($xls_array[$i][$x][$y]) ;
                            
                        $y++;
                    }
                    $x++;
                }
            }
        }
        //echo "<pre>"; print_r($xls_array1[0]); exit($file);
        return $xls_array1[0] ;
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
                $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                if( (strstr($value, "http://clients.edit-place.com") || strstr($value, "http://korben.edit-place.com"))  && ($rowCount>0) )
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
            }
            foreach ($wdth as $key => $value)
                $objPHPExcel->getActiveSheet()->getStyle($key . ($rowCount + 1))->applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);

            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }
?>
