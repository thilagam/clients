<?php     /* 
		   * New Option To Read Xlsx, Write Xlsx 
		   * Modified on 5 MAY 2015 by Lavanya Pant
		   */
?>
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
    <h2 class="heading">Armand Thiery</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Armand Thiery CSV  & XLS Compare</strong>
            </div>
        <form method="POST" class="form-horizontal" action="write_xls.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label">Armand Thiery CSV : </label>
                <div class="controls">
                    <input type="file" id="csvfile" name="csvfile">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Armand Thiery XLS/XLSX : </label>
                <div class="controls">
                    <input type="file" id="xlsfile" name="xlsfile">                   
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">Output </label>
                <div class="controls">
                    <input name="op" id="opformat" checked="" value="csv" type="radio"> csv <input name="op" id="opformat" value="xlsx" type="radio"> xlsx
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
				   <?php $path = str_replace(" ","_",$_REQUEST['file']); ?>
                    <strong>Writer File successfully generated! <a href="<?php echo "http://clients.edit-place.com/excel-devs/armand_thiery/writer-files/".$path; ?>">Click here to download</a><strong>
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
    if((document.importexcel.csvfile.value.match(/(.csv)$/i)))
        {
            error=error;
        }
        else
        {
            msg=msg+"please upload CSV file in first upload section (only .csv file) \n";
            error=error+1;
        }
    if((document.importexcel.xlsfile.value.match(/(.xls)$/i)) || (document.importexcel.xlsfile.value.match(/(.xlsx)$/i)))
        {
            error=error;
        }
        else
        {
            msg=msg+"please upload XLS file in second upload section (only .xls/.xlsx file) \n";
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
