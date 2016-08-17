<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");
set_time_limit(3600);

/*checking Ep source updated or not**/
$ep_src_ref_file =LBMCHE_EP_CONFIG_FILE;
	
if(!file_exists($ep_src_ref_file))
{
	$fp = fopen($ep_src_ref_file,"w");
	fclose($fp);
}

$arr_ep_ref_soc = unserialize(file_get_contents($ep_src_ref_file));	
$ep_source_updated=$arr_ep_ref_soc['updated'];

/*checking LBMCHE source updated or not**/
$lbm_src_ref_file = LBMCHE_REF_CONFIG_FILE;
	
if(!file_exists($lbm_src_ref_file))
{
	$fp = fopen($lbm_src_ref_file,"w");
	fclose($fp);
}
$arr_lbm_ref_soc = unserialize(file_get_contents($lbm_src_ref_file));	
$lbm_source_updated=$arr_lbm_ref_soc['updated'];		


//echo $ep_source_updated."--".$lbm_source_updated;exit;
//echo "<pre>";print_r($arr_ep_ref_soc);	exit;
		
if($ep_source_updated=='yes' || $lbm_source_updated=='yes')
{
	/**get all parents from previous folder**/
	$global_parents_data=array();
	$global_parents_data_file=array();
	
	getAllParentsFromAllXLS();
	//$global_parents_data=array_values(array_unique($global_parents_data));

    /*$fp = fopen(LBMCHE_REF_TXT_FILE,"w+");
    fwrite($fp,serialize($global_parents_data_file));
    fclose($fp);
    echo "<pre>";print_r(unserialize(file_get_contents(LBMCHE_REF_TXT_FILE)));exit(LBMCHE_REF_TXT_FILE);*/

	//echo implode("<br>",$global_parents_data);exit;
	//echo "<pre>";print_r($global_parents_data);print_r($global_parents_data_file);echo "</pre>";exit;
	
	list($ep_sku,$ep_source_xls)=getPricedenoteData();
	//echo "<pre>";print_r($ep_source_xls);echo "</pre>";exit;
	
	list($lbm_source_xls,$merge_array)=getReferentialData($ep_sku,$ep_source_xls);
	//echo "<pre>";print_r($lbm_source_xls);echo "</pre>";exit;

	$directory = LBMCHE_IMAGE_PATH."/";	
	$folders = glob($directory."*");
	
	usort($folders, "sorteddesc"); echo "<pre>";print_r($folders);//exit;
	
	$fcount=count($folders);
	if($fcount>0)
	{
		foreach($folders as $dir)
		{
			$check_dir=str_replace(".zip","",$dir);
			$dir_exist=is_dir($check_dir);
			if($dir_exist AND !strpos($dir, '.zip'))
			{	
				echo $folder=basename($dir);echo "--folder<br>";
				$filename="Writer_Final_".$folder;
				$path_file=LBMCHE_WRITER_FILE_PATH."/".$filename.".xls";
				$new_name = LBMCHE_WRITER_FILE_PATH."/".$filename."_".date("YmdHis").".xls";
				$xls3_path=LBMCHE_WRITER_FILE_PATH."/Writer_Final_3_".$folder.".xls";	
				
				$url=LBMCHE_URL."/view-pictures.php?client=LEBONMARCHE&reference=";
				
				if(file_exists($path_file))
				{
					//rename($path_file,$new_name);
				}	
				
				/**get images of a given folder*/
				
				$img_stack=getFolderImages($folder);
                
				print_r($img_stack);
                					
				echo "Creating the file .....$filename.xls\n<br>";	
				//flush();
				//ob_flush();

				$i=0;
				$ean_loop=0;
				$parents_final=array();
				$csv_array=array();
				foreach($img_stack as $brand)
				{
					$flag =0;
					$dbn_index=0;
	
					$j=0;
					/*foreach($merge_array as $index => $sarray) 
					{
						if($i==0)				
						$csv_array[0]=$merge_array[0];
						
						if($j>0)	
						{
							$parent=$sarray[2];
							if(preg_match("/$index/", $brand) && $index!='' && !in_array($parent,$parents_final)) //&& in_array($index,$matched_sku)
							{
								//echo $index."--".$brand."<br>";									
								if(!$dbn_index)
									$dbn_index=$index;
								if(!$csv_array[$index])	
								$csv_array[$index]=$merge_array[$index];
								
								$flag++;
								
								if(in_array($parent,$global_parents_data))
								{
									if($global_parents_data_file[$parent]!='Writer_Final_'.$folder.'.xls' && $global_parents_data_file[$parent]!='LEBONMARCHE_'.$folder.'.xls')
										$csv_array[$index][4]='DOUBLON ('.$global_parents_data_file[$parent].')';
								}	
								else	
									$csv_array[$index][4]='';
								$csv_array[$index][3]=$url.$index;
								$parents_final[]=$csv_array[$index][2];
								$i++;
							}						
						}
						$j++;
					}*/	
						//echo $flag."<br/>";	
				}
				
				$csv_array=array_values($csv_array);
				echo "Test<pre>";	print_r($csv_array);echo "</pre>";
				
				if(count($csv_array)>0)
				{
					//WriteXLS($csv_array,$folder);
					//chmod($path_file,0777);
				}
			}
		}	
			exit;
	}	//exit;
	//Ep ref  file updation	
	$arr_ep_ref_soc['updated'] = 'no';
	$fp = fopen($ep_src_ref_file,"w");
	fwrite($fp,serialize($arr_ep_ref_soc));
	fclose($fp); 
	
	//LBMCHE ref  file updation	
	$arr_lbm_ref_soc['updated'] = 'no';
	$fp = fopen($lbm_src_ref_file,"w");
	fwrite($fp,serialize($arr_lbm_ref_soc));
	fclose($fp);

    $refxls = '<table style="width:88%; padding-top:10px;" align="center" valign="center" cellpadding="2" cellspacing="2" class="gridtable">' ;
    $refxls .= "<tbody>";

    $rowCount = 0 ;
    foreach ($global_parents_data_file as $key=>$val)
    {
        /*if($rowCount==0)
            $refxls .= "<thead><th>".$key."</th><th>".$val."</th></thead><tbody>" ;
        else*/
            $refxls .= "<tr><td>".$key."</td><td>".$val."</td></tr>" ;

        $rowCount++;
    }
    $refxls .= "</tbody></table>" ;
    
    $fh = fopen(LBMCHE_REF_XLS_FILE, 'w+');
    fwrite($fh, $table);
    fclose($fh);
}
else
	echo "Source not Updated";

