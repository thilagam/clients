<?php
include_once("common_functions.php");

//Korben fucntions to get referece images

function getKorbenReferenceImages($client,$reference=NULL)
{
	$client_image_path=KORBEN_IMAGE_PATH."/$client/";
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);
	//print_r($ref_directory);exit;
	$loop=0;	
	if(count($ref_directory)>0)
	{
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;			
			
			if($client=='TOSCANE' || $client=='ARMAND_THIERY' )
				$files = glob($img_directory."/".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);		
			else
				$files = glob($img_directory."/*/".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);		
			
			if(count($files)>0)	
			{		
				foreach($files as $file)
				{	
					$string = basename($file);
					$img=$file;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
					
			
			//echo $img;exit;
				
					$reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery="">
								<img class="img-polaroid" src="'.$img.'" width=150 height=250/>
							</a>';				
				
				}
			}		
			continue;
		}
	}	
	echo $reference_images;
	//exit;

}
function getKorbenReferences($client,$check_reference=NULL)
{
	$client_image_path=KORBEN_IMAGE_PATH."/$client/";	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR);
	//echo "<pre>";print_r($refs);exit($client_image_path."*");
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;			
		$img_directory_name=basename($img_directory);
		
		if($client=='TOSCANE' || $client=='ARMAND_THIERY' )
			$reference_directories = glob($img_directory."/$check_reference*");
		else	
			$reference_directories = glob($img_directory."/$check_reference*", GLOB_ONLYDIR);
		
		/* usort($reference_directories, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); */ 	
		usort($reference_directories, "sorted");
		//echo "<pre>";print_r($reference_directories);		
		$references_text.='<div class="row-fluid">
							<div class="span12">
								<h4 class="heading">'.$img_directory_name.'</h4>
			';   
		
		if(count($reference_directories)>0)
		{
			$reference_array=array();
			foreach($reference_directories as $reference)
			{
				$reference=basename($reference);
				
				if($client)	
				{	
					if($client=='TOSCANE' || $client=='ARMAND_THIERY' )
						$reference = substr($reference,0,8);
					else
						$reference = substr($reference,0,7);
				}	
					
				$reference_array[$reference]=$img_directory_name;		
			}
			foreach($reference_array as $reference=>$value)
			{
				if($client)
					$references_text.='<a target="korben" href="'.SITE_URL.'/excel-devs/korben/view-pictures.php?client='.$client.'&reference='.$reference.'"><span class="badge">'.$reference.'</span></a>&nbsp;';
				else	
					$references_text.='<a target="korben" href="'.SITE_URL.'/excel-devs/korben/index.php?client='.$img_directory_name.'"><span class="badge badge-info">'.$reference.'</span></a>&nbsp;';
			}	
		}	
		else
		{
			$references_text.='<span class="label label-important">No References Found</span>';	
		}		
		
		$references_text.='		</div>
						</div>';		
		
		
	}
	echo $references_text;
	//exit;


}

/*Korben fucntions to get all references except given folder 
if ALL folders TRUE then getting all references other than given folder
if ALL false getting references of a given folder*/ 

function getALLKorbenReferencesExceptGivenFolder($client,$folder_id,$all_folders=TRUE)
{
	$client_image_path=KORBEN_IMAGE_PATH."/$client/" ;

	$refs=glob($client_image_path."*", GLOB_ONLYDIR); 	

	$folder_stat = stat($client_image_path.$folder_id);
	$folder_statmtime = (int)$folder_stat['mtime'];

	$loop=0;
	foreach($refs as $index=>$folder)
	{
		$stat = stat($folder);
		$statmtime = (int)$stat['mtime'];
		$img_directory = $folder;
		$img_directory_name=basename($img_directory);

		if( (($img_directory_name!=$folder_id && $all_folders ) || ($img_directory_name==$folder_id && !$all_folders)) && ($statmtime<=$folder_statmtime))
		{
			if($client=='ARMAND_THIERY' || $client=='TOSCANE' )
				$reference_directories = glob($img_directory."/*", GLOB_BRACE);
			else
				$reference_directories = glob($img_directory."/*", GLOB_ONLYDIR);

			usort($reference_directories, function($a, $b) {
				return filemtime($a) < filemtime($b);
			});
			
			if(count($reference_directories)>0)
			{
				foreach($reference_directories as $reference)
				{
					$reference=basename($reference);
					
					if($client)	
					{	
						if($client=='TOSCANE' || $client=='ARMAND_THIERY' ){
							$reference = substr($reference,0,8);
						}else{
							$reference = substr($reference,0,7);
						}
					}	
						
					$reference_array[]=$reference;
					$reference_array_folder_names[$reference]=$img_directory_name;
				}				
			}
		}	
	}
	//echo "<pre>";
	//print_r($reference_array);
	//print_r($folder_reference_array);
	//exit;
	$reference_array=array_values(array_unique($reference_array));
	
	return array($reference_array,$reference_array_folder_names);
}

