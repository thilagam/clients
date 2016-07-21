<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/korben_functions.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");


?>
<?php
$reference = $_REQUEST['reference'];
$upload_file = $_POST['upload'];
$client='DISTRICENTER';
$valid=$_REQUEST['valid'];

if($upload_file)
{
	if(isset($_FILES['csv_file']))
	{
		$upload = true;
		$filename = $_FILES['csv_file']['name'];	
		$tmpName = $client."_tmp.xlsx";

		if(!is_numeric($reference)&&$reference==0){$err_ref = "Erreur : vous devez entrer un num&eacute;ro de colonne";$upload=false;}
		if(strrchr($filename,'.')!=".xlsx"){$err_file = " Erreur : vous devez charger un fichier Xlsx";$upload=false;}
		
		if($upload) 
		{
			// upload korben source w.r.t client and save the reference in config file.
			$arrayCsv=uploadSource($client,$reference,$_FILES);			
		}
	}
}
else if($valid && $client && $reference)
{
	// upload korben source w.r.t client and save the reference in config file.
	uploadSource($client,$reference,NULL,$valid);//exit;
	 
	// Get All korben source files of a client
	if($client) 
		$arraySource=getAllPreviousSourceFiles($client);	
}
else
{	//Get All korben source files of a client
	if($client) 
		$arraySource=getAllPreviousSourceFiles($client);	
		
}

//getting reference from config file
if($client)
{
	
	$client_source_path=DISTRICENTER_SOURCE_PATH;
	$client_config_file=DISTRICENTER_CONFIG_FILE;	
	
	if(!file_exists($client_config_file))
	{	
		$fp = fopen($client_config_file,"w");
		fclose($fp);
	}
	$ser=file_get_contents($client_config_file);
	//$ser=substr($ser, 0, -1 );
	//var_dump(unserialize($ser));
	$arr_ref_client = unserialize($ser);
	//echo $arr_ref_client;
	//print_r($arr_ref_client);exit;
}

if(!$client)
{

}


//Get All korben source files of a client
function getAllPreviousSourceFiles($client)
{
    $client_source_path=DISTRICENTER_SOURCE_PATH;
	$client_config_file=DISTRICENTER_CONFIG_FILE;	
    
    $pattern = $client_source_path.'/'.$client.'*.*';
   // echo $pattern;exit;
    $arraySource = glob($pattern);  
    //sort($arraySource);
    usort($arraySource, function($a, $b) {
            return filemtime($a) < filemtime($b);
        });
    return $arraySource;
    
}

//upload korben source w.r.t client and save the reference in config file.
function uploadSource($client,$reference,$source_file=NULL,$valid=NULL)
{
     $client_source_path=DISTRICENTER_SOURCE_PATH;
	$client_config_file=DISTRICENTER_CONFIG_FILE;

    if(!$valid)
    {
        $file_name = $client."_tmp.xlsx";
        $source_file_temp = $client_source_path."/$file_name";  
        move_uploaded_file($source_file['csv_file']['tmp_name'], $source_file_temp);
        $arrayCsv = file($source_file_temp);
        return $arrayCsv;
    }
    else if($valid==1)
    {
        $file_name = $client."_tmp.xlsx";
        $old_name2 = $client_source_path."/$file_name";
        
        if(file_exists($old_name2))
        {
            //current becomes old
            $old_name1 = $client_source_path."/$client.xlsx";
            $new_name1 = $client_source_path."/$client"."_".date("YmdHis").".xlsx";
            if(file_exists($old_name1))
                rename($old_name1,$new_name1);

            //temp become new
            $new_name2 = $client_source_path."/$client.xlsx";
            rename($old_name2,$new_name2);

            //reference storing file updation
            $arr_ref_soc[basename($old_name1)] = $reference;
            $fp = fopen($client_config_file,"w");
            fwrite($fp,serialize($arr_ref_soc));
            fclose($fp);    
        }
    }
}
?>	
	
<div class="span10 content">			
	<? if($_REQUEST['client'])	{?>
	<h2 class="heading"><? echo str_replace("_",' ',$_REQUEST['client']);?> :: SOURCE UPLOAD</h2>  	
	
	<div class="span11">
		<form class="form-horizontal" name="upload-csv" action="" method="POST" enctype="multipart/form-data">
			<div class="control-group <?if($err_file)echo "error" ?>">
				<label class="control-label" for="csv_file">Nouveau fichier source</label>
				<div class="controls">
					<input type="file" id="csv_file" name="csv_file">
					<?if($err_file){?><span class="help-inline"><? echo $err_file; ?></span><?}?>
				</div>
			</div>
			<div class="control-group <? if($err_ref) echo "error" ?>">
				<label class="control-label" for="reference">Colonne de r&eacute;f&eacute;rence</label>
				<div class="controls">
					<input type="text" name="reference" id="reference" placeholder="reference">
					<?if($err_ref){?><span class="help-inline"><? echo $err_ref; ?></span><?}?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">					
					<button type="submit" value="Valider" name="upload" class="btn btn-primary">Valider</button>
				</div>
			</div>
			<input type="hidden" name="client" value="<? echo$_REQUEST['client']; ?>">
		</form>
	</div>
	
	<? if($upload_file&&$upload):
	//echo "<pre>";print_r($arrayCsv);exit;
	?>
	
	<div class="span11">		
			<h5>D&eacute;tails du fichier source envoy&eacute; pour <? echo $client?> : <? echo $dest?> 
				<a class="label label-info" href="upload-source.php?client=<? echo $client?>&valid=1&reference=<? echo $reference?>">Mettre &agrave; jour le fichier source</a>
			</h5>
			<table class="table table-bordered" style="overflow-x:scroll" width="90%">
				<tr>
				<?$line=explode(";",$arrayCsv[0]);$y=0;?>
					<? foreach($line as $l):$y++?>
					<th <?if($y==$reference)echo 'class="error"'?>><? echo $l?></th>	
					<? endforeach;?>	
				</tr>			
				<? for($i=1;$i<count($arrayCsv);$i++):$line=explode(";",$arrayCsv[$i]);$y=0;?>
				<tr>
					<? foreach($line as $l):$y++?>
					<td <?if($y==$reference)echo 'class="error"'?>><? echo $l?></td>	
					<? endforeach;?>
				</tr>
				<? endfor;?>
			</table>	
			<a class="label label-info" href="upload-source.php?client=<? echo $client?>&valid=1&reference=<? echo $reference?>">Mettre &agrave; jour le fichier source</a>
	</div>
	<? else : ?>	
	<div class="span11">			
		<h5>Details des fichiers sources pour <?=$client?></h5>
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
	<?endif?>
	
<?}else{?>
		<p class="text-center text-error span12">Client Parameter Missed</p>
<?}?>

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
