<?php 

/**
 * Gariner upload docx content to DB
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 May 23 2016
 */

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(GARNIER_PATH."/garnier.php");
 
$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file'])){
	
	odownloadZIP($_GET['file'],GARNIER_WRITER_FILE_PATH."/dev2/", "index.php?client=GARNIER");

}
$basiclib=new basiclib();
if(isset($_POST['submit']))
{ 
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
   
    if($file1['extension']=='XLSX' || $file1['extension']=='xlsx')
    {
		if($file1['extension'] == 'xlsx')
		{
			$xls1Arr  =$basiclib->xlsx_read($_FILES['userfile1']['tmp_name']) ;
			
		}
		//echo "<pre>"; print_r($xls1Arr);exit;
		$garnier=new garnier();

		$templates=$garnier->process_dev2($xls1Arr);

		//$tempaltes=true;
	    if ($templates!='')
	    	 	header("Location:dev2.php?client=GARNIER&msg=success&file=".$templates);
	    	else
	    	 	header("Location:dev2.php?client=GARNIER&msg=error");
    }
}
else
    header("Location:dev2.php?client=GARNIER");

   

?>
