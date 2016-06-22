<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>

<?
$client_source_path=constant("DECATHLON_XML_FILE_PATH2");
$pattern = '{'.DECATHLON_XML_FILE_PATH2.'/*.*}';
$arraySource = glob($pattern,GLOB_BRACE);
usort($arraySource, function($a, $b) {
		return filemtime($a) < filemtime($b);
	}); 
?>
<div class="span10 content">				
	<h2 class="heading">Decathlon XML Generation</h2>  	
	
	<div class="span11">
		<form class="form-horizontal" name="uploadexcel" action="write_xml2.php" method="POST" enctype="multipart/form-data" onsubmit="return checkfile();">
			<div class="control-group <?if($err_file)echo "error" ?>">
				<label class="control-label" for="excel_file">Upload Edited Excel File</label>
				<div class="controls">
					<input type="file" id="excel_file" name="excel_file">
					<?if($err_file){?><span class="help-inline"><? echo $err_file; ?></span><?}?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="titre_reference">TITRE Reference : </label>
				<div class="controls">
					<select name="titre_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==1) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="contenu_reference">CONTENU Reference : </label>
				<div class="controls">
					<select name="contenu_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==2) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="keywords_reference">KEYWORDS Reference : </label>
				<div class="controls">
					<select name="keywords_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==3) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="seotitle_reference">SEO Title Reference : </label>
				<div class="controls">
					<select name="seotitle_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==4) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="metadesc_reference">META DESCRIPTION Reference : </label>
				<div class="controls">
					<select name="metadesc_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==5) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group" style="display:none">
				<label class="control-label" for="linkdex_reference">LINDEX Reference : </label>
				<div class="controls">
					<select name="linkdex_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==6) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<div class="controls">					
					<button type="submit" value="Valider" name="upload" class="btn btn-primary">Valider</button>
				</div>
			</div>			
		</form>
		<?php
			if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
			{
			?>
				<div class="alert alert-success">
					<strong>XML successfully created! <a href="write_xml2.php?action=download&file=<?=$_REQUEST['file']?>">Click here to download</a> / <a target="_view" href="<?=SITE_URL?>/excel-devs/decathlon/xml/<?=$_REQUEST['file']?>.xml">View XML</a></span><strong>
				</div>
			<?	
			}	
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='error')
			{
			?>
				<div class="alert alert-error">
					<strong>Could not create XML<strong>
				</div>
			<?				
			}	
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='file_error')
			{
			?>
				<div class="alert alert-error">
					<strong>Could not read the data file<strong>
				</div>
			<?				
			}	
			?>
	</div>	
		
	<div class="span11">			
		<h5>Details des fichiers XML pour decathlon</h5>
		<table class="table table-bordered">
			<thead>
				<th>Source File</th>
				<th>Derni&egrave;re modif</th>
				<th>Taille du fichier</th>				
			</head>
			<tbody>
				<? 
					$file_url=str_replace(ROOT_PATH,'',$client_source_path);
				?>
				
				<?	if(count($arraySource)>0) :
					foreach($arraySource as $ar):
						$stat = stat($ar);
				?>
					<tr>
						<td> <a href="<?=$file_url.'/'?><?=basename($ar)?>"><?=basename($ar)?></a> </td>
						<td> <?=date("d/m/Y H:i:s",$stat['mtime'])?> </td>
						<td> <?=round($stat['size']/1000,0)?>ko </td>
					</tr>
				<?endforeach;else:?>
					<tr><td colspan="5">No Source files found</td></tr>
				<?endif;?>		
			</tbody>	
		</table>
	</div>
</div>
<script type="text/javascript">
function checkfile()
{
	var error=0;
	var msg='';
	if((document.uploadexcel.excel_file.value.match(/(.xls)$/i)) || (document.uploadexcel.excel_file.value.match(/(.xlsx)$/i)))
	{
		error=error;
	}
	else
	{
		msg=msg+"please upload Excel file.. \n";
		error=error+1;
	}
	
	//alert(error);	
	
	if(error>0)	
	{
		alert(msg);
		return false;
	}
	else
		return true;
}		
</script>	
</form>
<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
