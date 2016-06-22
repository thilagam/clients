<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once (INCLUDE_PATH . "/common_functions.php");
require_once (INCLUDE_PATH . "/reader.php");

$folder_id = $_REQUEST['folder_id'] ;

/*checking Ep source updated or not**/
$ep_src_ref_file = CAROLL_EP_CONFIG_FILE;

if (!file_exists($ep_src_ref_file)) {
    $fp = fopen($ep_src_ref_file, "w");
    fclose($fp);
}
$arr_ep_ref_soc = unserialize(file_get_contents($ep_src_ref_file));
$ep_source_updated = $arr_ep_ref_soc['updated'];

/*checking Caroll source updated or not**/
$caroll_src_ref_file = CAROLL_REF_CONFIG_FILE;

if (!file_exists($caroll_src_ref_file)) {
    $fp = fopen($caroll_src_ref_file, "w");
    fclose($fp);
}
$arr_caroll_ref_soc = unserialize(file_get_contents($caroll_src_ref_file));
$caroll_source_updated = $arr_caroll_ref_soc['updated'];

//echo $ep_source_updated."--".$caroll_source_updated;exit;

if ($ep_source_updated == 'yes' || $caroll_source_updated == 'yes' || !empty($folder_id) || 1!=2) {
    
    global $carollRefDataFile, $global_parents_data, $global_parents_data_file, $old_reference_ids;
    
    /* Begin updating references from writer files to config */
    getAllCarollRefs() ;
    $fp = fopen(CAROLL_WRITER_FILE_CONFIG_PATH."/refs2.txt","w");
    fwrite($fp,serialize($carollRefDataFile));
    fclose($fp);
    chmod(CAROLL_WRITER_FILE_CONFIG_PATH."/refs2.txt", 0777);
    /* End update references from writer files to config */
    
    $xls3reference=5;
    
    if(!empty($folder_id))
        $folders = array('/home/sites/site2/web/CLIENTS/CAROLL/'.$folder_id) ;
    else
        $folders = glob(CAROLL_IMAGE_PATH . "/*") ;
    //echo "<pre>";print_r($folders);exit;
    
    $array_allImages=getAllImages() ;
    
    /** EP source data **/
    
    if (!file_exists(CAROLL_EP_CONFIG_FILE)) {
        $fp = fopen(CAROLL_EP_CONFIG_FILE, "w");
        fclose($fp);
    }
    $arr_ref_soc_ep = unserialize(file_get_contents(CAROLL_EP_CONFIG_FILE));
    $reference_ep = $arr_ref_soc_ep["CAROLL.xls"];
    $master_ep_xls = CAROLL_EP_SOURCE_PATH . "/CAROLL.xls"; 
    $ep_source_xls = getEpSource($master_ep_xls, $reference_ep);
    $ep_source_keys = array_keys(array_filter($ep_source_xls));
    //echo "<pre>--".$master_ep_xls;	//print_r($ep_source_xls);exit($master_ep_xls);
    
    /** EP source data **/
    
    /** CAROLL source data **/
    
    if (!file_exists(CAROLL_REF_CONFIG_FILE)) {
        $fp = fopen(CAROLL_REF_CONFIG_FILE, "w");
        fclose($fp);
    }
    $arr_ref_soc = unserialize(file_get_contents(CAROLL_REF_CONFIG_FILE));
    $reference = $arr_ref_soc['CAROLL_SOURCE.xls'];
    $master_caroll_xls = CAROLL_REF_SOURCE_PATH . "/CAROLL_SOURCE.xls";
    $url = CAROLL_URL . "/view-pictures.php?client=CAROLL&reference=";  
    $caroll_source_xls = getCarollSource($master_caroll_xls, $ep_source_xls, $reference, $array_all_images_list, $url);
    //echo "<pre>caroll source--";print_r($caroll_source_xls);exit($folder) ;
    
    /** CAROLL source data **/
    
    $fcount = count($folders);
    if ($fcount > 0) {
        foreach ($folders as $dir) {

            /***********START******************/
            if (is_dir($dir) AND !strpos($dir, '.zip') AND basename($dir) != "caroll-xml-log" AND basename($dir) != "caroll-xml")
            {
                $folderid = basename($dir);
                $folder = basename(CAROLL_IMAGE_PATH . "/" . $folderid);
                
                $exl = file_exists(CAROLL_WRITER_FILE_PATH2 . $filename . ".xls") ? ".xls" : ".xlsx";
                $exl3 = file_exists(CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_3_" . $folder . ".xls") ? ".xls" : ".xlsx";
                
                $filename = "Writer_Final_" . $folder;
                $path_file = CAROLL_WRITER_FILE_PATH2 . "/" . $filename . $exl;
                $path_new_file = CAROLL_WRITER_FILE_PATH2 . "/" . $filename . ".xlsx";
                $new_name = CAROLL_WRITER_FILE_PATH2 . "/" . $filename . "_" . date("YmdHis") . $exl;
                $xls3_path = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_3_" . $folder . $exl3;
                $xls3_new_path = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_3_" . $folder . ".xlsx";

                if(file_exists($path_file))
                 rename($path_file,$new_name);
                 
				if(!$_REQUEST['folder_id'])
				{
					echo "<br><br>Creating the file .....$filename.xls\n<br>";
				}
                $array_all_images_list = array();
                $img_stack = array();
                foreach($array_allImages as $fldr=>$rfs)
                {
                    if($fldr != $folderid AND basename($dir) != "caroll-xml-log" AND basename($dir) != "caroll-xml")
                    {
                        foreach ($rfs as $rfs_)
                            array_push($array_all_images_list, $rfs_) ;
                    }
                    else
                        $img_stack = $rfs ;
                }
                $array_all_images_list = array_values(array_unique(array_filter($array_all_images_list)));
                
/*echo "<pre>array_all_images_list--";print_r($array_all_images_list); echo "<pre>img_stack--";print_r($img_stack);exit(sizeof($array_all_images_list).'='.sizeof($img_stack));*/

                $global_parents_data_file = getCarollPrevReferences($folderid);
                $global_parents_data = array_values(array_keys($global_parents_data_file));

                /**** START WRITE XLS INCLUDE ****/      
                
                // Getting matches                
                foreach ($img_stack as $img_reference) {
                    $flag = 0;
                    $dbn_index = 0;
                    $j = 0;
                    foreach ($caroll_source_xls as $index => $sarray) {
                        if ($j == 0) {
                            unset($ep_source_xls[1][10]);
                            $xls_array[0] = array_merge($caroll_source_xls[1], $ep_source_xls[1]);
                        }
                        if ($j > 0) {
                            if (preg_match("/$index/", $img_reference) && in_array($index, $ep_source_keys))
                            {
                                if (!$dbn_index)
                                    $dbn_index = $index;
                                if (!$xls_array[$index]) {
                                    unset($ep_source_xls[$index][10]);
                                    $xls_array[$index] = array_merge($caroll_source_xls[$index], $ep_source_xls[$index]);
                                }
                                if (in_array($index, $global_parents_data) && $global_parents_data_file[$index])
                                    $xls_array[$index][1] = 'DOUBLON (' . $global_parents_data_file[$index] . ')';
                                else
                                    $xls_array[$index][1] = '';
                            }
                        }
                        $j++;
                    }
                } 
/*echo "<pre>caroll_source_xls--";print_r($caroll_source_xls);echo "ep_source_xls###";print_r($ep_source_xls);echo "###img_stack";print_r($img_stack);exit('='.sizeof($img_stack));*/
//echo "<pre>"; print_r($xls_array);echo '###';print_r($caroll_source_xls);exit($url);

                if (count($xls_array) > 0) {
                    //generating XLSX with matched array
                    swriteXlsx($xls_array, $folder, $new_name);
                }

                /**** END WRITE XLS INCLUDE ****/

                if (file_exists($new_name)) {
					                    
                    /********** BEGIN WRITE XLS3 INCLUDE *****/                                        
                    $old_reference_ids = array(); 
                    getAllCarollMissingRefs($folderid);
                    $old_reference_ids=array_unique($old_reference_ids);

                    $final_xls=array();                    
                    $fileInfo  =   pathinfo($path_new_file) ;
        
					if($fileInfo['extension']=='xls')
					{
						$data1 = new Spreadsheet_Excel_Reader();
						$data1->setOutputEncoding('Windows-1252');//exit($new_name.' bb '.$path_new_file);
						$data1->read($path_new_file);
						$sheets=sizeof($data1->sheets);
						
						for ($i = 0; $i < $sheets; $i++)
						{
							if (sizeof($data1->sheets[$i]['cells'])>0)
							{
								$x = 1;
								while ($x <= sizeof($data1->sheets[$i]['cells']))
								{
									$y = 1;
									while ($y <= $data1->sheets[$i]['numCols'])
									{
										$caroll_source_xls[$x][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y]) ? $data1->sheets[$i]['cells'][$x][$y] : '' ;
										$y++;
									}
									$final_xls[1]=$caroll_source_xls[1];
									if($x>1)
									{
										$reference_id=$caroll_source_xls[$x][$xls3reference];
										if(!in_array($reference_id,$old_reference_ids))
										{
											$final_xls[$x]=$caroll_source_xls[$x];
										}
									}
									$x++;
								}
							}
						}
					}
					else
					{
						require_once (INCLUDE_PATH."/PHPExcel.php");

						$objReader = PHPExcel_IOFactory::createReader('Excel2007');
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($path_new_file);
						$sheetname = $objPHPExcel->getSheetNames();
						foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
							$xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
						}	//echo "<pre>";
						
						for ($i = 0; $i < sizeof($xlsArr1); $i++) {
							if (sizeof($xlsArr1[$i])>0) {
								$x = 0;
								while ($x < sizeof($xlsArr1[$i])) {
									$y = 0;//print_r($xlsArr1[$i][$x]);
									while ($y < sizeof($xlsArr1[$i][$x])) {
										$caroll_source_xls[$x+1][$y]=isset($xlsArr1[$i][$x][$y]) ? $xlsArr1[$i][$x][$y]:'';
										$y++;
									}
									$final_xls[1]=$caroll_source_xls[1];
									if($x>0)
									{
										$reference_id=$caroll_source_xls[$x][$xls3reference];
										if(!in_array($reference_id,$old_reference_ids))
										{
											$final_xls[$x]=$caroll_source_xls[$x];
										}
									}
									$x++;
								}
							}
						}
					}
                    $final_xls=array_values($final_xls);
                    //echo $folderid."<br><br><br><pre>_3"; print_r($final_xls);echo "</pre>";//exit; 

                    if(count($final_xls)>1)
                    {
						if (file_exists($xls3_path))
						{
							$new_name1 = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_3_" . $folder . "_" . date("YmdHis") . $exl3;
							rename($xls3_path, $new_name1);
						}
						if(!$_REQUEST['folder_id'])
						{
							echo "Creating the file Writer_Final_3_".$folderid.".xlsx\n";
						}
                        swriteXlsx3($final_xls,$folderid);
                    }
                    /********** END WRITE XLS3 INCLUDE *****/
                }
                /*else
                {
                    echo $folderid."<br><br><br>Not_exist_3 -->".$new_name; print_r($final_xls);echo "</pre>";exit;
                }*/
            }

            /***********END******************/
            unset($img_stack);unset($ref_brand);unset($xls_array);unset($final_xls);
            unset($array_all_images_list);
            unset($global_parents_data);
            unset($global_parents_data_file);
            unset($old_reference_ids);
        }
    }
    $wrfilename = "Writer_Final_".$_REQUEST['folder_id'].".xlsx";
    $wrfile = CAROLL_WRITER_FILE_PATH2."/".$wrfilename;

    if(!empty($_REQUEST['folder_id']) && file_exists($wrfile))
    {
		ob_start();
		chmod($wrfile,0777);
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: private");
		header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-excel;");
		header("Accept-Ranges: bytes");
		header('Content-Disposition: attachment; filename="'.$wrfilename.'"');
		header("Content-Length: ".filesize($wrfile));
		readfile($wrfile);
		exit;
    }
    //exit ;
    //Ep ref  file updation
    /*$arr_ep_ref_soc['updated'] = 'no';
    $fp = fopen($ep_src_ref_file, "w");
    fwrite($fp, serialize($arr_ep_ref_soc));
    fclose($fp);

    //Caroll ref  file updation
    $arr_caroll_ref_soc['updated'] = 'no';
    $fp = fopen($caroll_src_ref_file, "w");
    fwrite($fp, serialize($arr_caroll_ref_soc));
    fclose($fp);*/
} else
    echo "No New Source Found";
    

