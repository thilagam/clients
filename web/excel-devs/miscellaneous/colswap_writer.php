<?php
/**
 * Swap Dev :- Enterchange Colums value as per the input Ex: B to A
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Apr 13, 2015
 */
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH.'/html_to_doc.inc.php');
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext=$file1['extension'];
    
    $swapFrom = $_POST['input_reference'];
    $swapTo = $_POST['output_reference'];
    
    if($ext == 'zip' || $ext == 'rar')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   MISC_PATH."/swap-writer-file/".$_FILES['userfile1']['name'] ;
		  move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;
		  
		  if($ext=='rar')
			{
				$zip_file=pathinfo($srcFile);
				$zip_file['filename']   =   str_replace(" ","-",$zip_file['filename']) ;
				$path   =   $zip_file['dirname']."/".$zip_file['filename'].".rar" ;
				$rar_file = rar_open($path);
				$list = rar_list($rar_file);
				
				foreach($list as $file) {       
					preg_match('/RarEntry for file "(.*)"/', $file, $matches) ;
					if(strstr($file, 'RarEntry for file'))
					{
						$entry = rar_entry_get($rar_file, $matches[1]) or die("Failed to find such entry") ;
						$entry->extract(false, $zip_file['dirname']."/".$zip_file['filename']."/".(str_replace(" ","-",$matches[1])));
					}
				}
				rar_close($rar_file);
				$unzip_dir  =   $zip_file['dirname']."/".$zip_file['filename'] ;
				chmod($unzip_dir,0777) ;
			}
			else
			{
				chmod($srcFile, 0777);
				$unzip_dir = unzipfolder($srcFile);
				
			}
			
            if ($handle = opendir($unzip_dir))
			{
				while ($entry = readdir($handle)) {
					if($entry != "." && $entry != ".."){
			        $path = $unzip_dir."/".$entry;
			        //echo "<pre>";
			        //print_r (xlsx_read($path));					
			        //echo "</pre>";
			        $arr = xlsx_read($path);
			        for($i=0;$i<sizeof($arr[0][0]);$i++){
						$temp = $arr[0][0][$i][$swapTo];
						$arr[0][0][$i][$swapTo] = $arr[0][0][$i][$swapFrom];
						$arr[0][0][$i][$swapFrom] = $temp;			         
			        }
			        //echo "<pre>";
			        //print_r ($arr);
			        //echo "</pre>";
			        $path_new = $unzip_dir."/".$entry;
			        writeXlsx($arr[0][0],$path_new);
			        
				  }
			    }
			}
			closedir($handle);
			//echo $unzip_dir."<br />";
			//print_r($file1); $srcFile."<br />";
			$basiclib->zip_creation($unzip_dir.'/',MISC_PATH."/swap-writer-file/swapped_".$file1['filename'].".zip",'xlsx');
					
			header("Location:colswap.php?msg=success&file=swapped_".$file1['filename'].".zip");		
    }
    else
    {
            header("Location:colswap.php?msg=file_error");
    }
    	
}
else
    header("Location:colswap.php");
	
?>
