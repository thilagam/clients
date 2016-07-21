<?php
ob_start();
session_start();
ini_set('display_errors', 1);
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");

if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
{
	downloadXLS($_GET['file']);
	exit;
}	

if(isset($_POST['upload']))
{	
	
	$ref1 = $_POST['index_reference'];
	
	
	$file1	=	pathinfo($_FILES['userfile1']['name']) ;

	if($file1['extension']=='xls' || $file1['extension']=='xlsx' || $file1['extension']=='csv')
	{
		if($file1['extension'] == 'xlsx')
		{
			$xls1Arr  =	xlsxRead($_FILES['userfile1']['tmp_name'],$ref1) ;
			$xls1Arr=array_values(array_unique($xls1Arr));
			//echo "<pre>";print_r($xls1Arr);exit;
			
		}
		else if($file1['extension'] == 'csv')
		{
			$xls1Arr  =	csvRead($_FILES['userfile1']['tmp_name'],$ref1) ;
			$xls1Arr=array_values(array_unique($xls1Arr));			
		}
		else
		{
			$xls1Arr  =	xlsRead($_FILES['userfile1']['tmp_name'],$ref1) ;
			$xls1Arr=array_values(array_unique($xls1Arr));			
		}		
		
		/**get All images as array **/
		$array_all_images_list=getAllFolderImages();
		
		/**get all parents from previous folder**/
		$global_parents_data=array();
		$global_parents_data_file=array();
		//getAllParentsFromAllXLS();
		$global_parents_data=array_values(array_unique($global_parents_data));
		//echo "<pre>";print_r($global_parents_data);echo "</pre>||";exit;
		list($ep_sku,$ep_source_xls)=getPricedenoteData();
		//echo "<pre>";print_r($ep_source_xls);echo "</pre>||";exit;
		list($caroll_source_xls)=getReferentialData($ep_sku,$ep_source_xls);
		//echo count($caroll_source_xls);
		//echo "<pre>";print_r($caroll_source_xls);echo "</pre>||";exit;
		
		$table='<table class="table table-bordered">
				<tr>
					<th>Reference</th>
					<th>URL</th>
					<th>EP Source</th>
					<th>Caroll Source</th>
					<th>Matched in File</th>
				</tr>';	
			
		
		
		$xls_array=array();
		
		$xls_array[0][0]='Reference';
		$xls_array[0][1]='URL';
		$xls_array[0][2]='EP Source';
		$xls_array[0][3]='Caroll Source';
		$xls_array[0][4]='Matched in File';
		$xls_array[0][5]=0;
		
		$i=0;
		foreach($xls1Arr as $reference)
		{
			if($reference)
			{
				$error=0;
				$reference=trim($reference);
				$reference=substr($reference, 0, 5);
				//$reference=
				//cheking match in FTP
				//echo $reference;
				//echo "<pre>"; print_r($array_all_images_list);exit;
				$matched_reference=array_values(preg_grep_keys("/$reference/", $array_all_images_list));
				$matched_folder=$matched_reference[0];		
				//array_key_exists($reference, $array_all_images_list);//
				if($matched_folder)
					//$url='<a href="http://korben.edit-place.com/CAROLL/'.$matched_folder.'/'.$reference.'">http://korben.edit-place.com/CAROLL/'.$matched_folder.'/'.$reference.'</a>';
					$url='<a href="'.CAROLL_URL.'/view-pictures.php?client=CAROLL&reference='.$reference.'">'.CAROLL_URL.'/view-pictures.php?client=CAROLL&reference='.$reference.'</a>';
				else	
				{
					$url='Not Matched';
					$error=1;
				}	
					
				//cheking match in EP source
				//echo "<pre>"; print_r($ep_source_xls);exit;
				$ep_matched_reference=array_values(preg_grep_keys("/$reference/", $ep_source_xls));
				//$ep_ref=is_val_exists($reference,$ep_source_xls);
				$ep_ref=array_key_exists($reference, $ep_source_xls);
				//print_r($ep_matched_reference);exit;
				//if(count($ep_matched_reference)>0)
				if($ep_ref){
					$caroll_matched= "YES"; 
					//echo $caroll_matched;
				}
				else
				{
					$caroll_matched= "NO";
					$error=1;
				}
				//exit;
				//cheking match in LBM source
				//echo "<pre>"; print_r($caroll_source_xls);exit;
				$caroll_matched_reference=array_values(preg_grep_keys("/$reference/", $caroll_source_xls));
				$ep_src=is_val_exists($reference,$caroll_source_xls);
				//if(count($caroll_matched_reference)>0)
				if($ep_src)
				{
					$ep_matched= "YES"; 				
				}	
				else 
				{
					$ep_matched= "NO";
					$error=1;
				}
//echo "<pre>";print_r($ep_source_xls);echo "####";print_r($ep_matched_reference);
//echo "####";print_r($caroll_source_xls);exit;	
					
				if($error==1)
					$table.='<tr class="error">';	
				else
					$table.='<tr>';
					
				$table.='<td>'.$reference.'</td>';
				
				$table.= '<td>'.$url.'</td>';
				
				$table.= '<td>'.$ep_matched.'</td>';
				
				$table.= '<td>'.$caroll_matched.'</td>';
				
				$table.= '<td>'.$global_parents_data_file[$reference].'</td>';
				
				
				$table.='</tr>';
				
				//creating XLS array
				$xls_array[$i+1][0]=$reference;
				if($url!='Not Matched')
					$xls_array[$i+1][1]=CAROLL_URL.'/view-pictures.php?client=CAROLL&reference='.$reference;
				else	
					$xls_array[$i+1][1]=$url;
				$xls_array[$i+1][2]=$ep_matched;
				$xls_array[$i+1][3]=$caroll_matched;
				$xls_array[$i+1][4]=$global_parents_data_file[$reference];
				$xls_array[$i+1][5]=$error;
				
				$i++;
			}		
		
		}
		$table.='</table>';
		
		$_SESSION['table_data']=$table;
		
		 if($filename = WriteXLS($xls_array)) {
		  header("Location:check_reference.php?msg=success&file=".$filename);
		} else {
		  header("Location:check_reference.php?msg=error");
		}	
		
		//header("Location:check_reference.php");exit;		
		
	}
}

