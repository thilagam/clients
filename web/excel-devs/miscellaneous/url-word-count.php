<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

//To remove all the hidden text not displayed on a webpage
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

function strip_html_tags($str){
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
    $str = replaceWhitespace($str);
    $str = strip_tags($str);
    return $str;
} //function strip_html_tags ENDS

//To replace all types of whitespace with a single space
function replaceWhitespace($str) {
    $result = $str;
    foreach (array(
    "  ", " \t",  " \r",  " \n",
    "\t\t", "\t ", "\t\r", "\t\n",
    "\r\r", "\r ", "\r\t", "\r\n",
    "\n\n", "\n ", "\n\t", "\n\r",
    ) as $replacement) {
    $result = str_replace($replacement, $replacement[0], $result);
    }
    return $str !== $result ? replaceWhitespace($result) : $result;
}

    function convert_smart_quotes($string)
    {
        $arr= array(
            '&ocirc;'=> 'ô',
            '&eacute;'=> 'é',
            '&ecirc;' => 'ê',
            '&egrave;'=> 'è',
            '&Agrave;'=> 'À',
            '&agrave;'=> 'à',
            '&Acirc;' => 'Â',
            '&acirc;' => 'â',
            '&AElig;' => 'Æ',
            '&aelig;' => 'æ',
            '&Ccedil;'=> 'Ç',
            '&ccedil;'=> 'ç',
            '&Egrave;'=> 'È',
            '&egrave;'=> 'è',
            '&Eacute;'=> 'É',
            '&eacute;'=> 'é',
            '&Ecirc;' => 'Ê',
            '&ecirc;' => 'ê',
            '&Euml;'  => 'Ë',
            '&euml;'  => 'ë',
            '&Icirc;' => '',
            '&icirc;' => '',
            '&Iuml;'  => '',
            '&iuml;'  => '',
            '&Ocirc;' => '',
            '&ocirc;' => '',
            '&OElig;' => '',
            '&oelig;' => '',
            '&Ugrave;'=> '',
            '&ugrave;'=> '',
            '&Ucirc;' => '',
            '&ucirc;' => '',
            '&Uuml;'  => '',
            '&uuml;' => '',
            '&laquo;' => '',
            '&raquo;' => '',
            '&#39;' => "'",
            '' =>''
        );
        $string = str_replace(array_keys($arr), array_values($arr), $string);
        
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
                        '–');
    
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
                         '-');
        return str_replace($search, $replace, $string); 
    }
    
if($_REQUEST['getdivs'])
{
    $doc = new DOMDocument();
    $doc->loadHTML(strip_html_tags1(file_get_contents($_REQUEST['url'])));
    
    // all links in document
    $links = array();
    $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object
      
    $options = '' ;
    $ii = 1 ;
    foreach($arr as $item)
    {
        $optval = '' ;
        // DOMElement Object
        $id =  $item->getAttribute("id");
        $class =  $item->getAttribute("class");
        //$text = trim($item->nodeValue);
        
        if(!empty($id) || !empty($class))
        {
            $optval = (!empty($id) ? 'id=' . $id : '') . ((!empty($id) && !empty($class)) ? ',' : '') . (!empty($class) ? 'class=' . $class : '') ;
            $options .= '<option value="'.$ii.'">'.$optval.'</option>' ;
            $ii++;
        }
    }
    //echo '<pre>'; print_r($links); exit;
    exit($options) ;
}

if($_POST['submit'])
{
    $url = trim($_POST['url']) ;
    $div_list = $_POST['div_list'] ;
    $allcount = $_POST['allcount'] ;
    
    $doc = new DOMDocument();
    $doc->loadHTML(strip_html_tags1(file_get_contents($_REQUEST['url'])));
    
    // all links in document
    $links = array();
    $arr = $doc->getElementsByTagName("div"); // DOMNodeList Object
    
    $counVal[] = array("SI NO", "ID", "CLASS", "Word count", "Div content") ;
    if($allcount)
        $allcountVal = 0 ;
    $selectedSum = 0 ;
    
    $ii = 1 ;
    foreach($arr as $item)
    {
        // DOMElement Object
        $id =  $item->getAttribute("id");
        $class =  $item->getAttribute("class");
        $text = strip_html_tags(trim($item->nodeValue));
        
        if(!empty($id) || !empty($class))
        {
            if(in_array($ii, $div_list))
            {
                //$text1 = iconv("ISO-8859-1", "UTF-8", $text) ;
                $text1 = str_replace("", "œ", $text) ;
                $text1 = str_replace("", "'", $text1) ;
                $text1 = str_replace("", "'", $text1) ;
                $text1 = utf8_decode(convert_smart_quotes($text1));
                $counVal[] = array(sizeof($counVal), $id, $class, "<green>".str_word_count($text)."</green>", $text1) ;
                $selectedSum += str_word_count($text) ;
                //print_r($item->childNodes);print_r($item->item(0));
                //exit($item->nodeValue);
            }

            if($allcount)
                $allcountVal += str_word_count($text) ;

            $ii++;
        }
    }
    
    //echo '<pre>'; print_r($counVal); exit($allcountVal);
}

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<link rel="stylesheet" type="text/css" href="/css/jquery.multiselect.css" />
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" />
<script type="text/javascript" src="/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript">
$(function(){
    $("#div_list").multiselect({selectedText: "Div(s) selected."});
    
    /*jQuery.validator.addMethod("needsSelection", function(value, element) {
        return $(element).multiselect("getChecked").length > 0;
    });

    jQuery.validator.messages.needsSelection = 'Select Atleast One Course';

    jQuery.validator.addMethod("checkDivSel", function(value, element) {
        alert('kk');
        if (!$("input[name='div_list']:checked").val())
            return false;
        else
            return true;
    }, "Please select div(s).") ;*/

    jQuery.validator.addMethod("checkDivSel", function(value, element) {
        var dl = $('#div_list').val();  //alert('#div_list');
        if (dl=='')
            return false;
        else
            return true;
    }, "Please select div(s).");
    
    $("#wordcount").validate({
        rules : {
            url : {
                required : true
            },
            "div_list[]" : {
                required : true,
                checkDivSel : true
            }
        },
        messages : {
            url : {
                required : "Please enter url."
            },
            "div_list[]" : {
                required : "Please select div(s).",
                checkDivSel : "Please select div(s)."
            }
        }
    });
<?php
if($url){
?>
    $.post(
        "url-word-count.php",
        { "getdivs": "1", "url": "<?=$url?>" },
        function(data,status)
        {
            data = data.trim();
            $('#div_list').html(data);//alert(data);
            $('#div_list').multiselect('refresh');
        }
    );
<?php } ?>
});

