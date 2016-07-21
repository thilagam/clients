<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
if(isset($_GET['folder_id']))
{
	
	$folder_id=$_GET['folder_id'];
	
	
	//$url="http://korben.edit-place.com/CAROLL/$folder_id";	
	$url=CAROLL_URL."/view-pictures.php?client=CAROLL&reference=";
	
	/**get All images as array other than given folder**/
	$array_all_images_list=array();
	$array_all_images_list=getAllFolderImages($folder_id);
	
	//echo "<pre>";print_r($array_all_images_list);exit("|||".sizeof($array_all_images_list));
	
				
	/**get images of a given folder*/
	$img_stack=getFolderImages($folder_id);
	//echo "<pre>";print_r($img_stack);exit($folder_id);
		
	
		
	/**get all parents from previous folder**/
	$global_parents_data=array();
	$global_parents_data_file=array();
	getAllParentsFromAllXLS($folder_id);
	$global_parents_data=array_unique($global_parents_data);
	
	//echo "<pre>#####";print_r($global_parents_data);exit;
    //echo "<pre>";print_r($global_parents_data_file);
    //echo "<br>#####<br>";print_r($array_all_images_list);echo "<br>#####<br>";print_r($img_stack);
    //exit(sizeof($global_parents_data)."|||".sizeof($array_all_images_list)."|||".sizeof($img_stack));
	
	/************Getting EP source data***/	
	require_once(INCLUDE_PATH."/reader.php");
	$brands=array();
	$replace=array();
	
		$ref_file_ep = CAROLL_EP_CONFIG_FILE;
		$ep_xls_file='CAROLL.xls';
		$ref_brand='';
		
		
		if(!file_exists($ref_file_ep))
		{
			$fp = fopen($ref_file_ep,"w");
			fclose($fp);
		}
		$arr_ref_soc_ep = unserialize(file_get_contents($ref_file_ep));
		$reference_ep=$arr_ref_soc_ep[$ep_xls_file];
		
	
		$master_ep_xls=CAROLL_EP_SOURCE_PATH."/$ep_xls_file";
	
		/***********Getting File1 Data**********************/
	
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($master_ep_xls);
		//echo $data1->dump(TRUE,TRUE)	;exit;
		
		//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
		//echo "<pre>";	print_r($data1->sheets[0]);echo "</pre>";exit;	
		$sheets=sizeof($data1->sheets);	
		$removeColumns=array(1,2,3,5);
		for($i=0;$i<$sheets;$i++)
		{
		
			if($data1->sheets[$i]['numRows'])	
			{
				$x=1;
				$z=0;
				$parent_cell='';
							 
				while($x<=$data1->sheets[$i]['numRows']) {
					$y=1;
					
					while($y<=$data1->sheets[$i]['numCols']) {					
						
						
						if($x==1)
						{
							if(!in_array($y,$removeColumns))
							$ep_source_xls[$x][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
						}		
						else
						{
							$ref_brand=trim(str_replace("?","",$data1->sheets[$i]['cells'][$x][$reference_ep]));
							$ref_brand=trim(str_replace(" ","",$ref_brand));

							if($ref_brand && !in_array($y,$removeColumns))
							{
								if(!$ep_source_xls[$ref_brand][$y-1])									
									$ep_source_xls[$ref_brand][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
							}	
						}
						$y++;
					}
					$ep_source_xls[1]=array_values($ep_source_xls[1]);
					//$ep_source_xls[$ref_brand]=array_values($ep_source_xls[$ref_brand]);
					$x++;
				}
			}
		}		
		if(count($ep_source_xls)>0)
		{
			
			foreach($ep_source_xls as $reference=>$source)
			{
				$ep_source_xls[$reference]=array_values($source);
			}
		}
		//echo "<pre>";	print_r($ep_source_xls);echo "</pre>";exit;	
	
	
	/**getting CAROLL Source**/
	
	$ref_file = CAROLL_REF_CONFIG_FILE;
	$xls_file='CAROLL_SOURCE.xls';
	
	
	
	if(!file_exists($ref_file))
	{
		$fp = fopen($ref_file,"w");
		fclose($fp);
	}
	$arr_ref_soc = unserialize(file_get_contents($ref_file));	
	$reference=$arr_ref_soc[$xls_file];		
	
	
		$master_caroll_xls=CAROLL_REF_SOURCE_PATH."/$xls_file";		
	
		/***********Getting File1 Data**********************/
	
		$data2 = new Spreadsheet_Excel_Reader();
		$data2->setOutputEncoding('Windows-1252');
		$data2->read($master_caroll_xls);
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
							$ep_sku[]=$ref_brand;
							
							if($x==1 && $y==1)
								$caroll_source_xls[$x][$y-1]='url';
							else if($x==1 && $y>1)
							{							
								if($y==2)
								{
									$caroll_source_xls[$x][$y-1]='DOUBLON';
									//setting column C as Column O of Source file
									$caroll_source_xls[$x][$y-1+1]=$ep_source_xls[$x][10];
									//$caroll_source_xls[$x][$y-1+1]='Desc court';	
									//$caroll_source_xls[$x][$y-1+2]='Min. signs';	
									$caroll_source_xls[$x][$y-1+2]='Desc long';											 
									$caroll_source_xls[$x][$y-1+3]='Min. signs';	
									$caroll_source_xls[$x][$y-1+4]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
								}	
								else	
									$caroll_source_xls[$x][$y-1+4]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
							}	
							else if($x>1 && $y==1)	
								$caroll_source_xls[$ref_brand][$y-1]=$url.$ref_brand;
							else
							{
								if($y==2)
								{
									//if(in_array($data2->sheets[$i]['cells'][$x][$reference],$array_all_images_list))
									$reference_ele=$data2->sheets[$i]['cells'][$x][$reference];
									if(preg_grep ("/$reference_ele/", $array_all_images_list))
									{				
										$caroll_source_xls[$ref_brand][$y-1]='DOUBLON';
									}
									else
										$caroll_source_xls[$ref_brand][$y-1]='';
									
									//setting column C as Column O of Source file
									$caroll_source_xls[$ref_brand][$y-1+1]=isset($ep_source_xls[$ref_brand][10])?trim(str_replace("?","",$ep_source_xls[$ref_brand][10])):'';									
									//$caroll_source_xls[$ref_brand][$y-1+1]='';	
									//$caroll_source_xls[$ref_brand][$y-1+2]='#Formula';	
									$caroll_source_xls[$ref_brand][$y-1+2]='';	
									$caroll_source_xls[$ref_brand][$y-1+3]='#Formula';	
									$caroll_source_xls[$ref_brand][$y-1+4]=isset($data2->sheets[$i]['cells'][$x][$y])?trim(str_replace("?","",$data2->sheets[$i]['cells'][$x][$y])):'';									
									
								}	
								
								else
								{
									if(!$caroll_source_xls[$ref_brand][$y-1+4])
									{
										$caroll_source_xls[$ref_brand][$y-1+4]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
										$caroll_source_xls[$ref_brand][$y-1+4]=str_replace("\n","",$caroll_source_xls[$ref_brand][$y-1+4]);
										$caroll_source_xls[$ref_brand][$y-1+4]=str_replace("\r\n","",$caroll_source_xls[$ref_brand][$y-1+4]);
										$caroll_source_xls[$ref_brand][$y-1+4]=str_replace("<br>","",$caroll_source_xls[$ref_brand][$y-1+4]);
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
		//echo "<pre>"; print_r($img_stack); echo '####';   print_r($ep_source_xls); echo '####';   print_r($caroll_source_xls); echo "</pre>"; exit;
		//echo "<pre>"; print_r($caroll_source_xls);echo "</pre>";exit;
		//checking Matched data with FTP and in two source files
		
		$ep_source_keys = array_keys(array_filter($ep_source_xls));
		//echo "<pre>";print_r($ep_source_keys);echo "</pre>";exit;
		
		foreach($img_stack as $reference)
		{
			$flag =0;
			$dbn_index=0;
			$j=0;
			
			foreach($caroll_source_xls as $index => $sarray) 
			{
				if($j==0)		
				{				
					unset($ep_source_xls[1][10]);
					$xls_array[0]=array_merge($caroll_source_xls[1],$ep_source_xls[1]);
				}	
				
				if($j>0)	
				{
						
					if(preg_match("/$index/", $reference) && in_array($index,$ep_source_keys))
					{
						//echo $index."--".$reference."<br>";
						
				
						
						if(!$dbn_index)
							$dbn_index=$index;
						if(!$xls_array[$index])	
						{
							unset($ep_source_xls[$index][10]);
							$xls_array[$index]=array_merge($caroll_source_xls[$index],$ep_source_xls[$index]);					
						}	
						if(in_array($index,$global_parents_data) && $global_parents_data_file[$index] )
						{
							//echo $index."<br>";
							
							$xls_array[$index][1]='DOUBLON ('.$global_parents_data_file[$index].')';
						}	
						else	
							$xls_array[$index][1]='';
						
						//$i++;
					}						
				}	
				$j++;

			}				
				//echo $flag."<br/>";	
		}
		
		//echo "<pre>";	print_r($xls_array);	exit('|'.sizeof($xls_array).'|');

		if(count($xls_array)>0)
		{
			//$xls_array=array_values($xls_array);				
			
			//generating XLS with matched array
			
			WriteXLS($xls_array,$folder_id);
			
		}	
		else
		{
			header("Location:".CAROLL_URL."/folder-list.php?client=CAROLL");
		}
		
}
else
	header("Location:".CAROLL_URL."/folder-list.php?client=CAROLL");


function WriteXLS($data,$id)
{
	
	// include package
	include 'Spreadsheet/Excel/Writer.php';

	// create empty file
	//$id=time();
	$filename = CAROLL_WRITER_FILE_PATH."/Writer_Final_".$id.".xls";
	
	
	 //Renaming existing file
	if(file_exists($filename))
	{
		$new_name1 = CAROLL_WRITER_FILE_PATH."/Writer_Final_".$id."_".date("YmdHis").".xls";
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
		else if($rowCount>0 && $key==0)
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
		
		$filename = CAROLL_WRITER_FILE_PATH."/Writer_Final_".$id.".xls";
		chmod($filename,0777);
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: private");
		header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-excel;");
		header("Accept-Ranges: bytes");
		header('Content-Disposition: attachment; filename="Writer_Final_'.$id.'.xls"');
		header("Content-Length: ".filesize($filename));
		readfile($filename);
		exit;
	  
	} 

}
//get all folder images other than given folder**/
function getAllFolderImages($folder_id)
{
		$array_list = array();
		
		$path = CAROLL_IMAGE_PATH."/*";
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
							$s=array_reverse(explode("-",$string));
							//echo $string;
							if($s[1])
								$array_list[]=$s[1];
						}
					}
				}
			}
			$array_list=array_values(array_unique($array_list));
		}
		return $array_list;
	//print_r($array_list);
	//exit;
}
/**Get images of a given folder**/
function getFolderImages($folder_id)
{
		$img_stack = array();	
		$stack=array();
		$directory = CAROLL_IMAGE_PATH."/$folder_id/";
		$files = glob($directory."*.*");

		if(count($files)>0)	
		{
			foreach($files as $file)
			{
				$string = basename($file);
				$s=array_reverse(explode("-",$string));
				 //array_pop($s);					 
				//$s=implode("-",$s);
				if($s[1])
					array_push($stack,$s[1]);
			}
			$img_stack = array_values(array_unique($stack));
		}	
		//print_r($img_stack);
//echo "<pre>"; print_r($files);echo($folder_id);print_r($img_stack);echo "</pre>"; exit($string);
		return $img_stack;
}


/**getting all final xls files**/
function getAllParentsFromAllXLS($folder_id)
{
	global $global_parents_data,$global_parents_data_file;
	
	$path = CAROLL_WRITER_FILE_PATH."/Writer_Final_*";
	$arraySource = glob($path);
	//sort($arraySource);
	
	usort($arraySource, function($a, $b) {
		return filemtime($a) > filemtime($b);
	});
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
		//echo "<pre>";	print_r($data1->sheets[0]);echo "</pre>";exit;	
		
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
							$global_parents_data[]=$reference;
							$global_parents_data_file[$reference]=basename($file);
							
						}	
						
						$y++;
					}
					$x++;
				}
			}
		}
		//echo "<pre>";print_r($global_parents_data);exit;
		//return $excel_data;
}

?>
