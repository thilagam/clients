<?php 
	if(isset($_POST['path'])){


		ob_start();
		define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
		define("INCLUDE_PATH",ROOT_PATH."/includes");
		include_once(INCLUDE_PATH."/basiclib.php");
		include_once(INCLUDE_PATH."/common_functions.php");
		$basiclib=new basiclib();
		$pathArray=array(
						'1'=>ROOT_PATH."/CLIENTS/",
						'2'=>ROOT_PATH."/excel-devs/",
						'3'=>ROOT_PATH."/admin/",
						'4'=>ROOT_PATH."/includes/",
						'5'=>ROOT_PATH."/bnp/",
						'6'=>ROOT_PATH."/"
						);
		$path=$pathArray[$_POST['path']];//ROOT_PATH."/CLIENTS/";
		$savepath=ROOT_PATH."/test2/tree/".uniqid().".xlsx";
		$ref_directory=glob($path."*", GLOB_ONLYDIR);
		//echo "<pre>"; print_r($ref_directory);   
		$index=1;
		$countsArray=array(array('Directory','Files Count'));
		$countsArray[]=array('Total',getFileCount($path));
		foreach($ref_directory as $key => $value){
			$basename=basename($value);
			$value=$value."/";
				$temp=array();
				//$temp[]=$key+1;
				$temp[]=$basename;
				$temp[]=getFileCount($value);
				$countsArray[]=$temp;
				//echo "<pre>"; print_r($fileCount);
			//}
			//$index++;
		} 
		//echo "<pre>"; print_r($countsArray);exit;

		writeDownXlsx($countsArray,$savepath);
		// define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
		// $path=ROOT_PATH."/CLIENTS/AGATHA";
		// echo "<pre>"; print_r(getFileCount($path));
	}else{
	?>
	
<html>
	<head></head>
	<body style="">
		<div style="width:1000px;margin:0 auto">
		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
			<h1>Counts Dev</h1>
			<h5>Select to get counts </h5>
			<select name='path' >
				<option value='1'>Images FTP</option>
				<option value='2'>Devs</option>
				<option value='3'>PD1.5</option>
				<option value='4'>Includes</option>
				<option value='5'>BNP</option>
				<option value='6'>All</option>
			</select>
			<br/>
			<br/>

			<input type="submit" name='submit' value="submit">
		</form>
		</div>

	</body>

</html>

	<?php

	}

	function getFileCount($path) {
	    $size = 0;
	    $ignore = array('.','..','cgi-bin','.DS_Store');
	    $files = scandir($path);
	    foreach($files as $t) {
	        if(in_array($t, $ignore)) continue;
	        if (is_dir(rtrim($path, '/') . '/' . $t)) {
	            $size += getFileCount(rtrim($path, '/') . '/' . $t);
	        } else {
	            $size++;
	        }   
	    }
	    return $size;
	}



    // prints out how many were in the directory
   // echo "There were $i files";
	//exec('tree -h /var/www/test > /var/www/test/tree/t.txt');
	/**
     * Get an array that represents directory tree
     * @param string $directory     Directory path
     * @param bool $recursive         Include sub directories
     * @param bool $listDirs         Include directories on listing
     * @param bool $listFiles         Include files on listing
     * @param regex $exclude         Exclude paths that matches this regex
     */
    function directoryToArray($directory, $recursive = true, $listDirs = false, $listFiles = true, $exclude = '') {
        $arrayItems = array();
        $skipByExclude = false;
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
            preg_match("/(^(([\.]){1,2})$|(\.(svn|git|md))|(Thumbs\.db|\.DS_STORE))$/iu", $file, $skip);
            if($exclude){
                preg_match($exclude, $file, $skipByExclude);
            }
            if (!$skip && !$skipByExclude) {
                if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                    if($recursive) {
                        $arrayItems = array_merge($arrayItems, directoryToArray($directory. DIRECTORY_SEPARATOR . $file, $recursive, $listDirs, $listFiles, $exclude));
                    }
                    if($listDirs){
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
                        $arrayItems[] = $file;
                    }
                } else {
                    if($listFiles){
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
                        $arrayItems[] = $file;
                    }
                }
            }
        }
        closedir($handle);
        }
        return $arrayItems;
    }

?>
