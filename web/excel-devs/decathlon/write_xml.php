<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
    odownloadXml($_GET['file'], DECATHLON_XML_FILE_PATH, "index.php") ;

if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ref = $_REQUEST['index_reference'];
    $writexls = ($_REQUEST['op']=='xls') ? 'oWriteXLS' : 'writeXlsx' ;

        $ext = $file1['extension'];

        if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
        {
            $file_name = uniqid() . "_decathlon.xml" ;
            $file_path = DECATHLON_XML_FILE_PATH . "/" . $file_name ;
            
            if($file1['extension'] == 'xlsx')
            {
                $xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
                process($xls1Arr[0], 2, 0, $file_path) ;
            }
            else
            {
                $xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
                process($xls1Arr[0], 2, 1, $file_path) ;
            }

            if (file_exists($file_path))
                header("Location:index.php?msg=success&file=".$file_name);
            else
                header("Location:index.php?msg=error");
        }
}
else
    header("Location:index.php");

function process($data, $start, $xls, $file_path)
{
	$xmls = array() ;
	$sheetcount = 1;
	// Creating xml array from excel
	foreach ($data as $data_)
	{
		$key = $xls ;
		foreach ( $data_ as $dataArr ) :
			if($key>$xls)
				$xmls[] = $dataArr ;
			$key++;
		endforeach ;
		$sheetcount++;
	}

	// For xml file creation
	include_once "xml.php" ;
}
?>
