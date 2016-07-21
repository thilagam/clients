<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");

$dwndFile = SPEC_FLDR . $_REQUEST['u'] . "_files/" . $_REQUEST['dwnd'];
$rmvFile = SPEC_FLDR . $_REQUEST['u'] . "_files/" . $_REQUEST['rmv'];

if( file_exists($dwndFile) && $_REQUEST['dwnd'] )
{
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=".basename($dwndFile));
	header("Content-Type: mime/type");
	header("Content-Transfer-Encoding: binary");
	// UPDATE: Add the below line to show file size during download.
	header('Content-Length: ' . filesize($dwndFile));
	readfile($dwndFile);
	exit;
}

if( $_REQUEST['rmv'] && $_REQUEST['u'] )
{
	if(file_exists($rmvFile))
	{
		chmod($rmvFile, 0777);
		unlink($rmvFile);
	}

	$specXmlInfo = simplexml_load_file(SPEC_FLDR . $_REQUEST['u']);
	$specXmlFiles = explode("|",$specXmlInfo->files);
	unset($specXmlFiles[array_search($_REQUEST['rmv'],$specXmlFiles)]);
	$specXmlFile = implode("|",$specXmlFiles);

	$specXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<spec>
	<owner>".$specXmlInfo->owner."</owner>
	<date_of_launch>".$specXmlInfo->date_of_launch."</date_of_launch>
	<last_modified_date>".$specXmlInfo->last_modified_date."</last_modified_date>
	<info>".$specXmlInfo->info."</info>
	<usage>".$specXmlInfo->usage."</usage>
	<files>".$specXmlFile."</files>
</spec>";

	$fp = fopen(SPEC_FLDR . $_REQUEST['u'], 'w');
	fwrite($fp, $specXml);
	fclose($fp);
	chmod(SPEC_FLDR . $_REQUEST['u'], 0777);
}
?>
<!---->

 <div class="span2" style="float:left;">
    <div id="client-nav"><a class="client-nav" href="JavaScript:void(0)"><i class="icon-user"></i>CLIENTS SPEC LIST</a>

    </div>
<?php
$jsonData = json_decode(file_get_contents(MENU_CONFIG_JSON)) ;

foreach($jsonData->ul as $json)
	$menuItems[strtolower($json->h3)] = $json ;

ksort($menuItems);

$client_menu_style = str_replace(".","",$_REQUEST['u'])."_MENU";
$$client_menu_style = ' style="display:block;"';

$jMenu = '<div id="nallamenu">
	<ul>';

foreach($menuItems as $menuItem) :

	if($menuItem->ul)
	{
		$jMenu .= '
		<li>
			<h3>' . $menuItem->h3 . '</h3>
			<ul ' . ${$menuItem->menus} . '>';
			
		foreach($menuItem->ul as $subMenuItem) :

			$jMenu .= '
				<li>
					<h3 class="sub">' . $subMenuItem->h3 . '</h3>
					<ul class="subitems"' . ${$subMenuItem->menus} . '>';
					
			foreach($subMenuItem->li as $liSubMenuItem)
			{
				$file = ($liSubMenuItem->base ? $liSubMenuItem->base : $subMenuItem->base) . ((!$liSubMenuItem->base && !$subMenuItem->base) ? '' : '.') .$liSubMenuItem->href ;
				$fileordir = SPEC_FLDR . $file ;
				$default_url = !$default_url ? $file . '&client=' . $subMenuItem->client : $default_url ;
				$jMenu .= '<li><a class="subitems" href="' . SPEC_URL . $file . '&client=' . $subMenuItem->client . '">' . $liSubMenuItem->label . '</a></li>                    
						';
				if(!file_exists($fileordir)) { $fp = fopen($fileordir, 'w'); fclose($fp); chmod($fileordir, 0777); }
				if(!is_dir($fileordir.'_files')) { mkdir($fileordir.'_files', 0777); }
			}
			$jMenu .= '
					</ul>                       
				</li>';
		endforeach ;
		$jMenu .= '         
			</ul>
		</li>';
	}
	else
	{
		$jMenu .= '
		<li>
			<h3>' . $menuItem->h3 . '</h3>
			<ul ' . ${$menuItem->menus} . '>';
		
		foreach($menuItem->li as $liMenuItem)
		{
			$file = ($liMenuItem->base ? $liMenuItem->base : $menuItem->base) . ((!$liMenuItem->base && !$menuItem->base) ? '' : '.') . $liMenuItem->href ;
			$fileordir = SPEC_FLDR . $file ;
			$default_url = !$default_url ? $file . '&client=' . $menuItem->client : $default_url ;
			$jMenu .= '
				<li><a href="' . SPEC_URL . $file . '&client=' . $menuItem->client . '">' . $liMenuItem->label . '</a></li>';
				
			if(!file_exists($fileordir)) { $fp = fopen($fileordir, 'w'); fclose($fp); chmod($fileordir, 0777); }
			if(!is_dir($fileordir.'_files')) { mkdir($fileordir.'_files', 0777); }
		}
		$jMenu .= '
			</ul>
		</li>';
	}

