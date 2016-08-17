<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu1.php");
?>
<div class="span10 content">    
    <h2 class="heading">LBM dev</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Update price de notes file</strong>
            </div>
        <form method="POST" class="form-horizontal" action="lebon_ref_xls.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label">XLS/XLSX : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Reference : </label>
                <div class="controls">
                    <select name="index_reference1">
                    <?php
                    for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) {
                        ?><option value="<?php echo $j;?>" <?php if($j==2) echo "selected"; ?>><?php echo $i;?></option>
                    <?php
                    }
                    ?>
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
                    <strong>Writer File successfully generated! <a href="lebon_ref_xls.php?action=download&file=<?=$_REQUEST['file']?>">Click here to download</a><strong>
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
    if((document.importexcel.userfile1.value.match(/(.xls)$/i)) || (document.importexcel.userfile1.value.match(/(.xlsx)$/i)))
        {
            error=error;
        }
        else
        {
            msg=msg+"please upload Excel file (only xls/xlsx file) \n";
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

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
