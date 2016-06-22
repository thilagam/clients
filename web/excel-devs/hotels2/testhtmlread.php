<?php

/*things need to change start*/

ob_start();
//header('Content-Type: text/html; charset=utf-8');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
$text=readDocx("/home/sites/site2/web/excel-devs/hotels2/writer-files/dev3/Prague-Arts-and-Culture-ru_RU-EX.docx"); // Path of file
//echo htmlspecialchars($text);
$newArr= process_xmlData($text);
echo "<pre>";
print_r($newArr);

//FUNCTION :: read a docx file and return the string
function readDocx($filePath) {
    // Create new ZIP archive
    $zip = new ZipArchive;
    $dataFile = 'word/document.xml';
    // Open received archive file
    mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
    if (true === $zip->open($filePath)) {
        // If done, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // If found, read it to the string
            $data = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Return data without XML formatting tags
			//echo $xml->saveXML();exit;
            $contents = explode('\n',strip_tags($xml->saveXML()));
            $text = '';
            foreach($contents as $i=>$content) {
                $text .= $contents[$i];
            }
            return  $xml->saveXML();
        }
        $zip->close();
    }
    // In case of failure return empty string
    return "";
}

function process_xmlData($text){
	/*things need to change end*/
//$text=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>',$text);
$text = str_replace("[","####",$text);
$text = str_replace("]","#####",$text);

$text = str_replace("</w:tr>","]",$text);
$text = str_replace("<w:tr>","[",$text);
//echo $text;exit;
$matches = array();

//$pattern = '/\tblrowstart([^\]]*)\tblrowend/';

//$pattern = '/\[(tblrowstart^\tblrowend]*)\]/';
//$pattern='/\[([^\]]*)\]/';
//preg_match_all($pattern, $text, $matches);
	//echo "<pre>";print_r($matches);exit;
$matches=explode(']',$text);
/*$htmldata="
<style>
table, td, th {
    border: 1px solid green;
	border-collapse: collapse;
	padding:3px 5px;
}

th {
    background-color: green;
    color: white;
}
</style>
<table style>";*/
$ind=1;
$newArr=array();
foreach($matches as $key => $value){
	$value = str_replace("</w:tc>","]",$value);
	$value = str_replace("<w:tc>","[",$value);
	//echo $text2;
	$pattern='/\[([^\]]*)\]/';
	//$htmldata.="<tr id='row_".$key."'>";
	preg_match_all($pattern, $value, $matches2);
	$arrData='';
	//$htmldata.="<td >".$ind."</td>";
	foreach($matches2[1] as $k=>$v){
		$arrData[]=fix_russian(trim(strip_tags($v)));
		//$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
	$arrData = str_replace("####","[",$arrData);
	$arrData = str_replace("#####","]",$arrData);
	$newArr[$ind]=$arrData;
	//$htmldata.="</tr>";
//	echo "<pre>";print_r($matches2[0]);
	$ind++;
}
//$htmldata.="</table>";
//echo "<pre>"; print_r($matches);

//$htmldata = str_replace("####","[",$htmldata);
//$htmldata = str_replace("#####","]",$htmldata);

return $newArr;
}

function fix_russian($value){
	echo $value."==>".mb_detect_encoding($value)."<br />";
	//$value=utf8_decode($value);
	//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-5") ? iconv("ISO-8859-5", "UTF-8", $value) : $value) : '';
	//$value = html_entity_decode(htmlentities($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-5');
	
	//$value=utf8_encode($value);
	//
	//$value = isset($value) ? ((mb_detect_encoding($value) == "ASCII") ? mb_convert_encoding(iconv("ISO-8859-5", "UTF-8", $value), 'ISO-8859-5', 'UTF-8') : $value) : '';
	// setlocale(LC_CTYPE, 'en_AU.utf8');
	//$value= iconv('ISO-8859-5', 'ASCII//TRANSLIT',$value);
	//$value =iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
	//$value = iconv('CP1251', 'UTF-8', $value);
	//$value=mb_convert_encoding($value, 'ISO-8859-5', 'ASCII');
	 //$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
    //$value=recode($value);                   
	return $value;
	
}
function charset_decode_iso_8859_5 ($string) {
// Convert to KOI8-R, then return this decoded.
$string = convert_cyr_string($string, 'i', 'k');
 return$string;
}

/* Windows-1251 is Cyrillic */
function charset_decode_windows_1251 ($string) {
// Convert to KOI8-R, then return this decoded.
 $string = convert_cyr_string($string, 'w', 'k');
return charset_decode_koi8r($string);
}

function detect_encoding($str) {
    $win = 0;
    $koi = 0;

    for($i=0; $i<strlen($str); $i++) {
      if( ord($str[$i]) >224 && ord($str[$i]) < 255) $win++;
      if( ord($str[$i]) >192 && ord($str[$i]) < 223) $koi++;
    }

    if( $win < $koi ) {
      return 1;
    } else return 0;

  }

  // recodes koi to win
  function koi_to_win($string) {

    $kw = array(128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183,  184, 185, 186, 187, 188, 189, 190, 191, 254, 224, 225, 246, 228, 229, 244, 227, 245, 232, 233, 234, 235, 236, 237, 238, 239, 255, 240, 241, 242, 243, 230, 226, 252, 251, 231, 248, 253, 249, 247, 250, 222, 192, 193, 214, 196, 197, 212, 195, 213, 200, 201, 202, 203, 204, 205, 206, 207, 223, 208, 209, 210, 211, 198, 194, 220, 219, 199, 216, 221, 217, 215, 218);
    $wk = array(128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 163, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183,  184, 185, 186, 187, 188, 189, 190, 191, 225, 226, 247, 231, 228, 229, 246, 250, 233, 234, 235, 236, 237, 238, 239, 240, 242,  243, 244, 245, 230, 232, 227, 254, 251, 253, 255, 249, 248, 252, 224, 241, 193, 194, 215, 199, 196, 197, 214, 218, 201, 202, 203, 204, 205, 206, 207, 208, 210, 211, 212, 213, 198, 200, 195, 222, 219, 221, 223, 217, 216, 220, 192, 209);

    $end = strlen($string);
    $pos = 0;
    do {
      $c = ord($string[$pos]);
      if ($c>128) {
        $string[$pos] = chr($kw[$c-128]);
      }

    } while (++$pos < $end);

    return $string;
  }

  function recode($str) {

    $enc = detect_encoding($str);
    if ($enc==1) {
      $str = koi_to_win($str);
    }

    return $str;
  }


?>
