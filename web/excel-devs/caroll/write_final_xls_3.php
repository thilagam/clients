<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if(isset($_GET['folder_id']))
{
	
	$folder_id=$_GET['folder_id'];
	
	$reference=5;
	
	require_once(INCLUDE_PATH."/reader.php");
	
	
	/**get all parents from previous folder**/
	$global_parents_data=array();	
	getAllReferenceFromAllXLS($folder_id);
	$global_reference_data=array_unique($global_parents_data);
	
	$old_reference_ids=$global_reference_data;
	
	//echo "<pre>";	print_r($old_reference_ids);echo "</pre>";exit;
					
						 
				
		
		
/************Getting Write final xls Data***/	
		$file_path=CAROLL_WRITER_FILE_PATH."/Writer_Final_".$folder_id.".xls";
		if(!file_exists($file_path))
		{
			header("Location:".CAROLL_URL."/folder-list.php?client=CAROLL");
			exit;
		}	
		
	
		/***********Getting File1 Data**********************/
	
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file_path);
		//echo $data1->dump(TRUE,TRUE)	;exit;
		
		//echo "Number of sheets: " .sizeof($data1->sheets) . "\n";exit;
		//echo "<pre>";	print_r($data1->sheets[0]);echo "</pre>";exit;	
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
						$reference_id=$caroll_source_xls[$x][$reference];
						//echo $reference_id."<br>";
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
		//echo "<pre>";	print_r($final_xls);echo "</pre>";exit;
		
		
		if(count($final_xls)>1)
		{
			WriteXLS($final_xls,$folder_id);			
		}	
		else
			header("Location:".CAROLL_URL."/folder-list.php?client=CAROLL");
}	
		
function WriteXLS($data,$id)
{
	
	// include package
	include 'Spreadsheet/Excel/Writer.php';
	// create empty file
	//$id=time();
	$filename = CAROLL_WRITER_FILE_PATH."/Writer_Final_3_".$id.".xls";
	
	
	/* //Renaming existing file
	if(file_exists($filename))
	{
		$new_name1 = "/home/sites/site6/web/upload/CSV/CAROLL/Writer_Final_3_".$id."_".date("YmdHis").".xls";
		rename($filename,$new_name1);
		chmod($new_name1,0777);
	} */
	
	
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
		else if($rowCount>0 && $key==1)
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
		
		$filename = CAROLL_WRITER_FILE_PATH."/Writer_Final_3_".$id.".xls";
		chmod($filename,0777);
		
		header("Content-Transfer-Encoding: binary");
		header("Expires: 0");
		header("Pragma: private");
		header("Cache-Control: private,must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/vnd.ms-excel;");
		header("Accept-Ranges: bytes");
		header('Content-Disposition: attachment; filename="Writer_Final_3_'.$id.'.xls"');
		header("Content-Length: ".filesize($filename));
		readfile($filename);
	  
	} 
}
/**getting all final xls files**/
function getAllReferenceFromAllXLS($folder_id)
{
	global $global_parents_data;
	
	$path = CAROLL_WRITER_FILE_PATH."/Writer_Final_".$folder_id."_*";	
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
			getExceldata($file);
		}	
	}	
}
/**getting all parent from final xls file**/
function getExceldata($file)			
{
		global $global_parents_data;
		require_once(INCLUDE_PATH."/reader.php");
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file);
		//echo $data1->dump(TRUE,TRUE)	;exit;
		
		//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
		//echo "<pre>";	print_r($data2->sheets[$i]);echo "</pre>";exit;	
		$sheets=sizeof($data1->sheets);	
		for($i=0;$i<$sheets;$i++)
		{
		
			if($data1->sheets[$i]['numRows'])	
			{
				$x=1;
										 
				while($x<=$data1->sheets[$i]['numRows']) {
					$y=1;
					
					while($y<=$data1->sheets[$i]['numCols']) {
					
						if($x>1 && $y==6)
						{
							$reference=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
							$global_parents_data[]=$reference;
							
							
						}	
						
						$y++;
					}
					$x++;
				}
			}
		}
		//echo "<pre>";print_r($global_parents_data);
		//return $excel_data;
}
?>