//function to get all folders  list a client to generate Writer files
function getKorbenFoldersList($client)
{
	$client_folder_path=KORBEN_IMAGE_PATH."/$client/";
	$all_directory=glob($client_folder_path."*", GLOB_ONLYDIR);	
	
	if(count($all_directory))
	{
		usort($all_directory, "sortedarr");
		
		return $all_directory;
	}
}


function sortedarr($a, $b)
{
	if (filemtime($a) == filemtime($b)) {
	    return 0;
	}        
	return (filemtime($a) > filemtime($b)) ? -1 : 1 ;
}

//upload korben source w.r.t client and save the reference in config file.
function uploadKorbenSource($client,$reference,$source_file=NULL,$valid=NULL)
{
    $client_source_path=constant($client."_SOURCE_PATH");
    $client_config_file=constant($client."_CONFIG_FILE");

    if(!$valid)
    {
        $file_name = $client."_tmp.csv";
        $source_file_temp = $client_source_path."/$file_name";  
        move_uploaded_file($source_file['csv_file']['tmp_name'], $source_file_temp);
        $arrayCsv = file($source_file_temp);
        return $arrayCsv;
    }
    else if($valid==1)
    {
        $file_name = $client."_tmp.csv";
        $old_name2 = $client_source_path."/$file_name";
        
        if(file_exists($old_name2))
        {
            //current becomes old
            $old_name1 = $client_source_path."/$client.csv";
            $new_name1 = $client_source_path."/$client"."_".date("YmdHis").".csv";
            if(file_exists($old_name1))
                rename($old_name1,$new_name1);

            //temp become new
            $new_name2 = $client_source_path."/$client.csv";
            rename($old_name2,$new_name2);

            //reference storing file updation
            $arr_ref_soc[basename($old_name1)] = $reference;
            $fp = fopen($client_config_file,"w");
            fwrite($fp,serialize($arr_ref_soc));
            fclose($fp);    
        }
    }
}
//upload korben source w.r.t client and save the reference in config file.
function uploadKorben2Source($client,$reference,$source_file=NULL,$valid=NULL)
{
    $client_source_path=constant("K2_".$client."_SOURCE_PATH");
    $client_config_file=constant("K2_".$client."_CONFIG_FILE");

    if(!$valid)
    {
        $file_name = $client."_tmp.csv";
        $source_file_temp = $client_source_path."/$file_name";  
        move_uploaded_file($source_file['csv_file']['tmp_name'], $source_file_temp);
        $arrayCsv = file($source_file_temp);
        return $arrayCsv;
    }
    else if($valid==1)
    {
        $file_name = $client."_tmp.csv";
        $old_name2 = $client_source_path."/$file_name";
        
        if(file_exists($old_name2))
        {
            //current becomes old
            $old_name1 = $client_source_path."/$client.csv";
            $new_name1 = $client_source_path."/$client"."_".date("YmdHis").".csv";
            if(file_exists($old_name1))
                rename($old_name1,$new_name1);

            //temp become new
            $new_name2 = $client_source_path."/$client.csv";
            rename($old_name2,$new_name2);

            //reference storing file updation
            $arr_ref_soc[basename($old_name1)] = $reference;
            $fp = fopen($client_config_file,"w");
            fwrite($fp,serialize($arr_ref_soc));
            fclose($fp);    
        }
    }
}
//Get All korben source files of a client
function getAllPreviousKorbenSourceFiles($client)
{
    $client_source_path=constant($client."_SOURCE_PATH");
    $client_config_file=constant($client."_CONFIG_FILE");
    
    $pattern = $client_source_path.'/'.$client.'*.*';
    $arraySource = glob($pattern);  
    //sort($arraySource);
    usort($arraySource, function($a, $b) {
            return filemtime($a) < filemtime($b);
        });
    return $arraySource;
    
}
//Get All korben source files of a client
function getAllPreviousKorben2SourceFiles($client)
{
    $client_source_path=constant("K2_".$client."_SOURCE_PATH");
    $client_config_file=constant("K2_".$client."_CONFIG_FILE");
    
    $pattern = $client_source_path.'/'.$client.'*.*';
    $arraySource = glob($pattern);  
    //sort($arraySource);
    usort($arraySource, function($a, $b) {
            return filemtime($a) < filemtime($b);
        });
    return $arraySource;
    
}
//Korben csv Process
function korbenCsvProcess($all_reference_array,$all_reference_array_folder_names,$folder_reference_array,$csvArray,$source_index,$client)
{
	$xlsArray[0][0]='Doublon';
	$xlsArray[0][1]='Reference prod';
	$xlsArray[0][2]='URL';
	$xlsArray[0][3]='Comment';		
	
	for($i=0;$i<count($csvArray[0]);$i++)
	{
		if($i!=($source_index-1))
		{
				$string=$csvArray[0][$i];
				$string=korbenCleanString($string);				
				$xlsArray[0][]=$string;
				
		}
	}
	$i=1;
	foreach($folder_reference_array as $reference)
	{
		$url = SITE_URL."/excel-devs/korben/view-pictures.php?client=$client&reference=$reference";
		if(in_array($reference,$all_reference_array))
			$doublon = "DOUBLON (".$all_reference_array_folder_names[$reference].")";
		else 
			$doublon = '';
		$comment='';	
		
		if($client=='MORGAN' || $client=='MORGAN-UK' ||  $client=='MORGAN-HOF')$reference=substr($reference,2);
		
		$xlsArray[$i][0]=$doublon;
		$xlsArray[$i][1]=$reference;
		$xlsArray[$i][2]=$url;
		$xlsArray[$i][3]=$comment;
		
		//echo $reference."<pre>";print_r($csvArray);exit;
		if(array_key_exists($reference,$csvArray))
		{
			for($z=0;$z<count($csvArray[$reference]);$z++)
			{
				//echo $reference."<pre>";print_r($csvArray);exit;
				if($z!=($source_index-1))
				{
					$string=$csvArray[$reference][$z];
					$string=korbenCleanString($string);
					$xlsArray[$i][]=$string;
				}
			}
		}	
		
		$i++;
	} 
	return $xlsArray;

}