/**Get images of a given folder**/
function getFolderImages($folder_id)
{
	$img_stack = array();	
	$directory = LBMCHE_IMAGE_PATH."/$folder_id/";	
	$files = glob($directory."*.*");	
	
	if(count($files)>0)	
	{
		foreach($files as $file)
		{
			$string = basename($file);
			$s=explode("_",$string);
			 array_pop($s);
			 //$stack[]=$s[0];
			 $s=implode("_",$s);
			 if($s)
			 $stack[]=$s;
		}
		$img_stack =array_values(array_unique($stack));	
	}
	return $img_stack;		
}
/**getting all final xls files**/
function getAllParentsFromAllXLS()
{
	//exit('--'.LBMCHE_REF_XLS_FILE);
	global $global_parents_data,$global_parents_data_file;
	
    $global_parents_data_file=unserialize(file_get_contents(LBMCHE_REF_TXT_FILE)) ;
    $global_parents_data=array_keys($global_parents_data_file);
    
	/*require_once(INCLUDE_PATH."/reader.php");
	$data1 = new Spreadsheet_Excel_Reader();
	$data1->setOutputEncoding('Windows-1252');
	$data1->read(LBMCHE_REF_XLS_FILE);
	//echo "<pre>";//print_r($data1->sheets);echo "</pre>";
	
	$sheets=sizeof($data1->sheets);	
	for($i=0;$i<$sheets;$i++)
	{
		if($data1->sheets[$i]['numRows'])	
		{
			$x=1;					 
			while($x<=$data1->sheets[$i]['numRows']) {
				//print_r($data1->sheets[$i]['cells'][$x]);				
				$reference=isset($data1->sheets[$i]['cells'][$x][1]) ? $data1->sheets[$i]['cells'][$x][1] : '' ;
				$global_parents_data[]=$reference;
				$global_parents_data_file[$reference]=$data1->sheets[$i]['cells'][$x][2];
				$x++;
			}
		}
	}*/
	//exit(LBMCHE_REF_XLS_FILE);
}
/**getting all parent from final xls file**/
function getExceldata($file,$type)
{
	global $global_parents_data,$global_parents_data_file;
	require_once(INCLUDE_PATH."/reader.php");
	$data1 = new Spreadsheet_Excel_Reader();
	$data1->setOutputEncoding('Windows-1252');
	$data1->read($file);
	
	if($type=='final')
		$rindex=2;
	else if($type=='writer')
		$rindex=7;	
	
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
						$global_parents_data[]=$reference;
						$global_parents_data_file[$reference]=basename($file);
						$ref_array[]=$reference;
					}
					$y++;
				}
				$x++;
			}
		}
	}
	return $ref_array ;
	//return $excel_data;
}

