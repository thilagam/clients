<?php

/**
 * Bash Writer file Creates Multiple XSLX files from single xlsx file based on languages array("ANGLAIS","WEB","WFLAM");
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 FEB 3 2016
 */

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<script type="text/javascript" src="<?=SITE_URL?>/js/bootstrap-select.min.js"></script>
<div class="span10 content">    
    <h2 class="heading">BA&SH XLSX to multixlsx</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Writer file (.xlsx)  > .zip(.xlsx)</strong>
            </div>
        <form method="POST" class="form-horizontal" action="dev1_writer.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label">BA&SH : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            
            <div class='clearfix'></div>
			
            <div class="control-group">
                <div class="controls">
					<input type='hidden' name='op' value='xlsx' id='opformat'>                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Writer File successfully generated! <a href="dev1_writer.php?action=download&folder=<?=$_REQUEST['folder']?>&file=<?=$_REQUEST['file']?>">Click here to download</a><strong>
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
     if((document.importexcel.userfile1.value.match('.+\.xlsx')))
        {
            error=error;
        }
        else
        {
            msg=msg+"please select the file (.xlsx) \n";
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
$(document).ready(function()
{
    $('.selectpicker').selectpicker({
        style: 'btn-default'
    });
});
</script>
<style>select{width:45px;}.tdSep{float:left;width:120px;}label.reference{float:left;width:210px;}</style>

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
