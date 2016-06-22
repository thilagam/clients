<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	downloadXLS($_GET['file'], $_GET['ref']);

if(isset($_POST['upload']))
{	
	$modified=array();
	$replace=array();
	$ref = $_REQUEST['index_reference'] ;
	
	$file1	=	pathinfo($_FILES['excel_file']['name']) ;
    $writexls = ($_REQUEST['op']=='xls') ? 'WriteMultiSheetXLS' : 'writeMultiXlsx' ;

	if($file1['extension']=='xls' || $file1['extension']=='xlsx')
	{
		$ext = $file1['extension'];

		if($file1['extension'] == 'xls' || $file1['extension'] == 'xlsx')
        {
			if($file1['extension'] == 'xlsx')
			{
				$xls1Arr  =	xlsxRead($_FILES['excel_file']['tmp_name']) ; //echo '<pre>@@';print_r($xls1Arr); //exit();
                $results  = process($xls1Arr[0], 2, 0, $ref) ;
			}
			else
			{
				$xls1Arr  =	xlsRead($_FILES['excel_file']['tmp_name']) ;
                $results  = process($xls1Arr[0], 2, 0, $ref) ;
                
			}
			
			$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			$filename="Writer_File_".str_replace(' ', '_', $file1['filename'])."_".uniqid().".".$_REQUEST['op'] ;
			$file_path = ANDRE_WRITER_FILE_PATH."/".$refs[$ref-1]."/".$filename;
			//echo $file_path;
			//echo '<pre>@@'; print_r($xls1Arr); exit('*****');
            //echo '<pre>@@'; print_r($results); print_r($xls1Arr[1]); exit('*****'.$file_path);
			
			if($writexls( $results, $file_path, $xls1Arr[1])) {
			  header("Location:index.php?msg=success&file=".$filename.'&ref='.$ref);
			} else {
			  header("Location:index.php?msg=error");
			}
		}
	}
}
else
	header("Location:index.php");



