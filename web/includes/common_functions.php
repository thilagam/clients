<?php
include_once(INCLUDE_PATH."/config_path.php");

function unzip($file)
{
	// get the absolute path to $file
	//$path = pathinfo(realpath($file), PATHINFO_DIRNAME);
		$zip_file=pathinfo($file);
		$zip_file['filename']=str_replace(" ","-",$zip_file['filename']);
		$path=$zip_file['dirname']."/".$zip_file['filename'];
		if(!is_dir($path))
			mkdir($path,0777,TRUE);
			
		// php zip archieve instance
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === TRUE) {
		  // extract it to the path we determined above
		  //$zip->extractTo($path);
		  //$zip->close();
		  
			  for ( $i=0; $i < $zip->numFiles; $i++ )
			{
				$entry = $zip->getNameIndex($i);
				
				if ( substr( $entry, -1 ) == '/' ) continue; // skip directories
			   
				$fp = $zip->getStream( $entry );
				$ofp = fopen( $path.'/'.basename($entry), 'w' );
			   
				if ($fp )
				{
					
						while ( ! feof( $fp ) )
							fwrite( $ofp, fread($fp, 8192) );
					
				}		
				
				fclose($fp);
				fclose($ofp);
			} 
		} else {
		  echo "Doh! I couldn't open $file";
		}
}

// Function to decompress zip file
function unzipfolder($file) {
        $zip_file = pathinfo($file);
        $zip_file['filename'] = str_replace(" ", "-", $zip_file['filename']);
        $path = $zip_file['dirname'] . "/" . $zip_file['filename'];
		
        if (!is_dir($path))
            mkdir($path, 0777, TRUE);
        chmod($path, 0777);
		
        $zip = new ZipArchive; // php zip archieve instance
        $res = $zip->open($file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ((substr($entry, -1) == '/') || strstr($entry, '__MACOSX'))
                    continue;

                $entry1 = str_replace(' ', '_', $entry);
                $fp = $zip->getStream($entry);
                $ofp = fopen($path . '/' . basename($entry1), 'w');
                if ($fp) {
                    while (!feof($fp))
                        fwrite($ofp, fread($fp, 8192));
                }
                fclose($fp);
                fclose($ofp);
            }
            return $path;
        } else {
            echo "Doh! I couldn't open $file";
        }
    }

// Checking a directory is empty or not
function is_empty_dir($dir)
{
    if (($files = @scandir($dir)) && count($files) <= 2) {
        return true;
    }
    return false;
}
/**get image count of a zip and unzip files**/
function getImagesCount($dir)
{
	if(strpos($dir, '.zip'))
	{
		$zip = new ZipArchive; // php zip archieve instance
		$res = $zip->open($dir);
		if ($res === TRUE) {
		
			  return $zip->numFiles;
		}
		else
		{
			return "Zip file broken or empty!!";
		}		
	}
	else
	{
		$count=count(glob($dir."/*"));
		return $count;
	}

}
/**delete dir with all files in it**/
 function delTree($dir) {
   chmod($dir,0777);
   $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
      (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    
	return rmdir($dir);
}

// Reading csv file and return as an array
if(!function_exists('csvRead')){
function csvRead($csv_path,$csv_index=NULL)
{
	if(file_exists($csv_path))
	{	
		$arrayDesc =  file($csv_path);
		
		//$arrayDesc=explode("\n",$arrayDesc);
		//echo $csv_path."<pre>";print_r($arrayDesc);exit;
		
		$z = 0;	
		if(count($arrayDesc)>0)
		{
			foreach($arrayDesc as $d)
			{
				$desc = str_replace("\n","",$d);
				$desc = explode(";",$d);
				$index = ($csv_index-1);
				$referenceDesc = $desc[$index];
				if($z==0)$referenceDesc = 0;elseif(!$referenceDesc)$referenceDesc=$z;
				$csvArray[$referenceDesc] = $desc;
				$z++;
			}
		}
		
	}	
	else 
		$csvArray = array();
	return 	$csvArray;

}
}

// Function to write xls file
if(!function_exists('WriteXLS')){
	function WriteXLS($data,$file_path,$rename=NULL)
	{
		
		// include package
		include 'Spreadsheet/Excel/Writer.php';
		
		
		$excel = new Spreadsheet_Excel_Writer($file_path);
		$excel->setVersion(8);

		// add worksheet
		$sheet =& $excel->addWorksheet();
		$sheet->setColumn(0,count($data[1]),20);
		
		
		//custom color
		$excel->setCustomColor(22, 217, 151,149);
		$excel->setCustomColor(12, 252, 213,180);
		
		// create format for header row
		// bold, red with black lower border
			
		$format_a = array('bordercolor' => 'black',
						
				'bold'=>'1',
				'size' => '11',
				'FgColor'=>'22',
				'color'=>'black',
				'align' => 'center',
				'valign' => 'top'); 
			
		$format_headers =& $excel->addFormat($format_a); 
		$format_headers->setBorder(1);
		//$format_headers->setTextWrap();
		
		$wrap_format=& $excel->addFormat();
		$wrap_format->setVAlign('top');
		//$wrap_format->setBorder(1);
		$wrap_format->setFgColor(12);	
		//$wrap_format->setTextWrap();
		$wrap_format->setAlign('left');
		
		
		
		// add data to worksheet
		$rowCount=0;
		//echo (count($data[4])-2)."<pre>";print_r($data);echo "</pre>";exit;
		foreach ($data as $row) {
			//$col_cnt=count($row);
		  foreach ($row as $key => $value) {
			if($rowCount==0)
			{
				$sheet->write($rowCount, $key, $value,$format_headers);
			}
			else
				$sheet->write($rowCount, $key, $value,$wrap_format);
		  }
		  $rowCount++;
		}

		// save file to disk
		if ($excel->close() === true) {
			chmod($file_path,0777);
			header("Content-Transfer-Encoding: binary");
			header("Expires: 0");
			header("Pragma: private");
			header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download; charset=ISO-8859-1");
			header("Accept-Ranges: bytes");
			header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
			header("Content-Length: ".filesize($file_path));
			ob_clean();
			flush();
			readfile($file_path);
		  
		} 

	}
}	
/**Create Multisheet XLS File**/
if(!function_exists('WriteMultiSheetXLS')){
	function WriteMultiSheetXLS($datas,$file_path, $sheetnames)
	{

		// include package
		include_once 'Spreadsheet/Excel/Writer.php';
		// create empty file
		$file_details=pathinfo($file_path);
		$filename=$file_details['filename'];
		
		$excel = new Spreadsheet_Excel_Writer($file_path);
		$excel->setVersion(8) ;
		
		//custom color
		$excel->setCustomColor(22, 217, 151,149);
		$excel->setCustomColor(12, 252, 213,180);
		
		// create format for header row
		// bold, red with black lower border
		$format_a = array('bordercolor' => 'black',
						
				'bold'=>'1',
				'size' => '11',
				'FgColor'=>'22',
				'color'=>'black',
				'align' => 'center',
				'valign' => 'top'); 
			
		$header =& $excel->addFormat($format_a); 
		$header->setBorder(1);
		//$header->setTextWrap();
		
		$cell=& $excel->addFormat();
		$cell->setVAlign('top');
		//$wrap_format->setBorder(1);
		$cell->setFgColor(12);	
		//$wrap_format->setTextWrap();
		$cell->setAlign('left');
		
		$scell = '';
		
		foreach($datas as $sheet_cnt=>$data)
		{
			$sheet_name=$sheetnames[$sheet_cnt];
			$sheet_obj='sheet'.$sheet_cnt;
			$$sheet_obj=& $excel->addWorksheet($sheet_name);
			$$sheet_obj->setInputEncoding('utf-8');
			
			
			// add data to worksheet
			$rowCount=0;
			foreach ($data as $row) {
			  foreach ($row as $key => $value) {
				$value= (str_replace("�", "'", $value)) ;
				if($rowCount==0){
					$$sheet_obj->write($rowCount, $key, $value,$header);
				}
				elseif ($value=='#Formula') {
					$scell = Spreadsheet_Excel_Writer::rowcolToCell($rowCount,$key-1);
					$$sheet_obj->writeFormula($rowCount,$key, "=LEN($scell)", $cell);
				}
				else
				{                
					$$sheet_obj->write($rowCount, $key, $value,$cell);
				}
					
			  }
			  $rowCount++;
			}
		}
		// save file to disk
		if ($excel->close() === true) {
			return $filename ;
		} else {
			return false ;
		}
	}
}	
//XLS file reading
if(!function_exists('xlsRead')){
	function xlsRead($file)
	{
		require_once(INCLUDE_PATH."/reader.php");
		
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('UTF8');
		$data->read($file);
		$bound_sheets=$data->boundsheets;
		$sheets = sizeof($data->sheets);
		
		//echo "<pre>";print_r($data->sheets[0]);exit;

		for ($i = 0; $i < $sheets; $i++) {
			$sheetname[$i]=$bound_sheets[$i]['name'];
			if (sizeof($data->sheets[$i]['cells'])>0) {
				$x = 1;
				while ($x <= sizeof($data->sheets[$i]['cells'])) {
					$y = 1;
					while ($y <= $data->sheets[$i]['numCols']) {
						$data->sheets[$i]['cells'][$x][$y] = convert_smart_quotes(str_replace('�', '-', $data->sheets[$i]['cells'][$x][$y])) ;
						if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
						{
							$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';
				$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
						}   else   {
							$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';
							$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
						}
						$y++;
					}
					$x++;
				}
			}
		}
		return array($xls_array, $sheetname) ;
	}
}
//XLSX file reading
if(!function_exists('xlsxRead')){
    function xlsxRead($file)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
        
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
                        $xls_array[$i][$x][$y] = convert_smart_quotes(str_replace('�', '-', $xlsArr1[$i][$x][$y-1])) ;
                        if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname);
    }
}

    //XLS file reading
