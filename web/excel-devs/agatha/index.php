<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");

if(isset($_POST['submit']))
{
    ini_set("memory_limit","1000M");
    //ini_set("display_errors","on");
    $ref = $_REQUEST['index_reference'];
    $soc_ref_file = AGATHA_CONFIG_FILE ;
    
    if(!file_exists($soc_ref_file))
    {
        $fp = fopen($soc_ref_file,"w");
        fclose($fp);
    }
        $arr_ref_soc = unserialize(file_get_contents($soc_ref_file));   
    
    //upload file and display details
    
        if(isset($_FILES['userfile1']))
        {
            $test = true;
            $filename = $_FILES['userfile1']['name'];
            $tmpName = "AGATHA_tmp.xls";
            if(!is_numeric($ref)&&$ref==0){$err_ref = "Erreur : vous devez entrer un num�ro de colonne";$test=false;}
            if(strrchr($filename,'.')!=".xls"){$err_file = " Erreur : vous devez charger un fichier XLS";$test=false;}
            
            if($test) 
            {
                $dest = AGATHA_WRITER_FILE_PATH."/$tmpName";
                move_uploaded_file($_FILES['userfile1']['tmp_name'], $dest);
                //$arrayCsv = file($dest);
            }
            //replace temp file and store ref column
    
            $old_name2 = AGATHA_WRITER_FILE_PATH."/AGATHA_tmp.xls";   
            
            if(file_exists($old_name2))
            {
                //current becomes old
                $old_name1 = AGATHA_WRITER_FILE_PATH."/AGATHA.xls";
                $new_name1 = AGATHA_WRITER_FILE_PATH."/AGATHA"."_".date("YmdHis").".xls";
                rename($old_name1,$new_name1);
                chmod($new_name1,0777);
                //echo $old_name1."=>".$new_name1."<br/>";
                
                //temp become new
                $new_name2 = AGATHA_WRITER_FILE_PATH."/AGATHA.xls";
                rename($old_name2,$new_name2);
                chmod($new_name2,0777);
                //echo $old_name2."=>".$new_name2."<br/>";
                    
                //ref storing file updation
                $arr_ref_soc[basename($old_name1)] = $ref;
                $arr_ref_soc['updated'] = 'yes';
                $fp = fopen($soc_ref_file,"w");
                fwrite($fp,serialize($arr_ref_soc));
                fclose($fp);    
            }           
        }
    
}
$pattern = '{'.AGATHA_WRITER_FILE_PATH.'/AGATHA[\_][!A-Za-z]*,'.AGATHA_WRITER_FILE_PATH.'/AGATHA.*}';
$arraySource = glob($pattern,GLOB_BRACE);
sort($arraySource);
?>

<div class="span10 content">    
    <h2 class="heading">Fichier r&#233;f&#233;rentiel AGATHA</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Fichier r&#233;f&#233;rentiel AGATHA</strong>
            </div>
        <form method="POST" class="form-horizontal" action="" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">Fichier r&#233;f&#233;rentiel AGATHA : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="excel_file">Reference : </label>
                <div class="controls">
                    <select name="index_reference">
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
                <label class="control-label">Output </label>
                <div class="controls">
                    <input name="op" id="opformat" value="xls" type="radio"> xls <input name="op" id="opformat" value="xlsx" checked type="radio"> xlsx
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
            <?php
            if(isset($_REQUEST['msg']) && ($_REQUEST['msg']=='success') && $_REQUEST['zip'])
            {
            ?>
                <div class="alert alert-success">
                    <strong>Writer File successfully generated! <a href="write_xls.php?action=download&zip=<?=$_REQUEST['zip']?>">Click here to download</a><strong>
                </div>  
            <?      
            }   
            else if(isset($_REQUEST['msg']) && $_REQUEST['msg']=='success')
            {
            ?>
                <div class="alert alert-success">
                    <strong>Writer File successfully generated! <a href="write_xls.php?action=download&file=<?=$_REQUEST['file']?>">Click here to download</a><strong>
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
            <div class="alert alert-info">
                <strong>Details des fichiers sources pour AGATHA<strong>]
            </div>
            <div class="control-group">
                <div class="controls">
                    <? if(count($arraySource) >0 ){?>
            
                        <?  foreach($arraySource as $ar):$stat = stat($ar);?>
                            <div style="padding:10px 0"><a href="<?=AGATHA_URL?>/agathasourcefiles/<?=basename($ar)?>"><?=basename($ar)?></a>
                            | derni&#232;re modif : <?=date("d/m/Y H:i:s",$stat['mtime'])?> | taille du fichier <?=round($stat['size']/1000,0)?>ko<?if($arr_ref_soc[basename($ar)]):?>  | <span class="redtxt">colonne r&#232;f&#232;rence <?=$arr_ref_soc[basename($ar)]?></span><?endif;?>
                            </div>
                        <?endforeach;?> 
                    <? } ?>
                </div>
            </div>
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
