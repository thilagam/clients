<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if($_REQUEST['actn']=='dwnd' && $_REQUEST['ref'])
    odownloadXLS($_GET['file'], ANDRE_REF_WRITER_FILE_PATH, "ref_check.php") ;
        
global $global_parents_data,$global_parents_data_file,$global_parents_xls_file,$global_tmp_parents_data,$global_tmp_parents_data_file,$global_tmp_parents_xls_file;

if(isset($_POST['submit']) || ($_REQUEST['actn']=='dwnd' && $_REQUEST['ref']))
{
    $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    $ref = $_REQUEST['index_reference'];
    $wref = $_REQUEST['index_reference1'];
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    
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
	// Collecting references from writer files
        getAllParentsFromAllExcel($refs[$wref-1], 1, $ref, ANDRE_CLIENT_URL, ANDRE_WRITER_FILE_PATH) ;
	//echo 'PARENTS<pre>';print_r($global_parents_data_file);
	// Collecting references from writer files - temp folder
        getAllParentsFromAlltmpref($refs[$wref-1], 1, $ref, ANDRE_CLIENT_URL, ANDRE_TMP_WRITER_FILE_PATH) ;
       // echo "HERE";
     // echo '<pre>';print_r($global_tmp_parents_data);exit;//print_r($global_tmp_parents_xls_file);exit;
        //exit($refs[$ref+1] . " -- " . ANDRE_WRITER_FILE_PATH);
    }
	// Results view template
    $table = '<table style="width:98%; padding-top:10px;" align="center" valign="center" cellpadding="5" cellspacing="2" class="gridtable" border="1"><thead><tr><th>SI NO</th><th>Reference</th><th>Reference integration files</th><th>Reasons</th><th>Writer files</th><th>On ftp</th></tr></thead><tbody>';
    $si = 1; 
    foreach ($references as $reference) {
        $reference = trim(str_replace(" ", "", $reference)) ;
        $pattern = ANDRE_IMAGE_PATH.'/*/'.$reference.'_*.*' ;
        $arraySource = glob($pattern,GLOB_BRACE);
       // print_r($arraySource);exit;
        if(count($arraySource)>0){
			
            $refurl = "<a href='" . ANDRE_CLIENT_REF_URL . $reference . "' target='_blank'><b>ftp date:".date('y-m-d H:i',filemtime($arraySource[0]))."</b></a>";
        }else{
            $refurl = "<i>Not found</i>";
		}
        
        if($global_parents_xls_file[$reference])
            $reffile = "<b>".basename($global_parents_xls_file[$reference])."</b>";
        else
            $reffile = "<i>Not found</i>";
        
        if($global_tmp_parents_xls_file[$reference])
            $tmp_reffile = "<b>".basename($global_tmp_parents_xls_file[$reference])."</b>";
        else
            $tmp_reffile = "<i>Not found</i>";
        
        if($global_tmp_parents_xls_file[$reference] && $global_parents_xls_file[$reference] && (count($arraySource)>0))
        {
            $tgb ="<green>";
            $tge ="</green>";
        }
        else
        {
            $tgb ="<red>";
            $tge ="</red>";
        }
        
        $tmp_reffile_reasons = $global_tmp_parents_data[$reference] ? ($global_tmp_parents_data[$reference]) : '' ;
        
        $table1 .= "<tr><td>".$si."</td><td>{$tgb}".$reference."{$tge}</td><td>{$tmp_reffile}</td><td>".utf8_encode($tmp_reffile_reasons)."</td><td>{$reffile}</td><td>{$refurl}</td></tr>";
        
        $tmp_reffile_reasons = iconv("ISO-8859-1", "UTF-8", $tmp_reffile_reasons) ;
        $tmp_reffile_reasons = str_replace("", "oe", $tmp_reffile_reasons) ; //XXceXX
        $tmp_reffile_reasons = str_replace("", "'", $tmp_reffile_reasons) ;
        $tmp_reffile_reasons = str_replace("", "'", $tmp_reffile_reasons) ;
        $tmp_reffile_reasons = utf8_decode($tmp_reffile_reasons);
        
        $table2 .= "<tr><td>".$si."</td><td>{$tgb}".$reference."{$tge}</td><td>{$tmp_reffile}</td><td>{$tmp_reffile_reasons}</td><td>{$reffile}</td><td>{$refurl}</td></tr>";
        $si++ ;
    }
    $table1 = $table.$table1."</tbody></table>" ;
    
