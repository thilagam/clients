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
    <h2 class="heading">100% plagi&#233;s - Copier-coller auto</h2>
    <div class="span11">
            <div class="alert alert-info">
                <strong> Uploader un fichier</strong>
            </div>
        <form method="POST" class="form-horizontal" action="write_copiercoller.php" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label">fichier 1 (xls/xlsx) : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">fichier 2 (xls/xlsx) : </label>
                <div class="controls">
                    <input type="file" id="userfile2" name="userfile2">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Input r&#233;f&#233;rence : </label>
                <div class="controls">
                    <select name="input_reference" style="width:150px">

        <?php

        for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) {

        

            ?><option value="<?php echo $j;?>" <?php if($j==1) echo "selected"; ?>><?php echo $i;?></option>

        

        <?php       

        }

        ?>

        </select><div class="help-inline">From file 1</div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Output r&#233;f&#233;rence : </label>
                <div class="controls">
                    <select name="output_reference" style="width:150px">

        <?php

        for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) {

        

            ?><option value="<?php echo $j;?>" <?php if($j==1) echo "selected"; ?>><?php echo $i;?></option>

        

        <?php       

        }

        ?>

        </select><div class="help-inline">From file 2</div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Copied r&#233;f&#233;rence : </label>
                <div class="controls">
                    <select name="copied_reference" style="width:150px">

        <?php

        for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) {

        

            ?><option value="<?php echo $j;?>" <?php if($j==3) echo "selected"; ?>><?php echo $i;?></option>

        

        <?php       

        }

        ?>

        </select><div class="help-inline">From file 1</div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Translation text : </label>
                <div class="controls">
                    <select name="translation_text" style="width:150px">

        <?php

        for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) {

        

            ?><option value="<?php echo $j;?>" <?php if($j==4) echo "selected"; ?>><?php echo $i;?></option>

        

        <?php       

        }

        ?>

        </select><div class="help-inline">From file 1</div>
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Validate</button>
                </div>
            </div>
            <?php
            if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Fichier divis&#233; en lots avec succ&#232;s ! <a href="write_copiercoller.php?action=download&file=<?=$_REQUEST['file']?>">Cliquez ici pour t&#233;l&#233;charger le fichier</a><strong>
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
        if((document.importexcel.userfile2.value.match(/(.xlsx)$/i)) || (document.importexcel.userfile2.value.match(/(.xls)$/i)))
            error=error;
        else
        {
            msg=msg+"lease upload (xlsx/xls) file.. \n";
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

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
