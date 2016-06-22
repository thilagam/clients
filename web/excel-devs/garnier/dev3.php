<?php 

/**
 * Gariner Template Creation.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 May 25 2016
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
    <h2 class="heading">Garnier Template Creation</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong>Upload Source file to Insert keywords for translations (Read only 20 Columns from input file)</strong>
            </div>
        <form method="POST" class="form-horizontal" action="dev3_writer.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">XLSX : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">Select Langauage : </label>
                <div class="controls">
                    <select name="lang" id="lang" class="span3">
						<option value=" ">Select language</option>
						<option value="FR" selected="">FR</option>
						<option value="UK">UK</option>
                        <option value="ES">ES</option>
                        <option value="IT">IT</option>
                        <option value="DE">DE</option>
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
                    <strong>Writer File Successfully generated! <a href="dev3_writer.php?action=download&f=<?=$_REQUEST['f']?>&bool=zip"  target='_blank'>Click here to download Zip</a><strong><br />
					<strong>Writer File Successfully generated! <a href="dev3_writer.php?action=download&f=<?=$_REQUEST['f']?>&bool=xlsx"  target='_blank'>Click here to download XSLX</a><strong>	
                </div>
                
            <?  
            }   
            else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='error')
            {
            ?>
                <div class="alert alert-error">
                    <strong>There are no new Tempaltes to Generate<strong>
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
    if((document.importexcel.userfile1.value.match(/(.xlsx)$/i)) && (document.getElementById('lang').value != "" ))
    {
        error=error;
    }
    else
    {
        msg=msg+"please upload (xlsx/xls) file.. & language\n";
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
