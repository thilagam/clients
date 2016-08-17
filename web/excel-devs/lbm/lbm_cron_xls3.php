<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");

/**get all parents from previous folder**/
$global_parents_data=array();
$global_parents_data_file=array();
getAllParentsFromAllXLS();
$global_parents_data=array_values(array_unique($global_parents_data));
//$old_parents=$global_parents_data;
//echo "<pre>";print_r($global_parents_data);echo "</pre>";exit;	

$directory = LBMCHE_IMAGE_PATH."/";	
$folders = glob($directory."*");
usort($folders, "sorteddesc");
	
$fcount=count($folders);
if($fcount>0)
{
	foreach($folders as $dir)
	{			
		$check_dir=str_replace(".zip","",$dir);
		$dir_exist=is_dir($check_dir);
		if($dir_exist AND !strpos($dir, '.zip'))
		{	
			$folder=basename($dir);
			$filename="Writer_Final_".$folder;
			//echo "<pre>";print_r($global_parents_data);echo "</pre>";exit;	
			
			$file_path=LBMCHE_WRITER_FILE_PATH."/Writer_Final_".$folder.".xls";
			$xls3_path=LBMCHE_WRITER_FILE_PATH."/Writer_Final_3_".$folder.".xls";	
			
			if(file_exists($xls3_path))	
			{						
				$new_name1 = LBMCHE_WRITER_FILE_PATH."/Writer_Final_3_".$folder."_".date("YmdHis").".xls";
				rename($xls3_path,$new_name1);
				//chmod($new_name1,0777);
			}
			
			$reference=2;
			if(file_exists($file_path))
			{
				$final_xls=array();
				require_once(INCLUDE_PATH."/reader.php");

				$data1 = new Spreadsheet_Excel_Reader();
				$data1->setOutputEncoding('Windows-1252');
				$data1->read($file_path);
				//echo $data1->dump(TRUE,TRUE)	;exit;
				
				//echo "Number of sheets: " .sizeof($data1->sheets) . "\n";exit;
				//echo $file_path."<pre>";	print_r($data1->sheets[0]);echo "</pre>";exit;	
				$sheets=sizeof($data1->sheets);	
				for($i=0;$i<$sheets;$i++)
				{
					if($data1->sheets[$i]['numRows'])	
					{
						$x=1;					 
						while($x<=$data1->sheets[$i]['numRows']) {
							$y=1;
							while($y<=$data1->sheets[$i]['numCols']) {
								$lbm_source_xls[$x][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
								$y++;
							}
							$final_xls[1]=$lbm_source_xls[1];
							
							if($x>1)
							{
								$parent=$lbm_source_xls[$x][$reference];
								//echo "<pre>";print_r($global_parents_data);echo "</pre>";exit;	
								if(!in_array($parent,$global_parents_data))
								{
									$final_xls[$x]=$lbm_source_xls[$x];//echo $parent."<br>";
								}
							}
							$x++;
						}
					}
				}		
				$final_xls=array_values($final_xls);					
				//echo "<pre>";	print_r($final_xls);echo "</pre>";exit;
				if(count($final_xls)>1)
				{
					//echo "Test<pre>";	print_r($final_xls);echo "</pre>";exit;
					echo "Creating xls3 file of .....$folder\n<br>";
					flush();
					ob_flush();
					WriteXLS($final_xls,$folder);
					chmod($xls3_path,0777);
				}	
			}
		}
	}	
}
/**getting all final xls files**/
function getAllParentsFromAllXLS()
{
	global $global_parents_data,$global_parents_data_file;
	
	$path = LBMCHE_WRITER_FILE_PATH."/Writer_Final_Keepeek-Export*";
	$arraySource = glob($path);
	//sort($arraySource);	
	usort($arraySource, "sorted");
	//echo "<pre>";	print_r($arraySource);echo "</pre>";exit;
	foreach($arraySource as $excel)
	{
		$file=$excel;
		$basename=basename($file);	
			//echo $basename;exit;
		getExceldata($file,'final');		
	}

	//read all writer Excel files
	$path1 = LBMCHE_WRITER_FILE_PATH."/LEBONMARCHE_Keepeek-Export*.xls";
	$arraySource1 = glob($path1);
	//sort($arraySource);	
	usort($arraySource1, "sorted");
	//echo "<pre>";	print_r($arraySource1);echo "</pre>";exit;
	foreach($arraySource1 as $excel)
	{
		$file=$excel;
		$basename=basename($file);	
			//echo $file;
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
	
	if($type=='final')
	{
		$rindex=3;
		$folder= explode("_",str_replace(LBMCHE_WRITER_FILE_PATH.'/Writer_Final_','',$file));
		$folder_id=$folder[0];
	}	
	else if($type=='writer')
	{
		$rindex=7;	
		$folder= explode("_",str_replace(LBMCHE_WRITER_FILE_PATH.'/LEBONMARCHE_','',$file));
		$folder_id=$folder[0];
	}	
	
	if((is_array($folder) && count($folder)>1))
	{
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
	}
}

//write XLS file
function WriteXLS($data,$id)
{
	require_once 'Spreadsheet/Excel/Writer.php';
	$filename = LBMCHE_WRITER_FILE_PATH."/Writer_Final_3_".$id.".xls";

	//Renaming existing file
	if(file_exists($filename))
	{
		$new_name1 = LBMCHE_WRITER_FILE_PATH."/Writer_Final_3_".$id."_".date("YmdHis").".xls";
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
	$excel->close();
}
