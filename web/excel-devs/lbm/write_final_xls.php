<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");


if(isset($_GET['folder_id']))
{
	$folder_id=$_GET['folder_id'];
	
	//$url="http://korben.edit-place.com/LEBONMARCHE/$folder_id";
	$url=LBMCHE_URL . "/view-pictures.php?client=LEBONMARCHE&reference=";
			
	/**get All images as array other than given folder**/
	$array_all_images_list=getAllFolderImages($folder_id);
				
	/**get images of a given folder*/
	$img_stack=getFolderImages($folder_id);	
		
	/**get all parents from previous folder**/
	$global_parents_data=array();
	$global_parents_data_file=array();
	getAllParentsFromAllXLS($folder_id);
	$global_parents_data=array_values(array_unique($global_parents_data));
	//echo "<pre>";print_r($global_parents_data);echo "</pre>";exit;

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
	
	$master_xls=LBMCHE_EP_SOURCE_PATH."/$xls_file";

	$data2 = new Spreadsheet_Excel_Reader();
	$data2->setOutputEncoding('Windows-1252');
	$data2->read($master_xls);

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
							$ep_source_xls[$ref_brand][$y-1]=$url.$ref_brand;
						else
						{
							if($y==2)
							{
								if(in_array($data2->sheets[$i]['cells'][$x][$reference],$array_all_images_list))
								{				
									$ep_source_xls[$ref_brand][$y-1]='DOUBLON';
								}
								else
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
	//echo "<pre>";print_r($ep_sku);
	//echo "<pre>";	print_r($ep_source_xls);echo "</pre>";exit;

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

	$master_lbm_xls=LBMCHE_REF_SOURCE_PATH."/$lbm_xlsx_file";	
	
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
						
						if($ref_brand)
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
		
		//echo "<pre>";	print_r($merge_array);echo "</pre>";exit;
		
		$i=0;
		$ean_loop=0;
		$parents_final=array();
		foreach($img_stack as $brand)
		{
			$flag =0;
			$dbn_index=0;							
			$j=0;
			foreach($merge_array as $index => $sarray) 
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
							$csv_array[$index][4]='DOUBLON ('.$global_parents_data_file[$parent].')';
						else	
							$csv_array[$index][4]='';
						$csv_array[$index][3]=$url.$index;
						$parents_final[]=$csv_array[$index][2];
						$i++;
					}						
				}	
				$j++;

			}					
			//echo $flag."<br/>";	
		}			
		$csv_array=array_values($csv_array);			
		//echo "<pre>";	print_r($csv_array);echo "</pre>";exit;					
		WriteXLS($csv_array,$folder_id);			
	}	
	else
	{
		header("Location:".SITE_URL."/excel-devs/lebonmarche/folder-list.php?client=LEBONMARCHE");
	}	
}
else
	header("Location:".SITE_URL."/excel-devs/lebonmarche/folder-list.php?client=LEBONMARCHE");

function WriteXLS($data,$id)
{
	// include package
	include 'Spreadsheet/Excel/Writer.php';
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
	// save file to disk
	if ($excel->close() === true) {		
		$filename = LBMCHE_WRITER_FILE_PATH."/Writer_Final_".$id.".xls";
		chmod($filename,0777);
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: private");
		header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download; charset=ISO-8859-1");
		header("Accept-Ranges: bytes");
		header('Content-Disposition: attachment; filename="Writer_Final_'.$id.'.xls"');
		header("Content-Length: ".filesize($filename));
		ob_clean();
		flush();
		readfile($filename);
	}
}

/**function to get all childs of all parent**/
function getChildsku($lbm_source_xls)
{	
	 foreach($lbm_source_xls as $key=>$lbm_array)
	 {
		$parent=$lbm_array[2];
		$childs_array[$parent][]=$lbm_array[1];	 
	 }
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

//get all folder images other than given folder**/
function getAllFolderImages($folder_id)
{
	$array_list = array();		
	$path = LBMCHE_IMAGE_PATH."/*";
	$array = glob($path);
	if(count($array)>0)
	{
		foreach ($array as $a)
		{
			$array1 = glob($a."/*");
			$dir_name = basename($a);
			if($dir_name != $folder_id)
			{
				if(is_dir($a))
				{
					foreach($array1 as $file)
					{
						$string = basename($file);
						$s=explode("_",$string);
						array_pop($s);
						//$array_list[]=$s[0];
						$s=implode("_",$s);
						if($s)
						$array_list[]=$s;
					}
				}
			}
			
		}
		$array_list=array_values(array_unique($array_list));
	}
	return $array_list;
	//print_r($array_list);exit;
}
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
function getAllParentsFromAllXLS($folder_id)
{
	global $global_parents_data,$global_parents_data_file;
	
	$path = LBMCHE_WRITER_FILE_PATH."/Writer_Final_Keepeek-Export*";
	$arraySource = glob($path);
	//sort($arraySource);
	
	usort($arraySource, "sorted");
	//echo "<pre>";	print_r($arraySource);echo "</pre>";exit;	
	
	$currentFolderFile="Writer_Final_".$folder_id.".xls";
	
	foreach($arraySource as $excel)
	{
		$file=$excel;
		$basename=basename($file);
		if($currentFolderFile!=$basename)
		{
			//echo $file;
			getExceldata($file,'final');
		}	
	}	
	
	//read all writer Excel files
	$path1 = LBMCHE_WRITER_FILE_PATH."/LEBONMARCHE_Keepeek-Export*.xls";
	$arraySource1 = glob($path1);
	//sort($arraySource);	
	usort($arraySource1, "sorted");
	//echo "<pre>";	print_r($arraySource1);echo "</pre>";exit;
	$currentWriterFile="LEBONMARCHE_".$folder_id.".xls";
	foreach($arraySource1 as $excel)
	{
		$file=$excel;
		$basename=basename($file);	
			//echo $file;
		if($currentWriterFile!=$basename)	
			getExceldata($file,'writer');
	}
}
/**getting all parent from final xls file**/
function getExceldata($file,$type)			
{
	global $global_parents_data,$global_parents_data_file;
	require_once(INCLUDE_PATH."/reader.php");
	$data1 = new Spreadsheet_Excel_Reader();
	$data1->setOutputEncoding('Windows-1252');
	$data1->read($file);
	//echo $data1->dump(TRUE,TRUE)	;exit;
	
	//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
	//echo "<pre>";	print_r($data2->sheets[$i]);echo "</pre>";exit;	
	
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
						
					}	
					
					$y++;
				}
				$x++;
			}
		}
	}
	//return $excel_data;
}
?>