function getCarollSource($master_caroll_xls, $ep_source_xls, $reference, $array_all_images_list, $url )
{
    require_once (INCLUDE_PATH . "/reader.php");
    $data2 = new Spreadsheet_Excel_Reader();
    $data2->setOutputEncoding('Windows-1252');
    $data2->read($master_caroll_xls);
    $sheets = sizeof($data2->sheets);

    for ($i = 0; $i < $sheets; $i++) {
        if ($data2->sheets[$i]['numRows']) {
            $x = 1;
            $z = 1;
            while ($x <= $data2->sheets[$i]['numRows']) {
                $y = 1;
                while ($y <= $data2->sheets[$i]['numCols']) {
                    if ($data2->sheets[$i]['cells'][$x][$reference] != '') {
                        $ref_brand = trim(str_replace("?", "", $data2->sheets[$i]['cells'][$x][$reference]));
                        $ref_brand = trim(str_replace(" ", "", $ref_brand));
                        $ep_sku[] = $ref_brand;

                        if ($x == 1 && $y == 1)
                            $caroll_source_xls[$x][$y - 1] = 'url';
                        else if ($x == 1 && $y > 1) {
                            if ($y == 2) {
                                $caroll_source_xls[$x][$y - 1] = 'DOUBLON';
                                //setting column C as Column O of Source file
                                $caroll_source_xls[$x][$y - 1 + 1] = $ep_source_xls[$x][10];
                                $caroll_source_xls[$x][$y - 1 + 2] = 'Desc long';
                                $caroll_source_xls[$x][$y - 1 + 3] = 'Min. signs';
                                $caroll_source_xls[$x][$y - 1 + 4] = isset($data2->sheets[$i]['cells'][$x][$y]) ? $data2->sheets[$i]['cells'][$x][$y] : '';
                            } else
                                $caroll_source_xls[$x][$y - 1 + 4] = isset($data2->sheets[$i]['cells'][$x][$y]) ? $data2->sheets[$i]['cells'][$x][$y] : '';
                        } else if ($x > 1 && $y == 1)
                            $caroll_source_xls[$ref_brand][$y - 1] = $url . $ref_brand;
                        else {
                            if ($y == 2) {
                                $reference_ele = $data2->sheets[$i]['cells'][$x][$reference];
                                if (preg_grep("/$reference_ele/", $array_all_images_list))
                                    $caroll_source_xls[$ref_brand][$y - 1] = 'DOUBLON';
                                else
                                    $caroll_source_xls[$ref_brand][$y - 1] = '';

                                //setting column C as Column O of Source file
                                $caroll_source_xls[$ref_brand][$y-1+1]=isset($ep_source_xls[$ref_brand][10])?trim(str_replace("?","",$ep_source_xls[$ref_brand][10])):'';
                                $caroll_source_xls[$ref_brand][$y - 1 + 2] = '';
                                $caroll_source_xls[$ref_brand][$y - 1 + 3] = '#Formula';
                                $caroll_source_xls[$ref_brand][$y - 1 + 4] = isset($data2->sheets[$i]['cells'][$x][$y]) ? trim(str_replace("?", "", $data2->sheets[$i]['cells'][$x][$y])) : '';
                            } else {
                                if (!$caroll_source_xls[$ref_brand][$y - 1 + 4]) {
                                    $caroll_source_xls[$ref_brand][$y - 1 + 4] = isset($data2->sheets[$i]['cells'][$x][$y]) ? $data2->sheets[$i]['cells'][$x][$y] : '';
                                    $caroll_source_xls[$ref_brand][$y - 1 + 4] = str_replace("\n", "", $caroll_source_xls[$ref_brand][$y - 1 + 4]);
                                    $caroll_source_xls[$ref_brand][$y - 1 + 4] = str_replace("\r\n", "", $caroll_source_xls[$ref_brand][$y - 1 + 4]);
                                    $caroll_source_xls[$ref_brand][$y - 1 + 4] = str_replace("<br>", "", $caroll_source_xls[$ref_brand][$y - 1 + 4]);
                                }
                            }
                        }
                    }
                    $y++;
                }
                $x++;
            }
        }
    }//echo "<pre>"; print_r($caroll_source_xls);exit;
    return $caroll_source_xls;
}


