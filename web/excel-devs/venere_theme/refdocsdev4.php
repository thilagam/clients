<?

/**
 * Download docx file for "Venere Writer file Creates Multiple doc files from single xlsx file"
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 MAY 11 2016
 */

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
		echo "HERE";
		 odownloadDoc($_GET['file'], VENERE_THEME_WRITER_FILE_PATH."/dev4/".$_GET['folder']."/", "") ;
	}
?>