if(!function_exists('oxlsRead'))
{
    function oxlsRead($file,$lastcol='')
    {
        require_once(INCLUDE_PATH."/reader.php");
        
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252') ;
        $data->read($file);
        $bound_sheets=$data->boundsheets;
        $sheets = sizeof($data->sheets);

        for ($i = 0; $i < $sheets; $i++)
        {
            $sheetname[$i]=$bound_sheets[$i]['name'];
            if (sizeof($data->sheets[$i]['cells'])>0)
            {
                $x = 1;
                while ($x <= sizeof($data->sheets[$i]['cells']))
                {
                    $y = 1;
                    while ($y <= $data->sheets[$i]['numCols'])
                    {	
						if($lastcol=='' || $lastcol>=$y)
						{
							$data->sheets[$i]['cells'][$x][$y] = convert_smart_quotes($data->sheets[$i]['cells'][$x][$y]) ;
							/*if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
								$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';
							else*/
							$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';

							if(strlen($xls_array[$i][$x][$y])>strlen(utf8_decode($xls_array[$i][$x][$y])))
								$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
							else
								$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? utf8_encode($xls_array[$i][$x][$y]) : '';
							$xls_array[$i][$x][$y] = utf8_decode($xls_array[$i][$x][$y]) ;
						}
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname) ;
    }
}
//XLSX file reading
if(!function_exists('oxlsxRead'))
{
    function oxlsxRead($file,$lastcol='')
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
        
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
						if($lastcol=='' || $lastcol>=$y){
							//$xls_array[$i][$x][$y] =  convert_smart_quotes(html_entity_decode($xlsArr1[$i][$x][$y-1], ENT_QUOTES, "Windows-1252"));
							$xls_array[$i][$x][$y] = convert_smart_quotes(str_replace('–', '-', $xlsArr1[$i][$x][$y-1])) ;
							
							/*if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
								$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
							else*/
							$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
							
							if(strlen($xls_array[$i][$x][$y])>strlen(utf8_decode($xls_array[$i][$x][$y])))
								$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
							else
								$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? utf8_encode($xls_array[$i][$x][$y]) : '';
							$xls_array[$i][$x][$y] = utf8_decode($xls_array[$i][$x][$y]) ;
						}
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname);
    }
}
//XLSX file reading
if(!function_exists('xlsx_read'))
{
    function xlsx_read($file)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
        
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
                        
                        $xls_array[$i][$x][$y] = str_replace('–', '-', $xlsArr1[$i][$x][$y-1]) ;
                        if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }
                        
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname);
    }
}

//Get XLSX content
if(!function_exists('getXlsx')){
    function getXlsx($file)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php") ;
        $objReader = PHPExcel_IOFactory::createReader('Excel2007') ;
        $objReader->setReadDataOnly(true) ;
        $objPHPExcel = $objReader->load($file) ;
        $sheetname = $objPHPExcel->getSheetNames() ;
        
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet)
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false) ;
        
        return $xlsArr1 ;
    }
}

//Get XLS content
if(!function_exists('getXls')){
    function getXls($file)
    {
        require_once(INCLUDE_PATH."/reader.php");
        
        $data = new Spreadsheet_Excel_Reader() ;
        $data->setOutputEncoding('Windows-1252') ;
        
        $data->read($file) ;
        $xls1Arr = $data->sheets[0]['cells'] ;
        
        return $xls1Arr ;
    }
}

//Get XLS content
if(!function_exists('getXlsinfo')){
    function getXlsinfo($file)
    {
        require_once(INCLUDE_PATH."/reader.php");
        
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252') ;
        $data->read($file) ;
        
        return $data ;
    }
}