function getEpSource($master_ep_xls, $reference_ep, $ref_brand )
{
    require_once (INCLUDE_PATH . "/reader.php");
    $ref_brand = '';
    
    $data1 = new Spreadsheet_Excel_Reader();
    $data1->setOutputEncoding('Windows-1252');
    $data1->read($master_ep_xls);

    $sheets = sizeof($data1->sheets);
    $removeColumns = array(1, 2, 3, 5);
    for ($i = 0; $i < $sheets; $i++) {
        if ($data1->sheets[$i]['numRows']) {
            $x = 1;
            $z = 0;
            $parent_cell = '';

            while ($x <= $data1->sheets[$i]['numRows']) {
                $y = 1;
                while ($y <= $data1->sheets[$i]['numCols']) {
                    if ($x == 1) {
                        if (!in_array($y, $removeColumns))
                            $ep_source_xls[$x][$y - 1] = isset($data1->sheets[$i]['cells'][$x][$y]) ? $data1->sheets[$i]['cells'][$x][$y] : '';
                    } else {
                        $ref_brand = trim(str_replace("?", "", $data1->sheets[$i]['cells'][$x][$reference_ep]));
                        $ref_brand = trim(str_replace(" ", "", $ref_brand));

                        if ($ref_brand && !in_array($y, $removeColumns)) {
                            if (!$ep_source_xls[$ref_brand][$y - 1])
                                $ep_source_xls[$ref_brand][$y - 1] = isset($data1->sheets[$i]['cells'][$x][$y]) ? $data1->sheets[$i]['cells'][$x][$y] : '';
                        }
                    }
                    $y++;
                }
                $ep_source_xls[1] = array_values($ep_source_xls[1]);
                //$ep_source_xls[$ref_brand]=array_values($ep_source_xls[$ref_brand]);
                $x++;
            }
        }
    }

    if (count($ep_source_xls) > 0)
        foreach ($ep_source_xls as $reference => $source)
            $ep_source_xls[$reference] = array_values($source);

    //echo "<pre>"; print_r($ep_source_xls);echo "</pre>";exit;
    
    return $ep_source_xls ;
}


