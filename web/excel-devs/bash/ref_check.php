<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if($_REQUEST['actn']=='dwnd' && $_REQUEST['ref'])
    odownloadXLS($_GET['file'], BASH_REF_WRITER_FILE_PATH, "ref_check.php") ;

if(isset($_POST['submit']) || ($_REQUEST['actn']=='dwnd' && $_REQUEST['ref']))
{
    $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $ref = $_REQUEST['index_reference'];
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $writexls = ($_REQUEST['op']=='xls') ? 'oWriteXLS' : 'writeXlsx' ;
    
    if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
            $references  = process($xls1Arr[0], 2, 0, $ref) ;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
            $references  = process($xls1Arr[0], 2, 1, $ref) ;
        }
	//// Collecting references from writer files
        getAllParentsFromAllExcel($refs[$ref-1], 1, $ref+1, BASH_CLIENT_URL, BASH_WRITER_FILE_PATH);
        //echo '<pre>'; print_r($global_parents_data);  print_r($global_parents_xls_file); print_r($references);exit;
    }

    // Template for reference status/results
    $table = '<table style="width:98%; padding-top:10px;" align="center" valign="center" cellpadding="5" cellspacing="2" class="gridtable" border="1"><thead><tr><th>Reference</th><th>Filename</th><th>Url</th></tr></thead><tbody>';
    $xlsArr[] = array('Reference', 'Filename', 'Url') ;
    
    foreach ($references as $reference) {
        $pattern = BASH_IMAGE_PATH.'/*/'.$reference.'*.*';
        $arraySource = glob($pattern,GLOB_BRACE);
        
        if(count($arraySource)>0)
        {
            $refurl = "<a href='" . BASH_CLIENT_REF_URL . $reference . "' target='_blank'><b>" . BASH_CLIENT_REF_URL . $reference . "</b></a>";
            $refurl_ = BASH_CLIENT_REF_URL . $reference ;
        } else {
            $refurl = "<i>Not found on ftp</i>";
            $refurl_ = "";
        }
        
        if($global_parents_xls_file[$reference])
        {
            $reffile = "<b>".basename($global_parents_xls_file[$reference])."</b>";
            $reffile_ = basename($global_parents_xls_file[$reference]);
        } else {
            $reffile = "<i>Not found on files</i>";
            $reffile_ = "";
        }
        
        if($global_parents_xls_file[$reference] && (count($arraySource)>0))
        {
            $tgb ="<green>";
            $tge ="</green>";
        }
        else
        {
            $tgb ="<red>";
            $tge ="</red>";
        }
            
        $table .= "<tr><td>{$tgb}".$reference."{$tge}</td><td>{$reffile}</td><td>{$refurl}</td></tr>";
        $xlsArr[] = array($reference, $reffile_, $refurl_) ;
    }
    $table .= "</tbody></table>" ;

    $file = "bash_references_".date('dmYhi').".".$_REQUEST['op'] ;
    //echo '<pre>'; print_r($xlsArr);exit(BASH_REF_WRITER_FILE_PATH."/".$file);
    /*$fp = fopen(BASH_REF_WRITER_FILE_PATH."/".$file, 'w+');
    fwrite($fp, $table);
    fclose($fp);*/
    
    $writexls($xlsArr, BASH_REF_WRITER_FILE_PATH."/".$file, 'C') ;
    
   //echo '<pre>'; print_r($global_parents_data);  print_r($global_parents_xls_file);
   //print_r($global_parents_data_file); exit;
}

include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");

function process($data, $start, $xls, $ref)
{
    $sheetcount = 1;
    foreach ($data as $data_) {
        $key = $xls ;
        foreach ( $data_ as $dataArr ) :
            if($key>$xls)
                $references[] = $dataArr[$ref] ;
            $key++;//echo '<pre>'; print_r($dataArr);
        endforeach ;
        $sheetcount++;
    }
    return $references ;
}
?>
<div class="span10 content">    
    <h2 class="heading">BASH xls dev - check reference</h2>        
    <div class="span11">
            <div class="alert alert-info">
                <strong>Generate refreence list</strong>
            </div>
        <form method="POST" class="form-horizontal" action="" name="importexcel"  ENCTYPE="multipart/form-data" onsubmit="return checkfile();">
            <div class="control-group">
                <label class="control-label" for="excel_file">XLS/XLSX : </label>
                <div class="controls">
                    <input type="file" id="userfile1" name="userfile1">                   
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Reference : </label>
                <div class="controls">
                    <select name="index_reference">
                    <?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?><option value="<?php echo $j;?>" <?php if($j==1) echo "selected"; ?>><?php echo $i;?></option>
                    <?php  }        ?>
            
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Output </label>
                <div class="controls">
                    <input type="radio" name="op" id="opformat" value="xls" /> xls <input type="radio" name="op" id="opformat" value="xlsx" checked /> xlsx
                </div>
            </div>
            <div class="control-group">
                <div class="controls">                  
                    <button type="submit" value="Upload" name="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
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
<style>red{color:red;}green{color:green;}</style>
<?php
if(isset($_POST['submit']))
{ ?>
    <div class="alert alert-success">
        <strong><a href="ref_check.php?actn=dwnd&ref=<?=$ref?>&file=<?=$file?>">Click here to download</a></strong>
    </div>
<?php
    echo $table;
}
?>
    </div>

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