//Get XLS modified content
if(!function_exists('getXlsModified')){
    function getXlsModified($file)
    {
        require_once(INCLUDE_PATH."/reader.php");
        $data = new Spreadsheet_Excel_Reader() ;
        $data->setOutputEncoding('Windows-1252') ;
        $data->read($file) ;
        if($data->sheets[0]['numRows'])
        {
            $x=1;
            while($x<=$data->sheets[0]['numRows']) {
                $y=1;
                while($y<=$data->sheets[0]['numCols']) {
                
                    $xls_array[$x][$y]=isset($data->sheets[0]['cells'][$x][$y]) ? $data->sheets[0]['cells'][$x][$y] : '';
                    $y++;
                }
                $x++;
            }
        }
        return $xls_array ;
    }
}
//Get Andre References
function getAndreReferences($check_reference=NULL)
{
    $client_image_path=ANDRE_IMAGE_PATH."/";
    
    $refs=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
    
    $loop=0;    
    foreach($refs as $index=>$folder)
    {
        $img_directory = $folder;           
        $img_directory_name=basename($img_directory);
        
		// Getting all client reference images
        $reference_directories = glob($img_directory."/$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);   
        
        usort($reference_directories, function($a, $b) {
            return filemtime($a) < filemtime($b);
        });     
           
        $references_text.='<div class="row-fluid">
                            <div class="span12">
                                <h4 class="heading">'.$img_directory_name.'</h4>
            ';   
        
        if(count($reference_directories)>0)
        {
            $reference_array=array();
			
			// Creating reference array from each reference directory images
            foreach($reference_directories as $image)
            {
                $image=basename($image);
                $s=explode("_",$image);         
                if($s[0])
                {               
                    $reference=$s[0];                   
                    $reference_array[$reference]=$img_directory_name;       
                }   
            }
            ksort($reference_array);
			
			// Creating view pictures link for each references
            foreach($reference_array as $reference=>$value)
            {
                $references_text.='<a target="andre" href="'.SITE_URL.'/excel-devs/andre/view-pictures.php?client=ANDRE&reference='.$reference.'"><span class="badge">'.$reference.'</span></a>&nbsp;';
                
            }   
        }   
        else
        {
            $references_text.='<span class="label label-important">No References Found</span>'; 
        }       
        
        $references_text.='     </div>
                        </div>';        
        
        
    }
    echo $references_text;
    //exit;
}

//Korben fucntions to get referece images

function getAndreReferenceImages($reference=NULL)
{
    $client_image_path=ANDRE_IMAGE_PATH."/";
    
    $ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
    $loop=0;    
    if(count($ref_directory)>0)
    {
		// Looping through each client reference directory for respective image reference
        foreach($ref_directory as $index=>$folder)
        {
            $img_directory = $folder;
			
			// Getting all client reference images
            $files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);

            if(count($files)>0) 
            {       
				// Creating each client images link
                foreach($files as $file)
                {   
                    $string = basename($file);
                    $img=$file;
                    $img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
                
                    $reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery="">
                                <img class="img-polaroid" src="'.$img.'" width=150 height=250/>
                            </a>';
                }
            }       
            continue;
        }
    }   
    echo $reference_images;
}

function getClientReferences($check_reference = NULL, $client_image_path, $url, $client)
{
	// Getting all folder names
    $refs = glob($client_image_path . "/*", GLOB_ONLYDIR);	// All directories list
    $loop = 0;
    foreach ($refs as $index => $folder) {
        $img_directory = $folder;
        $img_directory_name = basename($img_directory);

	// Getting all jpg images per directory
	if($client=='Garnier' || $client=='GARNIER' ){
		$reference_directories = glob($img_directory."/*$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG,*.png,*.PNG}", GLOB_BRACE);
	}else{
		$reference_directories = glob($img_directory."/*$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
	}
        /* usort($reference_directories, function($a, $b) {
            return filemtime($a) < filemtime($b);
        }); */

	// Sorting images in descending order(last modified date)
	usort($reference_directories, "sorted") ;
        $references_text .= '<div class="row-fluid" id="'.$img_directory_name.'"><div class="span12"><h4 class="heading">' . $img_directory_name . '</h4>';

        if (count($reference_directories) > 0)
        {
            $reference_array = array();
            foreach ($reference_directories as $image) {
                $image = basename($image) ;
		// Get reference name from image url
                $s = getRefFrmImg($client, $image) ;
                if ($s[0]) {
                    $reference = $s[0];
                    $reference_array[$reference] = $img_directory_name;
                }
            }
            ksort($reference_array);
	    $client = str_replace(array("LAHALLESH","LAHALLECL"),array("LAHALLE","LAHALLE"),$client);

            foreach ($reference_array as $reference => $value)
            {
                $pathinfo = pathinfo($reference);
                $reference=$pathinfo['filename'];
                $references_text .= '<a target="'.$client.'" href="' . $url . '/view-pictures.php?client=' . $client . '&reference=' . $reference . ($client=='CLARKS' ? '&ln='.substr($client_image_path, (sizeof($client_image_path)-3) , 2) : '') . '"><span class="badge">' . $reference . '</span></a>&nbsp;';
            }
            if($_REQUEST['debug'])
            {
                echo '<pre>';print_r($reference_array);
            }
        }
        else
            $references_text .= '<span class="label label-important">No References Found</span>';

        $references_text .= '</div></div>';
    }
            if($_REQUEST['debug']){exit;}
    echo $references_text;
}

// Client reference name from image url for each clients
function getRefFrmImg($client, $image)
{
    if($client=='CHEVIGNON' || $client=='SANMARINA' || $client=='CLARKS' || $client=='TRUFFAUT' || $client=='LAHALLESH')
        $s = explode("_", $image);
    elseif($client=='GALERIES_LAFAYETTE')
    {
        $s = explode("_", $image); $s[0]=$s[1];
    } 
    elseif($client=='LEBONMARCHE' || $client=='ANDRE' || $client=='IKKS')
    {
        $s = explode("_", $image);
    }
     elseif($client=='Garnier')
    {
        $s = array($image);
    }
    elseif($client=='CAROLL')
    {
        $s=array_reverse(explode("-",$image)); $s[0]=$s[1];
    }
    elseif($client=='LEROYMERLIN')
    {
        $s=explode("[",$image);
    } 
    elseif($client=='COSMOPARIS' || $client=='ACCESSORIE_DIFFUSION')
    {
        $s = explode("@", $image);
        $s[0] = substr($s[1], 0, strpos($s[1], "_")); 
    }elseif($client=='MONOPRIX')
    {
        $s = explode("-", $image);
        if($s[0]=='g'){
			$s[0] = $s[1]; 
		}
    }
    elseif($client=='NAKAMURA')
	$s = explode(".", $image);
	
	elseif($client=='CELIO')
	$s = explode("_", $image);
    else
        $s = explode("-", $image);
        
    return $s ;
}

// Find client reference urls 
function getClientReferenceImages($reference = NULL, $client_image_path)
{
    $ref_directory = glob($client_image_path . "/*", GLOB_ONLYDIR);	// All directories list
    $loop = 0;
    if (count($ref_directory) > 0) {
	
		// Looping through each client reference directory for respective image reference
        foreach ($ref_directory as $index => $folder) {
            $img_directory = $folder;
			
			// Getting all client reference images
            $files = glob($img_directory . "/*" . $reference . "{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
            if (count($files) > 0) {
			
				// Creating url for client reference images
                foreach ($files as $file) {
                    $string = basename($file);
                    $img = $file;
                    $img = str_replace($_SERVER['DOCUMENT_ROOT'], "", $img);
                    $reference_images .= '<a href="' . SITE_URL . $img . '" data-gallery=""><img class="img-polaroid" src="' . $img . '" width=150 height=250/></a>';
                }
            }
            continue;
        }
    }
    echo $reference_images;
}

// Return client image urls for reference
if(!function_exists('getClientReferencePics'))
{
    function getClientReferencePics($reference = NULL, $client_image_path)
    {
        $pattern = NAFNAF_IMAGE_PATH.'/*/'.$reference.'-*'; // Naf naf client image name pattern
		
		// Getting all client image reference
        $arraySource = glob($pattern,GLOB_BRACE);
        sort($arraySource);
        
        if (count($arraySource) > 0) {
		
			// Creating image url for each client references 
            foreach ($arraySource as $file) {
                $string = basename($file);
                $img = $file;
                $img = str_replace($_SERVER['DOCUMENT_ROOT'], "", $img);
                $reference_images .= '<a href="' . SITE_URL . $img . '" data-gallery=""><img class="img-polaroid" src="' . $img . '" width=150 height=250/></a>';
            }
        }
        echo $reference_images;
    }
}

//convert smart quotes
if(!function_exists('convert_smart_quotes')){
    function convert_smart_quotes($string)
    {
        $search = array(chr(145), 
                        chr(146), 
                        chr(147), 
                        chr(148), 
                        chr(151),
                        chr(230),
                        chr(156),
                        "’",
                        "‘",
                        '“',
                        '”',
                        '–',
                        '–',
                        '–');
    
        $replace = array("'", 
                         "'", 
                         '"', 
                         '"', 
                         '-',
                         'ae',
                         'oe',
                         "'",
                         "'",
                         '"',
                         '"',
                         '-',
                         '-',
                         '-');
        return str_replace($search, $replace, $string); 
    }
}	

/**create zip file**/
if(!function_exists('create_zip'))
{
    function create_zip($files = array(),$destination = '',$overwrite = true)
    {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
            //cycle through each file
            $zfcnt=0;
            foreach($files as $file) {
                //make sure the file exists              
                if(file_exists($file[0]) && !is_dir($file[0])) {
                    $valid_files[$zfcnt][0] = $file[0];
                    $valid_files[$zfcnt][1] = $file[1];
                    $zfcnt++;
                }
            }
        }
        
        //if we have good files...
        if(count($valid_files)) {
		
            //create the archive
            $zip = new ZipArchive();
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true)
            {
                return false;
            }
            //add the files
            $numItems = count($valid_files);
            $i = 0;
            foreach($valid_files as $file) {
                if(getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows') :
                    $file_name=($file[1]);
                    $file_name=iconv('Windows-1252', 'IBM850',$file[1]);
                    $file_name=str_replace(".txt","",$file_name);
                    $file_name=$file_name.".txt";
                elseif(getOS($_SERVER['HTTP_USER_AGENT']) == 'Macintosh') :
                    $file_name=utf8_encode($file[1]);
                    $file_name=str_replace(".txt","",$file_name);
                    $file_name=$file_name.".txt";
                else :
                    $file_name=(html_entity_decode(($file[1]), ENT_COMPAT, 'UTF-32'));
                    $file_name=str_replace('€','&#8364;',$file_name);
                    $file_name=str_replace('Œ','&#338;',$file_name);
                endif ;

                $zip->addFile($file[0],$file_name);
            }
            //debug
            //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;exit;
            //close the zip -- done!
            $zip->close();
            //check to make sure the file exists
            return file_exists($destination);
        }
        else
        {
            return false;
        }
    }
}

/** zip creation **/
if(!function_exists('zip_creation'))
{
    function zip_creation($srcPath, $srcFile)
    {
        if ($handle = opendir($srcPath)) {
                
            $zip = new ZipArchive(); // Load zip library 
            $zip_name = $srcFile; // Zip name
            if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
            { 
                // Opening zip file to load files
                $error .= "* Sorry ZIP creation failed at this time";
            }
            
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $zip_file=pathinfo($srcPath."$entry") ;
                    $zip->addFile($srcPath.$zip_file['filename'].".".$zip_file['extension'], $zip_file['filename'].".".$zip_file['extension']) ;
                }
            }
            closedir($handle);
            $zip->close();
        }
    }
}