function getPricedenoteData()
{
	/**getting EP Source**/	
	$ref_file = LBMCHE_EP_CONFIG_FILE;
	$xls_file='LEBONMARCHE.xls';
	
	if(!file_exists($ref_file))
	{
		$fp = fopen($ref_file,"w");
		fclose($fp);
	}
	$arr_ref_soc = unserialize(file_get_contents($ref_file));	
	$reference=$arr_ref_soc[$xls_file];
	//$xls_file='LEBONMARCHE_Test.xls';

	require_once(INCLUDE_PATH."/reader.php");
	$brands=array();
	$replace=array();
	$ep_sku=array();
	error_reporting(E_ALL ^ E_NOTICE);
	//error_reporting(0);
	
	$master_xls=LBMCHE_EP_SOURCE_PATH."/$xls_file";

	$data2 = new Spreadsheet_Excel_Reader();
	$data2->setOutputEncoding('Windows-1252');
	$data2->read($master_xls);
	
	//echo $data2->dump(TRUE,TRUE)	;exit;
	
	//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
	//echo "<pre>";	print_r($data2->sheets[0]);echo "</pre>";exit;
	
	$sheets=sizeof($data2->sheets);	
	for($i=0;$i<$sheets;$i++)
	{
		if($data2->sheets[$i]['numRows'])	
		{
			$x=1;
			$z=1;					
			 
			while($x<=$data2->sheets[$i]['numRows']) {
				$y=1;
				
				while($y<=$data2->sheets[$i]['numCols']) {

					 if($data2->sheets[$i]['cells'][$x][$reference]!='')		
					 {
						$ref_brand=trim(str_replace("?","",$data2->sheets[$i]['cells'][$x][$reference]));		
						$ref_brand=trim(str_replace(" ","",$ref_brand));							
						
						if($x==1 && $y==1)
							$ep_source_xls[$x][$y-1]='url';
						else if($x==1 && $y>1)
						{							
							if($y==2)
							{
								$ep_source_xls[$x][$y-1]='DOUBLON';
								$ep_source_xls[$x][$y-1+1]='Titre';	
								$ep_source_xls[$x][$y-1+2]='Nb CAR 32';	
								$ep_source_xls[$x][$y-1+3]='Descriptif';											 
								$ep_source_xls[$x][$y-1+4]='Nb CAR 300-900';	
								$ep_source_xls[$x][$y-1+5]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
							}	
							else	
								$ep_source_xls[$x][$y-1+5]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
						}	
						else if($x>1 && $y==1)	
							$ep_source_xls[$ref_brand][$y-1]=$url."/".$ref_brand;
						else
						{
							if($y==2)
							{
								$ep_source_xls[$ref_brand][$y-1]='';
								
								$ep_source_xls[$ref_brand][$y-1+1]='';	
								$ep_source_xls[$ref_brand][$y-1+2]='#Formula';	
								$ep_source_xls[$ref_brand][$y-1+3]='';	
								$ep_source_xls[$ref_brand][$y-1+4]='#Formula';	
								$ep_source_xls[$ref_brand][$y-1+5]=isset($data2->sheets[$i]['cells'][$x][$y])?trim(str_replace("?","",$data2->sheets[$i]['cells'][$x][$y])):'';									
								
							}							
							else
							{
								$ep_source_xls[$ref_brand][$y-1+5]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
								$ep_source_xls[$ref_brand][$y-1+5]=str_replace("\n","",$ep_source_xls[$ref_brand][$y-1+5]);
								$ep_source_xls[$ref_brand][$y-1+5]=str_replace("\r\n","",$ep_source_xls[$ref_brand][$y-1+5]);
								$ep_source_xls[$ref_brand][$y-1+5]=str_replace("<br>","",$ep_source_xls[$ref_brand][$y-1+5]);
							}
						}
					 }		
					 $y++;
				}
				if($x>1 && $ref_brand)
						$ep_sku[]=$ref_brand;
				$x++;
			}
		}
	}
	$ep_sku=array_values(array_unique($ep_sku));	
	return array($ep_sku,$ep_source_xls);	
}
//get lBM source file data of xlsx
function getReferentialData($ep_sku,$ep_source_xls)
{
	/************Getting LBMCHE source data***/	
	$ref_file_lbm = LBMCHE_REF_CONFIG_FILE;
	$lbm_xlsx_file='LEBONMARCHE_SOURCE.xlsx';
	
	$ref_brand='';		
	
	if(!file_exists($ref_file_lbm))
	{
		$fp = fopen($ref_file_lbm,"w");
		fclose($fp);
	}
	$arr_ref_soc_lbm = unserialize(file_get_contents($ref_file_lbm));	
	$reference_lbm=$arr_ref_soc_lbm[$lbm_xlsx_file];
	//echo $reference_lbm;exit;
	//$lbm_xls_file='LEBONMARCHE_SOURCE_Test.xls';

	$master_lbm_xls=LBMCHE_REF_SOURCE_PATH."/$lbm_xlsx_file";	
	$parent_array=array();
	require_once(INCLUDE_PATH."/simplexlsx.class.php");
	$xlsx = new SimpleXLSX($master_lbm_xls);				
	for($j=1;$j <= $xlsx->sheetsCount();$j++){
		list($cols) = $xlsx->dimension($j);
		$sheet_rows=$xlsx->rows($j);
		//echo "<pre>";	print_r($sheet_rows);exit;
		
		if(count($xlsx->rows($j))>0)
		{
			$row=0;
			$z=0;
			$parent_cell='';
			foreach( $xlsx->rows($j) as $k => $r) {
				for( $i = 0; $i < $cols; $i++) {
					if($i==3)
						break;
					if($row==0)
							$lbm_source_xls[$row+1][$i]=( isset($r[$i]) ? ($r[$i]) : '' );
					else
					{							
						$ref_brand=trim(str_replace("?","",$sheet_rows[$row][$reference_lbm-1]));		
						$ref_brand=trim(str_replace(" ","",$ref_brand));
						
						if($ref_brand && !strstr($ref_brand, 'color'))
							$lbm_source_xls[$ref_brand][$i]= ( isset($r[$i]) ? ($r[$i]) : '' );
					}	
					//$lbm_source_xls[$row+1][$i+1] = ( isset($r[$i]) ? ($r[$i]) : '' );
				}
				if($row>0)
				{
					
					if(!$sheet_rows[$row][$reference_lbm])
					{
						$lbm_source_xls[$ref_brand][$reference_lbm]=$sheet_rows[$row][$reference_lbm-1];
					}	
						$parent_cell=$lbm_source_xls[$ref_brand][$reference_lbm];
					//echo "<pre>";print_r($ep_sku);exit;
									
					if(in_array($ref_brand,$ep_sku) && $row>0 && !in_array($parent_cell,$parent_array))
					{
						//index o is sku and 1 is parent
						$matched_array[$z][0]=$ref_brand;
						$matched_array[$z][1]=$lbm_source_xls[$ref_brand][$reference_lbm];
						//to check in final result
						$matched_sku[]=$ref_brand;
						
						$parent_array[$z]=$lbm_source_xls[$ref_brand][$reference_lbm];
						$z++;
					}
				}
				$row++;
			}
		}
	}
		//echo "<pre>";	print_r($matched_array);echo "</pre>";exit;
		
	/*Get all child sku of a parent of matched array **/
	if(count($matched_array)>0)
	{
		$k=0;
		if($k==0)
		{
			$merge_array[$k]=mergesourceandlbm(1,1,$lbm_source_xls,$ep_source_xls);
			$k++;
		}	
		$all_parents_childs=getChildsku($lbm_source_xls);	
		
		foreach($matched_array as $match)
		{
			$parents_childs=array();
			$parents_childs=$all_parents_childs[$match[1]];				
			
			if(count($parents_childs)>0)
			{
				foreach($parents_childs as $child)
				{
					$merge_array[$child]=mergesourceandlbm($match[0],$child,$lbm_source_xls,$ep_source_xls);
					$k++;
				}	
			}
		}
	}	
	return array($lbm_source_xls,$merge_array);
}

