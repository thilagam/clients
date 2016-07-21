<?php
ob_start();
ini_set('display_errors', 1);
ini_set('max_execution_time',999999);
ini_set('memory_limit', '4096M');
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH", ROOT_PATH . "/includes");
define("SITE_URL","http://clients.edit-place.com");
define("CAROLL_URL", SITE_URL."/excel-devs/caroll");
define("CAROLL_IMAGE_PATH", ROOT_PATH."/CLIENTS/CAROLL");
define("CAROLL_PATH", ROOT_PATH."/excel-devs/caroll");
define("CAROLL_EP_SOURCE_PATH", CAROLL_PATH."/ep-source-files");
define("CAROLL_EP_CONFIG_FILE", CAROLL_PATH."/ep-config.txt");
define("CAROLL_REF_SOURCE_PATH", CAROLL_PATH."/caroll-source-files");
define("CAROLL_REF_CONFIG_FILE", CAROLL_PATH."/caroll-config.txt");
define("CAROLL_WRITER_FILE_PATH", CAROLL_PATH."/writer-files");
define("CAROLL_WRITER_FILE_PATH_TEST", CAROLL_PATH."/writer-files-test");
define("CAROLL_EXCEL_TIME_CONFIG", CAROLL_PATH."/caroll_excel_time.txt");
define("CAROLL_WRITER_FILE_CONFIG_PATH", CAROLL_PATH."/writer-files-config");
define("CAROLL_XML_PATH", CAROLL_PATH."/XML");
define("CAROLL_REF_SEARCH_PATH", CAROLL_PATH."/reference-search");

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
if ($ep_source_updated == 'yes' || $caroll_source_updated == 'yes' || !empty($folder_id)) {
	
	global $carollRefDataFile, $global_parents_data, $global_parents_data_file, $old_reference_ids;
	getAllCarollRefs() ;
	
	$fp = fopen(CAROLL_WRITER_FILE_CONFIG_PATH."/refs.txt","w");
	fwrite($fp,serialize($carollRefDataFile));
	fclose($fp);
	chmod(CAROLL_WRITER_FILE_CONFIG_PATH."/refs.txt", 0777);
	
    $xls3reference=5;
    
    if(!empty($folder_id))
		$folders = array('/home/sites/site2/web/CLIENTS/CAROLL/'.$folder_id) ;
    else
		$folders = glob(CAROLL_IMAGE_PATH . "/*");
		
	//$folders = array('/home/sites/site2/web/CLIENTS/CAROLL/2014_09_08_CAROLL_P08_JPEG_BD','/home/sites/site2/web/CLIENTS/CAROLL/2014_09_16_CAROLL_PO_P30_JPEG_BD', '/home/sites/site2/web/CLIENTS/CAROLL/2014_09_17_CAROLL_ST_P03_JPEG_BD') ;
	//$folders = array('/home/sites/site2/web/CLIENTS/CAROLL/2014_09_16_CAROLL_PO_P30_JPEG_BD') ;
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
	$ep_source_xls = getEpSource($master_ep_xls, $reference_ep );
	$ep_source_keys = array_keys(array_filter($ep_source_xls));
	//echo "<pre>ep source keys--";print_r($ep_source_keys);
	
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
	$caroll_source_xls = getCarollSource($master_caroll_xls, $reference, $array_all_images_list, $url );
	//echo "<pre>caroll source--";print_r($caroll_source_xls);//exit($folder) ;
	
	/** CAROLL source data **/
	
    //echo "<pre>";print_r($array_allImages);exit("|||".sizeof($array_allImages));
    
    $fcount = count($folders);
    if ($fcount > 0) {
        foreach ($folders as $dir) {

            /***********START******************/
            if (is_dir($dir) AND !strpos($dir, '.zip') AND basename($dir) != "caroll-xml-log" AND basename($dir) != "caroll-xml")
            {
                $folderid = basename($dir);
                $folder = basename(CAROLL_IMAGE_PATH . "/" . $folderid);
                
                $filename = "Writer_Final_" . $folder;
                $path_file = CAROLL_WRITER_FILE_PATH_TEST . "/" . $filename . ".xls";
                $new_name = CAROLL_WRITER_FILE_PATH_TEST . "/" . $filename . "_" . date("YmdHis") . ".xls";
                $xls3_path = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_3_" . $folder . ".xls";

                /*if(file_exists($path_file))
                 rename($path_file,$new_name);*/

                echo "<br><br>Creating the file .....$filename.xls\n<br>";

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
				
				/*echo "<pre>array_all_images_list--";print_r($array_all_images_list);
				echo "<pre>img_stack--";print_r($img_stack);exit(sizeof($array_all_images_list).'='.sizeof($img_stack));*/

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
                //echo "<pre>xls array--";print_r($xls_array);echo "</pre>";exit('='.sizeof($xls_array));

                //if(count($xls_array)>1){echo "<pre>"; print_r($xls_array);}   exit;
                if (count($xls_array) > 0) {
                    //generating XLS with matched array
                    WriteXLS($xls_array, $folder, $new_name);
                }

                /**** END WRITE XLS INCLUDE ****/

                if (file_exists($new_name)) {
                    if (file_exists($xls3_path)) {
                        $new_name1 = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_3_" . $folder . "_" . date("YmdHis") . ".xls";
                        rename($xls3_path, $new_name1);
                    }                    
                    /********** BEGIN WRITE XLS3 INCLUDE *****/
                    $old_reference_ids = array(); 
					getAllCarollMissingRefs($folderid);
					$old_reference_ids=array_unique($old_reference_ids);

					$final_xls=array();
					$data1 = new Spreadsheet_Excel_Reader();
					$data1->setOutputEncoding('Windows-1252');
					$data1->read($path_file);
					$sheets=sizeof($data1->sheets);	
					for($i=0;$i<$sheets;$i++)
					{
						if($data1->sheets[$i]['numRows'])	
						{
							$x=1;			 
							while($x<=$data1->sheets[$i]['numRows']) {
								$y=1;
								while($y<=$data1->sheets[$i]['numCols']) {

									$caroll_source_xls[$x][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
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
					$final_xls=array_values($final_xls);
					//echo $folderid."<br><br><br><pre>_3";	print_r($final_xls);echo "</pre>";//exit; 

					if(count($final_xls)>1)
					{
						echo "Creating the file Writer_Final_3_".$folderid.".xls\n";
						WriteXLS3($final_xls,$folderid);
					}
                    /********** END WRITE XLS3 INCLUDE *****/
                }
                /*else
                {
					echo $folderid."<br><br><br>Not_exist_3 -->".$new_name;	print_r($final_xls);echo "</pre>";exit;
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
    //exit ;
    //Ep ref  file updation
    $arr_ep_ref_soc['updated'] = 'no';
    $fp = fopen($ep_src_ref_file, "w");
    fwrite($fp, serialize($arr_ep_ref_soc));
    fclose($fp);

    //Caroll ref  file updation
    $arr_caroll_ref_soc['updated'] = 'no';
    $fp = fopen($caroll_src_ref_file, "w");
    fwrite($fp, serialize($arr_caroll_ref_soc));
    fclose($fp);
} else
    echo "No New Source Found";

function getCarollSource($master_caroll_xls, $reference, $array_all_images_list, $url )
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
	}
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

//get all folder images other than given folder**/
function getAllFolderImages($folder_id) {
    $array_list = array();
    $path = CAROLL_IMAGE_PATH . "/*";
    $array = glob($path);
    if (count($array) > 0) {
        foreach ($array as $a) {
            $array1 = glob($a . "/*");
            $dir_name = basename($a);
            if ($dir_name != $folder_id) {
                if (is_dir($a)) {
                    foreach ($array1 as $file) {
                        $string = basename($file);
                        $s = array_reverse(explode("-", $string));
                        //echo $string;
                        if ($s[1])
                            $array_list[] = $s[1];
                    }
                }
            }
        }
        $array_list = array_values(array_unique($array_list));
    }
    return $array_list;
}

/**Get images of a given folder**/
function getFolderImages($folder_id) {
    $img_stack = array();
    $stack = array();
    $directory = CAROLL_IMAGE_PATH . "/$folder_id/";
    $files = glob($directory . "*.*");
    //echo "<pre>";print_r($files);exit("|||".$directory."*.*");

    if (count($files) > 0) {
        foreach ($files as $file) {
            $string = basename($file);
            $s = array_reverse(explode("-", $string));

            if ($s[1])
                array_push($stack, $s[1]);
        }
        $img_stack = array_values(array_unique($stack));
    }
    return $img_stack;
}

/**Get images of a given folder**/
function getCarollPrevReferences($folder_id) {
    $references = array();

    foreach (unserialize(file_get_contents(CAROLL_WRITER_FILE_CONFIG_PATH."/refs.txt")) as $k1 => $v1) {
        if (!strstr($k1, $folder_id)) {
            foreach ($v1 as $k2 => $v2)
                $references[$v2] = $k1;
        }
    }
    return $references;
}

function WriteXLS($data, $id, $new_name) {
    // include package
    include_once 'Spreadsheet/Excel/Writer.php';

    // create empty file
    $filename = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_" . $id . ".xls";

    //Renaming existing file
    if (file_exists($filename)) {
        //$new_name1 = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_" . $id . "_" . date("YmdHis") . ".xls";
        rename($filename, $new_name);
        chmod($new_name, 0777);
    }
    $excel = new Spreadsheet_Excel_Writer($filename);
    $excel->setVersion(8);

    // add worksheet
    $sheet = &$excel->addWorksheet();
    $sheet->setColumn(0, count($data[1]), 20);

    //custom color
    $excel->setCustomColor(22, 217, 151, 149);
    $excel->setCustomColor(12, 252, 213, 180);

    // create format for header row
    // bold, red with black lower border
    $format_a = array('bordercolor' => 'black', 'bold' => '1', 'size' => '11', 'FgColor' => '22', 'color' => 'black', 'align' => 'center', 'valign' => 'top');
    $format_headers = &$excel->addFormat($format_a);
    $format_headers->setBorder(1);
    //$format_headers->setTextWrap();
    $wrap_format = &$excel->addFormat();
    $wrap_format->setVAlign('top');
    //$wrap_format->setBorder(1);
    $wrap_format->setFgColor(12);
    //$wrap_format->setTextWrap();
    $wrap_format->setAlign('left');

    // add data to worksheet
    $rowCount = 0;
    //echo (count($data[4])-2)."<pre>";print_r($data);echo "</pre>";exit;
    foreach ($data as $row) {
        //$col_cnt=count($row);
        foreach ($row as $key => $value) {
            if ($rowCount == 0) {
                $sheet->write($rowCount, $key, $value, $format_headers);
            } else if ($rowCount > 0 && $key == 0) {
                $sheet->writeUrl($rowCount, $key, $value, '', $wrap_format);
            } else if ($value == '#Formula') {
                if ($value == '#Formula') {
                    $cell = Spreadsheet_Excel_Writer::rowcolToCell($rowCount, $key - 1);
                    $sheet->writeFormula($rowCount, $key, "=LEN($cell)", $wrap_format);
                } else
                    $sheet->write($rowCount, $key, '', $wrap_format);
            } else
                $sheet->write($rowCount, $key, $value, $wrap_format);
        }
        $rowCount++;
    }
    // save file to disk
    if ($excel->close() === true) {
        $filename = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_" . $id . ".xls";
        chmod($filename, 0777);
    }
}

function WriteXLS3($data, $id) {
    // include package
    include_once 'Spreadsheet/Excel/Writer.php';

    $filename = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_3_" . $id . ".xls";

    $excel = new Spreadsheet_Excel_Writer($filename);
    $excel->setVersion(8);
    // add worksheet
    $sheet = &$excel->addWorksheet();
    $sheet->setColumn(0, count($data[1]), 20);

    //custom color
    $excel->setCustomColor(22, 217, 151, 149);
    $excel->setCustomColor(12, 252, 213, 180);

    // create format for header row
    // bold, red with black lower border
    $format_a = array('bordercolor' => 'black', 'bold' => '1', 'size' => '11', 'FgColor' => '22', 'color' => 'black', 'align' => 'center', 'valign' => 'top');

    $format_headers = &$excel->addFormat($format_a);
    $format_headers->setBorder(1);
    $wrap_format = &$excel->addFormat();
    $wrap_format->setVAlign('top');
    $wrap_format->setFgColor(12);
    $wrap_format->setAlign('left');

    // add data to worksheet
    $rowCount = 0;
    //echo (count($data[4])-2)."<pre>";print_r($data);echo "</pre>";exit;
    foreach ($data as $row) {
        //$col_cnt=count($row);
        foreach ($row as $key => $value) {
            if ($rowCount == 0) {
                $sheet->write($rowCount, $key, $value, $format_headers);
            } else if ($rowCount > 0 && $key == 1) {
                $sheet->writeUrl($rowCount, $key, $value, '', $wrap_format);
            } else if ($value == '#Formula') {
                if ($value == '#Formula') {
                    $cell = Spreadsheet_Excel_Writer::rowcolToCell($rowCount, $key - 1);
                    $sheet->writeFormula($rowCount, $key, "=LEN($cell)", $wrap_format);
                } else
                    $sheet->write($rowCount, $key, '', $wrap_format);
            } else
                $sheet->write($rowCount, $key, $value, $wrap_format);
        }
        $rowCount++;
    }

    // save file to disk
    if ($excel->close() === true) {
        $filename = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_3_" . $id . ".xls";
        chmod($filename, 0777);
    }
}

function getAllCarollMissingRefs($folder_id) {
    global $old_reference_ids;

    $path = CAROLL_WRITER_FILE_PATH_TEST . "/Writer_Final_" . $folder_id . "_*";
    $arraySource = glob($path);

    usort($arraySource, function($a, $b) {
        return filemtime($a) > filemtime($b);
    });

    $currentFolderFile = "Writer_Final_" . $folder_id . ".xls";

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
    global $old_reference_ids;
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

function getAllCarollRefs()
{
	global $carollRefDataFile;
	
	$path = CAROLL_WRITER_FILE_PATH_TEST."/Writer_Final_*";
	$arraySource = glob($path);
	
	usort($arraySource, function($a, $b) {
		return filemtime($a) > filemtime($b);
	});//echo "<pre>--";print_r($arraySource);exit('kkk');
	foreach($arraySource as $excel)
	{
		$basename=basename($file);
		getCarollRefdata($excel,'final');
	}	
}

function getCarollRefdata($file,$type)
{
	global $carollRefDataFile;
	require_once(INCLUDE_PATH."/reader.php");
	$data1 = new Spreadsheet_Excel_Reader();
	$data1->setOutputEncoding('Windows-1252');
	$data1->read($file);
	
	if($type=='final')
		$rindex=6;		
	
	$sheets=sizeof($data1->sheets);	
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
						$reference=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
						$carollRefDataFile[basename($file)][]=$reference;
					}
					$y++;
				}
				$x++;
			}
		}
	}
}
?>