//get all folder images**/
function getAllImages() {
    $array_list = array();
    $path = CAROLL_IMAGE_PATH . "/*";
    $array = glob($path);
    if (count($array) > 0) {
        foreach ($array as $a) {
            $array1 = glob($a . "/*");
            $dir_name = basename($a);
            if (is_dir($a)) {
                foreach ($array1 as $file) {
                    $string = basename($file);
                    $s = array_reverse(explode("-", $string));
                    //echo $string;
                    if ($s[1])
                        $array_list[$dir_name][] = $s[1];
                }
            }
            $array_list[$dir_name] = array_values(array_unique($array_list[$dir_name]));
        }
    }//echo "<pre>"; print_r($array_list);echo "</pre>";exit;
    return $array_list;
}

/**Get images of a given folder**/
function getCarollPrevReferences($folder_id) {
    $references = array();

    foreach (unserialize(file_get_contents(CAROLL_WRITER_FILE_CONFIG_PATH."/refs2.txt")) as $k1 => $v1) {
        if (!strstr($k1, $folder_id)) {
            foreach ($v1 as $k2 => $v2)
                $references[$v2] = $k1;
        }
    }
    return $references;
}

	function swriteXlsx($data, $id, $new_name)
    {
        $file_path = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_" . $id . ".xlsx";
        
		//Renaming existing file
		if (file_exists($file_path)) {
			//$new_name1 = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_" . $id . "_" . date("YmdHis") . ".xls";
			rename($file_path, $new_name);
			chmod($new_name, 0777);
		}
        
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

		$stylArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '2C2B2B')), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => '000000'))), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CFE7F5', )), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

		$stylHeadArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '8E8A8A', )), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF'))), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $wdth[$col] = 1;
                $col++;
            }
            $rowCount++;
        }
    
        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = utf8_encode(str_replace("", "'", $value)) ;
                if(strstr($value, "clients.edit-place.com"))
                {
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                elseif ($value == '#Formula')
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), "=LEN(".(chr(ord($col) - 1).($rowCount+1)).")");
                else
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                $col++;
            }
            foreach ($wdth as $key => $value)
                $objPHPExcel->getActiveSheet()->getStyle($key . ($rowCount + 1))->applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);

            $rowCount++;
        }

        unset($wdth);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
		if(file_exists($file_path))
			chmod($file_path, 0777);
    }

	function swriteXlsx3($data, $id)
    {
        $file_path = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_3_" . $id . ".xlsx";
        
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

		$stylArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '2C2B2B')), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => '000000'))), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'CFE7F5', )), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

		$stylHeadArr = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => '8E8A8A', )), 'borders' => array('inside' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF')), 'outline' => array('style' => PHPExcel_Style_Border::BORDER_HAIR, 'color' => array('rgb' => 'FFFFFF'))), 'alignment' => array('wrap' => true, 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP));

        $rowCount = 0;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($row as $key => $value) {
                $wdth[$col] = 1;
                $col++;
            }
            $rowCount++;
        }
    
        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {
                $value = str_replace("", "œ", $value) ;
                $value = str_replace("", "'", $value) ;
                $value = utf8_encode(str_replace("", "'", $value)) ;
                if(strstr($value, "clients.edit-place.com"))
                {
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                elseif ($value == '#Formula')
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), "=LEN(".(chr(ord($col) - 1).($rowCount+1)).")");
                else
					$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                $col++;
            }
            foreach ($wdth as $key => $value)
                $objPHPExcel->getActiveSheet()->getStyle($key . ($rowCount + 1))->applyFromArray($rowCount > 0 ? $stylArr : $stylHeadArr);

            $rowCount++;
        }
        unset($wdth);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(25);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
		if(file_exists($file_path))
			chmod($file_path, 0777);
    }


