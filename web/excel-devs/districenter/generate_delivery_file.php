<?php

/**
 * Swap Dev :- Enterchange Colums value as per the input Ex: B to A
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Apr 21, 2015
 */

ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<div class="span10 content">    
    <h2 class="heading"> DISTRICENTER</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong>Upload DISTRICENTER Writer File to Generate Delivery File</strong>
            </div>
        <form method="POST" class="form-horizontal" action="delivery_process.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">XLSX : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group" style="display:none">
                <label class="control-label" for="reference">Reference : </label>
                <div class="controls">
                    <select name="index_reference">
                    <?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?><option value="<?php echo $j;?>" <?php if($j==1) echo "selected"; ?>><?php echo $i;?></option>
                    <?php  }        ?>
            
                    </select>
                </div>
            </div>
			<div class="control-group" style="display:none">
                <label class="control-label">Output </label>
                <div class="controls">
                    <input name="op" id="opformat" value="xls" type="radio"> xls <input name="op" id="opformat" value="xlsx" checked="" type="radio"> xlsx
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Upload & Output</button>
                </div>
            </div>
            
            
            
            
            
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Writer File successfully generated! <a href="<?php echo "./"."tempfiles"."/".$_REQUEST['file']; ?>">Click here to download</a><strong>
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
    if((document.importexcel.userfile1.value.match(/(.xlsx)$/i)) || (document.importexcel.userfile1.value.match(/(.xls)$/i)))
    {
        error=error;
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

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
