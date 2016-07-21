<?
/**
 * Basic Lib is a PHP-functions library. It is collection of commonly used functions 
 *
 * PHP versions 4 and 5
 *
 * @package    Edit-place
 * @copyright  Edit-place
 * @license    Edit-place
 * @version    1.0
 * @category   Library Class
 * @author 	   Vinayak Kadolkar
 */
class Basiclib
{

	
	/**
	  * Function normalise Url String 
	  * function used to Read Accented string & process it to normal without accents 
	  *	Based on OS the decoding will be used . for linux decoded character will be used to match
	  * @package clientsEditPlace
	  * @author Vinayak
	  * @param  string $str  
	  * @return string $str
	  *
	  */
	function normaliseUrlString($str){

			$table = array(
				'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
				'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
				'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
				'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a','œ'=>'c','Œ'=>'C', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
				'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
				'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
				'ÿ'=>'y','Ÿ'=>'Y', 'Ŕ'=>'R', 'ŕ'=>'r', ' '=>'_', "'"=>'_', '/'=>'','€'=>'','ü'=>'u','Ü'=>'U','Š'=>'S',','=>'',':'=>''
			);
			
			if ($this->getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
            {
				$table=$this->utf8dec_key_converter($table);
			}
			
		   $str = strtr($str, $table);
				
			// get rid of any remaining unwanted characters
			$str = preg_replace("[^A-Za-z0-9/-/_]", "", $str);
			
			// remove repeated underscores
			$str = preg_replace('/[_]+/', '_', $str);
			if ($this->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
            {	
				try{
					$str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
				} catch (Exception $e) {}
			}else{
			   $str = iconv('iso-8859-1', 'ASCII//TRANSLIT//IGNORE', $str);
			}
			return $str; 
	}
	/**
	  * Function utf8dec_key_converter
	  * function used to decode all array keys from utf8 
	  * 
	  * @package clientsEditPlace
	  * @author Vinayak
	  * @param  string $str  
	  * @return string $str
	  *
	  */
	function utf8dec_key_converter($array)
	{	$arr=array();
		foreach($array as $key => $val){
			if($key=="Ÿ" || $key=="œ" || $key=="Œ" || $key=="€" || $key=="Š"){
				$arr[htmlentities($key)]=$val;
			}else{
				$arr[utf8_decode($key)]=$val;
			}
		}
		return $arr;
	}
	/**
	  * Function utf8dec_val_converter
	  * function used to decode all array values from utf8 
	  * 
	  * @package clientsEditPlace
	  * @author Vinayak
	  * @param  string $str  
	  * @return string $str
	  *
	  */
	function utf8dec_val_converter($array)
	{	$arr=array();
		foreach($array as $key => $val){
			$arr[$key]=utf8_decode($val);
		}
		return $arr;
	}
		
	/**
	  * Function utf8_converter
	  * function used to recursive encode array values to utf8
	  * 
	  * @package clientsEditPlace
	  * @author Vinayak
	  * @param  string $str  
	  * @return string $str
	  *
	  */
	function utf8_converter($array)
	{
		array_walk_recursive($array, function(&$item, $key){
			if(!mb_detect_encoding($item, 'utf-8', true)){
					$item = utf8_encode($item);
			}
		});
	 
		return $array;
	}

	/**
	  * Function xlsx_read to read xlsx files and return array
	  * It will rrequired PHP Excel class 
	  * The Values will be given with decoded format for windows & linux with 2 different encoding & decoding pattern
	  * 
	  * 
	  * @package ClientsEditPlace
	  * @author  Vinayak
	  * @autor   Arun
	  * @param   string $file  path of the file   
	  * @return  array 
	  *
	  */
	function xlsx_read($file)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
       // $count=1;
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                    $y = 1;
                    while ($y <= sizeof($xlsArr1[$i][$x])) {
                        
                        $xls_array[$i][$x][$y] = str_replace('–', '-', $xlsArr1[$i][$x][$y-1]) ;
                        /**
                         * Based on Client OS the Encoding & Decoding Pattern changes  
                         * */
                        if ($this->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
							$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $xls_array[$i][$x][$y]=html_entity_decode(htmlentities($xls_array[$i][$x][$y], ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
                        }
						$xls_array[$i][$x][$y] =str_replace("\0", "", $xls_array[$i][$x][$y]);
                       // $count++;
                        $y++;
                    }
                    $x++;
                }
            }
        }
        //echo $count;exit;
        return array($xls_array, $sheetname);
    }

