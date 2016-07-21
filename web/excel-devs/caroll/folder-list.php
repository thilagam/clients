<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file'])){
	if(file_exists(CAROLL_WRITER_FILE_PATH.'/'.$_GET['file'].'.xlsx'))
		odownloadXLSX($_GET['file'].".xlsx", CAROLL_WRITER_FILE_PATH, "folder-list.php?client=CAROLL") ;
	elseif(file_exists(CAROLL_WRITER_FILE_PATH.'/'.$_GET['file'].'.xls'))
		odownloadXLS($_GET['file'].".xls", CAROLL_WRITER_FILE_PATH, "folder-list.php?client=CAROLL") ;
}

include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>

<div class="span10 content">			
	<h2 class="heading">CAROLL :: Generate Writer XLS Files</h2>  

<?php	

//get All LBM folders
$folders_list=getCAROLLFoldersList();

$client_path=CAROLL_PATH;

$url=str_replace(ROOT_PATH,'',$client_path)."/write_final_xls.php";

$xlsurl=str_replace(ROOT_PATH,'',$client_path)."/caroll_cron.php";
		
	if(count($folders_list)>0):
?>
	
		<div class="">
			<table class="table table-bordered">
				<thead>
					<th>Folder</th>					
					<th>Folder images</th>					
					<th>Writer XLS</th>
				</thead>
				<tbody>								
					<?php
					foreach($folders_list as $folder)
					{	
						$check_dir=str_replace(".zip","",$folder);
						
						if(is_dir($check_dir) AND !strpos($folder, '.zip'))
						{	
							chmod($check_dir,0777);									
							$img_count=getImagesCount($check_dir);
							
							
							?><tr>	
							
							
							<td><a href="<?=CAROLL_URL?>/reference.php?client=CAROLL#<?=basename($check_dir)?>" target='_blank' ><?=basename($check_dir)?></a></td>							
							<td><?=$img_count?></td>						
							
							<td><a href="<?=$xlsurl?>?folder_id=<?=basename($check_dir)?>">Re-create</a> / <a href="<?=CAROLL_URL?>/folder-list.php?client=CAROLL&type=final&action=download&file=Writer_Final_<?=basename($check_dir)?>">Download</a>
							
							<?
							$file_path = CAROLL_WRITER_FILE_PATH."/Writer_Final_".basename($check_dir).".xls";
							$old_file_path= CAROLL_WRITER_FILE_PATH."/Writer_Final_".basename($check_dir)."_old.xls";
							$xls3_path=CAROLL_WRITER_FILE_PATH."/Writer_Final_3_".basename($check_dir).".xls";
							
							if(file_exists($xls3_path) && !is_dir($xls3_path) )
							{
								?> / <a href="<?=CAROLL_URL?>/folder-list.php?client=CAROLL&type=xls3&action=download&file=Writer_Final_3_<?=basename($check_dir)?>">Final xls3</a>
							<?}?>
							
							
								</td>
							</tr>
						<?		  
							
						}
						else if(!is_dir($check_dir) OR is_empty_dir($check_dir))
						{							
							$img_count=getImagesCount($check_dir);
							
							?><tr>
							
							
							<td><?=basename($folder)?><b style="color:red"> New</b></td>							
							<td><?=$img_count?></td>							
							<td> - </td>
							</tr>
							
						<?	
						}						
						$loop++;					
					}
				?>
			</table>				
		</div>
	<? else : ?>	
		<p class="text-center text-error span12">No Folders found</p>
	<?endif;?>
</div>
<?php
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
{
	$type=$_GET['type'];
	downloadXLS($_GET['file'],$type);
}
function downloadXLS1($file,$type)
{
	global $url;
	$filename=$file.".xls";
	$path_file=CAROLL_WRITER_FILE_PATH."/".$filename;
	
	
	if(file_exists($path_file))
	{	
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: private");
		header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-excel;");
		header("Accept-Ranges: bytes");
		header('Content-Disposition: attachment; filename="'.basename($path_file).'"');
		header("Content-Length: ".filesize($path_file));
		ob_clean();
		flush();
		readfile($path_file);		
		exit;
	}	
	else
	{
		if($type=='final')
		{			
			$id=str_replace("Writer_Final_","",$file);
			//echo $url;exit;
			header("Location:$url?folder_id=$id");
		}			
		else if($type=='xls3')
		{
			echo $path_file." Not Exist";
		}
	}	
}

?>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
