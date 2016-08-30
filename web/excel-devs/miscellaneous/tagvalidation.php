<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<!--<link href="/css/chosen.css" type="text/css" rel="stylesheet" />-->
<div class="span10 content">    
    <h2 class="heading">Tag validation</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Highlighting selected columns with invalid html tags which entered !</strong>
            </div>
        <form method="POST" class="form-horizontal" action="tagvalidation_xls.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">File XLS/XLSX : </label>
                <div class="controls">
                    <div style="position:relative;">
                        <a class='btn btn-' href='javascript:;'>
                            fichier excel..
                            <input type="file" style='position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="userfile1" id="userfile1" size="40"  onchange='$("#upload-file-info1").html($(this).val());'>
                        </a>
                        &nbsp;
                        <span class='label label-success' id="upload-file-info1"></span>
                    </div>
                </div>
            </div>
	    <div class="control-group">
                <label class="control-label">Tags<span class="f_req">*</span></label>
                <div class="controls">
		    <input type="text" name="tags" id="tags" class="span10" value="h2,p,ul,li,a,strong">
<div class="help-block">Please enter tags sperated by comma </div>
                </div>
            </div>
	    <div class="control-group" id="refs">
		<div id="refRow1">
			<label class="control-label">Reference</label>
		        <div class="controls">                  
		            <select name="reference[]" style="width:150px" class="chzn_a" id="refsel1">
		                <?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?>
		                    <option value="<?php echo $j;?>" <?php if($j==9) echo "selected"; ?>><?php echo $i;?></option>
		                <?php } ?>
		            </select>
		            <span id="adrmv1">&nbsp;
		                <a class="btn" id="ad1" onclick="addref(1)"><i class="icon-plus"></i></a>
		            </span>
		        </div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Mots-cl&#233;s traduits ajout&#233;s avec succ&#232;s ! <a href="tagvalidation_xls.php?action=download&file=<?=$_REQUEST['file']?>">Cliquez ici pour t&#233;l&#233;charger le fichier</a><strong>
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
<!--<script src="http://harvesthq.github.io/chosen/chosen.jquery.js"></script>-->
<script type="text/javascript">
$(document).ready(function() {
	//$(".chzn_a").chosen({allow_single_deselect : true});
});
   function addref(id)
   {
        idx = parseInt(id);
	$('#adrmv'+idx).html('');
	$('#refs').append('<div id="refRow'+(idx+1)+'"><label class="control-label"></label><div class="controls"><select name="reference[]" class="chzn_a" style="width:150px" id="refsel'+(idx+1)+'"><?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?><option value="<?php echo $j;?>" <?php if($j==9) echo "selected"; ?>><?php echo $i;?></option><?php } ?></select><span id="adrmv'+(idx+1)+'">&nbsp;<a class="btn" onclick="addref('+(idx+1)+')"><i class="icon-plus"></i></a>&nbsp;<a class="btn" onclick="rmvref('+(idx+1)+')"><i class="icon-minus"></i></a></span></div></div>');
	/*$(".chzn_a").chosen({
	   allow_single_deselect : true,
	   disable_search : true
	});
    	$("#refsel"+(idx+1)).trigger("liszt:updated");*/
    }
    
    function rmvref(id)
    {
        idx = parseInt(id);
	$('#refRow'+idx).remove();
	if(idx==2)
		$('#adrmv'+(idx-1)).html('&nbsp;<a class="btn" onclick="addref('+(idx-1)+')"><i class="icon-plus"></i></a>');
	else
		$('#adrmv'+(idx-1)).html('&nbsp;<a class="btn" onclick="addref('+(idx-1)+')"><i class="icon-plus"></i></a>&nbsp;<a class="btn" onclick="rmvref('+(idx-1)+')"><i class="icon-minus"></i></a>');
    }

function checkfile()
{
    var error=0;
    var msg='';
    if( document.importexcel.userfile1.value.match(/(.xlsx)$/i) ||document.importexcel.userfile1.value.match(/(.xls)$/i) )
    {
	if(document.importexcel.tags.value.trim()=='')
	{
	   msg=msg+"please enter tags(seperated by comma) \n";
           error=error+1;
	}
    }
    else
    {
        msg=msg+"please upload (xlsx/xls) file.. \n";
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
<style>
div[id^="refRow"]{margin-bottom:10px;}
</style>
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
