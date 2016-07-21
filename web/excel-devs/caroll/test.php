<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

$array_list = array();
    $path = CAROLL_IMAGE_PATH . "/2014_09_26_CAROLL_PO_P30_JPEG_BD/*";
    $array = glob($path);echo "<pre>"; print_r($array);echo "</pre>";exit;
	
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

getAllParentsFromAllXLS($folder_id);
$global_parents_data=array_unique($global_parents_data);

echo "<pre>#####";print_r($global_parents_data);exit;
?>
