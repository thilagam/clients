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
$client_source_path=constant("CAROLL_XML_PATH");
$pattern = '{'.CAROLL_XML_PATH.'/*.*}';
$arraySource = glob($pattern,GLOB_BRACE);
usort($arraySource, function($a, $b) {
		return filemtime($a) < filemtime($b);
	}); 
?>
<div class="span10 content">				
	<h2 class="heading">Caroll XML Generation</h2>  	
	
	<div class="span11">
		<form class="form-horizontal" name="uploadexcel" action="caroll_xml.php" method="POST" enctype="multipart/form-data" onsubmit="return checkfile();">
			<div class="control-group <?if($err_file)echo "error" ?>">
				<label class="control-label" for="excel_file">Upload Edited Excel File</label>
				<div class="controls">
					<input type="file" id="excel_file" name="excel_file">
					<?if($err_file){?><span class="help-inline"><? echo $err_file; ?></span><?}?>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">SAS Reference : </label>
				<div class="controls">
					<select name="sas_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==23) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">Name Reference : </label>
				<div class="controls">
					<select name="name_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==6) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">Desc Court Reference : </label>
				<div class="controls">
					<select name="short_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==3) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">Desc long Reference : </label>
				<div class="controls">
					<select name="long_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==4) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">Composition Reference : </label>
				<div class="controls">
					<select name="comp_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==9) echo "selected"; ?>><?php echo $i;?></option>
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
					<strong>XML successfully created! <a href="caroll_xml.php?action=download&file=<?=$_REQUEST['file']?>&ref=<?=$_REQUEST['ref']?>">Click here to download</a> / <a target="_view" href="<?=SITE_URL?>/excel-devs/caroll/XML/<?=$_REQUEST['file']?>.xml">View XML</a></span><strong>
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
		<h5>Details des fichiers XML pour CAROLL</h5>
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