function getDivs()
{
    var url = $('#url').val();
    $.post(
        "url-word-count.php",
        { "getdivs": "1", "url": url },
        function(data,status)
        {
            data = data.trim();
            $('#div_list').html(data);//alert(data);
            $('#div1').show();$('#div2').show();$('#div3').show();
            $('#div_list').multiselect('refresh');
        }
    );
}
</script>
<div class="span10 content">    
    <h2 class="heading">Web url word count</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong> Word count for div element contents </strong>
            </div>
        <form method="POST" class="form-horizontal" action="" name="wordcount" id="wordcount" ENCTYPE="multipart/form-data"><!-- onsubmit="return checkfile();"-->
            <div class="control-group">
                <label class="control-label" for="excel_file">Url : </label>
                <div class="controls">
                    <input type="text" id="url" name="url" value="<?=$url?>" style="width:400px;">&nbsp;<a href="javascript:void(0);" onclick="getDivs();">Get div tags the given url</a>
                </div>
            </div>
            <div class="control-group" id="div1" style="display:none;">
                <label class="control-label">Divs : </label>
                <div class="controls" id="divsarea">
                    <select multiple="multiple" id="div_list" name="div_list[]" class="span4" data-placeholder="S&eacute;lectionner div(s)" style="min-width:200px;">
                    </select>
                </div>
            </div>
            <div class="control-group" id="div2" style="display:none;">
                <label class="control-label"> </label>
                <div class="controls" id="divsarea">
                    <input type="checkbox" name="allcount" value="1" <?php if($allcount){ ?>checked<?php } ?> /> Count of all the words of the webpage
                </div>
            </div>
            <div class="control-group" id="div3" style="display:none;">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Get word count</button>
                </div>
            </div>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Writer File successfully generated! <a href="write_lotxls.php?action=download&file=<?=$_REQUEST['file']?>">Click here to download</a><strong>
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
            
            if($allcount && (sizeof($counVal)>1)){ echo "<div class='alert alert-success'><strong>All words count</strong> : " . $allcountVal . "</div>" ; }
            
            if($_POST['submit'] && (sizeof($counVal)>1))
            {
                echo "<div class='alert alert-success'><strong>Sum of selected div's word count</strong> : " . $selectedSum . "</div>" ;
                $table = '<table style="width:88%; padding-top:10px;" align="center" valign="center" cellpadding="5" cellspacing="5" class="gridtable" border="1"><thead>' ;
        
                $rowCount = 0 ;
                foreach ($counVal as $row)
                {
                    $table .= "<tr>" ;
                    foreach ($row as $key => $value)
                    {
                        $value= (str_replace("�", "'", $value)) ;
                        if($rowCount==0)
                        {
                            $table .= "<th>".$value."</th>" ;
                            //$sheet->write($rowCount, $key, $value,$format_headers);
                        }
                        else
                             $table .= "<td>".$value."</td>" ;
                    }      
                    if($rowCount==0)
                        $table .= "</tr></thead><tbody>" ;
                    else
                        $table .= "</tr>" ;
                    $rowCount++;
                }
                $table .= "</tbody></table>" ;
                echo $table;
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
#divsarea { font:12px Helvetica, arial, sans-serif }
#divsarea h1, #divsarea h2, #divsarea p { margin:10px 0 }
#divsarea .hidden { visibility:hidden }

green{color:green; font-weight:bold;}

#divsarea .message { padding:10px; margin:15px 0; display:block; text-align:left }
#divsarea .message-title { font-weight:bold; font-size:1.25em }
#divsarea .message-body { margin-top:4px }
#divsarea .error, #divsarea .notice, #divsarea .success { padding:.8em; margin-bottom:1em; border:2px solid #ddd }
#divsarea .error { background:#FBE3E4; color:#8a1f11; border-color:#FBC2C4 }
#divsarea .notice { background:#FFF6BF; color:#514721; border-color:#FFD324 }
#divsarea .success { background:#E6EFC2; color:#264409; border-color:#C6D880 }

.error, .error a { color:#8a1f11 }
#divsarea .notice a { color:#514721 }
#divsarea .success a { color:#264409 }
</style>
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>