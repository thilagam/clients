<?php
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
  if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='zip'){
    odownloadZIP($_GET['file'], PERNODRICARD_WRITER_FILE_PATH."/dev1/", "dev1Miss.php") ;
  }
if(isset($_POST['submit']))
{ 
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $basiclib=new basiclib();

    if($file1['extension']=='xlsx')
    {
	  $data  = $basiclib->xlsx_optimised_read($_FILES['userfile1']['tmp_name'],1) ;
	  $pernodricard=new pernodricard();
      //$columns=array(8,11,12,13,14,16,17,18,19,20,21,22,23);
      $pathFile=$pernodricard->missedTemplates($data[0][0]);
      //echo "<pre>"; print_r($pathFile);exit;
      /* Uplaod the file for future reference */    
      move_uploaded_file($_FILES['userfile1']['tmp_name'],$pathFile[0].$_FILES['userfile1']['name']);

      /* Create Zip file  */    
      //$zipPath=dirname(rtrim($pathFile[0],'/'));
      $basiclib->zip_creation($pathFile[0],$pathFile[1],'docx');
       
        
    	if (!empty($pathFile))
    	 	header("Location:dev1Miss.php?client=PERNODRICARD&msg=success&file1=".basename($pathFile[1]));
    	else
    	 	header("Location:dev1Miss.php?client=PERNODRICARD&msg=error");
    }
}
else
    header("Location:dev1Miss.php?client=PERNODRICARD");