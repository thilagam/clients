<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

 // You need to add server side validation and better error handling here
	if( ! ini_get('date.timezone') )
		{
   			date_default_timezone_set('UTC');
		}
$data = array();
if(empty($_FILES)){
	$data['error']='Please Select Files to Upload';
}
else{
	
	

//print_r($_FILES);
if(isset($_GET['files']))
{	
	$error = false;
	$files = array();
	$original=array();
	
	$uploaddir = MISC_TAG_UPLOAD_FILE_PATH.'/';
	foreach($_FILES as $file)
	{
		$filename= uniqid().basename($file['name']);
		if(move_uploaded_file($file['tmp_name'],$uploaddir.$filename))
		{
			$files[] = $filename;
			$original[]=$file['name'];
		}
		else
		{
		    $error = true;
		}
	}
	$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
	
	/** Include path **/
	//set_include_path(get_include_path() . PATH_SEPARATOR . '../libraries/');
	
	/** PHPExcel_IOFactory */
	include_once INCLUDE_PATH.'/PHPExcel/IOFactory.php';
	
	//$inputFileName = dirname( dirname(__FILE__) ).'/assets/uploads/'.$files[0];
	$inputFileName = MISC_TAG_UPLOAD_FILE_PATH.'/'.$files[0];
	//$inputFileName2=dirname( dirname(__FILE__) ).'/assets/uploads/'.$files[1];
	//echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory to identify the format<br />';
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
	
	
	//echo '<hr />';
	
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	
	//$objPHPExcel = PHPExcel_IOFactory::load($inputFileName2);
	//$sheetData2 = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	//echo "<pre>";
	//print_r($sheetData);
	
	$data['columns_sheet1']=$sheetData[1];
//	$data['columns_sheet2']=$sheetData2[1];
	$data['filename1']=$original[0];
//	$data['filename2']=$original[1];
	$data['savedfilename1']=$files[0];
//	$data['savedfilename2']=$files[1];
	
}
else
{
	$data['success'] = 'Form was submitted';$data['formData'] = $_POST;
}
//print_r($data);
}
echo json_encode($data);

?>