if(!function_exists('cleanString'))
{
    function cleanString($string) {
        
        $find[] = '';  // left side double smart quote
        $find[] = '';  // right side double smart quote
        $find[] = "";  // left side single smart quote
        $find[] = "";  // right side single smart quote
        $find[] = '';  // elipsis
        $find[] = '';  // em dash
        $find[] = '';
        $find[] = '…';
        $find[] = "’";
        $find[] = "–";
        
        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = '...';
        $replace[] = '-';
        $replace[] = '-';
        $replace[] = '...';
        $replace[] = "'";
        $replace[] = "-";
        
        return str_replace($find, $replace, $string);
    }
}

if(!function_exists('detect_encoding'))
{
    function detect_encoding($string)
    {  
        static $list = array('utf-8', 'windows-1251');
          
        foreach ($list as $item) {
            $sample = iconv($item, $item, $string);
            if (md5($sample) == md5($string))
                return $item;
        }
        return null;
    }
}

if(!function_exists('getAllParentsFromAllExcel'))
{
    function getAllParentsFromAllExcel($ref, $urlid, $refid, $client_url, $file_path)
    {
        // Array variables to store all previous reference data from previous writer files 
		global $global_parents_data,$global_parents_data_file,$global_parents_xls_file ;
		
        $refs = glob($file_path . '/*' , GLOB_ONLYDIR);	// All directories list
       //print_r($refs);exit;
        foreach ($refs as $refdir)
        {
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xls', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
           // print_r($arraySource);
            if(!empty($arraySource)){
				foreach($arraySource as $excel)
				{
					$file=$excel;
					$basename=basename($file);
					//exit;
					// Get reference from xls writer file data
					getExcelDatas($file, $urlid, $refid, $client_url);
				}
			}
        }
        
        foreach ($refs as $refdir)
        {
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xlsx', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
            //echo '<pre>';print_r($arraySource); exit($refdir . '/*.xlsx');
            
            foreach($arraySource as $excel)
            {
                $file=$excel;
                $basename=basename($file);
				
				// Get reference from xlsx writer file data
                getXlsxDatas($file, $urlid, $refid, $client_url);
            }
        }//echo '<pre>@@';print_r($global_parents_data_file); exit('*****'.$client_url);
    }
}

if(!function_exists('newgetAllParentsFromAllExcel'))
{
    function newgetAllParentsFromAllExcel($ref, $urlid, $refid, $client_url, $file_path)
    {
        // Array variables to store all previous reference data from previous writer files 
		global $global_parents_data,$global_parents_data_file,$global_parents_xls_file ;
		
        $refs = glob($file_path . '/*' , GLOB_ONLYDIR);	// All directories list
      	//echo "<pre>"; print_r($refdir);
        foreach ($refs as $refdir)
        {
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xls', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
           	//echo "<pre>"; print_r($arraySource);
            if(!empty($arraySource)){
				foreach($arraySource as $excel)
				{
					$file=$excel;
					$basename=basename($file);
					//exit;
					// Get reference from xls writer file data
					getExcelDatas($file, $urlid, $refid, $client_url);
				}
			}
        }
        
        foreach ($refs as $refdir)
        {
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xlsx', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
            //echo '<pre>';print_r($arraySource); //exit($refdir . '/*.xlsx');
            
            foreach($arraySource as $excel)
            {
                $file=$excel;
                $basename=basename($file);
				
				// Get reference from xlsx writer file data
                getXlsxDatas($file, $urlid, $refid, $client_url);
            }
        }//echo '<pre>@@';print_r($global_parents_data_file); exit;//('*****'.$client_url);
    }
}