/**function to get all childs of all parent**/
function getChildsku($lbm_source_xls)
{	
	 foreach($lbm_source_xls as $key=>$lbm_array)
	 {
		$parent=$lbm_array[2];
		$childs_array[$parent][]=$lbm_array[1];	 
	 }
	//$sku=$matched_cell[0];	
	/* foreach($lbm_source_xls as $key=>$lbm_array)
	{
		if(in_array($parent,$lbm_array))
		{
			$childs_array[]=$lbm_array[1];
		}
	}	
	return $childs_array; */
	//echo "<pre>";print_r($childs_array);exit;
	return $childs_array;
}
/**function to get a sku content and it to all childs of parent**/
function mergesourceandlbm($sourcesku,$matchsku,$lbm_source_xls,$ep_source_xls)
{
	
	$lbm_sku=$lbm_source_xls[$matchsku];
	$ep_sku=$ep_source_xls[$sourcesku];
	
	return array_merge($lbm_sku,$ep_sku);
}

//write XLS file
function WriteXLS($data,$id)
{
	require_once 'Spreadsheet/Excel/Writer.php';

	// create empty file
	//$id=time();
	$filename = LBMCHE_WRITER_FILE_PATH."/Writer_Final_".$id.".xls";
	
	//Renaming existing file
	if(file_exists($filename))
	{
		$new_name1 = LBMCHE_WRITER_FILE_PATH."/Writer_Final_".$id."_".date("YmdHis").".xls";
		rename($filename,$new_name1);
		chmod($new_name1,0777);
	}
	
	$excel = new Spreadsheet_Excel_Writer($filename);
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
		else if($rowCount>0 && $key==3)
		{
			$sheet->writeUrl($rowCount, $key,$value,'',$wrap_format);
		}	
		else if($value=='#Formula')	
		{
			if($value=='#Formula')
			{
				$cell = Spreadsheet_Excel_Writer::rowcolToCell($rowCount,$key-1);
				$sheet->writeFormula($rowCount,$key, "=LEN($cell)",$wrap_format);
			}
			else	
				$sheet->write($rowCount,$key,'',$wrap_format);
		}
		else
			$sheet->write($rowCount, $key, $value,$wrap_format);
	  }
	  $rowCount++;
	}
	$excel->close();
}
