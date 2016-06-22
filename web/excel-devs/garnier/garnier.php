 <?php


mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
define("INCLUDE_PATH",ROOT_PATH."/includes");
require_once(INCLUDE_PATH.'/basiclib.php');
include_once(INCLUDE_PATH."/config_path.php");
require_once(INCLUDE_PATH."/PHPWord.php");
include_once(GARNIER_PATH."/dbfunctions.php");
/**
 * Garnier Lib is a PHP-functions library used in Garnier Devs. It is collection of commonly used functions for Interrent devs
 *
 * PHP versions 5
 *
 * @package    Edit-place
 * @copyright  Edit-place
 * @license    Edit-place
 * @version    1.0
 * @category   Library Class
 * @author 	   Vinayak Kadolkar
 */
class Garnier 
{
	public $table='cl_garnier_gen';
	public $global_parents_data=array();
	public $global_parents_data_file=array();
	public $global_parents_xls_file=array();
	public $garnierUrl='';
	public $genarray=array();
	public $allImages=array();
	public $genRefs=array();



	/**
	* Function Uniquegenrefs get
	* function used to get references from GEN REF table
	*
	* @package clientsEditPlace
	* @author Vinayak
	* @retuen array $rows
	*/
	function get_uniquegenrefs(){
		$dbfunctions = new dbfunctions();
		$sql="SELECT `garnier_reference`
			   FROM `".$table."`
			   WHERE `garnier_status`=1";
		//echo $sql;
		$row=array();
		$res=$dbfunctions->mysql_qry($sql,1);
		while ($row = mysql_fetch_array($res, MYSQL_NUM)) {
			$rows[]=$row[0];
		}
		//$res=mysql_fetch_array($res);
		return $rows;
	}

	/**
	* Function globalUniqueGenRefs get
	* function used to get references from GEN REF table
	*
	* @package clientsEditPlace
	* @author Vinayak
	* @retuen array $rows
	*/
	function globalUniqueGenRefs(){
		$dbfunctions = new dbfunctions();
		$global_parents_data=array();
		$global_parents_data_file=array();
		$global_parents_xls_file=array();

		$sql="SELECT `garnier_reference`,`garnier_folder`,`garnier_filename`
			   FROM `".$this->table."`
			   WHERE `garnier_status`=1";
		//echo $sql;exit;
		$row=array();
		$res=$dbfunctions->mysql_qry($sql,1);
		while ($row = mysql_fetch_array($res, MYSQL_NUM)) {
			$global_parents_data[]=$row[0];
			$global_parents_data_file[$row[0]]=$row[1];
			$global_parents_xls_file[$row[0]]=$row[2];
		}
		//$res=mysql_fetch_array($res);
		return array($global_parents_data,$global_parents_data_file,$global_parents_xls_file);
	}