function korbenCleanString($string)
{
	$string=str_replace("<br>"," ",$string);
	$string=str_replace("</br>"," ",$string);
	$string=str_replace("\r\n"," ",$string);
	$string=str_replace("\n"," ",$string);
	$string=str_replace("\r"," ",$string);
	$string=str_replace("\t"," ",$string);
	return $string;
}

//Korben excel Process
function korbenExcelProcess($all_reference_array, $all_reference_array_folder_names, $folder_reference_array, $csvArray, $source_index, $ignoreCols, $disposal, $dbln_disposal, $client, $folder_id)
{
    // Header info for doublon sheet
    $doublon_xls[0][0] = 'Reference prod';
    $doublon_xls[0][1] = 'URL';
    $doublon_xls[0][2] = 'Doublon';
    $doublon_xls[0][3] = 'Titre';
    $doublon_xls[0][4] = 'Nbcar (40 max)';
    $doublon_xls[0][5] = 'Descriptif long';
    $doublon_xls[0][6] = 'Nbcar (200-350)';
    $doublon_xls[0][7] = 'Descriptif Market Place';
    $doublon_xls[0][8] = 'Nbcar (90-110)';
    $doublon_xls[0][9] = 'Comment';

    // Header info for main sheet
    $xlsNoInfo[0][0] = 'Reference prod';
    $xlsNoInfo[0][1] = 'URL';

    // Header info for main sheet
    $xlsArray[0][0] = 'Reference prod';
    $xlsArray[0][1] = 'URL';
    $xlsArray[0][2] = 'Titre';
    $xlsArray[0][3] = 'Nbcar (40 max)';
    $xlsArray[0][4] = 'Descriptif long';
    $xlsArray[0][5] = 'Nbcar (200-350)';
    $xlsArray[0][6] = 'Descriptif Market Place';
    $xlsArray[0][7] = 'Nbcar (90-110)';
    $xlsArray[0][8] = 'Comment';
    
	//unset($csvArray['3331008']);
	//unset($csvArray['3326002']);

    // Count of columns to be ignored
    $igCnt = min($ignoreCols);

    // Array to hold first row for writer file (main sheet and doublonshhet)
    for ($i = 0; $i < count($csvArray[0]); $i++) {
        if ($i != ($source_index - 1)) {
            if (!in_array($igCnt, $ignoreCols)) {
                $string = $csvArray[0][$i];

		// Cleaning the cell value
                $string = korbenCleanString($string);

		// header info for sheet 1 which have info in source file
                $xlsArray[0][] = $string;

		// header info for doublon sheet
                $doublon_xls[0][] = $string;
            }
            $igCnt++;
        }
    }

    $arraySource = glob(constant("K2_".$client."_WRITER_FILE_PATH") . "/*.xlsx");
	if($client=='BONOBO'){
		usort($arraySource, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});

	}else{
		usort($arraySource, function($a, $b) {
			return filemtime($a) > filemtime($b);
		});
	}

	$clientRf['CACHECACHE'] = 1;
	$clientRf['MORGAN'] = 3;
	$clientRf['BONOBO'] = 3;
	$clientRf['BONOBO-UK'] = 3;
	$clientRf['MORGAN-UK'] = 5;
	$clientRf['MORGAN-HOF'] = 3;
	$clientRf['SCOTTAGE2'] = 3;

	foreach ($arraySource as $excelFile) {

		if(!strstr($excelFile, $folder_id.".xlsx"))
		{
			require_once (INCLUDE_PATH."/PHPExcel.php");
	
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');
			$objReader->setReadDataOnly(true);
			$objPHPExcel = $objReader->load($excelFile);
			$sheetname = $objPHPExcel->getSheetNames();
			$ws=1;
			foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
				if($ws==1){
					$xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
				}
				$ws++;
			}//echo "<pre>";print_r($xlsArr1);exit;
	
			for ($i = 0; $i < sizeof($xlsArr1); $i++) {
				if (sizeof($xlsArr1[$i])>0) {
					$x = 0;
					while ($x < sizeof($xlsArr1[$i])) {
						$y = 1;
						while ($y <= sizeof($xlsArr1[$i][$x])) {
							$xlsArr1[$i][$x][$y-1] = str_replace("´", "’", $xlsArr1[$i][$x][$y-1]) ;
							$clRfName = isset($xlsArr1[$i][$x][$y-1]) ? $xlsArr1[$i][$x][$y-1] : '' ;
							//if($x>0 && $y==$clientRf[$client])
							if($x>0 && $y<2 && !$clRf[$clRfName])
							{
								$clRf[$clRfName] = basename($excelFile);
							}
							$y++;
						}
						$x++;
					}
				}
			}
		}
	}

    $i = 1;
    $xlsArrCnt = 1; // Row count for sheet 1 which have info in source file
    $xlsNoInfoCnt = 1; // Row count for sheet 3 which have no info in source file
    $dblnArrCnt = 1; // Row count for doublon sheet

	//echo '###';echo "<pre>";print_r($clRf);exit;//echo '###';print_r($csvArray);exit('--'.$clientRf[$client]);

    foreach ($folder_reference_array as $reference) {
        $url = SITE_URL . "/excel-devs/korben/view-pictures.php?client=$client&reference=$reference";

        /*if (in_array($reference, $all_reference_array))
            $doublon = "DOUBLON (" . $all_reference_array_folder_names[$reference] . ")";
        else*/
	
	$doublon = '';
        $comment = '';

        if ($client == 'MORGAN' || $client == 'MORGAN-UK' || $client == 'MORGAN-HOF')
            $reference = substr($reference, 2);


	if($clRf[$reference])
	{
	    $doublon = "DOUBLON (" . $clRf[$reference] . ")";

	    // Doublon sheet info
            $doublon_xls[$dblnArrCnt][0] = $reference;
            $doublon_xls[$dblnArrCnt][1] = $url;
            $doublon_xls[$dblnArrCnt][2] = $doublon;
            $doublon_xls[$dblnArrCnt][3] = '';
            $doublon_xls[$dblnArrCnt][4] = '';
            $doublon_xls[$dblnArrCnt][5] = '';
            $doublon_xls[$dblnArrCnt][6] = '';
            $doublon_xls[$dblnArrCnt][7] = '';
            $doublon_xls[$dblnArrCnt][8] = '';
            $doublon_xls[$dblnArrCnt][9] = $comment;

	}
	elseif ($csvArray[$reference]) {
	    // SHEET 1 -  Images which have info in source file
            $xlsArray[$xlsArrCnt][0] = $reference;
            $xlsArray[$xlsArrCnt][1] = $url;
            $xlsArray[$xlsArrCnt][2] = '';
            $xlsArray[$xlsArrCnt][3] = '';
            $xlsArray[$xlsArrCnt][4] = '';
            $xlsArray[$xlsArrCnt][5] = '';
            $xlsArray[$xlsArrCnt][6] = '';
            $xlsArray[$xlsArrCnt][7] = '';
            $xlsArray[$xlsArrCnt][8] = $comment;//echo '--'.$reference.'<br>';print_r($csvArray[$reference]);
        }
	else
	{
	    // SHEET 3 -  Images which have no info in source file
            $xlsNoInfo[$xlsNoInfoCnt][0] = $reference;//exit($reference);
            $xlsNoInfo[$xlsNoInfoCnt][1] = $url;
            //echo '||'.$reference.'<br>';print_r($csvArray[$reference]);
	}

//echo '--'.$reference.'<br>';//echo "<pre>";print_r($csvArray);echo '###'; print_r($doublon_xls);echo '###'; print_r($xlsNoInfo);exit($folder_id);

	// Array to hold data for writer file (main sheet and doublonshhet)
        if (array_key_exists($reference, $csvArray)) {
            $igCnt = min($ignoreCols);
            for ($z = 0; $z < count($csvArray[$reference]); $z++) {

                if ($z != ($source_index - 1)) {
                    if (!in_array($igCnt, $ignoreCols)) {
                        $string = $csvArray[$reference][$z];

			// Cleaning the cell value
                        $string = korbenCleanString($string);

			//if ($csvArray[$reference])
			if($xlsArray[$xlsArrCnt])
				$xlsArray[$xlsArrCnt][] = $string;	
			elseif($doublon_xls[$dblnArrCnt])
				$doublon_xls[$dblnArrCnt][] = $string;	

                        /*if (empty($doublon))
                            $xlsArray[$xlsArrCnt][] = $string;
                        else
                            $doublon_xls[$dblnArrCnt][] = $string;*/
                    }
                    $igCnt++;
                }
            }
        }
        $i++;//echo $xlsArrCnt.'-'.$xlsNoInfoCnt.'<br>';

	if($clRf[$reference])
		$dblnArrCnt++; // Numberof rows for sheet 2
        elseif ($csvArray[$reference])
            $xlsArrCnt++; // Numberof rows for sheet 1 
        else
            $xlsNoInfoCnt++; // Numberof rows for sheet 3
    }