// Saving results file
    $file = "andre_references_".date('dmYhi').".xls" ;
    $fp = fopen(ANDRE_REF_WRITER_FILE_PATH."/".$file, 'w+');
    fwrite($fp, $table.$table2."</tbody></table>");
    fclose($fp);
    
    /*$configFile = BASH_CONFIG_FILE ;
    if(filesize($configFile)>0)
    {
        $handle = fopen($configFile, "r");
        $products = array_unique(unserialize(fread($handle, filesize($configFile))));
        $products = array_filter($products);
        fclose($handle);
    }
    else
    {
        $products = array();
    }*/
    
   //echo '<pre>'; print_r($global_parents_data);  print_r($global_parents_xls_file);
   //print_r($global_parents_data_file); exit;
}

include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");

function process($data, $start, $xls, $ref)
{
    $sheetcount = 1;

    // Updating data array keys
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

    function getAllParentsFromAlltmpref($ref, $urlid, $refid, $client_url, $file_path)
    {
        global $global_tmp_parents_data,$global_tmp_parents_data_file,$global_tmp_parents_xls_file;
        $arraySource = glob($file_path.'/'.$ref.'/*.xls', GLOB_BRACE);
//exit($file_path.'/'.$ref.'/*.xls');
		//echo "<pre>";print_r($arraySource);
        sort($arraySource);
        
        usort($arraySource, function($a, $b) {
            return filemtime($a) > filemtime($b);
        });
        
        foreach($arraySource as $excel)
        {
            $file=$excel;
           // echo (file_exists($file))? "YES YIP": "NO";
            $basename=basename($file);
            //echo "here";
            //echo $file."<pre>";
            gettmprefDatas($file, $urlid, $refid, $client_url);//return;
        }
        //exit;
      //echo "<pre>";print_r($global_tmp_parents_xls_file);exit;
    }

    function gettmprefDatas($file, $urlid, $refid, $client_url)
    {
        require_once INCLUDE_PATH . '/reader.php' ;
        global $global_tmp_parents_data,$global_tmp_parents_data_file,$global_tmp_parents_xls_file;
        //echo "<br />".$client_url."----------------------------------".$refid."-----------------------------------<br />FILE : ".$file."<br />";
        $data1 = new Spreadsheet_Excel_Reader();
        $data1->setOutputEncoding('Windows-1252');
        $data1->read($file);
		
//echo "<pre>";print_r($data1->sheets);//($urlid.'--'.$refid.'--'.$client_url);

        $sheets=sizeof($data1->sheets);
        for($i=0;$i<$sheets;$i++)
        {
            if($data1->sheets[$i]['numRows'])   
            {
                $x=1;
                while($x<=$data1->sheets[$i]['numRows']) {
                    if($x>1)
                    {	
						//echo $data1->sheets[$i]['cells'][$x][$refid]."<br />";
                        $data1->sheets[$i]['cells'][$x][$refid] = convert_smart_quotes($data1->sheets[$i]['cells'][$x][$refid]) ;
                        $data1->sheets[$i]['cells'][$x][$refid] = str_replace("http://korben.edit-place.com/ANDRE", $client_url, $data1->sheets[$i]['cells'][$x][$refid]);
                        //if(strstr($data1->sheets[$i]['cells'][$x][$urlid], $client_url) && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]))
                        if((strstr($data1->sheets[$i]['cells'][$x][$urlid], $client_url) || strstr($data1->sheets[$i]['cells'][$x][$urlid], 'http://korben.edit-place.com/ANDRE')) && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]))
                        {
                            $global_tmp_parents_data[$data1->sheets[$i]['cells'][$x][$refid]]  =   $data1->sheets[$i]['cells'][$x][8];
                            $global_tmp_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]   =   $data1->sheets[$i]['cells'][$x][$urlid];
                            $global_tmp_parents_xls_file[$data1->sheets[$i]['cells'][$x][$refid]]  =   $file;
                            //echo $data1->sheets[$i]['cells'][$x][$refid]."<br />";
                        }
                    }
                    $x++;
                }
            }
        }
    }

    function getAllParentsFromAllExcel($ref, $urlid, $refid, $client_url, $file_path)
    {
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        $arraySource = glob($file_path.'/'.$ref.'/*.xls', GLOB_BRACE);
//exit($file_path.'/'.$ref.'/*.xls');
        sort($arraySource);
        
        usort($arraySource, function($a, $b) {
            return filemtime($a) > filemtime($b);
        });
        
        foreach($arraySource as $excel)
        {
            $file=$excel;
            $basename=basename($file);
            getExcelDatas($file, $urlid, $refid, $client_url);
        }//echo '<pre>@@';print_r($global_parents_data_file); exit('*****'.$client_url);
    }

    function getExcelDatas($file, $urlid, $refid, $client_url)
    {
        require_once INCLUDE_PATH . '/reader.php' ;
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        
        $data1 = new Spreadsheet_Excel_Reader();
        $data1->setOutputEncoding('Windows-1252');
        $data1->read($file);

//echo "<pre>";print_r($data1->sheets);//exit($urlid.'--'.$refid.'--'.$client_url);

        $sheets=sizeof($data1->sheets);
        for($i=0;$i<$sheets;$i++)
        {
            if($data1->sheets[$i]['numRows'])   
            {
                $x=1;
                while($x<=$data1->sheets[$i]['numRows']) {
                    if($x>1)
                    {
                        $data1->sheets[$i]['cells'][$x][$refid] = ($data1->sheets[$i]['cells'][$x][$refid]) ;
                        $data1->sheets[$i]['cells'][$x][$refid] = str_replace("http://korben.edit-place.com/ANDRE", $client_url, $data1->sheets[$i]['cells'][$x][$refid]);

                        if((strstr($data1->sheets[$i]['cells'][$x][$urlid], $client_url) || strstr($data1->sheets[$i]['cells'][$x][$urlid], 'http://korben.edit-place.com/ANDRE')) && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]))
                        //if(!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]])
                        {

                            $global_parents_data[]  =   $data1->sheets[$i]['cells'][$x][$refid];
                            $global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]   =   $data1->sheets[$i]['cells'][$x][$urlid];
                            $global_parents_xls_file[$data1->sheets[$i]['cells'][$x][$refid]]  =   $file;
                        }
                    }
                    $x++;
                }
            }
        }
    }

?>
<div class="span10 content">    
    <h2 class="heading">Andre xls dev - check reference</h2>        
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
                <label class="control-label" for="reference">Writer file reference : </label>
                <div class="controls">
                    <select name="index_reference1">
                    <?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?><option value="<?php echo $j;?>" <?php if($j==5) echo "selected"; ?>><?php echo $i;?></option>
                    <?php  }        ?>
            
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="reference">Input file reference : </label>
                <div class="controls">
                    <select name="index_reference">
                    <?php for ($i = 'A', $j = 1; $j <= 26; $i++, $j++) { ?><option value="<?php echo $j;?>" <?php if($j==3) echo "selected"; ?>><?php echo $i;?></option>
                    <?php  }        ?>
            
                    </select>
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
    echo $table1;
}
?>
    </div>

</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
