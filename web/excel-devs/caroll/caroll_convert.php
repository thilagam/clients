<?php
ob_start();
session_start();
ini_set('display_errors', 1);
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
echo "Started";
getAllParentsFromAllXLS();

/**getting all final xls files**/
function getAllParentsFromAllXLS()
{
	global $global_parents_data,$global_parents_data_file;
	
	$path = CAROLL_WRITER_FILE_PATH."/Writer_Final_*";
	$arraySource = glob($path);
	//sort($arraySource);
	
	usort($arraySource, function($a, $b) {
		return filemtime($a) > filemtime($b);
	});
	//To many files in Array giving Internal server Error so Added natch system here
	//Segregated array in multiples of 1000 and then processed
	
	$countArr=count($arraySource);
	$batchArr=array();
	$start=0;
	$limit= (int)($countArr/1000);
	for($bi=1;$bi<=($limit+1);$bi++){
		if($bi==($limit+1)){
			$end=($end+($countArr%1000));
		}else{
			$end=($bi*1000);
		}
		//echo $start." to ".$end."<br />";
		
		$batchArr[$bi]=array_slice($arraySource,$start,$end);	
		$start=$end+1;		
	}
	
	//$arraySource=array_slice($arraySource, 0, 1000);
	//echo "<pre>";	print_r($batchArr);echo "</pre>";exit;
	//getExceldata($arraySource[0],'final');	
	unset($arraySource);
	foreach($batchArr as $bk=>$val){
		foreach($val as $key=> $file)
		{	
			//if($key<=2){ //Remove 
				//$file=$excel;
				$basename=basename($file);
				$info=pathinfo($file);
				$xlsdata=getExceldata($file,'final');
				$tempArray=array(
					'file'=>$basename,
					'data'=>$xlsdata
					);
				
				$txtfilename="";
				$content = json_encode($tempArray);
				$fp = fopen(CAROLL_PATH."/writer_texts/".$info['filename'].".txt","wb");
				fwrite($fp,$content);
				fclose($fp);
				
			//}//Remove Temp
		
		}
		unset($val);
	}
}

/**getting all parent from final xls file**/
function getExceldata($file,$type)			
{
	
	$fileInfo  =   pathinfo($file) ;
		
	if($type=='final')
		$rindex=6;
        
	if($fileInfo['extension']=='xls')
	{
		require_once(INCLUDE_PATH."/reader.php");
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file);
		
		return $data1->sheets[0];
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
		
		return $xlsArr1[0];
	}
}
?>
