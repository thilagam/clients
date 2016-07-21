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
    <h2 class="heading">TUI</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong>Upload zip file to generate Writer file</strong>
            </div>
        <form method="POST" class="form-horizontal" action="process.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">Zip : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Template Type : </label>
                <div class="controls">
                    <select name="type">
                        <option value="1">PAYS / REGION / SOUS-REGION</option>
                        <option value="2">DESTINATION SUR MESURE</option>
                        <option value="3">THEME</option>
                    </select>
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
                    <strong>Writer File long successfully generated! <a href="process.php?action=download&file=<?=$_REQUEST['file']?>"  target='_blank'>Click here to download long file</a><strong>
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
    if((document.importexcel.userfile1.value.match(/(.zip)$/i)))
    {
        error=error;
    }
    else
    {
        msg=msg+"please upload Zip file only \n";
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