function getAllCarollMissingRefs($folder_id) {
    global $old_reference_ids;

    $path = CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_" . $folder_id . "_*";
    $arraySource = glob($path);

    usort($arraySource, function($a, $b) {
        return filemtime($a) > filemtime($b);
    });

    $currentFolderFile = "Writer_Final_" . $folder_id . (file_exists(CAROLL_WRITER_FILE_PATH2 . "/Writer_Final_" . $folder_id . ".xlsx") ? ".xlsx" : ".xls");

    foreach ($arraySource as $excel) {
        $file = $excel;
        $basename = basename($file);
        if ($currentFolderFile != $basename) {
            getCarollMissingRefdata($file);
        }
    }
}

/**getting all parent from final xls file**/
function getCarollMissingRefdata($file) {
	$fileInfo  =   pathinfo($file) ;
    global $old_reference_ids;
        
	if($fileInfo['extension']=='xls')
	{
		require_once (INCLUDE_PATH . "/reader.php");
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file);

		$sheets = sizeof($data1->sheets);
		for ($i = 0; $i < $sheets; $i++) {
			if ($data1->sheets[$i]['numRows']) {
				$x = 1;
				while ($x <= $data1->sheets[$i]['numRows']) {
					$y = 1;
					while ($y <= $data1->sheets[$i]['numCols']) {

						if ($x > 1 && $y == 6) {
							$data1->sheets[$i]['cells'][$x][$y] = str_replace("´", "’", $data1->sheets[$i]['cells'][$x][$y]) ;
							$reference = isset($data1->sheets[$i]['cells'][$x][$y]) ? $data1->sheets[$i]['cells'][$x][$y] : '';
							$old_reference_ids[] = $reference;
						}
						$y++;
					}
					$x++;
				}
			}
		}
	}
	elseif($fileInfo['extension']=='xlsx')
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
                        if($x>0 && $y==6)
						{
							$xlsArr1[$i][$x][$y-1] = str_replace("´", "’", $xlsArr1[$i][$x][$y-1]) ;
							$reference = isset($xlsArr1[$i][$x][$y-1]) ? $xlsArr1[$i][$x][$y-1] : '' ;
							$old_reference_ids[] = $reference;
						}
                        $y++;
                    }
                    $x++;
                }
            }
        }
        //echo "<pre>--";print_r($carollRefDataFile);exit($file.'|');
	}
}

