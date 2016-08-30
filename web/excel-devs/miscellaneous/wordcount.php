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

function writeXlsx($data,$hrows,$urlrow,$file_path)
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
            $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1),$value);
            $col++;
        }
        $rowCount++;
    }
    
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
    
    for ($i=1; $i <= sizeof($hrows); $i++) {
        if($i==1)
        {
            $cellColor = '86B9F7' ;
        }
        else
        {
            $cellColor = 'C4ACDC' ;
        }
        $objPHPExcel->getActiveSheet()->getStyle('A'.$hrows[$i-1])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>$cellColor)));
        $objPHPExcel->getActiveSheet()->getStyle('B'.$hrows[$i-1])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>$cellColor)));
        $objPHPExcel->getActiveSheet()->getStyle('C'.$hrows[$i-1])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>$cellColor)));
        $objPHPExcel->getActiveSheet()->getStyle('D'.$hrows[$i-1])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>$cellColor)));
        $objPHPExcel->getActiveSheet()->getStyle('E'.$hrows[$i-1])->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>$cellColor)));
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$hrows[$i-1].":E".$hrows[$i-1])->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }
    
    for ($i=0; $i < sizeof($urlrow); $i++)
    { 
        $objPHPExcel->getActiveSheet()->getStyle('A'.$urlrow[$i])->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$urlrow[$i])->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$urlrow[$i])->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$urlrow[$i])->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$urlrow[$i])->getFont()->setBold(true);
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$urlrow[$i].":E".$urlrow[$i])->getFont()->setSize(10);
    }
    
    $objPHPExcel->getActiveSheet()->getStyle('A1:E' . sizeof($data))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($file_path);
        
    @chmod($file_path, 0777) ;//echo "<pre>";print_r($data);exit;
    
    if(file_exists($file_path))
        return true ;
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
    $string = str_replace($search1, $replace1, $string) ;
    
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


if($_POST['submit'])
{
    $urlrow = array() ;
    $urls = $_POST['url'] ;
    $xls[] = array("Url", "Total de mots sur la page", ("Total de caractères sur la page"), "", "", "") ;
    array_push($urlrow, sizeof($xls)) ;
    
    // Scrapping all urls to get html content 
    foreach ($urls as $key => $url)
    {
        $fileContents = convert_smart_quotes(strip_html_tags1(file_get_contents($url))) ;
        $fileContents = str_replace("é", "&#233;", $fileContents) ;
        $fileContents = utf8_decode($fileContents) ;
        
        $doc = new DOMDocument();
        $doc->loadHTML($fileContents) ;
        
	// Parsing html to get id, class, div text, word and character counts
        $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object

        $allwordcount = 0 ;
        $allcharcount = 0 ;
        
        $ii = 1 ;
        foreach($arr as $item)
        {
            // DOMElement Object
            $id =  $item->getAttribute("id");
            $class =  $item->getAttribute("class");
            $text = (strip_tags(trim($item->nodeValue))) ;
            
            if(!empty($id) || !empty($class))
            {
                $optval = (!empty($id) ? $id : '') . ' |' . (!empty($class) ? $class : '') ;
                if(!$divIdentifier[$url][$optval])
                {
                    $divIdentifier[$url][$optval] = $text ;
                    $urlcnt[$url]['wcount'] += str_word_count($text) ;
                    $urlcnt[$url]['ccount'] += strlen($text) ;
                }
                $allwordcount += str_word_count($text) ;
                $allcharcount += strlen($text) ;

                $ii++;
            }
        }
        array_push($xls, array($url, $allwordcount, $allcharcount, "", "", "")) ;
        unset($doc) ;
    }
    $hrows = array(1) ;
    
    // Output excel with url basic info(word and character counts) and extnded details like class, id and div content 
    foreach ($divIdentifier as $key => $value)
    {
        array_push($xls, array("", "", "", "", "", "")) ;
        
        array_push($xls, array("URL", "Mots sur la page", ("Caractères sur la page"), "", "", "")) ;
        array_push($urlrow, sizeof($xls)) ;
        array_push($hrows, sizeof($xls)) ;
        
        array_push($xls, array($key, $urlcnt[$key]['wcount'], $urlcnt[$key]['ccount'], "", "", "")) ;
        array_push($hrows, sizeof($xls)) ;
        
        array_push($xls, array("", "", "", "", "", "")) ;
        array_push($urlrow, sizeof($xls)) ;
        array_push($hrows, sizeof($xls)) ;
        
        array_push($xls, array("ID", "CLASS", "DIV CONTENT", "WORD COUNT", "CHARACTER COUNT")) ;
        array_push($urlrow, sizeof($xls)) ;
        array_push($hrows, sizeof($xls)) ;
        foreach ($value as $key1 => $value1) {
            $idcls = explode("|", $key1) ;
            array_push($xls, array(trim($idcls[0]), trim($idcls[1]), $value1, str_word_count($value1), strlen($value1))) ;
        }
    }
    $file_name = "WordCount_" . date('YmdHis') . ".xlsx" ;
    $file_path = MISC_WORD_COUNT_FILE_PATH . "/" . $file_name ;

    if (writeXlsx($xls,$hrows,$urlrow,$file_path))
        header("Location:wordcount.php?msg=success&file=".$file_name);
    else
        header("Location:wordcount.php?msg=error");
}
elseif($_POST['upload'])
{
    if($_POST['word_type'] == 2)
    {
        $urls = explode("\n", trim($_POST['urll']));
        $urls = array_map("trim", $urls) ;
    }
    else
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
        
        foreach ($urlArr as $urlArr_) {
            foreach ($urlArr_ as $key => $value) {
                $urls[] = trim($value) ;
            }
        }
    }
    
    $options = '' ;
    foreach ($urls as $url)
    {
        $doc = new DOMDocument();
        $doc->loadHTML(strip_html_tags1(file_get_contents($url)));
        
        $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object
          
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
                if(!$divIdentifier[$optval])
                {
                    $options .= '<option value="'.$ii.'" selected>'.$optval.'</option>' ;
                    $divIdentifier[$optval] = 1 ;
                }
                $ii++;
            }
        }
        unset($doc) ;   unset($arr) ;
    }
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
    //$("#div_list").multiselect({selectedText: "Div(s) selected."});
});