	/**
	 * Function updateNewGenRefs
	 * update new generated references to the DB
	 * @param array $data
	 * @param string $generatedFile
	 * @param array $options  	
	 * @return
	 */
	public function updateNewGenRefs($data,$generatedFile,$options)
	{
		if(empty($data) || $generatedFile=='' || empty($options))
		{
			return false;
		}
		/* Get Generated file information to add in DB */
		$fileinfo=pathinfo($generatedFile);
		$filetime= date("Y-m-d H:i:s", filemtime($generatedFile));
		$filename=$fileinfo['filename'];			

		$localRefArr=array();
		$localdoup=array();
		$insertArr=array();
		$gen_array=array();
		$status=array();
		//echo "<pre>"; print_r($data);exit;
		foreach ($data as $key => $value)
		{
			
			if(!empty($value[1]))
			{
				//$row['url']= $options['garnierUrl'].'/view-pictures.php?client=GARNIER&reference=' . $value[2] ;
				/* Gen array to insert in db */
				
				$gen_temp=array();
				$gen_temp[]=$value[8];
				$gen_temp[]=$this->escape_quote($value[19]);
				$gen_temp[]='';
				$gen_temp[]='';
				$gen_temp[]=$this->escape_quote(serialize($value));
				$gen_temp[]=$filename.".xlsx";
				$gen_temp[]=date('Y-m-d H:i:s',strtotime('now'));
				$gen_temp[]='NULL';
				$gen_temp[]=1;
				$gen_array[]=$gen_temp;
			}			
		}

		//echo "<pre>";print_r($gen_array);exit;
		$dbfunctions = new dbfunctions();
		if(!empty($gen_array))
		{
			$sql='';
			$shard=0;
			$shardSize = 50;
			foreach($gen_array as $key => $value){
				if ($shard % $shardSize == 0) {
					if ($shard != 0) 
					{
						//mysqy_query($sql);
						$sql=rtrim($sql,',');
						//echo "<br />".$sql."<br />";
						if($dbfunctions->mysql_qry($sql,0)){
							$status['success']="Records Inserted";
							$status['flag']=true;
						}else{
							$status['error']="records not Inserted";
							$status['flag']=false;
						}
					}
					$sql = "INSERT INTO `cl_garnier_gen`(`garnier_id` ,`garnier_reference`,`garnier_file_url` ,`garnier_folder` ,`garnier_image_count` ,`garnier_data`,`garnier_filename`,`garnier_create_date` ,`garnier_export_date`,`garnier_status`) VALUES ";
				}
				$newRef[]=$row[$ref];
				$sql.=" (NULL,'".$value[0]."','".$value[1]."','".$value[2]."','".$value[3]."','".$value[4]."','".$value[5]."','".$value[6]."','".$value[7]."','1'),";
				$shard++;
				//echo $sql;exit;
			}
			//Insert Last set of Batch
			$sql=rtrim($sql,',');
			if($sql!=''){
			$sql=rtrim($sql,',');
				if($dbfunctions->mysql_qry($sql,0)){
					$status['success']="Records Inserted";
					$status['flag']=true;
				}else{
					$status['error']="records not Inserted";
					$status['flag']=false;
				}
			}

			return $status;
		}

	}

	/**
	 * Function process_dev1
	 * function to process dev 1 data and segregate text from 1 column to other
	 *
	 * @param array $data
	 * @return string $filename
	 */		
	public function process_dev1($data)
	{
		//echo "<pre>"; print_r($data);exit;
		$globals=$this->globalUniqueGenRefs();
		
		$this->garnierUrl=GARNIER_URL.'/view-pictures.php?client=GARNIER&reference=';
				
		$this->global_parents_data=$globals[0];
		$this->global_parents_data_file=$globals[1];
		$this->global_parents_xls_file=$globals[2];
		$this->allImages=$this->findAllImages();
		//echo 
		//echo "FINAL<pre>"; print_r($this->allImages);exit;
		$newData=array();
		$count=count($data[0])-1;
		foreach ($data[0] as $key => $value) 
		{	
			if($key!=0 && $key!=$count){
				$newData[]=$this->segregateDev1($value,$key);
			}else{
				$newData[]=$value;
			}
			# code...
		}
        //echo "FINAL<pre>"; print_r($newData);exit;
		$file_name = uniqid() ."_".date('y-m-d')."_garnier.xlsx" ;
    	$file_path = GARNIER_WRITER_FILE_PATH . "/dev1/" . $file_name ;
       
    	$this->writeMultiSheets($newData,$file_path,$data[1]);
    	$options=array('garnierUrl'=>$this->garnierUrl);
    	$this->updateNewGenRefs($this->genarray,$file_name,$options);
		//echo "FINAL<pre>"; print_r($newData);
		//exit;
		return $file_name;
	}

