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
		$zip = new ZipArchive;
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

function csvRead($csv_path,$csv_index=NULL)
{
	
	if(file_exists($csv_path))
	{
		$arrayDesc =  file($csv_path);
		//$arrayDesc=explode("\n",$arrayDesc);
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
    function oxlsRead($file)
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
    function oxlsxRead($file)
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
                        //$xls_array[$i][$x][$y] =  convert_smart_quotes(html_entity_decode($xlsArr1[$i][$x][$y-1], ENT_QUOTES, "Windows-1252"));
                        $xls_array[$i][$x][$y] = convert_smart_quotes($xlsArr1[$i][$x][$y-1]) ;
                        
                        /*if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                        else*/
                        $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
                        
                        if(strlen($xls_array[$i][$x][$y])>strlen(utf8_decode($xls_array[$i][$x][$y])))
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        else
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? utf8_encode($xls_array[$i][$x][$y]) : '';
                        $xls_array[$i][$x][$y] = utf8_decode($xls_array[$i][$x][$y]) ;
                        
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
//Get Andre References
function getAndreReferences($check_reference=NULL)
{
    $client_image_path=ANDRE_IMAGE_PATH."/";
    
    $refs=glob($client_image_path."*", GLOB_ONLYDIR);
    
    $loop=0;    
    foreach($refs as $index=>$folder)
    {
        $img_directory = $folder;           
        $img_directory_name=basename($img_directory);
        
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
    
    $ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);  
    $loop=0;    
    if(count($ref_directory)>0)
    {
        foreach($ref_directory as $index=>$folder)
        {
            $img_directory = $folder;                       
            
            $files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);        
            
            
            if(count($files)>0) 
            {       
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
    $refs = glob($client_image_path . "/*", GLOB_ONLYDIR);
    $loop = 0;
    foreach ($refs as $index => $folder) {
        $img_directory = $folder;
        $img_directory_name = basename($img_directory);
        $reference_directories = glob($img_directory . "/$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
        usort($reference_directories, function($a, $b) {
            return filemtime($a) < filemtime($b);
        });
        $references_text .= '<div class="row-fluid"><div class="span12"><h4 class="heading">' . $img_directory_name . '</h4>';

        if (count($reference_directories) > 0)
        {
            $reference_array = array();
            foreach ($reference_directories as $image) {
                $image = basename($image);
                $s = explode("-", $image);
                if ($s[0]) {
                    $reference = $s[0];
                    $reference_array[$reference] = $img_directory_name;
                }
            }
            ksort($reference_array);
            foreach ($reference_array as $reference => $value)
            {
                $references_text .= '<a target="nafnaf" href="' . $url . '/view-pictures.php?client=' . $client . '&reference=' . $reference . '"><span class="badge">' . $reference . '</span></a>&nbsp;';
            }
        }
        else
            $references_text .= '<span class="label label-important">No References Found</span>';

        $references_text .= '</div></div>';
    }
    echo $references_text;
}

function getClientReferenceImages($reference = NULL, $client_image_path)
{
    $ref_directory = glob($client_image_path . "/*", GLOB_ONLYDIR);
    $loop = 0;
    if (count($ref_directory) > 0) {
        foreach ($ref_directory as $index => $folder) {
            $img_directory = $folder;
            $files = glob($img_directory . "/*" . $reference . "{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
            if (count($files) > 0) {
                foreach ($files as $file) {
                    $string = basename($file);
                    $img = $file;
                    $img = str_replace($_SERVER['DOCUMENT_ROOT'], "", $img);
                    $reference_images .= '<a href="http://clients.edit-place.com' . $img . '" data-gallery=""><img class="img-polaroid" src="' . $img . '" width=150 height=250/></a>';
                }
            }
            continue;
        }
    }
    echo $reference_images;
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
            if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
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
        
        $replace[] = '"';
        $replace[] = '"';
        $replace[] = "'";
        $replace[] = "'";
        $replace[] = '...';
        $replace[] = '-';
        $replace[] = '-';
        $replace[] = '...';
        $replace[] = "'";
        
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
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        $arraySource = glob($file_path.'/'.$ref.'/*.xls', GLOB_BRACE);
        sort($arraySource);
        
        usort($arraySource, function($a, $b) {
            return filemtime($a) > filemtime($b);
        });
        
        foreach($arraySource as $excel)
        {
            $file=$excel;
            $basename=basename($file);
            getExcelDatas($file, $urlid, $refid, $client_url);
        }
    }
}

if(!function_exists('getExcelDatas'))
{
    function getExcelDatas($file, $urlid, $refid, $client_url)
    {
        require_once INCLUDE_PATH . '/reader.php' ;
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
        
        $data1 = new Spreadsheet_Excel_Reader();
        $data1->setOutputEncoding('Windows-1252');
        $data1->read($file);
        
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

                        if(strstr($data1->sheets[$i]['cells'][$x][$urlid], $client_url) && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]))
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
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR); 		
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;			
		$img_directory_name=basename($img_directory);
		
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
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	
	$loop=0;	
	if(count($ref_directory)>0)
	{
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			$files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
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


//Get LBM References
function getLBMReferences($check_reference=NULL)
{
	$client_image_path=LBM_IMAGE_PATH."/";
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR);
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;
		$img_directory_name=basename($img_directory);
		
		$reference_directories = glob($img_directory."/$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);	
		
		usort($reference_directories, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); 	
			
		$references_text.='<div class="row-fluid"><div class="span12"><h4 class="heading">'.$img_directory_name.'</h4>';
		if(count($reference_directories)>0)
		{
			$reference_array=array();
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
	$client_image_path=LBM_IMAGE_PATH."/";
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);	
	$loop=0;	
	if(count($ref_directory)>0)
	{
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			$files = glob($img_directory."/*".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
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
	if(count($all_directory))
	{
		usort($all_directory, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});
		
		return $all_directory;
	}
	

}

//function to get all folders  list a Caroll to generate Writer files
function getCAROLLFoldersList()
{
	$client_folder_path=CAROLL_IMAGE_PATH."/";	
	$all_directory=glob($client_folder_path."*");
	if(count($all_directory))
	{
		usort($all_directory, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});
		
		return $all_directory;
	}
}

if(!function_exists('oWriteXLS')){
    function oWriteXLS($data,$file_path,$rename=NULL)
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
        foreach ($data as $row) {
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

function odownloadXLS($filename, $path, $rurl)
{
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

function odownloadDoc($filename, $path, $rurl)
{
    $path_file = $path."/".$filename ;
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

function dbconnect()
{
    $con = mysql_connect("localhost","root","DJafwaFqXRmqCR4U");
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("ExcelDevs", $con);
}
?>