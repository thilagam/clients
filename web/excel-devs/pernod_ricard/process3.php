<?php

/**
 * Pernod Ricard Upload Translation to DB
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
* @since      1.0 MAY 23 2016
 */
 

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(PERNODRICARD_PATH."/pernodricard.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(INCLUDE_PATH."/upload.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
  if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
	 odownloadXLS($_GET['file'], PERNODRICARD_WRITER_FILE_PATH."/dev3/", "dev3.php") ;
  }
if(isset($_POST['submit']))
{ 
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $language = $_POST['lang'];
    $basiclib=new basiclib();

    if($file1['extension']=='xlsx')
    {
	  $data  = $basiclib->xlsx_optimised_read($_FILES['userfile1']['tmp_name'],52) ;
	    //echo "<pre>"; print_r($data[0][0]);exit;
	  $pernodricard=new pernodricard();
	  //$columns=array(8,11,12,13,14,16,17,18,19,20,21,22,23);
      $columns=array('H','K','L','M','N','P','Q','R','S','T','U','V','W');
      $filename = PERNODRICARD_WRITER_FILE_PATH."/dev3/pernodricard-delivery-".uniqid().".xlsx";
       
        
    	if ($pernodricard->writeXlsxPernodRicard($data[0][0],$columns,$filename,$language))
    	 	header("Location:dev3.php?client=PERNODRICARD&msg=success&file1=".basename($filename));
    	else
    	 	header("Location:dev3.php?client=PERNODRICARD&msg=error");
    }
}
else
    header("Location:dev3.php?client=PERNODRICARD");