	/**
	 * Function segregate
	 *
	 * @param
	 * @return
	 */		
	public function segregateDev1($data,$sheet)
	{
		//echo "<pre>"; print_r($data);
		$newData=array();
		$index2=1;
		foreach ($data as $key => $value) {
			if($key!=0){
				//echo "<br>".$key."=".$value[7];
			//	$value[7]=str_replace('�"',"-",$value[7]);
                $value[7]=json_encode(utf8_encode($value[7]));
				$value[7] = str_replace('\u00e2\u0080\"', '-',$value[7]);
				$value[7]=utf8_decode(json_decode($value[7]));
                $value[7]=$this->fix_text($value[7]);
                //$value[7] = str_replace("&rsquo;","'",$value[7]);  
                  
				$link=array();
                //Check For Meta title 
                $original_metatitle=$value[13];
				$value[13]=ltrim(trim($this->get_string_between($value[7],'META TITLE','META DESCRIPTION')),':');
                if($value[13]==''){
                    $value[13]=ltrim(trim($this->get_string_between($value[7],'meta title','meta description')),':');
                    if($value[13]==''){
                        $value[13]= $original_metatitle;
                    }
                }
                //meta description
                $original_metadesc=$value[15];
				$value[15]=ltrim(trim($this->get_string_between($value[7],'META DESCRIPTION','TITLE')),':');
                if($value[15]==''){
                    $value[15]=ltrim(trim($this->get_string_between($value[7],'meta description','title')),':');
                    if($value[15]==''){
                        	$value[15]=$original_metadesc;
                    }
                }
				//$titleTemp=trim($this->get_string_between($value[7],'META DESCRIPTION:','TEXT:'));
                $original_title=$value[6];
				if($value[6]=='')
				{   
				    
					$value[6]=explode('TITLE',$this->get_string_between($value[7],'TITLE','TEXT'));
					$value[6]=ltrim($value[6][1],':');//trim($this->get_string_between($value[6],'TITLE:',''));
                    if($value[6]=='')
				    {
				        $value[6]=explode('title',$this->get_string_between($value[7],'title','text'));
					    $value[6]=ltrim($value[6][1],':');
                        if($value[6]=='')
				        {
				            $value[6]=$original_title;
                        }
				    }
                    
				}
                $original_text=$value[7];
				//$value[7]=ltrim(trim($this->get_string_between($value[7],'TEXT','')),':');
				
				$value[7]=preg_replace('/\\bTEXT\\b/i', '***text***', $value[7]);
				//echo '<br><br>NO:'.$index2." == ".$value[7]."<br><br>";
				
                $prefix = "***text***";
                $index = strpos($value[7], $prefix) + strlen($prefix);
              //  echo $index."<br>";
                if($index>20){
                    $value[7] = substr($value[7], $index);
                }
                //}
                if($value[7]==''){
                    $prefix = "***text***";
                    $index = strpos($original_text, $prefix) + strlen($prefix);
                    if($index>20){
                        $value[7] = substr($original_text, $index);
                    }
               
                    if($value[7]==''){
                        $value[7]=$original_text;
                    }
                }
                $value[6]=ltrim(trim($value[6]),' : ');
                $value[7]=ltrim(trim($value[7]),' : ');
                $value[13]=ltrim(trim($value[13]),' : ');
                $value[15]=ltrim(trim($value[15]),' : ');
			// echo $value[7];exit;
				//$value[16]='';
				if(!in_array(trim($value[8]),$this->genRefs)){
					$link=$this->checkFtp(trim($value[8]),$value);
				}else{
					//echo "DOUBL";
                    if($value[8]!=''){
					   $link=array(0,'DOUBLOON-SHEET:'.$sheet." LINE :".$key);
                    }else{
                        $link=array(0,'N/A');
                    }
				}
				//$link=$this->checkFtp($value[1],$value);
				$value[19]=$link[1];
				$newData[]=$value;
                //echo "<pre>"; print_r($value);
                
				if($link[0]){
					//echo "<pre>"; print_r($link);
					$this->genarray[]=$value;
					$this->genRefs[]=trim($value[1]);
				
				}
				$index2++;
			}else{
				$newData[]=$value;
			}
		}
       //exit;
		return $newData;
		
	}

	function get_string_between($string, $start, $end)
	{
	    $string = " " . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return "";
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	/**
	 * Function checkFtp
	 *
	 * @param
	 * @return
	 */		
	public function checkFtp($reference,$data)
	{	
		$link='';
		$new=false;
		if(in_array($reference,$this->allImages))
		{
			if(in_array($reference, $this->global_parents_data)){
				$link="DOUBLOON-".$this->global_parents_xls_file[$reference];
			}else{
				$link=$this->garnierUrl.$reference;
				$new=true;
			}
		}else{
			$link = 'NA';
		}

		return array($new,$link);
	}

	function findAllImages(){
		$pattern = GARNIER_IMAGE_PATH.'/*.*';
		//echo $pattern;
		//$arraySource = glob($pattern,GLOB_BRACE);
		$arraySource=$this->glob_recursive($pattern);
		$images=array();
		//echo "<pre>"; print_r($arraySource);exit;
		foreach($arraySource as $key => $value){
			$basename=basename($value);
			if (substr($basename, 0, 1) == 0) {
				$basename = substr($basename, 1, strlen($basename));
				$tempImg=array(basename($value));
				$tempImg[0]=substr($tempImg[0], 0 , (strrpos($tempImg[0], ".")));
				$images[]=$tempImg[0];//preg_replace("/[^0-9]/", '',basename($basename));
			}else{
				$tempImg=array(basename($value));
				$tempImg[0]=substr($tempImg[0], 0 , (strrpos($tempImg[0], ".")));
				
				$images[]=$tempImg[0];
				//$images[]=preg_replace("/[^0-9]/", '',basename($value));
			}

		}
        //sort($images);
        // print_r($images);exit;//
       return $images;
	}

	function glob_recursive($pattern, $flags = 0) {
		   $files = glob($pattern, $flags);
		   foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
			  $files = array_merge($files, $this->glob_recursive($dir.'/'.basename($pattern), $flags));
		   }
		   return $files;
	}

