<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
    
include_once(INCLUDE_PATH."/config_path.php") ;
include_once(INCLUDE_PATH."/common_functions.php") ;

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
    odownloadXLS($_GET['file'], MISC_WORD_COUNT_FILE_PATH, "wordcount.php") ;

function strip_html_tags1($str){
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(
        array(// Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            ),
        "", //replace above with nothing
        $str );
    $str = preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si', '', $str) ;
    return $str;
}

function writeXlsx($data,$hrows,$file_path)
{
    /** PHPExcel */
    include_once INCLUDE_PATH.'/PHPExcel.php';//echo "<pre>";print_r($hrows);print_r($data);exit($ref);
    
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
            //echo $col."--".$rowCount."--".$value."<br>";
            /*$value = html_entity_decode(htmlentities($value), ENT_COMPAT, 'UTF-8') ;*/
            
            $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1),$value);
            $col++;
        }//echo "<br><br>";
        $rowCount++;
    }//exit;
    
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
    for ($i=0; $i < sizeof($hrows); $i++) {
        if($i>0)
        {
            $objPHPExcel->getActiveSheet()->getStyle('A'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
            $objPHPExcel->getActiveSheet()->getStyle('B'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
            $objPHPExcel->getActiveSheet()->getStyle('C'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
            $objPHPExcel->getActiveSheet()->getStyle('D'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
            $objPHPExcel->getActiveSheet()->getStyle('E'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
            $objPHPExcel->getActiveSheet()->getStyle('F'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'C4ACDC')));
        }
        else {            
            $objPHPExcel->getActiveSheet()->getStyle('A'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
            $objPHPExcel->getActiveSheet()->getStyle('B'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
            $objPHPExcel->getActiveSheet()->getStyle('C'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
            $objPHPExcel->getActiveSheet()->getStyle('D'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
            $objPHPExcel->getActiveSheet()->getStyle('E'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
            $objPHPExcel->getActiveSheet()->getStyle('F'.$hrows[$i])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'86B9F7')));
        }
        
    }
    
    // Save Excel 2007 file
    /*$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter->save($file_path);*/
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($file_path);
        
    @chmod($file_path, 0777) ;//echo "<pre>";print_r($data);exit;
    
    if(file_exists($file_path))
        return true ;
}

function convert_smart_quotes1($string)
{
    $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151),
                    chr(230),
                    chr(156),
                    "’",
                    "‘",
                    '“',
                    '”',
                    '–',
                    '–',
                    "’");

    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-',
                     'ae',
                     'oe',
                     "'",
                     "'",
                     '"',
                     '"',
                     '-',
                     '-',
                     "'");
    return str_replace($search, $replace, $string) ;
}

function convert_smart_quotes($string)
{
    $search1= array(
        '&ocirc;',
        '&eacute;',
        '&ecirc;',
        '&egrave;',
        '&Agrave;',
        '&agrave;',
        '&Acirc;',
        '&acirc;',
        '&AElig;',
        '&aelig;',
        '&Ccedil;',
        '&ccedil;',
        '&Egrave;',
        '&egrave;',
        '&Eacute;',
        '&eacute;',
        '&Ecirc;',
        '&ecirc;',
        '&Euml;',
        '&euml;',
        '&#39;'
    );
    $replace1= array(
        'ô',
        'é',
        'ê',
         'è',
        'À',
        'à',
        'Â',
        'â',
        'Æ',
        'æ',
        'Ç',
        'ç',
        'È',
        'è',
        'É',
        'é',
        'Ê',
        'ê',
        'Ë',
        'ë',
        "'"
    );
    //$replace1 = array_map("utf8_decode", $replace1) ;
    //echo '<pre>@@'; print_r(array_keys($arr)); print_r(array_values($arr)); exit;
    $string = str_replace($search1, $replace1, $string) ;
    //$string = str_replace($search1, 'XXXANXXX', $string) ;
    
    $search = array(chr(145), 
                    chr(146), 
                    chr(147), 
                    chr(148), 
                    chr(151),
                    chr(230),
                    chr(156),
                    "’",
                    "‘",
                    '“',
                    '”',
                    '–',
                    '–',
                    "’");

    $replace = array("'", 
                     "'", 
                     '"', 
                     '"', 
                     '-',
                     'ae',
                     'oe',
                     "'",
                     "'",
                     '"',
                     '"',
                     '-',
                     '-',
                     "'");

    //return str_replace("&eacute;", "XXXANOO", str_replace($search, $replace, $string));
    return str_replace($search, $replace, $string) ;
}

if($_POST['submit'])
{
    $urls = $_POST['url'] ;
    $xls[] = array("URL", "ID", "CLASS", "Div content", "Word count", "Character count") ;
    $hrows = array(1) ;
    
    foreach ($urls as $key => $url)
    {
    //echo htmlentities(str_replace("'", "'", strip_html_tags1(file_get_contents($url))));
    }//exit;
    foreach ($urls as $key => $url)
    {
        //echo "<br>url=".$url;
        $fileContents = convert_smart_quotes(strip_html_tags1(file_get_contents($url))) ;
        $fileContents = str_replace("é", "&#233;", $fileContents) ;
        $fileContents = utf8_decode($fileContents) ;
        
        $doc = new DOMDocument();
        $doc->loadHTML($fileContents) ;
        
        $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object
        
        $div_list = $_POST['div_list'.($key+1)] ;
        //echo '<pre>@@'; print_r($arr); exit(strip_html_tags1(file_get_contents($url)).'*****'.$url);

        $allwordcount = 0 ;
        $allcharcount = 0 ;
        
        $ii = 1 ;
        foreach($arr as $item)
        {
            // DOMElement Object
            $id =  $item->getAttribute("id");
            $class =  $item->getAttribute("class");
            $text = (strip_tags(trim($item->nodeValue))) ;
        //echo "<br>###" . $text;
            
            if(!empty($id) || !empty($class))
            {
                if(in_array($ii, $div_list))
                {
                    $divArr[] = array("", $id, $class, $text, str_word_count($text), strlen($text)) ;
                    //print_r($item->childNodes);print_r($item->item(0));exit($item->nodeValue);
                }
                $allwordcount += str_word_count($text) ;
                $allcharcount += strlen($text) ;

                $ii++;
            }
        }
        array_push($hrows, (sizeof($xls)+1)) ;
        array_push($xls, array($url, "", "", "", $allwordcount, $allcharcount)) ;//echo '<pre>@@'; print_r($xls);
        if(sizeof($divArr)>0)
            $xls = array_merge($xls, ($divArr)) ;
        array_push($xls, array("", "", "", "", "", "")) ;
        
        unset($doc) ;unset($divArr) ;
    }
    //exit('|||');//echo '<pre>@@'; print_r($hrows); print_r($xls); 
                
    $file_name = "WordCount_" . date('YmdHis') . ".xlsx" ;
    $file_path = MISC_WORD_COUNT_FILE_PATH . "/" . $file_name ;

    if (writeXlsx($xls,$hrows,$file_path))
        header("Location:wordcount.php?msg=success&file=".$file_name);
    else
        header("Location:wordcount.php?msg=error");
    
    //echo '<pre>@@'; print_r($hrows); print_r($xls); exit('|||');
}
elseif($_POST['upload'])
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    if($file1['extension']=='xls' || $file1['extension']=='xlsx')
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
        }
    }
    $urlArr = ($xls1Arr[0][0]) ;
    $urlArr = array_values($urlArr) ;
    unset($urlArr[0]);
    $urlArr = array_values($urlArr) ;
    
    foreach ($urlArr as $urlArr_) {
        foreach ($urlArr_ as $key => $value) {
            $urls[] = trim($value) ;
        }
    }    
    foreach ($urls as $url)
    {
        $doc = new DOMDocument();
        $doc->loadHTML(strip_html_tags1(file_get_contents($url)));
        
        $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object
          
        $options = '' ;
        $ii = 1 ;
        foreach($arr as $item)
        {
            $optval = '' ;
            // DOMElement Object
            $id =  $item->getAttribute("id");
            $class =  $item->getAttribute("class");
            
            if(!empty($id) || !empty($class))
            {
                $optval = (!empty($id) ? 'id=' . $id : '') . ((!empty($id) && !empty($class)) ? ',' : '') . (!empty($class) ? 'class=' . $class : '') ;
                $options .= '<option value="'.$ii.'" selected>'.$optval.'</option>' ;
                $ii++;
            }
        }
        $option[] = $options ;
        unset($doc) ;   unset($arr) ;
    }
    //echo '<pre>@@';print_r($urls); print_r($option); exit('*****');
}

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<link rel="stylesheet" type="text/css" href="/css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
<script type="text/javascript" src="/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(function(){
<?php
if($_POST['upload'])
{
    $sno = 1;
    foreach ($urls as $key => $url) {
?>
        $("#div_list<?=$sno?>").multiselect({selectedText: "Div(s) selected."});
<?php
        $sno++;
    }
}
?>
});
</script>
<div class="span10 content">    
    <h2 class="heading">Comptage de mots avec url</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong>Comptage de mots avec url</strong>
            </div>
        <form method="POST" class="form-horizontal" action="" name="wordcount" id="wordcount" ENCTYPE="multipart/form-data"><!-- onsubmit="return checkfile();"-->
            <div class="control-group">
                <label class="control-label" for="excel_file">XLS / XLSX : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