function downloadXLS($filename, $ref)
{
	$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	$path_file=ANDRE_WRITER_FILE_PATH."/".$refs[$ref-1]."/".$filename;
	if(file_exists($path_file))
	{
        $excel  =   pathinfo($path_file) ;
		header("Content-type: application/".$excel['extension']);
		header("Content-Disposition: attachment; filename=$filename");
		ob_clean();
		flush();
		readfile("$path_file") ;
		exit;
	}
	else
		header("Location:index.php");
}
    

    function process($data, $start, $xls, $ref)
    {
		
		//echo '<pre>@@';print_r($data); //exit();
		
        global $global_parents_data,$global_parents_data_file,$global_parents_xls_file;
		$refs =   array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		// Writer file reference directory
		$refdir = ANDRE_WRITER_FILE_PATH."/".$refs[$ref-1]."/";
		//echo $refdir;
		 if (!is_dir($refdir))
        {
            mkdir($refdir);
            chmod($refdir, 0777);
        }
       // exit;
		// Collecting references from writer files
		//getAllParentsFromAllXLS($refs[$ref-1], 1, 5);
		 getAllParentsFromAllExcelL1($refs[$ref-1], 1, $ref+2, ANDRE_CLIENT_URL,ANDRE_WRITER_FILE_PATH);
        
		$products =   array();    //echo '<pre>';//print_r($data);
		//echo"<pre>";print_r($global_parents_xls_file);exit;
	// Updating data array keys
        $sheetcount = 1;
        foreach ($data as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
                $datas[$sheetcount-1][$key][0] = '' ;
                $datas[$sheetcount-1][$key][1] = '' ;
                foreach ( $dataArr as $idx=>$col ) :
                    $datas[$sheetcount-1][$key][$idx+1] = $col ;
                endforeach ;
                $key++;
            endforeach ;
            $sheetcount++;
        }
		
		//echo 'Debug <pre>@@';print_r($datas); exit;
		
		$new_doublon_product_check = array();
		$new_doublon_composition_check = array();
		
        $sheetcount = 1;    //echo '###'.$ref.'###';print_r($datas);exit;
        foreach ($datas as $xlsArr1) {
            $key = $xls ; 
            foreach ( $xlsArr1 as $col ) :
		// processing rows - excluding header
                if($key>$xls)
                {
					//echo $xlsArr1[$key][4]." | ";
					  //exit;
									
					$col_ref = trim($col[$ref+1]); //echo '<br>###'.$col_ref.'###<br>';
                    $pattern = ANDRE_IMAGE_PATH.'/*/'.$col_ref.'_*.*';
					$arraySource = glob($pattern,GLOB_BRACE);
					sort($arraySource);

                    

                   if($key > 3){ // remove headers 
					   echo $key;  

					if(count($arraySource)>0)
					{
						
						
						$path = pathinfo($arraySource[0]) ;
						
						$url_path="http://clients.edit-place.com/excel-devs/andre/view-pictures.php?client=ANDRE&reference=";
						
						if(!in_array($col_ref, $global_parents_data))
						{
							$datas[$sheetcount-1][$key][0] = $url_path.$col_ref ;
						}
						elseif(in_array($col_ref, $global_parents_data) && empty($datas[$sheetcount-1][$key][1]) == 1)
						{
							$datas[$sheetcount-1][$key][0] = $url_path.$col_ref;
							$file_L = pathinfo($global_parents_xls_file[$col_ref],PATHINFO_BASENAME);
							$datas[$sheetcount-1][$key][1] = 'Doublon ('.$file_L.')' ;
						}
						
						
						if(sizeof($new_doublon_product_check) > 0){
						     					    
						   if($position = array_search($xlsArr1[$key][4], $new_doublon_product_check)){
							  //echo $position."-".$xlsArr1[$key][4];
							  $datas[$sheetcount-1][$key][0] = $url_path.$col_ref;
							  $line = intval($position) - 2;
						      $datas[$sheetcount-1][$key][1] = (strcmp(trim($xlsArr1[$key][4]),trim($new_doublon_composition_check[$position]))) ? 'Doublon with Same Product name & Composition, Line No:=>'.$line : '' ;	
						     //echo "<br />";exit;
						    }  						
						}
						
					$new_doublon_product_check[$key] = $xlsArr1[$key][4];
					$new_doublon_composition_check[$key] = $xlsArr1[$key][9];
					
					//echo "<pre>"; print_r ($new_doublon_product_check);
					//echo "<pre>"; print_r ($new_doublon_composition_check);	   
						
					}
                    else {
                        //unset($datas[$sheetcount-1][$key]);
                        $datas[$sheetcount-1][$key][0] = "NA";
                        $datas[$sheetcount-1][$key][1] = " ";
                        
                        $new_doublon_product_check[$key] = $xlsArr1[$key][4];
					    $new_doublon_composition_check[$key] = $xlsArr1[$key][9];
                    }
                  }
                  else{
					 unset($datas[$sheetcount-1][$key]); 
				  }	   
                }
                $key++;
            endforeach ;
            $sheetcount++;
        }
        
        //echo "<pre>"; print_r ($new_doublon_product_check); 
        //echo "<pre>"; print_r ($new_doublon_composition_check); 
        //echo "Debug 1<pre>"; print_r ($datas); 
        //exit;		
        
        $sheetcount = 1;
        foreach ($datas as $data_) {
            $key = $xls ;
            foreach ( $data_ as $dataArr ) :
				$results[$sheetcount-1][$key] = $dataArr ;
		// Update writer file header
                if(($key == $xls) && !empty($results[$sheetcount-1][$key+1][1]))
                {
                    $results[$sheetcount-1][$key][0]='URL';
                    $results[$sheetcount-1][$key][1]='Description';
                    $results[$sheetcount-1][$key][2]='Nb chars.';
                    $results[$sheetcount-1][$key][3]='Patronage';
                    $results[$sheetcount-1][$key][4]='Référence andré';
                    $results[$sheetcount-1][$key][5]='Nom du produit';
                    $results[$sheetcount-1][$key][6]='Composant semelle';
                    $results[$sheetcount-1][$key][7]='Composant doublure';
                    $results[$sheetcount-1][$key][8]='Composant dessus';
                    $results[$sheetcount-1][$key][9]='Composant propreté';
                }
                elseif(!empty($results[$sheetcount-1][$key][1]))
                {
                    //$results[$sheetcount-1][$key][2]='#Formula';
                    $results[$sheetcount-1][$key][2]='';
                }
				$key++;
            endforeach ;
            $sheetcount++;
        }   //print_r($results);exit;
        return $results ;
    }

    

    /**getting all final xls files**/
    function getAllParentsFromAllXLS($ref, $urlid, $refid)
    {
        global $global_parents_data,$global_parents_data_file;
        $arraySource = glob(ANDRE_WRITER_FILE_PATH.'/'.$ref.'/*.xls');		
        //sort($arraySource);
		//print_r($arraySource);exit;
        
        if(count($arraySource)>0)
		{
			usort($arraySource, function($a, $b) {
				return filemtime($a) > filemtime($b);
			});
			//echo "<pre>";  print_r($arraySource);
			
			foreach($arraySource as $excel)
			{
				$file=$excel;
				$basename=basename($file);
				getExceldata($file, $urlid, $refid); 
			}
		}	
        //echo "<pre>";print_r($global_parents_data); print_r($global_parents_data_file);exit;
       
    }
    
    /**getting all parent from final xls file**/
    function getExceldata($file, $urlid, $refid)
    {
        global $global_parents_data,$global_parents_data_file;
        require_once(INCLUDE_PATH."/reader.php");
        $data1 = new Spreadsheet_Excel_Reader();
        $data1->setOutputEncoding('UTF8');
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
                        //echo strstr($data1->sheets[$i]['cells'][$x][$urlid], 'ANDRE')."<br>";
						if(strstr($data1->sheets[$i]['cells'][$x][$urlid], 'ANDRE') && (!$global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]))
                        {
                            $global_parents_data[]  =   $data1->sheets[$i]['cells'][$x][$refid];
                            $global_parents_data_file[$data1->sheets[$i]['cells'][$x][$refid]]   = basename($file);//  $data1->sheets[$i]['cells'][$x][$urlid];
                        }
                    }
                    $x++;
                }
            }
        }
        //print_r($global_parents_data_file);exit('$$$');
        //return array($global_parents_data, $global_parents_data_file);
    }

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
                    
                    if(strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A'))
                    {
                        $objWorksheet->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                        $objWorksheet->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                    }
                    $col++;
                }
                $rowCount++;
            }
            //$objWorksheet->getStyle('1')->getFont()->setBold(true);
            $sheetCount++ ;
        }
        $objPHPExcel->removeSheetByIndex(0);
        
        //echo $file_path;
        
        //echo "<pre>";print_r($data);exit;
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);

        @chmod($file_path, 0777) ;
        
        if(file_exists($file_path))
            return true ;
    }

    function getAllParentsFromAllExcel($ref, $urlid, $refid, $client_url, $file_path)
    {
        // Array variables to store all previous reference data from previous writer files 
		global $global_parents_data,$global_parents_data_file,$global_parents_xls_file ;
		
        $refs = glob($file_path . '/*' , GLOB_ONLYDIR);	// All directories list
        //echo $refid;
       // print_r($refs);exit;
        foreach ($refs as $refdir)
        {
			$dirName=basename($refdir);
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xls', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
           print_r($arraySource);exit;
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

    function get_character_number($end) 
{
    $count = 1;
    $char = 'A';
    $end = strtoupper($end);
    while ($char !== $end) {
        $count++;
        $char++;
    }
    return $count;
}



function getAllParentsFromAllExcelL1($ref, $urlid, $refid, $client_url, $file_path)
    {
        // Array variables to store all previous reference data from previous writer files 
		global $global_parents_data,$global_parents_data_file,$global_parents_xls_file ;
		
        $refs = glob($file_path . '/*' , GLOB_ONLYDIR);	// All directories list
        
        
        //print_r($refs);exit;
        foreach ($refs as $refdir)
        {
			//echo $refdir;
			$dirName=basename($refdir);
			// Getting all client references from writer file
            $arraySource = glob($refdir . '/*.xls', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
            //echo "<pre>"; print_r($arraySource); echo "</pre>";
            if(!empty($arraySource)){
				foreach($arraySource as $excel)
				{
					$file=$excel;
					$basename=basename($file);
					//exit;
					// Get reference from xls writer file data
					$new_col_ref = intval((intval(ord($dirName))-96)+2);
					//echo $refid."-".$ref."-".$new_col_ref."-".$urlid."-".$client_url."<br />";
					
					getExcelDatas($file, $urlid, $new_col_ref, $client_url);
				}
			}
        }
        
        foreach ($refs as $refdir)
        {
			// Getting all client references from writer file
			$dirName=basename($refdir);
            $arraySource = glob($refdir . '/*.xlsx', GLOB_BRACE); //exit($file_path.'/'.$ref.'/*.xls');
            sort($arraySource) ;
            usort($arraySource, "sorted") ;
            //echo '<pre>';print_r($arraySource);echo '</pre>';
            
            foreach($arraySource as $excel)
            {
                $file=$excel;
                $basename=basename($file);
				
				// Get reference from xlsx writer file data
				$new_col_ref = intval((intval(ord($dirName))-96)+2);
				//echo $refid."-".$ref."-".$new_col_ref."-".$urlid."-".$client_url."<br />";
                getXlsxDatas($file, $urlid, $new_col_ref, $client_url);
            }
        }//echo '<pre>@@';print_r($global_parents_data_file); exit('*****'.$client_url);
        //exit;
    }

?>
