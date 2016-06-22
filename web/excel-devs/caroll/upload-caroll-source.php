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
<?php
$client_source_path=constant("CAROLL_REF_SOURCE_PATH");
$client_config_file=constant("CAROLL_REF_CONFIG_FILE");	



if(isset($_POST['upload']))
{
	$ref = $_REQUEST['index_reference'];	
	
	//upload file and display details
	
	if(isset($_FILES['excel_file']))
	{
		$test = true;
		$filename = $_FILES['excel_file']['name'];
		$tmpName = "CAROLL_SOURCE_tmp.xls";
		if(!is_numeric($ref)&&$ref==0){$err_ref = "Erreur : vous devez entrer un numéro de colonne";$test=false;}
		if(strrchr($filename,'.')!=".xls"){$err_file = " Erreur : vous devez charger un fichier XLS";$test=false;}
		
		if($test) 
		{
			$dest = "$client_source_path/$tmpName";
			move_uploaded_file($_FILES['excel_file']['tmp_name'], $dest);			
		}
		//replace temp file and store ref column

		$old_name2 = $client_source_path."/CAROLL_SOURCE_tmp.xls";	
		
		if(file_exists($old_name2))
		{
			//current becomes old
			$old_name1 = "$client_source_path/CAROLL_SOURCE.xls";
			$new_name1 = "$client_source_path/CAROLL_SOURCE"."_".date("YmdHis").".xls";
			rename($old_name1,$new_name1);
			chmod($new_name1,0777);
			
			
			//temp become new
			$new_name2 = "$client_source_path/CAROLL_SOURCE.xls";
			rename($old_name2,$new_name2);
			chmod($new_name2,0777);
			//echo $old_name2."=>".$new_name2."<br/>";
				
			//ref storing file updation
			$arr_ref_soc[basename($old_name1)] = $ref;
			$arr_ref_soc['updated'] = 'yes';
			$fp = fopen($client_config_file,"w");
			fwrite($fp,serialize($arr_ref_soc));
			fclose($fp);	
		}
		header("Location:upload-caroll-source.php");
	}
	else
		header("Location:upload-caroll-source.php");	
}
else
{
	//getting reference from config file	
	$arr_ref_client = unserialize(file_get_contents($client_config_file));	
	//Getting All previous Source files

	$pattern = '{'.$client_source_path.'/CAROLL_SOURCE.*,'.$client_source_path.'/CAROLL_SOURCE[\_][!A-Za-z]*.xls}';
	//$pattern = $client_source_path.'/CAROLL_SOURCE*.*';
	$arraySource = glob($pattern,GLOB_BRACE);
	/* usort($arraySource, function($a, $b) {
		return filemtime($a) > filemtime($b);
	}); */
}	

?>	
	
<div class="span10 content">				
	<h2 class="heading">CAROLL  :: FICHIER PRISE DE NOTES FILE UPLOAD</h2>  	
	
	<div class="span11">
		<form class="form-horizontal" name="uploadexcel" action="" method="POST" enctype="multipart/form-data" onsubmit="return checkfile();">
			<div class="control-group <?if($err_file)echo "error" ?>">
				<label class="span4 control-label" for="excel_file">Fichier prise de notes Caroll</label>
				<div class="span4 controls">
					<input type="file" id="excel_file" name="excel_file">
					<?if($err_file){?><span class="help-inline"><? echo $err_file; ?></span><?}?>
				</div>
			</div>
			<div class="control-group">
				<label class="span4 control-label" for="reference">Reference : </label>
				<div class="span4 controls">
					<select name="index_reference">
					<?php
					for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) :
					?><option value="<?php echo $j;?>" <?php if($j==2) echo "selected"; ?>><?php echo $i;?></option>
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
	</div>	
	
	<!-- 3 Rows of Input file -->
	<?php $header_rows=1; 
      $latest_source_file = display_input_xls_xlsx_file("caroll-source-files/CAROLL_SOURCE.xls",6);
      
?>	<?php //echo "<pre>";print_r($latest_source_file); ?>
  <?php if(count($latest_source_file) > 0) { ?>
   	<div class="span11">
		<div style="overflow-y: scroll;height:200px;width:100%;font-size:10px;box-shadow:2px 2px 15px #000;">
	     <table class="table table-striped table-bordered">
	<?php for($i=1;$i<=count($latest_source_file);$i++){
		   if($i <= $header_rows) { $columns_show=0; ?>
		     <thead><tr class="alert-success" style="text-transform: uppercase;">
			<?php for($j=1;$j<=count($latest_source_file[$i])+1&& !empty($latest_source_file[$i][$j]);$j++) { ?> 
				    <th style="white-space: nowrap;"><?php echo $latest_source_file[$i][$j]; $columns_show=intval($j); ?></th>	
			<?php } ?>	    		     				  
			</tr></thead>		 
		<?php }else{ ?>
			  <tbody><tr>
			<?php for($j=1;$j<=count($latest_source_file[$i])+1;$j++){ ?> 
				        <?php if($j <= $columns_show) { ?>
				         <td><?php echo $latest_source_file[$i][$j]; ?></td>	
			<?php } } ?>	  	    		     				  
			</tr></tbody>                   
        <?php } }?>            
		</table>	
	   </div>
	   <br /><br />	
	</div>
    <?php } ?>
	
	
	
	
		
	<div class="span11">			
		<h5>Details des fichiers ref sources pour CAROLL</h5>
		<table class="table table-bordered">
			<thead>
				<th>Source File</th>
				<th>Derni&egrave;re modif</th>
				<th>Taille du fichier</th>
				<th>Colonne r&eacute;f&eacute;rence</th>
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
						<td> <?if($arr_ref_client[basename($ar)]):?> <?=$arr_ref_client[basename($ar)]?><?else: echo "-"; endif;?> </td>							
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
	if((document.uploadexcel.excel_file.value.match(/(.xls)$/i)))
		{
			error=error;
		}
		else
		{
			msg=msg+"please upload Excel file (only .xls file) \n";
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
