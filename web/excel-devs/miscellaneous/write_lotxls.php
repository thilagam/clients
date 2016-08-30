<?php
    ob_start();
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
    define("INCLUDE_PATH",ROOT_PATH."/includes");
    
    include_once(INCLUDE_PATH."/config_path.php") ;
    include_once(INCLUDE_PATH."/common_functions.php") ;

    if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
        odownloadXLS($_GET['file'], MISC_CREATELOT_FILE_PATH, "createlot.php") ;

    if(isset($_POST['submit']))
    {
        $ref = $_REQUEST['index_reference'];
        $count = $_REQUEST['count1'];
        $opt = $_POST['opt'];
        $file1  =   pathinfo($_FILES['userfile1']['name']) ;
        $writexls = ($_REQUEST['op']=='xls') ? 'bWriteXLS' : 'writeXlsx' ;

        if($file1['extension']=='xls' || $file1['extension']=='xlsx')
        {
            $ext = $file1['extension'];
            if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
            {
                if($file1['extension'] == 'xlsx')
                {
                    $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
                    $results  = process($xls1Arr[0], 2, 0, $ref, $count, $opt) ;
                }
                else
                {
                    $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
                    $results  = process($xls1Arr[0], 2, 1, $ref, $count, $opt) ;
                }
                
                $file_name = "Lot_" . date('YmdHis') . "." . $_REQUEST['op'] ;
                $file_path = MISC_CREATELOT_FILE_PATH . "/" . $file_name ;

                if ($writexls($results[0][0], $results[1], $file_path, $ref))
                    header("Location:createlot.php?msg=success&file=".$file_name);
                else
                    header("Location:createlot.php?msg=error");
            }
        }
    }
    else
        header("Location:createlot.php");


    function process($data, $start, $xls, $ref, $count, $opt)
    {
        $mergeArr[] = 0;
        $mrge = 0;
        $sheetcount = 1;

	// Looping through one purticular column values
        foreach ($data as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $datas[$sheetcount-1][$key][0] = ($key==$xls) ? 'LOT' : '' ;
                foreach ( $dataArr as $idx=>$col ) :
                    $datas[$sheetcount-1][$key][$idx] = $col ;
                endforeach ;
                if($key > $xls)
                {
			// Grouping data rows with given number of chars/words. Also creating one merged column for these rows.
                    $tmp = $datas[$sheetcount-1][$key][$ref] ;
                    if($opt=='word')
                        $mrge = $mrge+(str_word_count($tmp));
                    else
                        $mrge = $mrge+(strlen($tmp));

                    if($mrge>=$count)
                    {
                        $mergeArr[]=$key-1;
                        $mrge = 0;
                    }
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
	// Updating results array to add url header
        $sheetcount = 1;
        foreach ($datas as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $results[$sheetcount-1][$key] = $dataArr ;
                if($key == $xls)
                    $results[$sheetcount-1][$key][0] = 'Lot';
                $key++;
            endforeach ;
            $sheetcount++;
        }
        return array($results, $mergeArr) ;
    }

    function writeXlsx($data,$mergeArr,$file_path,$ref)
    {
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
            foreach ($row as $key => $value)
            {                
                $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                //$value = str_replace("", "oe", $value) ; //XXceXX
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));

                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1),$value);
                $col++;
            }
            $rowCount++;
        }
        
        for ($i=1; $i < sizeof($mergeArr); $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($mergeArr[$i-1]+2),'LOT '.$i);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($mergeArr[$i-1]+2).':A'.($mergeArr[$i]+1));
        }
        
        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }

    function bWriteXLS($data,$mergeArr,$file_path,$ref)
    {
        // include package
        include_once 'Spreadsheet/Excel/Writer.php';

        $excel = new Spreadsheet_Excel_Writer($file_path);
        $excel->setVersion(8);

        // add worksheet
        $sheet =& $excel->addWorksheet();
        //$sheet->setInputEncoding('iso-8859-15');
        $sheet->setColumn(0,count($data[1]),20);
        
        //custom color
        $excel->setCustomColor(22, 217, 151,149);
        $excel->setCustomColor(12, 252, 213,180);
        
        // create format for header row
        // bold, red with black lower border
            
        $format_a = array('bordercolor' => 'black',
                            'bold'=>'1',
                            'size' => '11',
                            'FgColor'=>'22',
                            'color'=>'black',
                            'align' => 'center',
                            'valign' => 'top');

        $format_headers =& $excel->addFormat($format_a);
        $format_headers->setBorder(1);
        //$format_headers->setTextWrap();
        
        $wrap_format=& $excel->addFormat();
        $wrap_format->setVAlign('top');
        //$wrap_format->setBorder(1);
        $wrap_format->setFgColor(12);   
        //$wrap_format->setTextWrap();
        $wrap_format->setAlign('left');
        
        // add data to worksheet
        $rowCount=0;
        echo "<pre>";print_r($mergeArr);
        foreach ($data as $row) {
            //$col_cnt=count($row);
          foreach ($row as $key => $value) {

            $value = iconv("ISO-8859-1", "UTF-8", $value) ;
            $value = str_replace("", "oe", $value) ; //XXceXX
            $value = str_replace("", "'", $value) ;
            $value = str_replace("", "'", $value) ;
            $value = utf8_decode($value);            
            /*if (in_array($rowCount-1, $mergeArr) && ($key==0))
            {
                $lotnumber = (array_search(($rowCount-1), $mergeArr)+1) ;
                echo ($rowCount-1) . "-- LOT " . $lotnumber . "<br>" ;
                unset($lotnumber) ;
            }*/
                
            if($rowCount==0)
            {
                $sheet->write($rowCount, $key, $value,$format_headers);
            }
            elseif (in_array($rowCount-1, $mergeArr) && ($key==0))
            {
                $lotnumber = (array_search(($rowCount-1), $mergeArr)+1) ;
                $sheet->write($rowCount, $key, "LOT ".$lotnumber, $wrap_format);
                if($rowCount>1)
                {
                    $row1 = $mergeArr[array_search(($rowCount-1), $mergeArr) - 1]+1 ;
                    $row2 = $rowCount - 1 ;
                    $sheet->setMerge($row1, 0, $row2, 0);
                    echo ($row1) . "-- " . $row2 . "<br>" ;
                }
                unset($lotnumber) ;
            }
            else
                $sheet->write($rowCount, $key, $value,$wrap_format);
          }
          $rowCount++;
        }
        
        $row1 = $row2+1 ;
        $row2 = $rowCount - 1 ;
        $sheet->setMerge($row1, 0, $row2, 0);
        //echo ($row1) . "|| " . $row2 . "<br>" ;echo "</pre>";exit;
        // save file to disk
        if ($excel->close() === true)
            return $file_path ;
    }
?>