	/*
	 * write korben xlsx 2 is used for writing k2 xlsx with taking care of special characters
	 * @param $datas data to be written
	 * @param $file_path file path where it to be written 
	 * @sheetnames Sheetnames of xlsx to be written
	 *
	 * */

	function writeMultiSheets($datas, $file_path, $sheetnames)
	{	
		//echo "<pre>"; print_r($datas[1]);exit;
	    // PHPExcel
	    include_once INCLUDE_PATH . '/PHPExcel.php';

	    // PHPExcel_Writer_Excel2007
	    include_once INCLUDE_PATH . '/PHPExcel/Writer/Excel2007.php';

	    // Create new PHPExcel object
	    $objPHPExcel = new PHPExcel();

	    // Set properties
	    $objPHPExcel -> getProperties() -> setCreator("edit-place");
	    //echo "<pre>";print_r($datas);print_r($sheetnames);exit($file_path);

	    $sheetCount = 0;
	    foreach ($datas as $idx => $data) 
	    {
	        // Rename sheet
	        $sheet_name = $sheetnames[$idx];

	        $objWorksheet = new PHPExcel_Worksheet($objPHPExcel);
	        $objPHPExcel -> addSheet($objWorksheet);
	        $objWorksheet -> setTitle($sheet_name);

	        $rowCount = 0;
	        foreach ($data as $row) 
	        {
	            $col = 'A';
	            foreach ($row as $key => $value) 
	            {
	                $wdth[$col] = 1;
	                $col++;
	            }
	            $rowCount++;
	        }

	        $rowCount = 0;
	        foreach ($data as $row)
	        {
	            $col = 'A';
	            foreach ($row as $key => $value) 
	            {
	                
	                //$value = str_replace("", "œ", $value) ;
	                $value = str_replace("", "'", $value) ;
	                $value = str_replace("", "'", $value) ;
                    //$value=str_replace(utf8_decode("–"),"-",$value);
                    

                     
                    //$value = utf8_decode($value); 
	                if($key=='14' && $rowCount!=0 && $sheetCount!=0){
	                	$objWorksheet -> setCellValue($col . ($rowCount + 1), '=LEN(M'.($rowCount + 1).')');
	                }else if($key=='16' && $rowCount!=0 && $sheetCount!=0){
	                	$objWorksheet -> setCellValue($col . ($rowCount + 1), '=LEN(O'.($rowCount + 1).')');
	                }else{
	                	$objWorksheet -> setCellValue($col . ($rowCount + 1), $value);
	            	}
	                if (strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='S')) {
	                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setUrl($value);
	                    $objWorksheet -> getCell($col . ($rowCount + 1)) -> getHyperlink() -> setTooltip($value);
	                }
	                $col++;
	            }
	            $rowCount++;
	        }
	
	        $sheetCount++;
	    }
	    $objPHPExcel -> removeSheetByIndex(0);

	    // Save Excel 2007 file
	    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	    $objWriter -> save($file_path);