function chwtype(val)
{
    if(val==1)
    {
        $("#text_words").hide();
        $("#csv").show();
        $("#prcs").text('Compter');
    }
    else
    {
        $("#text_words").show();
        $("#csv").hide();
        $("#prcs").text('Process');
    }
}
</script>
<div class="span10 content">    
    <h2 class="heading">Comptage de mots</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong>Comptage de mots</strong>
            </div>
            <div class="alert">
                Ce dev vous permet de compter le nombre de caract&#232;res ou mots sur la totalit&#233; d&#8217;une ou plusieurs pages Web, ou bien par section (divs) sur ces pages. 
Si vous uploadez un fichier Excel, merci de bien veiller &#224; mettre vos URLs en colonne A. Le fichier g&#233;n&#233;r&#233; vous donnera toutes les donn&#233;es n&#233;cessaires pour chaque URL.
            </div>
        <form method="POST" class="form-horizontal" action="" name="wordcount" id="wordcount" ENCTYPE="multipart/form-data"><!-- onsubmit="return checkfile();"-->
            <div class="control-group">
                <label class="control-label" for="excel_file">Type : </label>
                <div class="controls">
                    <input type="radio" name="word_type" class="uni_style" onchange="chwtype(this.value)"  value="1" id="csvxls" checked> File
                    <input type="radio" name="word_type" class="uni_style" onchange="chwtype(this.value)"  value="2" id="opt_text" <?php if($_POST['word_type']==2){echo "checked";} ?>> Texte
                </div>
            </div>
            
            <div class="control-group" id="csv" style="display: <?php if(!$_POST['word_type']){echo "block";}elseif($_POST['word_type']==2){echo "none";}else{echo "block";} ?>;">
                <label class="control-label" for="excel_file">Xls / xlsx : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            
            <div class="control-group formSep" id="text_words" style="display: <?php if($_POST['word_type']==2){echo "block";}else{echo "none";} ?>;">
                <label class="control-label" for="excel_file">Url(s) : </label>
                <div class="controls">
                    <textarea name="urll" id="txtarea_sp" cols="1" rows="6" class="span8"></textarea>
                </div>
            </div>
<?php
if($_POST['upload'])
{
?>
            <div class="control-group" style="display:none;">
                <label class="control-label">Divs </label>
                <div class="controls">
                    <div class="divDropDown"><select multiple="multiple" id="div_list" name="div_list" class="span4" data-placeholder="S&eacute;lectionner div(s)" style="min-width:200px;"><?=$options?></select></div>
                    
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"> Url(s)</label>
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
                    <button type="submit" value="Upload" name="upload" id="prcs" class="btn btn-primary">Compter</button>
                </div>
            </div>
<?php } ?>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Comptage de caract&#232;res et mots effectu&#233; avec succ&#232;s ! <a href="wordcount.php?action=download&file=<?=$_REQUEST['file']?>">Cliquez ici pour t&#233;l&#233;charger le fichier</a><strong>
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
input[type="radio"]{margin-bottom:8px;}
</style>
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