<?php
if($_POST['upload'])
{
?>
            <div class="control-group">
                <label class="control-label"> Urls / Div(s) </label>
                <div class="controls">
<?php
    $sno = 1;
    foreach ($urls as $key => $url) {
?>
        <div>
            <input type="hidden" name="url[]" value="<?=$url?>" />
            <strong><?=$sno?></strong>.&nbsp;
<?php
            echo $url;
?>
            <div class="divDropDown"><select multiple="multiple" id="div_list<?=$sno?>" name="div_list<?=$sno?>[]" class="span4" data-placeholder="S&eacute;lectionner div(s)" style="min-width:200px;"><?=($option[$key])?></select></div><hr>
        </div>
<?php
        $sno++;
    }
?>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Process</button>
                </div>
            </div>
<?php
} else { ?>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="upload" class="btn btn-primary">upload</button>
                </div>
            </div>
<?php } ?>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Fichier divis&#233; en lots avec succ&#232;s ! <a href="wordcount.php?action=download&file=<?=$_REQUEST['file']?>">Cliquez ici pour t&#233;l&#233;charger le fichier</a><strong>
                </div>
            <?  
            }   
            else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='error')
            {
            ?>
                <div class="alert alert-error">
                    <strong>Could not save spreadsheet<strong>
                </div>
            <?              
            }   
            else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='file_error')
            {
            ?>
                <div class="alert alert-error">
                    <strong>Could not read the data file<strong>
                </div>
            <?              
            }
            ?>
        </form>
    </div>

<script type="text/javascript">
function checkfile()
{
    var error=0;
    var msg='';
    /*var dl = $('#div_list').val();  //alert('#div_list');
    if (dl=='')
        return false;
    else
        return true;*/
        
    if(document.wordcount.url.value != '')
    {
        
    }
    else
    {
        msg=msg+"please enter url.. \n";
        error=error+1;
    }
    if(error>0) 
    {
        alert(msg);
        return false;
    }
    else
        return true;
}
</script>
<style>
.divDropDown{padding:10px 0 0;}
hr{margin: 5px 0!important;}
</style>
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>