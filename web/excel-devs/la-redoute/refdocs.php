<?
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

//print_r($_GET);exit;
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
//print_r($_GET);
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	print_r (pathinfo($_GET['file'], PATHINFO_EXTENSION));
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='docx'){
		//echo "HERE";
		 odownloadDoc($_GET['file'], LA_REDOUTE_WRITER_FILE_PATH."/dev1/".$_GET['folder']."/", "") ;
	}
?>