//xlsx lbm file reading
function xlsxRead($file_path, $ref)
{
	require_once(INCLUDE_PATH."/simplexlsx.class.php");
	$xlsx = new SimpleXLSX($file_path);
	
	for($j=1;$j <= $xlsx->sheetsCount();$j++){
	
		list($cols) = $xlsx->dimension($j);
		$sheet_rows=$xlsx->rows($j);
		//echo "<pre>";	print_r($sheet_rows);exit;
		
		if(count($xlsx->rows($j))>0)
		{
			$row=0;			
			foreach( $xlsx->rows($j) as $k => $r) {
				for( $i = 0; $i < $cols; $i++) {					
					if($i==($ref-1))					
					{														
						$reference=( isset($r[$i]) ? ($r[$i]) : '' );
						if($reference && $reference!=1)
							$xls_index_array[]= $reference;
					}	
					
				}		
				$row++;
			}
		}
	}
	//echo "<pre>";print_r($xls_index_array);exit;
	return $xls_index_array;
	
}


function xlsRead($file,$index)
{
	require_once(INCLUDE_PATH."/reader.php");
	
	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF8');
	$data->read($file);	
	
	$bound_sheets=$data->boundsheets;
	$sheets = sizeof($data->sheets);

	for ($i = 0; $i < $sheets; $i++) {
		$sheetname[$i]=$bound_sheets[$i]['name'];
		if (sizeof($data->sheets[$i]['cells'])>0) {
			$x = 1;
			while ($x <= sizeof($data->sheets[$i]['cells'])) {
				$y = 1;
				while ($y <= $data->sheets[$i]['numCols']) {
					$data->sheets[$i]['cells'][$x][$y] = (str_replace('', '-', $data->sheets[$i]['cells'][$x][$y])) ;
					if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
					{
						$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';
			//$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
					}   else   {
						$xls_array[$i][$x][$y] = isset($data->sheets[$i]['cells'][$x][$y]) ? ((mb_detect_encoding($data->sheets[$i]['cells'][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $data->sheets[$i]['cells'][$x][$y]) : $data->sheets[$i]['cells'][$x][$y]) : '';
						//$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
					}
					if(strlen($xls_array[$i][$x][$y])>strlen(utf8_decode($xls_array[$i][$x][$y])))
						$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
					else
						$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? utf8_encode($xls_array[$i][$x][$y]) : '';
					$y++;
				}
				if($x>1)	
					$xls_index_array[]=$xls_array[$i][$x][$index];
				$x++;
			}
		}
	}
	return $xls_index_array;
	//return array($xls_array, $sheetname) ;
}
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
			//if($key<=200){ //Remove 
				//$file=$excel;
				$basename=basename($file);
				//echo pathinfo($file);
				getExceldata($file,'final');
			//}//Remove Temp
		
		}
		unset($val);
	}
}
/**getting all parent from final xls file**/
function getExceldata($file,$type)			
{
		global $global_parents_data,$global_parents_data_file;

	$fileInfo  =   pathinfo($file) ;
		
	if($type=='final')
		$rindex=6;
        
	if($fileInfo['extension']=='xls')
	{
		require_once(INCLUDE_PATH."/reader.php");
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($file);
		//echo $data1->dump(TRUE,TRUE)	;exit;
		
		//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
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
		unset($data1);
		//echo "<pre>";print_r($global_parents_data);exit;
		//return $excel_data;
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
							
								$reference=isset($xlsArr1[$i][$x][$y-1]) ? $xlsArr1[$i][$x][$y-1] : '';
								$global_parents_data[]=$reference;
								$global_parents_data_file[$reference]=basename($file);
							
							}
		                $y++;
		            }
		            $x++;
		        }
		    }
		}
		unset($objReader);
	}
}
function getPricedenoteData()
{
	/**getting EP Source**/
	
	
	$ref_file = CAROLL_EP_CONFIG_FILE;
	$xls_file='CAROLL.xls';
	
	
	
	if(!file_exists($ref_file))
	{
		$fp = fopen($ref_file,"w");
		fclose($fp);
	}
	$arr_ref_soc = unserialize(file_get_contents($ref_file));	
	$reference=$arr_ref_soc[$xls_file];
	//$xls_file='LEBONMARCHE_Test.xls';
	
	
		require_once(INCLUDE_PATH."/reader.php");		
		$ep_sku=array();		
	
	
		$master_xls=CAROLL_EP_SOURCE_PATH."/$xls_file";
	
		/***********Getting File1 Data**********************/
	
		$data1 = new Spreadsheet_Excel_Reader();
		$data1->setOutputEncoding('Windows-1252');
		$data1->read($master_xls);
		//echo $data1->dump(TRUE,TRUE)	;exit;
		
		//echo "Number of sheets: " .sizeof($data1->sheets) . "\n";exit;
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
							$ref_brand=trim(str_replace("?","",$data1->sheets[$i]['cells'][$x][$reference]));		
							$ref_brand=trim(str_replace(" ","",$ref_brand));
							
							//echo $ref_brand."AND".$y."<br />";
							
							if($ref_brand && !in_array($y,$removeColumns))
							{
								if(!$ep_source_xls[$ref_brand][$y-1])									
									$ep_source_xls[$ref_brand][$y-1]=isset($data1->sheets[$i]['cells'][$x][$y])?$data1->sheets[$i]['cells'][$x][$y]:'';
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
	//$ref_file = CAROLL_REF_CONFIG_FILE."/caroll_config.txt";
	$ref_file = CAROLL_REF_CONFIG_FILE;
	$xls_file='CAROLL_SOURCE.xls';

	if(!file_exists($ref_file))
	{
		$fp = fopen($ref_file,"w");
		fclose($fp);
	}
	$arr_ref_soc = unserialize(file_get_contents($ref_file));	
	$reference=$arr_ref_soc[$xls_file];		
	//echo "<pre>";	print_r($arr_ref_soc);echo "</pre>";exit($ref_file);
	
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
								$caroll_source_xls[$ref_brand][$y-1]=$url."/".$ref_brand;
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
		//echo "<pre>";	print_r($matched_array);echo "</pre>";exit;		
		
		return array($caroll_source_xls);

}

//get all folder images other than given folder**/
function getAllFolderImages()
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
				if(is_dir($a) && $dir_name!='caroll-xml' && $dir_name!='caroll-xml-log')
				{
					foreach($array1 as $file)
					{						
						if(!is_dir($file))
						{
							$string = basename($file);
							
							$s1=array_reverse(explode("-",$string));
							//$array_list[]=$s[0];
							//$s=implode("_",$s);
							$s=$s1[1];
							if($s)
							{
								//echo basename($a)."<br>";
								$dir_name=basename($a);
								//$array_list[]=$s;
								$array_list[$s]=$dir_name;
							}	
						}	
					}
				}
				
				
			}
			//$array_list=array_values(array_unique($array_list));
		}
	//echo "<pre>";print_r($array_list);
	//exit;
	return $array_list;
	
}
function preg_grep_keys( $pattern, $input, $flags = 0 )
{
    $keys = preg_grep( $pattern, array_keys( $input ), $flags );
    $vals = array();
    foreach ( $keys as $key )
    {
        $vals[$key] = $input[$key];
		break;
    }	
    return $vals;
}
function getOS($userAgent) {
	// Create list of operating systems with operating system name as array key
	$oses = array('iPhone' => '(iPhone)', 'Windows' => 'Win16', 'Windows' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
	'Windows' => '(Windows 98)|(Win98)', 'Windows' => '(Windows NT 5.0)|(Windows 2000)', 'Windows' => '(Windows NT 5.1)|(Windows XP)', 'Windows' => '(Windows NT 5.2)', 'Windows' => '(Windows NT 6.0)|(Windows Vista)', 'Windows' => '(Windows NT 6.1)|(Windows 7)', 'Windows' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)', 'Windows' => 'Windows ME', 'Open BSD' => 'OpenBSD', 'Sun OS' => 'SunOS', 'Linux' => '(Linux)|(X11)', 'Safari' => '(Safari)', 'Macintosh' => '(Mac_PowerPC)|(Macintosh)', 'QNX' => 'QNX', 'BeOS' => 'BeOS', 'OS/2' => 'OS/2', 'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)');

	foreach ($oses as $os => $pattern) {// Loop through $oses array

		// Use regular expressions to check operating system type
		if (strpos($userAgent, $os)) {// Check if a value in $oses array matches current user agent.
			return $os;
			// Operating system was matched so return $oses key
		}
	}
	return 'Unknown';
	// Cannot find operating system so return Unknown
}
/**Create XLS File**/
function WriteXLS($data)
{
	$refdir = CAROLL_REF_SEARCH_PATH."/";

	// include package
	include_once 'Spreadsheet/Excel/Writer.php';
	// create empty file
	$filename="Reference_search_Caroll_".time();
	$excel = new Spreadsheet_Excel_Writer($refdir.$filename.".xls");
    $excel->setVersion(8) ;
	
	
    $excel->setCustomColor(22, 217, 151,149);
    $excel->setCustomColor(12, 252, 213,180);
	$excel->setCustomColor(13, 185, 0,0);
	
	
	
	// create format for header row
	// bold, red with black lower border
	$header_f=array(
            'bold'=>'1',
			'size' => '10',
            'color'=>'black',
            'FgColor'=>'22',
			'border'=>'1',
            'align' => 'center',
            'valign' => 'top'); 
	$header =& $excel->addFormat($header_f);
	$cell_f=array(
              'Size' => 10,
            //'FgColor'=>'12',
              'valign' => 'top'); 
	$cell =& $excel->addFormat($cell_f);

	$cell_e=array(
              'Size' => 10,
            'FgColor'=>'13',
              'valign' => 'top'); 
	$cell_error =& $excel->addFormat($cell_e);
    
	$rowCount=0;
	
	//echo "<pre>";print_r($data);exit;
	$sheet =& $excel->addWorksheet();
	
	foreach ($data as $row) {
	  foreach ($row as $key => $value) {
		$value= (str_replace("", "'", $value)) ;
		
		$error=$data[$rowCount][5];
		
			if($error==1)
			{
			
				if($rowCount==0 && $key!=5)
				{
					$sheet->write($rowCount, $key, $value,$header);
				}
				else if($rowCount>0 &&$key==1)
				{
					$sheet->writeUrl($rowCount,$key,$value,'',$cell_error);
				}
				else if($key==5)
				{
					continue;
				}		
				else		
					$sheet->write($rowCount, $key, $value,$cell_error);
					
				
			}	
			else
			{	
				if($rowCount==0 && $key!=5)
				{
					$sheet->write($rowCount, $key, $value,$header);
				}
				else if($rowCount>0 &&$key==1)
				{
					$sheet->writeUrl($rowCount,$key,$value,'',$cell);
				}
				else if($key==5)
				{
					continue;
				}		
				else		
					$sheet->write($rowCount, $key, $value,$cell);
			
			}
		}	
	  $rowCount++;
	}
    
    
	// save file to disk
	if ($excel->close() === true) {
		return $filename ;
	} else {
		return false ;
	}
}
function downloadXLS($filename)
{
	$filename=$filename.".xls";
	$path_file=CAROLL_REF_SEARCH_PATH."/".$filename;
	//echo $path_file;exit;
	if(file_exists($path_file))
	{	
		
		header("Content-type: application/xls");
		header("Content-Disposition: attachment; filename=$filename");
		ob_clean();
        flush();
		readfile("$path_file"); 
		exit;
	}	
	else
		header("Location:check_reference.php");
}

function is_val_exists($needle, $haystack) {
     if(in_array($needle, $haystack)) {
          return true;
     }
     foreach($haystack as $element) {
          if(is_array($element) && is_val_exists($needle, $element))
               return true;
     }
   return false;
}