// Get xlsx writer file data to store all previous reference data from previous writer files using phpExcel 
if(!function_exists('getXlsxDatas'))
{
    function getXlsxDatas($file, $urlid, $refid, $client_url)
    {
        // Array variables to store all previous reference data from previous writer files
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        require_once (INCLUDE_PATH."/PHPExcel.php") ;
        $objReader = PHPExcel_IOFactory::createReader('Excel2007') ;
        $objReader->setReadDataOnly(true) ;
        $objPHPExcel = $objReader->load($file) ;
        $sheetname = $objPHPExcel->getSheetNames() ;
        
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet)
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false) ;

//echo '<pre>@@';print_r($xlsArr1); exit($urlid.'--'.$refid.'--'.$client_url);

        for ($i = 0; $i < sizeof($xlsArr1); $i++)
        {
            if (sizeof($xlsArr1[$i])>0)
            {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i]))
                {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x]))
                    {
                        $xls_array[$i][$x][$y] = convert_smart_quotes(str_replace('�', '-', $xlsArr1[$i][$x][$y-1])) ;
                        if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }
                        
                        if((strstr($xls_array[$i][$x][$urlid], $client_url) || strstr($xls_array[$i][$x][$urlid], "http://korben.edit-place.com") || strstr($xls_array[$i][$x][$urlid], "http://clients.edit-place.com")) && (!$global_parents_data_file[$xls_array[$i][$x][$refid]]) && (!empty($xls_array[$i][$x][$refid])))
                        {
							// store all previous reference data from previous writer files to global variable 
                            $global_parents_data[]  =   $xls_array[$i][$x][$refid] ;
                            $global_parents_data_file[$xls_array[$i][$x][$refid]]   =   $xls_array[$i][$x][$urlid] ;
                            $global_parents_xls_file[$xls_array[$i][$x][$refid]]  =   $file ;
                        }
                        
                      
                        
                        $y++;
                    }
                    $x++;
                }
            }
        }
    }
}

// Get xlsx writer file data to store all previous reference data from previous writer files
if(!function_exists('getExcelDatas'))
{
    function getExcelDatas($file, $urlid, $refid, $client_url)
    {
        require_once INCLUDE_PATH . '/reader.php' ;
		
        // Array variables to store all previous reference data from previous writer files
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        
        $data1 = new Spreadsheet_Excel_Reader();
        $data1->setOutputEncoding('Windows-1252');
        $data1->read($file);

//echo "<pre>";print_r($data1->sheets);exit($urlid.'--'.$refid.'--'.$client_url);

        $sheets=sizeof($data1->sheets);
        for($i=0;$i<$sheets;$i++)
        {
            if($data1->sheets[$i]['numRows'])   
            {
                $x=1;
                while($x<=$data1->sheets[$i]['numRows']) {
                    if($x>1)
                    {
                        $data1->sheets[$i]['cells'][$x][$refid] = convert_smart_quotes($data1->sheets[$i]['cells'][$x][$refid]) ;
                        $data1->sheets[$i]['cells'][$x][$refid] = isset($data1->sheets[$i]['cells'][$x][$refid]) ? ((mb_detect_encoding($data1->sheets[$i]['cells'][$x][$refid]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data1->sheets[$i]['cells'][$x][$refid]) : $data1->sheets[$i]['cells'][$x][$refid]) : '';

                        if(strlen($data1->sheets[$i]['cells'][$x][$refid])>strlen(utf8_decode($data1->sheets[$i]['cells'][$x][$refid])))
                            $data1->sheets[$i]['cells'][$x][$refid] = isset($data1->sheets[$i]['cells'][$x][$refid]) ? html_entity_decode($data1->sheets[$i]['cells'][$x][$refid],ENT_QUOTES,"UTF-8") : '';
                        else
                            $data1->sheets[$i]['cells'][$x][$refid] = isset($data1->sheets[$i]['cells'][$x][$refid]) ? utf8_encode($data1->sheets[$i]['cells'][$x][$refid]) : '';
                        $data1->sheets[$i]['cells'][$x][$refid] = utf8_decode($data1->sheets[$i]['cells'][$x][$refid]) ;

                        if((strstr($data1->sheets[$i]['cells'][$x][$urlid], $client_url) || strstr($data1->sheets[$i]['cells'][$x][$urlid], "http://korben.edit-place.com") || strstr($data1->sheets[$i]['cells'][$x][$urlid], "http://clients.edit-place.com")) && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]) && (!empty($data1->sheets[$i]['cells'][$x][$refid])))
                        {
							// store all previous reference data from previous writer files to global variable
                            $global_parents_data[]  =   $data1->sheets[$i]['cells'][$x][$refid] ;
                            $global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]   =   $data1->sheets[$i]['cells'][$x][$urlid] ;
                            $global_parents_xls_file[$data1->sheets[$i]['cells'][$x][$refid]]  =   $file ;
                        }
                        
                       
                        
                    }
                    $x++;
                }
            }
        }
    }
}