	    @chmod($file_path, 0777);
	    //echo "<pre>";print_r($data);exit;
	    if(file_exists($file_path))
	    {
	        return true;
	    }
    
	}
	/**
	* Function escape_quote
	* function used to clean single quotes for query
	* 
	* @package clientsEditPlace
	* @author Vinayak
	* @param string $str
	* @return string $str
	*/
	function escape_quote($str){
		return str_replace("'","''",$str);
	}
    
    function fix_text($str){
       /// $str=str_replace(utf8_decode("œ"),"œ",$str);
       // $str=str_replace(utf8_decode("“"),'"',$str);
       // $str=str_replace(utf8_decode("”"),'"',$str);
       // $string = str_replace("&hellip;","...",$string);
       /// $string = str_replace("&ndash;","-",$string);
        
        return $str;
    }
    
    	/**
	 * Function process_dev1
	 * function to process dev 1 data and segregate text from 1 column to other
	 *
	 * @param array $data
	 * @return string $filename
	 */		
	 public function process_dev2($data)
	 {
		//echo "<pre>"; print_r($data);exit;
        //echo 1.0000000000000001231123123;
        //echo log(875);exit;
			
		$this->garnierUrl=GARNIER_URL.'/view-pictures.php?client=GARNIER&reference=';
        $folder="garnier-".date('Ymd-His')."-".rand(1111,9999);
		$folderPath=GARNIER_WRITER_FILE_PATH."/dev2/".$folder."/";
		mkdir($folderPath, 0777, true);
        //echo $folderPath;
			//	exit;
		//$this->allImages=$this->findAllImages();

		$newData=array();
		//$count=count($data[0])-1;
		
        foreach($data[0] as $key=>$value){
            //Sheet
            $filename=$data[1][$key].".docx";
            $docData=array();
            foreach($value as $k=>$v){
              if($k!=0){  
              //echo "<pre>"; print_r($v);exit;  
                    
                  $temp=array();
                  if($v[9]!=''){
                    $files =glob(GARNIER_IMAGE_PATH."/*/".$v[9]."{*.jpg,*.jpeg,*.JPG,*.JPEG,*.png,*PNG}", GLOB_BRACE);
                  }else{
                    $files =array();
                  }
                  //echo "<pre>"; print_r($files);
                  //list($width, $height, $type, $attr) = getimagesize($files[0]);
                 // echo "<pre>"; print_r(array($width,$height));
                  $temp[]=(!empty($files))?$files[0]:'';
                  $temp[]=$this->cleanit($v[5]);
                  $temp[]=$this->cleanit($v[6]);
                  $temp[]=$this->cleanit($v[7]);
                  $temp[]=nl2br($this->cleanit($v[8]));
                  $docData[]=$temp;
              }
              
            }
            //$docData=array_reverse($docData);
            //echo "<pre>"; print_r($docData);exit; 
            $this->create_docx_delivery($docData,$folderPath,$filename);
            
        }
		//exit;
        $zipPath=$folderPath;
		$zipName=GARNIER_WRITER_FILE_PATH."/dev2/".$folder.".zip";
		//echo $zipPath."<br>";
		//echo $zipName;
        $basiclib=new Basiclib();
		$basiclib->zip_creationMultiFolder($zipPath, $zipName,'docx');
		//exit;

		return basename($zipName);
	}
    
    	/* create_docx_file function
   	* 
   	*  This will create docx of each row of xlsx file.
   	*  @param $header - top header content in array, 
	*  @param $data - 1 line of row xlxs file in arary,
	*  @param $path - path of docx file to be created,
	*  
	*/
	
	function create_docx_delivery($data,$path,$file_name_docx)
	{	$error=false;
	    // New Word Document
		$PHPWord = new PHPWord();

		// New portrait section
        foreach($data as $key=>$value){
    		$section = $PHPWord->createSection();
            
    		$PHPWord->addLinkStyle('NLink', array('color'=>'0000FF', 'underline'=>PHPWord_Style_Font::UNDERLINE_SINGLE)); // link style
    
    		// Add text elements
    		/* Header image*/
            if($value[0]!=''){	
                list($width, $height, $type, $attr) = getimagesize($value[0]);
                $newHeight=$height/($width/620);
                $section->addImage(
                    $value[0],
                    array('width' => 620, 'height' => $newHeight)
                );
        		//$section->addText($this->take_care_of_special_character($data[0]));		
        		$section->addTextBreak(0);
            }
    		/* Meta Title */		
    		//$section->addText($this->take_care_of_special_character($value[1]));	
    		//$section->addTextBreak(0);	
    		/* Meta Description */		
    		
    		$section->addText($this->take_care_of_special_character($value[2]),array('bold'=>true));		
    		
    		$section->addTextBreak(0);
    		/* Para Title 1 */		
    		$section->addText($this->take_care_of_special_character($value[3]),array('bold'=>true));		
    		$section->addTextBreak(0);
    		/* Para 1 */	
    		/*if(str_word_count($data[4])>110 || str_word_count($data[4])<90){
    			$section->addText($this->take_care_of_special_character($data[4]),array('fgColor'=>'red'));	
    			$error=true;	
    		}else{*/
    		$text=explode('<br />',$value[4]);
    		foreach ($text as $bkey => $bvalue) {
    			$section->addText($this->take_care_of_special_character($bvalue));	
    			$section->addTextBreak(0);
    		}
    		//$section->addText($this->take_care_of_special_character($value[4]));		
    		//}
    		
    
    	
	   
	   }
	   // Meta Description for Docx file
	  
		// $modified_text = $this->modifyCellContent($data[1]);
		
		// $textrun = $section->createTextRun();
		// $flag = 0;
		// $flag1 =0;
		// $link_string = "";
	 //    for ($i=0; $i<strlen($modified_text); $i++)
	 //    {
		// 	/* Start - Code is only used when we have to create link and bold tags in Docx file using php word library */	
		// 	if(strcmp($modified_text[$i],"[") == 0)
		// 	{
		// 		$flag = 1;
		// 	}
		// 	elseif(strcmp($modified_text[$i],"]") == 0)
		// 	{
		// 		$flag = 0;
		// 	}
			
		// 	if(strcmp($modified_text[$i],"{") == 0)
		// 	{
		// 		$flag1 = 1;
		// 	}
		// 	elseif(strcmp($modified_text[$i],"}") == 0)
		// 	{
		// 		$flag1 = 0;
		// 	}
			
		// 	if($flag1 == 1)
		// 	{
		// 	   $link_string.=$modified_text[$i];
		// 	   continue;
		// 	}
		// 	elseif($flag1 == 0 && strcmp($modified_text[$i],"}") == 0)
		// 	{
		// 		//echo $link_string."<br />";
		// 	   $link_string = explode("~",str_replace("{","",$link_string));
		// 	   $textrun->addLink("$link_string[0]", "$link_string[1]", 'NLink');	
		// 	   $link_string = "";
		// 	   continue;	
		// 	}			
		// 	if($flag == 1 && strcmp($modified_text[$i],"[") != 0 && strcmp($modified_text[$i],"]") != 0)
		// 	  	$textrun->addText($modified_text[$i],array('bold'=>true));		    
		// 	elseif($modified_text[$i] != '[' && $modified_text[$i] != ']' && $flag == 0)
		// 		$textrun->addText($modified_text[$i],array('bold'=>false));	
		// 	/* End - Code is only used when we have to create link and bold tags in Docx file using php word library */					
		// }
		   
		// $section->addTextBreak(2);
       /* Error  */
       if($error){
       		$file_name_docx = "ERR_".$file_name_docx;
       }		
      	
     	
     	// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx);
		 
		return $file_name_docx;
	}
    
    function take_care_of_special_character($string){
        $string = str_replace("’","'",$string);
		$string = str_replace("‘","'",$string);
        $string = str_replace("&rsquo;","'",$string);
        $string = str_replace("&oelig;","oe",$string);
        $string = str_replace("&OElig;","OE",$string);
        $string = str_replace("&euro;","EUR",$string);
        $string = str_replace("&hellip;","...",$string);
        $string = str_replace("&ndash;","-",$string);
        $basiclib=new basiclib();
        if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
        	$string=str_replace("€",'EUR',$string); 
        	$string=str_replace("£","POUND",$string);
        	$string=str_replace("œ","oe",$string);
        	$string=str_replace("Œ","OE",$string);
        	$string = str_replace("–","-",$string);

        	$string=utf8_decode($string);
        	
        }
        return $string;
	}
	
	function cleanit($string){
		/*$basiclib=new basiclib();
		if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
			$string = str_replace("’","'",$string);
			$string = str_replace("‘","'",$string);
			$string = str_replace("&rsquo;","'",$string);
			$string = str_replace("&oelig;","oe",$string);
			$string = str_replace("&OElig;","OE",$string);
			$string = str_replace("&euro;","EUR",$string);
			$string = str_replace("&hellip;","...",$string);
			$string = str_replace("&ndash;","-",$string);
			$string=str_replace("€",'EUR',$string); 
        	$string=str_replace("£","POUND",$string);
        	$string=str_replace("œ","oe",$string);
        	$string=str_replace("Œ","OE",$string);
        	$string = str_replace("–","-",$string);
		}*/
		return $string;
	}
}
?>