//echo "<pre>";print_r($xlsArray);echo '###'; print_r($xlsNoInfo);echo '###'; print_r($doublon_xls);exit($folder_id);

    // Re-arranging columns for main sheet
    foreach ($xlsArray as $rkey => $rows)
        foreach ($rows as $colKey => $col)
            $xlsArr[$rkey][$colKey] = $disposal[$colKey] ? $xlsArray[$rkey][$disposal[$colKey] - 1] : $col;

//echo "<pre>";print_r($xlsArr); echo '###'; print_r($xlsNoInfo); echo '###'; print_r($doublon_xls); exit;

    // Re-arranging columns for doublon sheet
    foreach ($doublon_xls as $dbln_rkey => $dbln_rows)
        foreach ($dbln_rows as $dbln_colKey => $dbln_col)
            $doublonArr[$dbln_rkey][$dbln_colKey] = $dbln_disposal[$dbln_colKey] ? $doublon_xls[$dbln_rkey][$dbln_disposal[$dbln_colKey] - 1] : $dbln_col;

    return array($xlsArr, $doublonArr, $xlsNoInfo);
}

function writeKorbenXlsx($datas, $file_path, $sheetnames)
{
    // PHPExcel
    include_once INCLUDE_PATH . '/PHPExcel.php';

    // PHPExcel_Writer_Excel2007
    include_once INCLUDE_PATH . '/PHPExcel/Writer/Excel2007.php';

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel -> getProperties() -> setCreator("Anoop");
    //echo "<pre>";print_r($datas);print_r($sheetnames);exit($file_path);

    $stylArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '2C2B2B')), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => '000000'))), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CFE7F5', )), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

    $stylHeadArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '8E8A8A', )), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF'))), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

    $celWdth[0] = array('A' => 12, 'B' => 80, 'C' => 25, 'D' => 8, 'E' => 25, 'F' => 8, 'G' => 25, 'H' => 8, 'I' => 25, 'J' => 35, 'K' => 35, 'L' => 25, 'M' => 25, 'N' => 25, 'O' => 25);
    $celWdth[1] = array('A' => 12, 'B' => 80, 'C' => 40, 'D' => 25, 'E' => 8, 'F' => 25, 'G' => 8, 'H' => 25, 'I' => 8, 'J' => 25, 'K' => 35, 'L' => 35, 'M' => 25, 'N' => 25, 'O' => 25, 'P' => 38);
    $celWdth[2] = array('A' => 12, 'B' => 80);

    $sheetCount = 0;
    foreach ($datas as $idx => $data) {
        // Rename sheet
        $sheet_name = $sheetnames[$idx];

        $objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
        $objPHPExcel -> addSheet($objWorksheet);
        $objWorksheet -> setTitle($sheet_name);

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $wdth[$col] = 1;
                $col++;
            }
            $rowCount++;
        }

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $value = str_replace("", "œ", $value);
                $value = str_replace("", "'", $value);
                $value = str_replace("", "'", $value);
                //$value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));

                if (((($col == 'D' || $col == 'F' || $col == 'H') && ($sheetCount == 0)) || (($col == 'E' || $col == 'G' || $col == 'I') && ($sheetCount == 1))) && ($rowCount > 0))
                    $objWorksheet -> setCellValue($col . ($rowCount + 1), '=LEN(' . chr(ord($col) - 1) . ($rowCount + 1) . ')');
                else
                    $objWorksheet -> setCellValue($col . ($rowCount + 1), $value);

                if (strstr($value, "http://clients.edit-place.com/excel-devs/")) {
                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setUrl($value);
                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setTooltip($value);
                }
                $col++;
            }
            foreach ($wdth as $key => $value)
                $objWorksheet -> getStyle($key . ($rowCount + 1)) -> applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);
            $rowCount++;
        }

        foreach ($wdth as $key => $value)
            $objWorksheet -> getColumnDimension($key) -> setWidth($celWdth[$sheetCount][$key] ? $celWdth[$sheetCount][$key] : 25);
        $objWorksheet -> getRowDimension(1) -> setRowHeight(25);

        unset($wdth);
        $sheetCount++;
    }
    $objPHPExcel -> removeSheetByIndex(0);

    // Save Excel 2007 file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter -> save($file_path);

    @chmod($file_path, 0777);
    //echo "<pre>";print_r($data);exit;

    if (file_exists($file_path)) {
        chmod($file_path, 0777);
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Pragma: private");
        header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download; charset=ISO-8859-1");
        header("Accept-Ranges: bytes");
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header("Content-Length: " . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
    }
}
/*
 * write korben xlsx 2 is used for writing k2 xlsx with taking care of special characters
 * @param $datas data to be written
 * @param $file_path file path where it to be written 
 * @sheetnames Sheetnames of xlsx to be written
 * 
 * */
 if(!function_exists('writeKorbenXlsx2')){
function writeKorbenXlsx2($datas, $file_path, $sheetnames)
{
    // PHPExcel
    include_once INCLUDE_PATH . '/PHPExcel.php';

    // PHPExcel_Writer_Excel2007
    include_once INCLUDE_PATH . '/PHPExcel/Writer/Excel2007.php';

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set properties
    $objPHPExcel -> getProperties() -> setCreator("Anoop");
    //echo "<pre>";print_r($datas);print_r($sheetnames);exit($file_path);

    $stylArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '2C2B2B')), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => '000000'))), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CFE7F5', )), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

    $stylHeadArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '8E8A8A', )), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF'))), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

    $celWdth[0] = array('A' => 12, 'B' => 80, 'C' => 25, 'D' => 8, 'E' => 25, 'F' => 8, 'G' => 25, 'H' => 8, 'I' => 25, 'J' => 35, 'K' => 35, 'L' => 25, 'M' => 25, 'N' => 25, 'O' => 25);
    $celWdth[1] = array('A' => 12, 'B' => 80, 'C' => 40, 'D' => 25, 'E' => 8, 'F' => 25, 'G' => 8, 'H' => 25, 'I' => 8, 'J' => 25, 'K' => 35, 'L' => 35, 'M' => 25, 'N' => 25, 'O' => 25, 'P' => 38);
    $celWdth[2] = array('A' => 12, 'B' => 80);

    $sheetCount = 0;
    foreach ($datas as $idx => $data) {
        // Rename sheet
        $sheet_name = $sheetnames[$idx];

        $objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
        $objPHPExcel -> addSheet($objWorksheet);
        $objWorksheet -> setTitle($sheet_name);

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $wdth[$col] = 1;
                $col++;
            }
            $rowCount++;
        }

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
				//if($col!='A'){
					//$value = str_replace("?", "", $value);
					//$value = str_replace("?", "'", $value);
					//$value = str_replace("?", "'", $value);
					$value = str_replace("'", "'", $value) ;
					$value=utf8_encode($value);
					$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
					$value = str_replace("&#039;", "'", $value) ;
				//}
				//$value=html_entity_decode($value);
				//$value=html_entity_decode($value);
				//$value=utf8_encode($value);
				$value=utf8_decode($value);
				//$value=utf8_encode($value);
				//echo $value."   ";
                if (((($col == 'D' || $col == 'F' || $col == 'H') && ($sheetCount == 0)) || (($col == 'E' || $col == 'G' || $col == 'I') && ($sheetCount == 1))) && ($rowCount > 0))
                    $objWorksheet -> setCellValue($col . ($rowCount + 1), '=LEN(' . chr(ord($col) - 1) . ($rowCount + 1) . ')');
                else
                    $objWorksheet -> setCellValue($col . ($rowCount + 1), $value);

                if (strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='B')) {
                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setUrl($value);
                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setTooltip($value);
                }
                $col++;
            }
			//echo "<br />";
            foreach ($wdth as $key => $value)
                $objWorksheet -> getStyle($key . ($rowCount + 1)) -> applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);
            $rowCount++;
        }
//		exit;
			
        foreach ($wdth as $key => $value)
            $objWorksheet -> getColumnDimension($key) -> setWidth($celWdth[$sheetCount][$key] ? $celWdth[$sheetCount][$key] : 25);
        $objWorksheet -> getRowDimension(1) -> setRowHeight(25);

        unset($wdth);
        $sheetCount++;
    }
    $objPHPExcel -> removeSheetByIndex(0);

    // Save Excel 2007 file
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    $objWriter -> save($file_path);

    @chmod($file_path, 0777);
    //echo "<pre>";print_r($data);exit;

    if (file_exists($file_path)) {
        chmod($file_path, 0777);
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Pragma: private");
        header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download; charset=UTF-8");
        header("Accept-Ranges: bytes");
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header("Content-Length: " . filesize($file_path));
        ob_clean();
        flush();
        readfile($file_path);
    }
}
}

?>
