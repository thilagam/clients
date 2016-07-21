<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<div class="span10 content">	
	<h2 class="heading">Andre excel dev</h2>  		
	<div class="span11">
		    <div class="alert alert-info">
				<strong>Upload andre source file to generate writer file</strong>
			</div>
		<form method="POST" class="form-horizontal" action="write_xls.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
			<div class="control-group">
				<label class="control-label" for="excel_file">Andre XLS/XLSX : </label>
				<div class="controls">
					<input type="file" id="excel_file" name="excel_file">					
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="reference">Reference : </label>
				<div class="controls">
					<select name="index_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==6) echo "selected"; ?>><?php echo $i;?></option>
					<?php endfor;?>
					</select>			
				</div>
			</div>
            <div class="control-group">
                <label class="control-label">Output </label>
                <div class="controls">
                    <input type="radio" name="op" id="opformat" value="xls" /> xls <input type="radio" name="op" id="opformat" value="xlsx" checked /> xlsx
                </div>
            </div>
			<div class="control-group">
				<div class="controls">					
					<button type="submit" value="Valider" name="upload" class="btn btn-primary">Upload</button>
				</div>
			</div>
			<?php
			if(isset($_REQUEST['msg']) && ($_REQUEST['msg']=='success') && $_REQUEST['zip'])
			{
			?>
				<div class="alert alert-success">
					<strong>Writer File successfully generated! <a href="write_xls.php?action=download&zip=<?=$_REQUEST['zip']?>">Click here to download</a><strong>
				</div>	
			<?		
			}	
			else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
			{
			?>
				<div class="alert alert-success">
					<strong>Writer File successfully generated! <a href="write_xls.php?action=download&file=<?=$_REQUEST['file']?>&ref=<?=$_REQUEST['ref']?>">Click here to download</a><strong>
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
		</form>
	</div>

<script type="text/javascript">
	function checkfile()
	{
		var error=0;
		var msg='';
		if((document.importexcel.excel_file.value.match(/(.xlsx)$/i)) || (document.importexcel.excel_file.value.match(/(.xls)$/i)))
		{
			error=error;
		}
		else
		{
			msg=msg+"please upload xls/xlsx file. \n";
			error=error+1;
		}

		if(error>0)	
		{
			alert(msg);
			return false;
		}
		else
			return true;
	}
	</script>		
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>

