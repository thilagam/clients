<?php
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/includes/spec/' . $_REQUEST['folder'] . '_files/' . $_REQUEST['file'];

$path_info = pathinfo($file_path); // Spec file path info

// Defining application type for file types
$application_xls = "application/xls";

$application_xlsx = "application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

$application_doc = "application/msword";

$application_docx = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";

$application_xml = "text/xml";

$application_zip = "application/octet-stream";

$application_csv = "text/csv";

// File application type
$file_application_type = "application_".strtolower($path_info['extension']);

//exit($$file_application_type . '--' . $path_info['basename']);

if(file_exists($file_path))
{
	// Download spec file
	/*header("Content-type: " . $$file_application_type);
	header("Content-Disposition: attachment; filename=".$path_info['basename']);
	ob_clean();
	flush();
	readfile("$path_file"); 
	exit;*/
	
	// Download spec file
	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers
	header("Content-Type: application/".strtolower($path_info['extension']));
	header("Content-Disposition: attachment; filename=".html_entity_decode(str_replace(" ","_",$path_info['basename']), ENT_COMPAT, 'UTF-8'));
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($file_path));
	ob_end_flush();
	readfile($file_path);
	exit;
}else
	exit($file_path .' is not found !');
?>