    /**
	  * Function xlsx_read to read xlsx files and return array
	  * It will rrequired PHP Excel class 
	  * The Values will be given with decoded format for windows & linux with 2 different encoding & decoding pattern
	  * 
	  * 
	  * @package ClientsEditPlace
	  * @author  Vinayak
	  * @autor   Arun
	  * @param   string $file  path of the file   
	  * @return  array 
	  *
	  */
	function xlsx_optimised_read($file,$maxcol=0,$maxrow=0)
    {
        require_once (INCLUDE_PATH."/PHPExcel.php");
        
        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($file);
        $sheetname = $objPHPExcel->getSheetNames();
        foreach ($objPHPExcel->getWorksheetIterator() as $objWorksheet) {
            $xlsArr1[] = $objWorksheet->toArray(null,true,true,false);
        }
       // $count=1;
        for ($i = 0; $i < sizeof($xlsArr1); $i++) {
            if (sizeof($xlsArr1[$i])>0) {
                $x = 0;
                while ($x < sizeof($xlsArr1[$i])) {
                	if($maxrow==0 || $maxrow>=$x){
	                    $y = 1;
	                    while ($y <= sizeof($xlsArr1[$i][$x])) {
	                        if($maxcol==0 || $maxcol>=$y){
		                        $xls_array[$i][$x][$y] = str_replace('–', '-', $xlsArr1[$i][$x][$y-1]) ;
		                        /**
		                         * Based on Client OS the Encoding & Decoding Pattern changes  
		                         * */
		                        if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
		                        {
		                            $xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? ((mb_detect_encoding($xls_array[$i][$x][$y]) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $xls_array[$i][$x][$y]) : $xls_array[$i][$x][$y]) : '';
									$xls_array[$i][$x][$y] = isset($xls_array[$i][$x][$y]) ? html_entity_decode($xls_array[$i][$x][$y],ENT_QUOTES,"UTF-8") : '';
		                        }   else   {
		                            $xls_array[$i][$x][$y]=html_entity_decode(htmlentities($xls_array[$i][$x][$y], ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
		                        }
								$xls_array[$i][$x][$y] =str_replace("\0", "", $xls_array[$i][$x][$y]);
		                       // $count++;
							}
	                        $y++;
	                    }
                	}
                    $x++;
                }
            }
        }
        //echo $count;exit;
        return array($xls_array, $sheetname);
    }


	/**
	  * Function write_Xlsx to write xlsx files and return file
	  * It will rrequired PHP Excel class 
	  * The Values will be given with decoded format for  linux with different decoding pattern and for windows it will be not changed
	  * 
	  * 
	  * @package ClientsEditPlace
	  * @author  Vinayak
	  * @param   string $file_path  path at which file to be created with file name
	  * @param   array  $data  data
	  * @param   array $anchor_cols array of coulumns
	  * @return  string $file_path new file path after writing 
	  *
	  */
     function writeXlsx($data,$file_path, $anchor_cols=null)
    {
        $anchorCols = array_filter(explode(",", $anchor_cols)) ;
        /** PHPExcel */
        include_once INCLUDE_PATH.'/PHPExcel.php';
        
        /** PHPExcel_Writer_Excel2007 */
        include_once INCLUDE_PATH.'/PHPExcel/Writer/Excel2007.php';
        
        /* Create new PHPExcel object*/
        $objPHPExcel = new PHPExcel();
        
        /* Set properties*/
        $objPHPExcel->getProperties()->setCreator("Edit-Place");
        
        /* Add some data */
        $objPHPExcel->setActiveSheetIndex(0);

        $rowCount=0;
        foreach ($data as $row)
        {
            $col = 'A';
            foreach ($row as $key => $value)
            {	/* Based on OS Apply Encoding */
				if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
                {      
					$value = iconv("ISO-8859-1", "UTF-8", $value) ;
					$value = str_replace("", htmlentities("œ"), $value) ;
					$value = str_replace("", "'", $value) ;
					$value = str_replace("", "'", $value) ;
					$value = html_entity_decode(htmlentities($value,  ENT_QUOTES, 'UTF-8'), ENT_QUOTES ,mb_detect_encoding($value));
					$value=html_entity_decode($value);
					//$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
					//$value = isset($value) ? html_entity_decode(htmlentities($value,ENT_QUOTES,"UTF-8")) : '';
                        
				}
				//$value=str_replace("_x0019_","'",$value);
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='A')) || (in_array($col, $anchorCols)))
                {
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setUrl($value);
                    $objPHPExcel->getActiveSheet()->getCell($col.($rowCount+1))->getHyperlink()->setTooltip($value);
                }
                $col++;
            }
            $rowCount++;
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
        
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
    }
	/**
	  * Function create Zip with predefined Extension
	  * 
	  * @package ClientsEditPlace
	  * @author  Vinayak
	  * @param   string $srcPath  path at which file to be created with file name
	  * @param   string  $srcFile  name of the file 
	  * @param   string $ext  extension of file to consider in zip
	  * @return  nill
	  */
    function zip_creation($srcPath, $srcFile,$ext){
			if ($handle = opendir($srcPath)) {
					
				$zip = new ZipArchive(); // Load zip library 
				$zip_name = $srcFile; // Zip name
				//echo $zip_name;exit;
				
				if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE)
				{ 
					// Opening zip file to load files
					//echo "failed"; exit;
					$error .= "* Sorry ZIP creation failed at this time";
				}
				//echo $error;
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						$zip_file=pathinfo($srcPath."$entry") ;
						//echo $zip_file['filename']."<br />";
						if($zip_file['extension']==$ext){
							//echo $srcPath.$zip_file['filename'].".".$zip_file['extension']."<br />";
							$zip->addFile($srcPath.$zip_file['filename'].".".$zip_file['extension'], $zip_file['filename'].".".$zip_file['extension']) ;
						}
					}
				}
				closedir($handle);
				$zip->close();
			}
		}

	/**
	  * Function create Zip with predefined Extension
	  * 
	  * @package ClientsEditPlace
	  * @author  Vinayak
	  * @param   string $srcPath  path at which file to be created with file name full path
	  * @param   string  $srcFile  name of the file full path
	  * @param   string $ext  extension of file to consider in zip
	  * @return  nill
	  */
    function zip_creationMultiFolder($srcPath, $srcFile,$ext)
    {
		// Get real path for our folder
		$rootPath = realpath($srcPath);

		// Initialize archive object
		$zip = new ZipArchive();
		$zip->open($srcFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

		// Create recursive directory iterator
		/** @var SplFileInfo[] $files */
		$files = new RecursiveIteratorIterator(
		    new RecursiveDirectoryIterator($rootPath),
		    RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file)
		{
		    // Skip directories (they would be added automatically)
		    if (!$file->isDir())
		    {
		        // Get real and relative path for current file
		        $filePath = $file->getRealPath();
		        $relativePath = substr($filePath, strlen($rootPath) + 1);

		        // Add current file to archive
		        $zip->addFile($filePath, $relativePath);
		    }
		}

		// Zip archive will be created only after closing object
		$zip->close();
	}

	function downloadTar($filename, $path, $rurl)
	{
		$path_file = $path."/".$filename ;
		if(file_exists($path_file))
		{
			header("Content-Type: application/x-gzip-compressed");
			header("Content-Disposition: attachment; filename=$filename");
			ob_clean();
			flush();
			readfile("$path_file"); 
			exit;
		}   
		else
			header("Location:index.php");
	}

	function correct_encoding($array) {
		$arr=array();
		foreach($array as $key => $val){
			$current_encoding = mb_detect_encoding('ę', 'auto');
			//echo $current_encoding;
			$arr[$key] = $this->utf82iso88592($val);
			//=utf8_decode($val);
		}
		return $arr;

	}

	function utf82iso88592($text) {
		$text = str_replace("\xC4\x85", '&#261;', $text);
		$text = str_replace("\xC4\x84", '&#260;', $text);
		$text = str_replace("\xC4\x87", '&#263;', $text);
		$text = str_replace("\xC4\x86", '&#262;', $text);
		$text = str_replace("ę", '&#281;', $text);
		$text = str_replace("\xC4\x98", '&#280;', $text);
		$text = str_replace("\xC5\x82", '&#322;', $text);
		$text = str_replace("\xC5\x81", '&#321;', $text);
		$text = str_replace("\xC3\xB3", '&oacute;', $text);
		$text = str_replace("\xC3\x93", '&Oacute;', $text);
		$text = str_replace("\xC5\x9B", '&#347;', $text);
		$text = str_replace("\xC5\x9A", '&#346;', $text);
		$text = str_replace("\xC5\xBC", '&#380;', $text);
		$text = str_replace("\xC5\xBB", '&#379;', $text);
		$text = str_replace("\xC5\xBA", '&#380;', $text);
		$text = str_replace("\xC5\xB9", '&#379;', $text);
		$text = str_replace("\xc5\x84", '&#379;', $text);
		$text = str_replace("\xc5\x83", '&#323;', $text);

		return $text;
		}

	//$text="ą Ą ć Ć Ę ł Ł ó Ó ś Ś ż Ż ż Ż ń Ń";
	//$text="&#261; &#260; &#263; &#262; &#280; &#322; &#321; &oacute; &Oacute; &#347; &#346; &#380; &#379; &#380; &#379; &#324; &#323;";

   function ISO_convert($array)
	{
		$array_temp = array();
		
		foreach($array as $name => $value)
		{
			if(is_array($value))
			  $array_temp[(mb_detect_encoding($name." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($name) : $name )] = ISO_convert($value);
			else
			  $array_temp[(mb_detect_encoding($name." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($name) : $name )] = (mb_detect_encoding($value." ",'UTF-8,ISO-8859-1') == 'UTF-8' ? utf8_decode($value) : $value );
		}

		return $array_temp;
	}

	function langEnc($text,$lang){
		switch($lang){
			case 'TR':
					
					$turkish = array("ı","İ" ,"ğ","Ğ", "ü","Ü", "ş","Ş", "ö","Ö", "ç","Ç","₤");//turkish letters
					$english   = array("&#305;","&#304;", "&#287;","&#286;","&#252;","&#220;", "&#351;","&#350;","&#246;","&#214;","&#231;","&#199;","&#8356;");//Turkish Html Converts

					$text = str_replace($turkish, $english, $text);
			break;
			case 'JP':
					$jap = array('&nbsp;');
					$english   = array("");

					$text = str_replace($jap, $english, $text);
			break;
			case 'PL':
					$text = $this->utf82iso88592($text);
			break;
			case 'it':
					$it = array('š','Š','a');
					$english   = array("&#x161;","&#x160;",'š');

					$text = str_replace($jit, $english, $text);
			break;		
		}
		return $text;
	}

	function replacements($text){
		$np = array("","" ,""," ", "","", "' ","","");
		$replacement   = array("œ","'", "-","-",""," ", "'","","",);
		$text = str_replace($np, $replacement, $text);
		return $text;
			
	}
	
	function remove_extracols($data){
		
		$high=0;
		$highkey='1';
		$newArray=array();
		$data=array_filter_recursive($data);
		foreach($data as $key => $row){
			if(!empty($row)){
				$count=count($row);
				$high=($count>$high)? $count : $high;
				$newArray = array_keys($row);
				$highkey=($count>=$high)? $newArray[$count-1] : $highkey;
			}
		}
		$newData=arrray();
		foreach($data as $key => $row){
			 $position = array_search($highkey, array_keys($categories));
			 if ($position !== false) {
					array_splice($row, ($position + 1));
				}
			 $newData[]=$row;
	
		}
		return $newData;
	}
	
	function start_time(){
			//place this before any script you want to calculate time
		return microtime(true);
	}
	
	function end_time($time_start){
		// Display Script End time
		$time_end = microtime(true);

		//dividing with 60 will give the execution time in minutes other wise seconds
		return($time_end - $time_start);

	}
	
	// Function to decompress zip file
function unzipfolder($file) {
        $zip_file = pathinfo($file);
        $zip_file['filename'] = str_replace(" ", "-", $zip_file['filename']);
        $path = $zip_file['dirname'] . "/" . $zip_file['filename'];
		
        if (!is_dir($path))
            mkdir($path, 0777, TRUE);
        chmod($path, 0777);
		
        $zip = new ZipArchive; // php zip archieve instance
        $res = $zip->open($file);
        if ($res === TRUE) {
            // extract it to the path we determined above
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry = $zip->getNameIndex($i);
                if ((substr($entry, -1) == '/') || strstr($entry, '__MACOSX'))
                    continue;

                $entry1 = str_replace(' ', '_', $entry);
                $fp = $zip->getStream($entry);
                $ofp = fopen($path . '/' . basename($entry1), 'w');
                if ($fp) {
                    while (!feof($fp))
                        fwrite($ofp, fread($fp, 8192));
                }
                fclose($fp);
                fclose($ofp);
            }
            return $path;
        } else {
            echo "Doh! I couldn't open $file";
        }
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

	
}