//get OS of a user
if(!function_exists('getOS')){
function getOS($userAgent) {
        // Create list of operating systems with operating system name as array key
        $oses = array('iPhone' => '(iPhone)', 'Windows' => 'Win16', 'Windows' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
        'Windows' => '(Windows 98)|(Win98)', 'Windows' => '(Windows NT 5.0)|(Windows 2000)', 'Windows' => '(Windows NT 5.1)|(Windows XP)', 'Windows' => '(Windows NT 5.2)', 'Windows' => '(Windows NT 6.0)|(Windows Vista)', 'Windows' => '(Windows NT 6.1)|(Windows 7)', 'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows' => 'Windows ME', 'Open BSD' => 'OpenBSD', 'Sun OS' => 'SunOS', 'Linux' => '(Linux)|(X11)', 'Safari' => '(Safari)', 'Macintosh' => '(Mac_PowerPC)|(Macintosh)', 'QNX' => 'QNX', 'BeOS' => 'BeOS', 'OS/2' => 'OS/2', 'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

        foreach ($oses as $os => $pattern) {// Loop through $oses array

            // Use regular expressions to check operating system type
            if (strpos($userAgent, $os)) {// Check if a value in $oses array matches current user agent.
                return $os;
                // Operating system was matched so return $oses key
            }
        }
        return 'Unknown';
        // Cannot find operating system so return Unknown
    }	
}	


//Get Caroll References
function getCarollReferences($check_reference=NULL)
{
	$client_image_path=CAROLL_IMAGE_PATH."/";
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list 		
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;			
		$img_directory_name=basename($img_directory);

		// Get all client image references 
		$reference_directories = glob($img_directory."/*$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);	
		
		usort($reference_directories, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});

		$references_text.='<div class="row-fluid">
							<div class="span12">
								<h4 class="heading">'.$img_directory_name.'</h4>';

		if(count($reference_directories)>0)
		{
			$reference_array=array();
			
			// Creating reference array from each reference directory images
			foreach($reference_directories as $image)
			{
				$image=basename($image);
				$s=array_reverse(explode("-",$image));
				if($s[1])
				{				
					$reference=$s[1];
					$reference_array[$reference]=$img_directory_name;
				}	
			}
			// Creating view pictures link for each references
			foreach($reference_array as $reference=>$value)
			{
				$references_text.='<a target="caroll" href="'.SITE_URL.'/excel-devs/caroll/view-pictures.php?client=CAROLL&reference='.$reference.'"><span class="badge">'.$reference.'</span></a>&nbsp;';
				
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
}
//Caroll fucntions to get referece images

function getCarollReferenceImages($reference=NULL)
{
	$client_image_path=CAROLL_IMAGE_PATH."/";
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
	$loop=0;	
	if(count($ref_directory)>0)
	{
	// Looping through each client reference directory for respective image reference
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			// Get all client image references 
			$files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
				// Creating each client images link
				foreach($files as $file)
				{	
					$string = basename($file);
					$img=$file;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
					$reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery=""><img class="img-polaroid" src="'.$img.'" width=150 height=250/></a>';
				}
			}		
			continue;
		}
	}	
	echo $reference_images;
}

//Caroll fucntions to get referece images

function getGarnierReferenceImages($reference=NULL)
{
	$client_image_path=GARNIER_IMAGE_PATH."/";
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
	$loop=0;	
	if(count($ref_directory)>0)
	{
	// Looping through each client reference directory for respective image reference
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			// Get all client image references 
			$files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
				// Creating each client images link
				foreach($files as $file)
				{	
					$string = basename($file);
					$img=$file;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
					$reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery=""><img class="img-polaroid" src="'.$img.'" width=100% height=100%/></a>';
				}
			}		
			continue;
		}
	}	
	echo $reference_images;
}

