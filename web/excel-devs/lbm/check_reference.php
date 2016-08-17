<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu1.php");
?>

<div class="span10 content">			
	<h3 class="heading">LEBONMARCHE Reference Cheking in All FTP folders,Source and Old writer Files</h3>  
	<div class="span11">
		<form class="form-horizontal" name="uploadexcel" action="check_reference_write_xls.php" method="POST" enctype="multipart/form-data" onsubmit="return checkfile();">
			<div class="control-group <?if($err_file)echo "error" ?>">
				<label class="span4 control-label" for="userfile1">LEBONMARCHE Reference XLS/XLSX/CSV</label>
				<div class="span4  controls">
					<input type="file" id="userfile1" name="userfile1">
					<?if($err_file){?><span class="help-inline"><? echo $err_file; ?></span><?}?>
				</div>
			</div>
			<div class="control-group">
				<label class="span4 control-label" for="reference">Reference : </label>
				<div class="span4  controls">
					<select name="index_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==2) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
			<div class="control-group">
				<label class="span4 control-label"></label>
				<div class="span4 controls">					
					<button type="submit" value="Valider" name="upload" class="btn btn-primary">Valider</button>
				</div>
			</div>			
		</form>		
		<?php
			 if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
			{
			?>
				<div class="alert alert-success">
					<strong>Reference File successfully generated! <a href="check_reference_write_xls.php?action=download&file=<?=$_REQUEST['file']?>&ref=<?=$_REQUEST['ref']?>">Click here to download</a><strong>
				</div>
			<?	
			}	
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='error')
			{
			?>
				<div class="alert alert-error">
					<strong>Could not save spreadsheet<strong>
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
		<?php echo $_SESSION['table_data'];//unset($_SESSION['table_data']);?>		
	</div>	
	
</div>

<script type="text/javascript">
function checkfile()
{
	var error=0;
	var msg='';
	if((document.uploadexcel.userfile1.value))
		{
			error=error;
		}
		else
		{
			msg=msg+"please upload LEBONMARCHE XLS/XLSX/CSV \n";
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
<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