function getAllCarollRefs()
{
    global $carollRefDataFile;
    
    $path = CAROLL_WRITER_FILE_PATH2."/Writer_Final_*";
    $arraySource = glob($path);
    
    usort($arraySource, function($a, $b) {
        return filemtime($a) > filemtime($b);
    });	//echo "<pre>--";print_r($arraySource);exit($path.'kkk');
    foreach($arraySource as $excel)
    {
        $basename=basename($file);
        getCarollRefdata($excel,'final');
    }
}

function getCarollRefdata($file,$type)
{
	$fileInfo  =   pathinfo($file) ;echo $fileInfo['extension'].'--'.$file.'--<br><br>';
    global $carollRefDataFile;
    
    if($type=='final')
        $rindex=6;
        
	if($fileInfo['extension']=='xls')
	{
		require_once(INCLUDE_PATH."/reader.php");
		
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file);   
		
		$sheets=sizeof($data1->sheets);
		//echo "<pre>--";print_r($data1->sheets[0]['cells']);exit($file.'|');
		
		for($i=0;$i<$sheets;$i++)
		{
			if($data1->sheets[$i]['numRows'])   
			{
				$x=1;                
				while($x<=$data1->sheets[$i]['numRows']) {
					$y=1;
					while($y<=$data1->sheets[$i]['numCols']) {
						if($x>1 && $y==$rindex)
						{
							$data1->sheets[$i]['cells'][$x][$y] = str_replace("´", "’", $data1->sheets[$i]['cells'][$x][$y]) ;
							$reference = isset($data1->sheets[$i]['cells'][$x][$y]) ? $data1->sheets[$i]['cells'][$x][$y] : '' ;
							$carollRefDataFile[basename($file)][]=$reference;
						}
						$y++;
					}
					$x++;
				}
			}
		}
	}
	elseif($fileInfo['extension']=='xlsx')
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
                        if($x>0 && $y==$rindex)
						{
							$xlsArr1[$i][$x][$y-1] = str_replace("´", "’", $xlsArr1[$i][$x][$y-1]) ;
							$reference = isset($xlsArr1[$i][$x][$y-1]) ? $xlsArr1[$i][$x][$y-1] : '' ;
							$carollRefDataFile[basename($file)][]=$reference;
						}
                        $y++;
                    }
                    $x++;
                }
            }
        }
        //echo "<pre>--";print_r($carollRefDataFile);exit($file.'|');
	}
}

?>
