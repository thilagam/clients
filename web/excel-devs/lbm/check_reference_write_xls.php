<?php
ob_start();
session_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path1.php");
include_once(INCLUDE_PATH."/common_functions1.php");

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
		//echo "<pre>";print_r($xls1Arr);echo "</pre>";exit;
		
		
		/**get All images as array **/
		$array_all_images_list=getAllFolderImages();
		echo "<pre>";print_r($array_all_images_list);echo "</pre>";exit;
		
		/**get all parents from previous folder**/
		$global_parents_data=array();
		$global_parents_data_file=array();
		getAllParentsFromAllXLS();
		$global_parents_data=array_values(array_unique($global_parents_data));
		
		//echo implode("<br>",$global_parents_data);exit;
		//echo "G<pre>";print_r($global_parents_data_file);echo "</pre>";exit;
		
		list($ep_sku,$ep_source_xls)=getPricedenoteData();
		//echo "<pre>";print_r($ep_source_xls);echo "</pre>";exit;
		//exit;
		list($lbm_source_xls)=getReferentialData($ep_sku,$ep_source_xls);
		//echo "<pre>";print_r($lbm_source_xls);echo "</pre>";exit;
		
		$table='<table class="table table-bordered">
				<tr>
					<th>Reference</th>
					<th>URL</th>
					<th>EP Source</th>
					<th>LBM Source</th>
					<th>Matched in File</th>
				</tr>';	
			
		
		
		$xls_array=array();
		
		$xls_array[0][0]='Reference';
		$xls_array[0][1]='URL';
		$xls_array[0][2]='EP Source';
		$xls_array[0][3]='LBM Source';
		$xls_array[0][4]='Matched in File';
		$xls_array[0][5]=0;
		
		$i=0;
		foreach($xls1Arr as $reference)
		{
			if($reference)
			{
				$error=0;
				//cheking match in FTP
				$matched_reference=array_values(preg_grep_keys("/$reference/", $array_all_images_list));
				$matched_folder=$matched_reference[0];		
				
				if($matched_folder)				
					$url='<a href="'.LBMCHE_URL.'/view-pictures.php?client=LBM&reference='.$reference.'">'.LBMCHE_URL.'/view-pictures.php?client=LBM&reference='.$reference.'</a>';
				else	
				{
					$url='Not Matched';
					$error=1;
				}	
					
				//cheking match in EP source
				$ep_matched_reference=array_values(preg_grep_keys("/$reference/", $ep_source_xls));
				if(count($ep_matched_reference)>0)
					$ep_matched= "YES"; 
				else
				{
					$ep_matched= "NO";
					$error=1;
				}
				
				//cheking match in LBM source
				$lbm_matched_reference=array_values(preg_grep_keys("/$reference/", $lbm_source_xls));
				if(count($lbm_matched_reference)>0)
				{
					$lbm_matched= "YES"; 				
				}	
				else 
				{
					$lbm_matched= "NO";
					$error=1;
				}	
					
				if($error==1)
					$table.='<tr class="error">';	
				else
					$table.='<tr>';
					
				$table.='<td>'.$reference.'</td>';
				
				$table.= '<td>'.$url.'</td>';
				
				$table.= '<td>'.$ep_matched.'</td>';
				
				$table.= '<td>'.$lbm_matched.'</td>';
				
				$table.= '<td>'.$global_parents_data_file[$reference].'</td>';
				
				
				$table.='</tr>';
				
				//creating XLS array
				$xls_array[$i+1][0]=$reference;
				$xls_array[$i+1][1]=LBMCHE_URL.'/view-pictures.php?client=LBM&reference='.$reference;
				$xls_array[$i+1][2]=$ep_matched;
				$xls_array[$i+1][3]=$lbm_matched;
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
			//echo $file;
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
function getPricedenoteData()
{
	$ref_file = LBMCHE_EP_CONFIG_FILE;
	$xls_file='LEBONMARCHE.xls';
	if(!file_exists($ref_file))
	{
		$fp = fopen($ref_file,"w");
		fclose($fp);
	}
	$arr_ref_soc = unserialize(file_get_contents($ref_file));	
	$reference=$arr_ref_soc[$xls_file];

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
	$parent_array=array();
	require_once(INCLUDE_PATH."/simplexlsx.class.php");
	$xlsx = new SimpleXLSX($master_lbm_xls);						
	for($j=1;$j <= $xlsx->sheetsCount();$j++){
		list($cols) = $xlsx->dimension($j);
		$sheet_rows=$xlsx->rows($j);			
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
				}
				if($row>0)
				{
					
					if(!$sheet_rows[$row][$reference_lbm])
					{
						$lbm_source_xls[$ref_brand][$reference_lbm]=$sheet_rows[$row][$reference_lbm-1];
					}	
					$parent_cell=$lbm_source_xls[$ref_brand][$reference_lbm];				
				}
				$row++;
			}
		}
	}
	return array($lbm_source_xls);
}
/**function to get all childs of all parent**/
function getChildsku($lbm_source_xls)
{	
	 foreach($lbm_source_xls as $key=>$lbm_array)
	 {
		$parent=$lbm_array[2];
		$childs_array[$parent][]=$lbm_array[1];	 
	 }
	return $childs_array;
}
//get all folder images other than given folder**/
function getAllFolderImages()
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
	$refdir = LBMCHE_REF_SEARCH_PATH."/";

	// include package
	include_once 'Spreadsheet/Excel/Writer.php';
	// create empty file
	$filename="Reference_search_".time();
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
		$value= (str_replace("’", "'", $value)) ;
		
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
	$path_file=LBM_REF_SEARCH_PATH."/".$filename;
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
					$data->sheets[$i]['cells'][$x][$y] = (str_replace('–', '-', $data->sheets[$i]['cells'][$x][$y])) ;
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
