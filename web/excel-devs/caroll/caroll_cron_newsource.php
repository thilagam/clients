<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

/*checking Ep source updated or not**/
$ep_src_ref_file = CAROLL_EP_CONFIG_FILE;
	
	if(!file_exists($ep_src_ref_file))
	{
		$fp = fopen($ep_src_ref_file,"w");
		fclose($fp);
	}
		$arr_ep_ref_soc = unserialize(file_get_contents($ep_src_ref_file));	
		$ep_source_updated=$arr_ep_ref_soc['updated'];
/*checking Caroll source updated or not**/
$caroll_src_ref_file = CAROLL_REF_CONFIG_FILE;
	
	if(!file_exists($caroll_src_ref_file))
	{
		$fp = fopen($caroll_src_ref_file,"w");
		fclose($fp);
	}
		$arr_caroll_ref_soc = unserialize(file_get_contents($caroll_src_ref_file));	
		$caroll_source_updated=$arr_caroll_ref_soc['updated'];		
	
//echo $ep_source_updated."--".$caroll_source_updated;exit;
		
if($ep_source_updated=='yes' || $caroll_source_updated=='yes')
{
	$directory = CAROLL_IMAGE_PATH."/";	
	$folders = glob($directory."*");
	
	$fcount=count($folders);	
	if($fcount>0)
	{
		foreach($folders as $dir)
		{
			
			$check_dir=str_replace(".zip","",$dir);
			$dir_exist=is_dir($check_dir);
			//echo basename($dir)."|<br>"; && (basename($dir)=='2014_09_16_CAROLL_PO_P30_JPEG_BD')
			if($dir_exist AND !strpos($dir, '.zip'))
			{	
				$folder=basename($dir);
				$filename="Writer_Final_".$folder;
				$path_file=CAROLL_WRITER_FILE_PATH."/".$filename.".xls";
				//echo $path_file;exit;				
				$new_name = CAROLL_WRITER_FILE_PATH."/".$filename."_".date("YmdHis").".xls";
				$xls3_path=CAROLL_WRITER_FILE_PATH."/Writer_Final_3_".$folder.".xls";	
				
				if(file_exists($path_file))
				{
					//current becomes old
					 
					//unlink($new_name);
					rename($path_file,$new_name);
					//chmod($new_name,0777);
										
				}			
					
				echo "Creating the file .....$filename.xls\n<br>";
			
				$url=CAROLL_URL.'/write_final_xls.php?folder_id='.$folder;
				//$url = urlencode($url);				
				file_get_contents($url);
					
				//sleep(300);				
				if(file_exists($new_name))
				{
					//unlink($xls3_path);
					$xls3_url=CAROLL_URL.'/write_final_xls_3.php?folder_id='.$folder;
					//echo $xls3_path;
					//Renaming existing file
					if(file_exists($xls3_path))	
					{
						
						$new_name1 = CAROLL_WRITER_FILE_PATH."/Writer_Final_3_".$folder."_".date("YmdHis").".xls";
						rename($xls3_path,$new_name1);
						//chmod($new_name1,0777);
					}					
					file_get_contents($xls3_url);
				}	
				//break;
				
			}
			
		}	
	}
	//exit;
	//Ep ref  file updation
	
	$arr_ep_ref_soc['updated'] = 'no';
	$fp = fopen($ep_src_ref_file,"w");
	fwrite($fp,serialize($arr_ep_ref_soc));
	fclose($fp); 
	
	//Caroll ref  file updation
	
	$arr_caroll_ref_soc['updated'] = 'no';
	$fp = fopen($caroll_src_ref_file,"w");
	fwrite($fp,serialize($arr_caroll_ref_soc));
	fclose($fp);
	
}
else
	echo "No New Source Found";