//Get LBM References
function getLBMReferences($check_reference=NULL)
{
	$client_image_path=LBM_IMAGE_PATH."/";
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;
		$img_directory_name=basename($img_directory);
		
		// Get all client image references 
		$reference_directories = glob($img_directory."/$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);	
		
		usort($reference_directories, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); 	
			
		$references_text.='<div class="row-fluid"><div class="span12"><h4 class="heading">'.$img_directory_name.'</h4>';
		if(count($reference_directories)>0)
		{
			$reference_array=array();
			
			// Creating reference array from each reference directory images
			foreach($reference_directories as $image)
			{
				$image=basename($image);
				$s=(explode("_",$image));
				if($s[0])
				{				
					//$reference=substr($s[0],0,9);
					$reference=$s[0];
					$reference_array[$reference]=$img_directory_name;
				}	
			}
			ksort($reference_array);
			
			// Creating view pictures link for each references
			foreach($reference_array as $reference=>$value)
			{
				$references_text.='<a target="lebonmarche" href="'.SITE_URL.'/excel-devs/lebonmarche/view-pictures.php?client=LEBONMARCHE&reference='.$reference.'"><span class="badge">'.$reference.'</span></a>&nbsp;';
				
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
}

//LBM fucntions to get referece images
function getLBMReferenceImages($reference=NULL)
{
	$client_image_path=LBM_IMAGE_PATH."/"; // LBM client image directory
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	// All directories list
	$loop=0;	
	if(count($ref_directory)>0)
	{
		// Looping through each client reference directory for respective image reference
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			// Getting all client reference images
			$files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
				// Creating each client images link
				foreach($files as $file)
				{	
					$string = basename($file);
					$img=$file;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
					$reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery="">
								<img class="img-polaroid" src="'.$img.'" width=150 height=250/>
							</a>';
				}
			}		
			continue;
		}
	}	
	echo $reference_images;
}

//function to get all folders  list a LBM to generate Writer files
function getLBMFoldersList()
{
	$client_folder_path=LBM_IMAGE_PATH."/";
	$all_directory=glob($client_folder_path."*");
    //echo '<pre>';   print_r($all_directory);
	if(count($all_directory))
	{
		usort($all_directory, "sorted");
		return $all_directory;
	}
}

// Sorting file/directory by last modification date
    function sorted($a, $b)
    {
        if (filemtime($a) == filemtime($b)) {
            return 0;
        }        
        return (filemtime($a) > filemtime($b)) ? -1 : 1 ;
    }
	
// Sorting file/directory by last modification date in descending order
    function sorteddesc($a, $b)
    {
        if (filemtime($a) == filemtime($b)) {
            return 0;
        }        
        return (filemtime($a) < filemtime($b)) ? -1 : 1 ;
    }

// Function to convert doc file to txt file by executing antiword command
if(!function_exists('o_docToTxt'))
{
    function o_docToTxt($filein, $fileout)
    {
        $doc2txt = "/usr/bin/antiword ";
        $cmd = $doc2txt." ".$filein." > ".$fileout."";
        
        $ret = 0;
        if(file_exists($filein))
        {
            $output = array();
            shell_exec($cmd);
        } 
        else 
        {
          $ret = -1;
        }
        return $ret;
    }
}

if(!function_exists('read_doc_txt')){
	function read_doc_txt($filename){
		//echo "HERE";
		$content = shell_exec('/usr/bin/antiword'.$filename);
		return $content;

		}
	
}

// Function to convert docx file to text file using php zip read
if(!function_exists('o_docxToTxt'))
{
    function o_docxToTxt($path, $outpath)
    {
        if (!file_exists($path))
            return -1;
        $zh = zip_open($path);
        $content = "";
        while (($entry = zip_read($zh))){
            $entry_name = zip_entry_name($entry);
            if (preg_match('/word\/document\.xml/im', $entry_name)){
                $content = zip_entry_read($entry, zip_entry_filesize($entry));
                break;
            }
        }
        $text_content = "";
        if ($content){
            $xml = new XMLReader();
            $xml->XML($content);
            while($xml->read()){
                if ($xml->name == "w:t" && $xml->nodeType == XMLReader::ELEMENT){
                    $text_content .= $xml->readInnerXML();
                    $space = $xml->getAttribute("xml:space");
                    if ($space && $space == "preserve")
                        $text_content .= " ";
                }
                if (($xml->name == "w:p" || $xml->name == "w:br" || $xml->name == "w:cr") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\n";
                if (($xml->name == "w:tab") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\t";
            }
            file_put_contents($outpath, $text_content);
            return 0;
        }
        return -1;
    }
}

//function to get all folders  list a Caroll to generate Writer files
function getCAROLLFoldersList()
{
	$client_folder_path = CAROLL_IMAGE_PATH."/" ;
	$all_directory = glob($client_folder_path."*") ;

	if(count($all_directory))
	{
		/* usort($all_directory, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); */
		usort($all_directory, "sorted");
		return $all_directory;
	}
}


//function to get all folders  list a Caroll to generate Writer files
function getCELIOFoldersList()
{
	$client_folder_path = CELIO_IMAGE_PATH."/" ;
	$all_directory = glob($client_folder_path."*",GLOB_ONLYDIR) ;

	if(count($all_directory))
	{
		/* usort($all_directory, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); */
		usort($all_directory, "sorted");
		return $all_directory;
	}
}
// Function to write xlsx file
if(!function_exists('writeXlsx')){
    function writeXlsx($data,$file_path, $anchor_cols=null)
    {
        $anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");
        
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {                
                $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                //if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                if((strstr($value, "http://clients.edit-place.com/excel-devs/")) || (in_array($col, $anchorCols) && !empty($value) && ($rowCount>0)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
            }
            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;  //echo "<pre>";print_r($data);exit($file_path);
        
        if(file_exists($file_path))
            return true ;
    }
}

// Function to write xlsx file with encoding and modifying œ and single quotes of cell values
if(!function_exists('writeDownXlsx')){
    function writeDownXlsx($data,$file_path, $anchor_cols=null)
    {
        $anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");
        
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {                
                $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                //if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='C')) || (in_array($col, $anchorCols) && !empty($value) && ($rowCount>0)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
            }
            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        if(file_exists($file_path)) {
            chmod($file_path,0777);
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Pragma: private");
            header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/force-download; charset=ISO-8859-1");
            header("Accept-Ranges: bytes");
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
            header("Content-Length: ".filesize($file_path));
            ob_clean();
            flush();
            readfile($file_path);
        }
    }
}

// Function to create xlsx file having multiple sheets using phpExcel plugin
if(!function_exists('writeMultiXlsx')){
    function writeMultiXlsx($datas,$file_path, $sheetnames)
    {
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
            
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");
        
        $sheetCount = 0 ;
        foreach($datas as $idx => $data)
        {
            // Rename sheet
            $sheet_name=$sheetnames[$idx];
            
            $objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
            $objPHPExcel->addSheet($objWorksheet);
            $objWorksheet->setTitle($sheet_name);

            //$objPHPExcel->setActiveSheetIndex($idx);
            $rowCount=0;
            foreach ($data as $row)
            {
                $col = 'A';
                foreach ($row as $key => $value)
                {
                    $value = iconv("ISO-8859-1", "UTF-8", $value) ;
                    $value = str_replace("", "œ", $value) ;
                    $value = str_replace("", "'", $value) ;
                    $value = str_replace("", "'", $value) ;
                    $value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8'));
                    $objWorksheet->setCellValue($col.($rowCount+1), $value);

                    $col++;
                }
                $rowCount++;
            }
            //$objWorksheet->getStyle('1')->getFont()->setBold(true);
            $sheetCount++ ;
        }
        $objPHPExcel->removeSheetByIndex(0);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);

        @chmod($file_path, 0777) ;//echo "<pre>";print_r($data);exit;
        
        if(file_exists($file_path))
            return true ;
    }
}

// Function to write xlsx file using html table and php fwrite function
if(!function_exists('oWriteXLSX')){
    function oWriteXLSX($data,$file_path,$rename=NULL)
    {
        $table = '<table style="width:88%; padding-top:10px;" align="center" valign="center" cellpadding="2" cellspacing="2" class="gridtable"><thead>' ;
        
        $rowCount = 0 ;
        foreach ($data as $row)
        {
            $table .= "<tr>" ;
            foreach ($row as $key => $value)
            {
                $value= (str_replace("�", "'", $value)) ;
                if($rowCount==0)
                {
                    $table .= "<th>".$value."</th>" ;
                    //$sheet->write($rowCount, $key, $value,$format_headers);
                }
                elseif ($value=='#Formula')
                {
                    for ($i = 'A', $j = 1; $j <= 26; $i++, $j++)
                    {
                        if($j == ($key + 1))
                            $scell_val = $i ;
                    }
                    $table .= "<td>"."=LEN(M" . ($rowCount + 1).")"."</td>" ;
                    //$table .= "<td>"."=LEN(".$scell . ($rowCount + 1).")"."</td>" ;
                    //$table .= "<td>".$scell . '--' . ($rowCount + 1)."</td>" ;
                }
                else
                     $table .= "<td>".$value."</td>" ;
            }      
            if($rowCount==0)
                $table .= "</tr></thead><tbody>" ;
            else
                $table .= "</tr>" ;
            $rowCount++;
        }
        $table .= "</tbody></table>" ;
        
        $pathinfo = pathinfo($file_path) ;
        $xlsx_file = $pathinfo['dirname']."/".$pathinfo['filename'].".xlsx" ;
        
        $fh = fopen($xlsx_file, 'w+');
        fwrite($fh, $table);
        fclose($fh);

        if (file_exists($xlsx_file))
            return $xlsx_file ;
    }
}

// Function to write xls file without encoding cell values
if(!function_exists('oWriteXLS')){
    function oWriteXLS($data,$file_path,$rename=NULL)
    {
        // include package
        include_once 'Spreadsheet/Excel/Writer.php';

        $excel = new Spreadsheet_Excel_Writer($file_path);
        $excel->setVersion(8);

        // add worksheet
        $sheet =& $excel->addWorksheet();
        $sheet->setColumn(0,count($data[1]),20);
        
        //custom color
        $excel->setCustomColor(22, 217, 151,149);
        $excel->setCustomColor(12, 252, 213,180);
        
        // create format for header row
        // bold, red with black lower border
            
        $format_a = array('bordercolor' => 'black',
                            'bold'=>'1',
                            'size' => '11',
                            'FgColor'=>'22',
                            'color'=>'black',
                            'align' => 'center',
                            'valign' => 'top');

        $format_headers =& $excel->addFormat($format_a); 
        $format_headers->setBorder(1);
        //$format_headers->setTextWrap();
        
        $wrap_format=& $excel->addFormat();
        $wrap_format->setVAlign('top');
        //$wrap_format->setBorder(1);
        $wrap_format->setFgColor(12);   
        //$wrap_format->setTextWrap();
        $wrap_format->setAlign('left');
        
        // add data to worksheet
        $rowCount=0;
        //echo (count($data[4])-2)."<pre>";print_r($data);echo "</pre>";exit;
        foreach ($data as $row) {
            //$col_cnt=count($row);
          foreach ($row as $key => $value) {
            $value= (str_replace("�", "'", $value)) ;
            if($rowCount==0)
            {
                $sheet->write($rowCount, $key, $value,$format_headers);
            }
            elseif ($value=='#Formula')
            {
                $scell = Spreadsheet_Excel_Writer::rowcolToCell($rowCount,$key-1);
                $sheet->writeFormula($rowCount,$key, "=LEN($scell)", $wrap_format);
            }
            else
                $sheet->write($rowCount, $key, $value,$wrap_format);
          }
          $rowCount++;
        }
        
        // save file to disk
        if ($excel->close() === true)
            return $file_path ;
    }
}

// Function to write xls file without encoding cell values
if(!function_exists('write_xls')){
    function write_xls($data,$file_path,$rename=NULL)
    {
        // include package
        include_once 'Spreadsheet/Excel/Writer.php';

        $excel = new Spreadsheet_Excel_Writer($file_path);
        $excel->setVersion(8);

        // add worksheet
        $sheet =& $excel->addWorksheet();
        $sheet->setInputEncoding('utf-8');
        $sheet->setColumn(0,count($data[1]),20);
        
        //custom color
        $excel->setCustomColor(22, 217, 151,149);
        $excel->setCustomColor(12, 252, 213,180);
        
        // create format for header row
        // bold, red with black lower border
            
        $format_a = array('bordercolor' => 'black',
                        
                'bold'=>'1',
                'size' => '11',
                'FgColor'=>'22',
                'color'=>'black',
                'align' => 'center',
                'valign' => 'top'); 

        $format_headers =& $excel->addFormat($format_a); 
        $format_headers->setBorder(1);
        //$format_headers->setTextWrap();
        
        $wrap_format=& $excel->addFormat();
        $wrap_format->setVAlign('top');
        //$wrap_format->setBorder(1);
        $wrap_format->setFgColor(12);   
        //$wrap_format->setTextWrap();
        $wrap_format->setAlign('left');
        
        // add data to worksheet
        $rowCount=0;
        //echo (count($data[4])-2)."<pre>";print_r($data);echo "</pre>";exit;
        foreach ($data as $row) {
            //$col_cnt=count($row);
            //$value = utf8_decode($value);
          foreach ($row as $key => $value) {
            if($rowCount==0)
            {
                $sheet->write($rowCount, $key, $value,$format_headers);
            }
            else
                $sheet->write($rowCount, $key, $value,$wrap_format);
          }
          $rowCount++;
        }
        
        // save file to disk
        if ($excel->close() === true)
            return $file_path ;
    }
}

// Function to download xls/xlsx files
function odownloadXLS($filename, $path, $rurl)
{
    if(strstr($filename, ".xlsx"))
        odownloadXLSX($filename, $path, $rurl) ;
    
    if(!strstr($filename, ".xls") && !strstr($filename, ".xlsx"))
        $filename = $filename . ".xls" ;
    
    $path_file = $path."/".$filename ;
    if(file_exists($path_file))
    {
        header("Content-type: application/xls");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file"); 
        exit;
    }
    else
        header("Location:".$rurl);
}

// Function to download xls/xlsx files
function odownloadXLSX($filename, $path, $rurl)
{
    if(!strstr($filename, ".xls") && !strstr($filename, ".xlsx"))
        $filename = $filename . ".xlsx" ;
    
    $path_file = $path."/".$filename ;
    if(file_exists($path_file))
    {
        header("Content-type: application/xlsx");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file");
        exit;
    }
    else
        header("Location:".$rurl);
}

// Function to download doc/docx files
function odownloadDoc($filename, $path, $rurl)
{
    if(!strstr($filename, ".doc") && !strstr($filename, ".docx"))
        $filename = $filename . ".doc" ;
    
    $path_file = $path."/".$filename ;
    //echo $path_file;
    if(file_exists($path_file))
    {
        header("Content-type: application/msword");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file"); 
        exit;
    }
    else
        header("Location:".$rurl);
}

// Function to download xml files
function odownloadXml($filename, $path, $rurl)
{
    if(!strstr($filename, ".xml"))
        $filename = $filename . ".xml" ;
    
    $path_file = $path."/".$filename ;//exit($path_file);
    if(file_exists($path_file))
    {
        header("Content-type: text/xml");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file"); 
        exit;
    }
    else
        header("Location:".$rurl);
}

// Function to download zip files
function odownloadZIP($filename, $path, $rurl)
{
    $path_file = $path."/".$filename ;
    if(file_exists($path_file))
    {
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file");
        exit ;
    }
    else
        header("Location:".$rurl);
}

// Function to download csv files
function odownloadCSV($filename, $path, $rurl)
{
    $path_file = $path."/".$filename ;
    if(file_exists($path_file))
    {
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");
        ob_clean();
        flush();
        readfile("$path_file"); 
        exit;
    }   
    else
        header("Location:index.php");
}

// Function to create csv file
function oWriteCSV($data,$filename, $path)
{
    $path_file = $path."/".$filename ;
    $fp = fopen($path_file, 'w+');
    foreach ($data as $fields) {
        //fputcsv($fp, $fields,";",'"');        
        fputcsv($fp,$fields,";",'"');
    }
    rewind($fp);
    $csv = fgetcsv($fp, 9999999, ';');
    fclose($fp);    
    
    // save file to disk
    if (file_exists($path))
        return true;
    else
        return false;
}

// Excel devs database connection
function dbconnect()
{
    $con = mysql_connect("localhost","translationuser","7CLzYsRSWmHsna6j");
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("ExcelDevs", $con);
}

// Function to write xlsx file without encoding and modifying œ and single quotes of cell values
if(!function_exists('swriteXlsx')){
    function swriteXlsx($data,$file_path, $anchor_cols=null)
    {
        $anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set properties
        $objPHPExcel->getProperties()->setCreator("Anoop");
        
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = str_replace("", "'", $value) ;
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                if((strstr($value, "http://clients.edit-place.com/excel-devs/")) || (in_array($col, $anchorCols) && !empty($value) && ($rowCount>0)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
            }
            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }
}

// Function to read xlsx file by modifying single quotes and hyphen characters of cell value
    function sxlsxRead($file)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
        
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
                        $xls_array[$i][$x][$y] = str_replace("´", "’", str_replace('–', '-', $xlsArr1[$i][$x][$y-1])) ;
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname);
    }

// Function to read xls file by modifying single quotes of cell value
    function sxlsRead($file)
    {
        require_once(INCLUDE_PATH."/reader.php");
        
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('Windows-1252') ;
        $data->read($file);
        $bound_sheets=$data->boundsheets;
        $sheets = sizeof($data->sheets);

        for ($i = 0; $i < $sheets; $i++)
        {
            $sheetname[$i]=$bound_sheets[$i]['name'];
            if (sizeof($data->sheets[$i]['cells'])>0)
            {
                $x = 1;
                while ($x <= sizeof($data->sheets[$i]['cells']))
                {
                    $y = 1;
                    while ($y <= $data->sheets[$i]['numCols'])
                    {
                        $data->sheets[$i]['cells'][$x][$y] = str_replace("´", "’", $data->sheets[$i]['cells'][$x][$y]) ;
                        $xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? $data->sheets[$i]['cells'][$x][$y] : '';
                        $y++;
                    }
                    $x++;
                }
            }
        }
        return array($xls_array, $sheetname) ;
    }


	/**
      * Function replace_frCharacters
      * function used to replace French characters on matching 
	  *
      * @package clientsEditPlace
      * @author Vinayak K
      * @param string $str
      * @return string $str
      *
      */
    function replace_frCharacters($str){
		
		return htmlspecialchars ($str);
	}
	
	
	require_once INCLUDE_PATH.'/PHPExcel/IOFactory.php';
    function display_input_xls_xlsx_file($file,$row_count){
	if(file_exists($file)){	
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
		foreach ($cell_collection as $cell) {
			//$column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
			$column = PHPExcel_Cell::columnIndexFromString($objPHPExcel->getActiveSheet()->getCell($cell)->getColumn());
			$row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
			if($row == $row_count)
			  break;
			$data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
			$arr_data[$row][$column] = $data_value;
			
		}   
		return $arr_data;
	}
	}
?>
