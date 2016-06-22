<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']) && isset($_GET['ref']))
    odownloadXLS($_GET['file'], BASH_SENTENCE_FILE_PATH . "/" . $refs[$_GET['ref']-1], "sentence.php") ;

if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $writexls = ($_REQUEST['op']=='xls') ? 'oWriteXLS' : 'writeXlsx' ;
    $ref = $_REQUEST['index_reference'];

    // Language sentence text
    global $langtext;

    $langtext = ($_REQUEST['langtext']=='fr') ? "Notre mannequin mesure 1,78 m et porte une T1" : "Our model measures 1,78m and is wearing a T1 size" ;

    if($file1['extension']=='xls' || $file1['extension']=='xlsx')
    {
        $ext = $file1['extension'];

        if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
        {
            if($file1['extension'] == 'xlsx')
            {
                $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
                //echo '<pre>'; print_r($xls1Arr[0]);exit;
                $results  = process($xls1Arr[0], 2, 0, $ref) ;
            }
            else
            {
                $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
                //echo '<pre>'; print_r($xls1Arr[0]);exit;
                $results  = process($xls1Arr[0], 2, 1, $ref) ;
            }
            //echo '<pre>'; print_r($results); exit($langtext);
            
            $file_name = uniqid() . "_bash_sentence." . $_REQUEST['op'] ;
            $file_path = BASH_SENTENCE_FILE_PATH . "/" . $file_name ;

            if ($writexls($results[0], $file_path))
                header("Location:sentence.php?msg=success&file=".$file_name);
            else
                header("Location:sentence.php?msg=error");
        }
    }
}
else
    header("Location:sentence.php");

    function process($data, $start, $xls, $ref)
    {
        // Language sentence text
        global $langtext;
        $sheetcount = 1;//echo '<pre>';
        foreach ($data as $data_)
        {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                foreach ( $dataArr as $idx=>$col ) :
                    $results[$sheetcount-1][$key][$idx-1] = $col ;
                endforeach ;
                if($key>$xls)
                    $results[$sheetcount-1][$key][$ref-1] .= "\n\n" . utf8_decode($langtext) ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        return $results ;
    }
?>
