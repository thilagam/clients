<?php
ob_start();
header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
    odownloadXLS($_GET['file'], LBMCHE_REF_WRITER_FILE_PATH, "index.php") ;

if(isset($_POST['submit']))
{
    require_once(INCLUDE_PATH."/reader.php");//print_r($_FILES);exit;
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    
    if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
            //echo '<pre>'; print_r($xls1Arr[0]);exit;
            $results  = process($xls1Arr[0], 2, 0, $ref1, $ref2) ;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
            //echo '<pre>'; print_r($xls1Arr[0]);exit;
            $results  = process($xls1Arr[0], 2, 0, $ref1, $ref2) ;
        }

        if($filename = WriteXLS( $results, str_replace(' ', '_', $file1['filename']), $xls1Arr[1], $ref)) {
          header("Location:lebon_ref.php?msg=success&file=".$filename);
        } else {
          header("Location:lebon_ref.php?msg=error");
        }
    }
    
}
else
    header("Location:lebon_ref.php");

function WriteXLS($datas,$name, $sheetnames, $ref)
{
    //echo '<pre>'; //print_r($error_cells); print_r($datas);exit;
    // include package
    include_once 'Spreadsheet/Excel/Writer.php';
    // create empty file
    $filename="LEBONMARCHE";
    rename(LBMCHE_REF_WRITER_FILE_PATH."/".$filename.".xls", LBMCHE_REF_WRITER_FILE_PATH."/LEBONMARCHE_".date("YmdHis").".xls");//exit;
    $excel = new Spreadsheet_Excel_Writer(LBMCHE_REF_WRITER_FILE_PATH."/".$filename.".xls");
    $excel->setVersion(8) ;
    //custom color
    //$excel->setCustomColor(22, 213, 226,184);
    $excel->setCustomColor(11, 31, 183, 20);

    //error cell format
    $format_e=array(
            'color'=>'black',
            'align' => 'center');
    $format_error =& $excel->addFormat($format_e); 
    $format_error->setFgColor('yellow');
    
    // create format for header row
    // bold, red with black lower border
    $header_f=array(
            'bold'=>'1',
            'size' => '10',
            'color'=>'black',
            'border'=>'1',
            'align' => 'center',
            'valign' => 'top'); 
    $header =& $excel->addFormat($header_f); 
    $header->setFgColor(11);
    $cell_f=array(
              'Size' => 10,
              'valign' => 'top'); 
    $cell =& $excel->addFormat($cell_f);
    
    foreach($datas as $sheet_cnt=>$data)
    {
        //echo $sheet_cnt; print_r($data);
        $sheet_name=$sheetnames[$sheet_cnt];
        $sheet_obj='sheet'.$sheet_cnt;
        $$sheet_obj=& $excel->addWorksheet($sheet_name);
        //$$sheet_obj->setInputEncoding('utf-8');
        //$$sheet_obj->writeFormula('C1', "=LEN(B1)");
        
        //foreach ($data as $row) {echo $row[6].'<br>--';}exit;
        // add data to worksheet        
        $rowCount=0;
        foreach ($data as $row) {
          foreach ($row as $key => $value) {
            $value= (str_replace("’", "'", $value)) ;
            if($rowCount==0){
                $$sheet_obj->write($rowCount, $key, $value,$header);
            }
            else
            {
                $error_index="'".$sheet_cnt."|".($rowCount)."|".($key)."'";
                if(in_array($error_index,$error_cells))
                {                       
                    $$sheet_obj->write($rowCount, $key, ($value),$format_error); //write error cell
                }   
                else    
                    $$sheet_obj->write($rowCount, $key, $value,$cell);
            }
          }
          $rowCount++;
        }
        
    }//exit;
    // save file to disk
    if ($excel->close() === true) {
        return $filename.".xls" ;
    } else {
        return false ;
    }
}

    function process($data, $start, $xls, $ref1, $ref2)
    {
        $ctime = 0;
        $mtime = '';
        foreach (glob(LBMCHE_REF_WRITER_FILE_PATH.'/*') as $file)
        {
            if ($mtime > filemtime($file))
            {
                $match = $file;
                $mtime = filemtime($file);
            }
        }
        
        $last_file  =   pathinfo($file) ;
        if($last_file['extension'] == 'xlsx')
            $xls_array = oxlsxRead($file) ;
        else
            $xls_array  = oxlsRead($file) ;
//echo '<pre>'; echo '###'; print_r($xls_array); exit($file) ;
        $sheetcount = 1;
        foreach ($xls_array[0] as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                if($key > $xls)
                    $references[] = $dataArr[2];
                $key++;
            endforeach ;
            $sheetcount++;
        }
        $references = array_filter($references);
        
        $sheetcount = 1;
        foreach ($data as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                if($key > $xls)
                {
                    $dataArr[$ref1] = trim($dataArr[$ref1]) ;
                    if(!in_array($dataArr[$ref1], $references) && !empty($dataArr[$ref1]))
                        $xls_array[0][$sheetcount-1][] = $dataArr ;
                        //$results[$sheetcount-1][] = $dataArr ;
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
        $sheetcount = 1;
        foreach ($xls_array[0] as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                foreach ( $dataArr as $idx=>$col ) :
                    $results[$sheetcount-1][$key][$idx-1] = $col ;
                endforeach ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        //echo '<pre>'; print_r($references); echo '###'; print_r($results); exit($file) ;
        return $results ;
    }

    function check_structure($val)
    {
        $return = TRUE;
        preg_match_all( '/\{(.*?)\}/', $val,  $match);//print_r($match);
        foreach ($match[1] as $key => $value)
        {
            if(substr_count($value, '|') < 2)
                $return = FALSE ;
        }
        return $return;
    }
?>
