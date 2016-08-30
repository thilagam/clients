<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
if (!ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

/** PHPExcel_IOFactory */
include_once INCLUDE_PATH.'/PHPExcel/IOFactory.php';

$data = array();
function convert_accent($string)
{
    return htmlspecialchars_decode(htmlentities(utf8_decode($string)));
}

function xlstags($file1, $col1 ) {

	$path = MISC_TAG_UPLOAD_FILE_PATH . '/';
	$objPHPExcel = PHPExcel_IOFactory::load($path . $file1);
	$sheetData = $objPHPExcel -> getActiveSheet() -> toArray(null, true, true, true);
	//echo "<pre>";print_r($sheetData);

	$objPHPExcel = PHPExcel_IOFactory::load($path . $file1);
	foreach ($sheetData as $key => $value) {
		$temp1 = $value[$col1];
		$arr = array();
		$tags='';
		//$data = "<span style='color:green;'>aa<em>BB</em><ul><li>one</li><li>two</li><li>three</li></ul></span>";
		$results = preg_match_all('~<([^/][^>]*?)>~',$temp1, $arr); 
		$results2 = preg_match_all('~</([^/][^>]*?)>~',$temp1, $arr2);
		$new_arr=array(
				$arr[0],$arr2[0]
				);
		foreach ($new_arr[0] as $key2 => $value2) {
			$end=(isset($new_arr[1][$key2]))? $new_arr[1][$key2] :'';
			$tags.= $value2.$end;
		}
		//echo $tags;
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$key,$tags);

	}
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($path . $file1);
	
	return TRUE;
}

if ($_POST) {

	//print_r($_POST);
	$file1 = (isset($_POST['filename1'])) ? $_POST['filename1']:'';	
	$col1 = (isset($_POST['col_file1'])) ? $_POST['col_file1']:'';

	$res=false;
	if ($file1 == ''  || $col1 == '') {
		$data['error'] = 'Validation Error Please select all Fields ';
	} else {
		// $res = '';
		$res = xlstags($file1,$col1);
		
		$data['error']='';
	}
	if($res){
		$webpath = MISC_TAG_UPLOAD_URL . '/';
		$data['success'] = "Success creating Comparison File";
		$data['file'] = $webpath . $file1;
	}
}
echo json_encode($data);
?>
