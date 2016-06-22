<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']); // Root path
define("INCLUDE_PATH",ROOT_PATH."/includes"); // Include files path

include_once(INCLUDE_PATH."/config_path.php"); // Configuration file (Devs writer file path, success/error messages and other configurations)
include_once(INCLUDE_PATH."/common_functions.php"); // Includes all common functions used for excel devs

// Array for reference names directory within writer files folder
$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

// Downloading writer file (Work only for download links in excel dev results processed)
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']) && isset($_GET['ref']))
    odownloadXLS($_GET['file'], ACCESSORIE_DIFFUSION_WRITER_FILE_PATH . "/" . $refs[$_GET['ref']-1], "index.php") ;

// Processing form submission of excel devs
if(isset($_POST['submit']))
{
    $file1  =   pathinfo($_FILES['userfile1']['name']) ; // Path info for files upload
    $writexls = ($_REQUEST['op']=='xls') ? 'oWriteXLS' : 'writeXlsx' ; // Function for output file format option 
    $ref = $_REQUEST['index_reference']; // Input file reference

        $ext = $file1['extension']; // Input file extension

	// Only xls/xlsx format will be considered for processing
	if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
	{
	    if($file1['extension'] == 'xlsx')
	    {
	    	// Reading xlsx file input to an array
		$xls1Arr  = oxlsxRead($_FILES['userfile1']['tmp_name']) ;
		
		// Processing xlsx file array for dev result
		$results  = process1($xls1Arr[0], 2, 0, $ref) ;
	    }
	    else
	    {
	    	// Reading xls file input to an array
		$xls1Arr  = oxlsRead($_FILES['userfile1']['tmp_name']) ;
		
		// Processing xlsx file array for dev result
		$results  = process1($xls1Arr[0], 2, 1, $ref) ;
	    }
	    // Output file name
	    $file_name = uniqid() . "_accessorie_diffusion." . $_REQUEST['op'] ;
	    
	    // Output file path
	    $file_path = ACCESSORIE_DIFFUSION_WRITER_FILE_PATH . "/" . $refs[$ref-1] . "/" . $file_name ;

	    // Redirecting to dev interface with processing status and download/view results link
	    if ($writexls($results[0], $file_path))
		header("Location:index.php?msg=success&file=".$file_name."&ref=".$ref);
	    else
		header("Location:index.php?msg=error");
	}
}
else
    header("Location:index.php"); // Redirecting to  dev interface

function WriteXLS($datas,$file_path, $sheetnames, $ref)
{
    $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

    // include package
    include_once 'Spreadsheet/Excel/Writer.php';
    
    // create empty file
    $excel = new Spreadsheet_Excel_Writer($file_path);
    $excel->setVersion(8) ;
    
    // create format for header row
    // bold, red with black lower border
    $header_f=array(
            'bold'=>'1',
            'size' => '10',
            'color'=>'black',
            'border'=>'1',
            'align' => 'center',
            'valign' => 'top'); 
    $header =& $excel->addFormat($header_f);
    $cell_f=array(
              'Size' => 10,
              'valign' => 'top'); 
    $cell =& $excel->addFormat($cell_f);

    foreach($datas as $sheet_cnt=>$data)
    {
        $sheet_name=$sheetnames[$sheet_cnt];
        $sheet_obj='sheet'.$sheet_cnt;
        $$sheet_obj=& $excel->addWorksheet($sheet_name);
        //$$sheet_obj->setInputEncoding('utf-8');
        
        // add data to worksheet
        $rowCount=0;
        foreach ($data as $key1=>$row) {
            
          foreach ($row as $key => $value) {
            $value= (str_replace("’", "'", $value)) ;
            if($rowCount==0)
                $$sheet_obj->write($rowCount, $key, $value,$header);
            else
                $$sheet_obj->write($rowCount, $key, $value,$cell);
          }
          $rowCount++;
        }
    }
    //echo '<pre>||||||||'; print_r($sheetnames); print_r($datas);exit($file_path);
    
    // save file to disk
    if ($excel->close() === true) {
        return $filename ;
    } else {
        return false ;
    }
}


    function process1($data, $start, $xls, $ref)
    {
        global $global_parents_data,$global_parents_data_file;
        $refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $refdir = ACCESSORIE_DIFFUSION_WRITER_FILE_PATH."/".$refs[$ref-1]."/";

	// Writer file reference directory
        if (!is_dir($refdir))
        {
            mkdir($refdir);
            chmod($refdir, 0777);
        }
        
// Collecting references from writer files
        getAllParentsFromAllExcel($refs[$ref-1], 1, $ref+1, ACCESSORIE_DIFFUSION_CLIENT_URL, ACCESSORIE_DIFFUSION_WRITER_FILE_PATH);
        
        $products =   array();

// Updating data array keys
        $sheetcount = 1;
        foreach ($data as $data_)
        {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $datas[$sheetcount-1][$key][0] = '' ;
                foreach ( $dataArr as $idx=>$col ) :
                    $datas[$sheetcount-1][$key][$idx] = $col ;
                endforeach ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
        $sheetcount = 1;//echo '<pre>';
        foreach ($datas as $xlsArr1)
        {
            $key = $xls ;
            foreach ( $xlsArr1 as $col ) :
// processing rows - excluding header
                if($key>$xls)
                {
                    $col_ref = str_replace(" ",  "", trim($col[$ref]));
                    $col_ref = str_replace("/",  "-", $col_ref);
                    //$col_ref = $col_ref[0] ;

// Find reference matching url

                    $pattern = ACCESSORIE_DIFFUSION_IMAGE_PATH.'/*/*@'.$col_ref.'*.*';
                    $arraySource = glob($pattern,GLOB_BRACE);
                    sort($arraySource);
                    
                    if(count($arraySource)>0)
                    {
                        $path = pathinfo($arraySource[0]) ;//echo $col_ref;print_r($path);
                        if(!in_array($col_ref, $refrncs) && !in_array($col_ref, $global_parents_data))
                        {
                            //$s = getRefFrmImg('COSMOPARIS', $path['filename']) ;
                            $datas[$sheetcount-1][$key][0] = ACCESSORIE_DIFFUSION_CLIENT_REF_URL . $col_ref ;
                            $pdtrow = $key ;
                            $refrncs[] = $col_ref ;
                        }
                        elseif(in_array($col_ref, $global_parents_data))
                        {
                            $datas[$sheetcount-1][$key][0] = 'Doublon' ;
                        }
                        elseif(in_array($col_ref, $refrncs))
                        {
                            $datas[$sheetcount-1][$key][0] = 'Doublon - ' . ($pdtrow+1) ;
                        }
                        $pdct_id = $key;
                    }
                    else
                    {
                        $datas[$sheetcount-1][$key][0] = 'NA';
                        $pdct_id = '';
                    }
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }//exit;
        //echo '<pre>'; print_r($datas);exit;
        
        return $datas ;
    }
?>