endforeach ;

$jMenu .= '
	</ul>
</div>';

echo($jMenu);

if($_POST['submit'] && file_exists(SPEC_FLDR . $_REQUEST['u']))
{
	$xmls = simplexml_load_file(SPEC_FLDR . $_REQUEST['u']);
	$attachment = $xmls->files;
	if($_FILES["attachment"]["size"] > 0)
	{
		$new_attachment = uniqid() . '-' . $_FILES['attachment']['name'];
		$attachment .= (!empty($attachment) ? '|' : '') . $new_attachment ;

		copy($_FILES['attachment']['tmp_name'], SPEC_FLDR . $_REQUEST['u'] . "_files/" . $new_attachment);
		chmod(SPEC_FLDR . $_REQUEST['u'] . "_files/" . $new_attachment, 0777);
		chown(SPEC_FLDR . $_REQUEST['u'] . "_files/" . $new_attachment, 5000);
	}
	unlink(SPEC_FLDR . $_REQUEST['u'] . "_files/" . '54ae71eecfda2-IMG-20141205-WA0025.jpg');

	$wxml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<spec>
	<owner>".$_POST['owner']."</owner>
	<date_of_launch>".$_POST['date_of_launch']."</date_of_launch>
	<last_modified_date>".$_POST['last_modified_date']."</last_modified_date>
	<info>".base64_encode($_POST['info'])."</info>
	<usage>".base64_encode($_POST['usage'])."</usage>
	<techdesc>".base64_encode($_POST['techDesc'])."</techdesc>
	<files>".$attachment."</files>
</spec>";

	$fp = fopen(SPEC_FLDR . $_REQUEST['u'], 'w');
	fwrite($fp, $wxml);
	fclose($fp);
	chmod(SPEC_FLDR . $_REQUEST['u'], 0777);

	header('Location:'.SPEC_URL . $_POST['clientu'] . '&client=' . $_POST['client']);
}

if(!$_REQUEST['u'])
	header('Location:'.SPEC_URL . $default_url);
	
$xmls = simplexml_load_file(SPEC_FLDR . $_REQUEST['u']);

$owner = $xmls->owner;
$date_of_launch = $xmls->date_of_launch;
$last_modified_date = $xmls->last_modified_date;

$info = base64_decode($xmls->info);
$usage = base64_decode($xmls->usage);
$techdesc = base64_decode($xmls->techdesc);
//$info = $xmls->info;
//$usage = $xmls->usage;

$attachmentFiles = implode("\n", array_map("attachmentFiles", explode("|", $xmls->files)));

function attachmentFiles($file)
{
	$file = trim($file);
	$class = substr($file,0,strpos($file,'.'));
	return (!empty($file) ? ('<div id="attachments" class="'.$class.'" ><a href="spec.php?dwnd='.$file.'&u='.$_REQUEST['u'].'">'.$file.'</a>   <i class=icon-trash icon-white" onclick="rmvAttch('."'".$file."', '".$class."'".')"></i></div>') : '');
}
$docs = $xmls->files
?>
	<!--/.well -->
</div><!--/span-->

<?php

?>
	<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="<?=SITE_URL?>/includes/ckfiles/ckeditor.js"></script>
	<script>
	function savespec()
	{
		var info = ($('#info').val()).trim() ;
		var owner = ($('#owner').val()).trim() ;
		if(info=='' || owner=='')
		{
			$('#msgSection').show();
			$('#msg').text('Please enter '+((info=='') ? 'specification details!' : 'owner name!')) ;
			return false;
		}
	}
	$(function() {
		$( "#date_of_launch" ).datepicker();
		$( "#last_modified_date" ).datepicker();
	});
	
/*
$.noConflict();
jQuery(document).ready(function(){
	jQuery('#specInfo').ckeditor({language: 'en'});
	jQuery('#specUsage').ckeditor({language: 'en'});
});
function rmvAttch(attchmt, cls)
{
	if(confirm("Sure you want to delete '"+attchmt+"'"))
	{
		jQuery.post( "spec.php", { "u": "<?=$_REQUEST['u']?>", "rmv": attchmt } );
		jQuery('.'+cls).remove();
	}
}*/

$(document).ready(function(){
	$('#specInfo').ckeditor({language: 'en'});
	$('#specUsage').ckeditor({language: 'en'});
});
function rmvAttch(attchmt, cls)
{
	if(confirm("Sure you want to delete '"+attchmt+"'"))
	{
		$.post( "spec.php", { "u": "<?=$_REQUEST['u']?>", "rmv": attchmt } );
		$('.'+cls).remove();
	}
}

	</script>
	<style>
		#folder,#subfolder{width:200px;}
		.processing {
			border-color: #BCE8F1;
			background: url("progress_bar.gif") no-repeat scroll center center #D9EDF7;
			color: #000000!important;
			font-size: 8pt;
			font-weight: bold;
			padding: 8px;
			text-align: center;
		}
		#msg {
			color:red;
			border-radius: 4px;
			margin-bottom: 20px;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		}
		#ui-datepicker-div{width:205px;height:225px;}
		.success{color:green!important;font-weight: normal !important;}
		textarea{width:600px;}
		#attachments{padding: 5px 0;}
	</style>
<div class="span10 content">    
    <h2 class="heading">Client Specification </h2>        
    <div class="span11">
        <div class="alert alert-info">
            <strong>Specs management - <?=str_replace('_', ' ', $_REQUEST['client'])?></strong>
        </div>
	<div class="alert alert-success" id="msgSection" style="display:none;">
            <strong id="msg"></strong>
        </div> 
		<form name="specForm" action="" id="specForm" onsubmit="return savespec();" method="post" enctype="multipart/form-data">
			<div class="control-group">
				<label class="control-label">Owner :</label>
				<div class="controls">
					<input type="text" name="owner" id="owner" placeholder="Owner name" value="<?=$owner?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Date of launch :</label>
				<div class="controls">                  
					<input type="text" name="date_of_launch" id="date_of_launch" value="<?=$date_of_launch?>" readonly>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Last modified date :</label>
				<div class="controls">                  
					<input type="text" name="last_modified_date" id="last_modified_date" value="<?=$last_modified_date?>" readonly>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Info :</label>
				<div class="controls">                  
					<textarea name="info" id="specInfo" class="ckeditor" placeholder="Specification" rows="5" cols="10"><?=$info?></textarea>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Usage :</label>
				<div class="controls">                  
					<textarea name="usage" id="specUsage" class="ckeditor" placeholder="Usage" rows="5" cols="10"><?=$usage?></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Technical Description :</label>
				<div class="controls">                  
					<textarea name="techDesc" id="specTechDesc" class="ckeditor" placeholder="Usage" rows="5" cols="10"><?=$techdesc?></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">Attachment :</label>
				<div class="controls">                  
					<input type="file" name="attachment"><?=$attachmentFiles?>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">                  
					<button type="submit" value="Save" name="submit" class="btn btn-primary">Save</button>
					<input type="hidden" name="clientu" value="<?=$_REQUEST['u']?>">
					<input type="hidden" name="client" value="<?=$_REQUEST['client']?>">
				</div>
			</div>
		</form>
		
		
<!--	-->	
<div id="windowTitleDialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
	<div class="modal-header">
		<a href="#" class="close" data-dismiss="modal">&times;</a>
		<h3>Please enter a new title for this window.</h3>
	</div>
	<div class="modal-body">
		
	</div>
	<div class="modal-footer">
		
	</div>
</div>


		
    </div>
</div>
