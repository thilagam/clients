<?php
/**
 * BNP Theme delivery file xlsx contain all data of docx files data.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Thilagam
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Aug 22 , 2016
 */
 
ob_start();

mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

ini_set('display_errors', 1);
/**
 * Include Files Here
 * */
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH.'/html_to_doc.inc.php');
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/basiclib.php");
include_once(TOYR_PATH."/dbfunctions.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], TOYR_WRITER_FILE_PATH."/dev2/", "delivery.php") ;
	}
		

if(isset($_POST['submit']))
{
    
	/*Create basic lib instance*/
    $basiclib=new basiclib();
    $dbfunctions=new dbfunctions();

	//$basiclib->unzipfolder($_FILES['userfile1']['name']);
    
    require_once(INCLUDE_PATH."/reader.php");
    
    $file1  =   pathinfo($_FILES['userfile1']['name']) ;
    $ext = $file1['extension'];
    
    if($ext == 'zip' || $ext == 'rar')
    {
       
		  $file1  =   pathinfo($_FILES['userfile1']['name']) ;
		  $tags = array_filter(array_map("trim", explode(",", $_REQUEST['tags'])));
		  $ext	=	$file1['extension'] ;
		  $srcFile    =   TOYR_WRITER_FILE_PATH."/dev2/"."toyr-" . date('ymd')."-".uniqid().".".$ext ;
		  move_uploaded_file($_FILES['userfile1']['tmp_name'], $srcFile) ;

		  if($ext=='rar')
			{
				$zip_file=pathinfo($srcFile);//exit($zip_file['filename']);
				$zip_file['filename']   =   str_replace(" ","-",$zip_file['filename']) ;
				$path   =   $zip_file['dirname']."/".$zip_file['filename'].".rar" ;
				$rar_file = rar_open($path);
				$list = rar_list($rar_file);
				
				foreach($list as $file) {       
					preg_match('/RarEntry for file "(.*)"/', $file, $matches) ;
					if(strstr($file, 'RarEntry for file'))
					{
						$entry = rar_entry_get($rar_file, $matches[1]) or die("Failed to find such entry") ;
						$entry->extract(false, $zip_file['dirname']."/".$zip_file['filename']."/".(str_replace(" ","-",$matches[1])));
					}
				}
				rar_close($rar_file);
				$unzip_dir  =   $zip_file['dirname']."/".$zip_file['filename'] ;
				chmod($unzip_dir,0777) ;
			}
			else
			{
				chmod($srcFile, 0777);
				$unzip_dir = unzipfolder($srcFile);
			}
      $docx_files = all_docx_files($unzip_dir);
      
			$final_array = array(array("SKN","UID"," PID","URL","MARQUE PROPRE ?","NOM DE L'ARTICLE","LONGUE DESCRIPTION(PRODUITS : 100 mots environ | PAGE : 200 mots environ)","MOTS CLEFS(7 mots clefs séparés par un point virgule sans espaces)"));


      $j=1; 
			$check_empty = "";
						
			$newFinalArray=array(array("SKN","UID"," PID","URL","MARQUE PROPRE ?","NOM DE L'ARTICLE","LONGUE DESCRIPTION(PRODUITS : 100 mots environ | PAGE : 200 mots environ)","MOTS CLEFS(7 mots clefs séparés par un point virgule sans espaces)"));


      foreach($docx_files as $lkey=>$docx)
      {
				
				$dummy_array = process_xmlData(readDocx($docx));
        $sheet_3_array = array();
        $other_data = array();
        
				for($i=1;$i<=sizeof($dummy_array);$i++)
        {
        	if($i==1)
          {
            $sql = "Select * from cl_toyr_articles where toyr_skn=".strip_tags($dummy_array[$i][2]);
            //echo $sql;exit;
            $result = $dbfunctions->mysql_qry($sql,1);  
            $other_data = mysql_fetch_assoc($result);
            $final_array[$j][$i] = $dummy_array[$i][2];
          }else{
            $final_array[$j][$i] = $dummy_array[$i][2];
          }
        }
        
        echo "<pre>"; print_r($final_array[$j]);exit;
       
		//echo $edito;exit;
        //echo html_entity_decode($edito);exit;
        $sql1 = "update cl_toyr_articles set toyr_nom = '".$final_array[$j][7]."' , toyr_longue_description = '".$final_array[$j][8]."', toyr_mots = '".$final_array[$j][8]."' where toyr_skn=".strip_tags($final_array[$j][2]);
        //echo $sql1;exit;
        $dbfunctions->mysql_qry($sql1,1); 
        $finalData = array();
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][2]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][3]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][4]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][5]); 
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][6]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][7]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][8]);
        $finalData[] = (!empty($other_data))?$other_data['toyr_skn']:strip_tags($final_array[$j][9]); 
        
        $newFinalArray[$j]=$finalData;
        $j++;
     	
      }
			
		  //echo "<pre>";print_r($newFinalArray); exit;

      $rand="toyr-delivery-".uniqid()."-".date('d-m-y-h-m');
    	$srcPath=TOYR_WRITER_FILE_PATH."/dev2/".$rand."/";
			$srcFile=TOYR_WRITER_FILE_PATH."/dev2/".$rand.".xlsx";
			
			writeXlsxVenereDev2($newFinalArray,$srcFile);
         
		    
      if(file_exists($srcFile)) {
			  header("Location:dev2.php?msg=success&folder=".$rand."&file=".$rand.".xlsx");
			} else {
	   	  header("Location:dev2.php?msg=error");
			}
		
            
  }
  else
  {
      header("Location:dev2.php?msg=file_error");
  }
    	
}
else
    header("Location:dev2.php");

  /*
    To convert [click here,link] to Actual link

  */
 
    function toLink($val)
    {
      //$val=preg_replace('/(\*\*)(.[^*]+)(\*\*)/', "<b>$2</b>", $val);
      //$val=preg_replace('/(\*\*)(.[^*]+)(\*\*)/', "<strong>$2</strong>", $val);
      $val=preg_replace('/(\*)(.[^*]+)(\*)/', "<i>$2</i>", $val);
     // preg_match("/(\[(.[^\]\]]+),(.[^\[\]]+)\])/",$val,$match);
		//echo "<pre>"; print_r($match);
		//echo "START".trim($match[3])."END<br>";
      //$val=preg_replace("/(\[(.[^\]\]]+),(.[^\[\]]+)\])/", "<a href='$3'>$2</a>" , $val);
      //$val=preg_replace("/(\[(.[^\]\]]+),(.[^\[\]]+)\])/", "<a href=".utf8_decode("«$3»")." >$2</a>" , $val);
	  //$val=preg_replace("/(\[(.[^\]\]]+),(.[^\[\]]+)\])/", "<a href=«##".trim('$3')."##»>$2</a>" , $val);
	  //$val=str_replace("## ","",$val);
	  //$val=str_replace(" ##","",$val);
	//$val=preg_replace("/(||||(.+),(.+)|||||)/", "<a href='$3'>$2 </a>" , $val);
      return $val;
    }

   /* modify_content function
   * 
   *  Modify content for column 8 10 12
   *  @param $value -  value of column cell
   *  @param $col - column number of input data array
   *  @return $value - return the $value with tags
   */	

function modify_content($value,$col){

	return $value;
}	

   /* all_docx_files function
   *  
   *  Read all docx files from unziped folder
   *  @param $path - path of unziped folder
   *  @return $docx_files - return the name + path of docx
   */	

function all_docx_files($path){
   $docx_files = array();
   $docx_files_data = array();
   $i=1;	
   if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && $entry != "__MACOSX") {
            $docx_files[$i] = $path."/".$entry;
            $i++;
        }
        
    }
    closedir($handle);
  }
  return $docx_files;	
}	

   /* process_xmlData function 
   * 
   *  Read data from xml file generate by function readDocx
   *  @param $text - xml file input as string.
   *  @param $newArr - return data as array
   */	

function process_xmlData($text){
	/*things need to change end*/
//$text=preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>',$text);
$text = str_replace("[","||||",$text);
$text = str_replace("]","|||||",$text);

$text = str_replace("</w:tr>","]",$text);
$text = str_replace("<w:tr>","[",$text);

//echo $text;exit;
$matches = array();

//$pattern = '/\tblrowstart([^\]]*)\tblrowend/';

//$pattern = '/\[(tblrowstart^\tblrowend]*)\]/';
//$pattern='/\[([^\]]*)\]/';
//preg_match_all($pattern, $text, $matches);
	//echo "<pre>";print_r($matches);exit;
$matches=explode(']',$text);
//echo "<pre>"; print_r($matches);
$ind=1;
$newArr=array();
foreach($matches as $key => $value){
	/* if($key != 1){ */ // Dont include second row of DOCX file as per specification
	$value = str_replace("</w:tc>","]",$value);
	//$value = str_replace("<w:tc>","[",$value);
	//echo $text2;
	//$pattern='/\[([^\]]*)\]/';
	//$htmldata.="<tr id='row_".$key."'>";
	//preg_match_all($pattern, $value, $matches2);
  $matches2=explode(']', $value);
  //echo "<pre>"; print_r($matches2);
	$arrData='';
	//$htmldata.="<td >".$ind."</td>";
  //echo "<pre>"; print_r($matches2);
	foreach($matches2 as $k=>$v){
		$tempData=read_docx_special_character_function(trim($v));
    $tempData=str_replace("|||||","]",$tempData);
    $tempData=str_replace("||||","[",$tempData);
    $arrData[]=$tempData;
      //$arrData[]=read_docx_special_character_function(trim(paragraph($v)));
    //$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
  //$arrData = str_replace("|||||","]",$arrData);
	//$arrData = str_replace("||||","[",$arrData);
	
	$newArr[$ind]=$arrData;
	//$htmldata.="</tr>";
//	echo "<pre>";print_r($matches2[0]);
	$ind++;
   /* }	*/
}
//$htmldata.="</table>";
//echo "<pre>"; print_r($matches);

//$htmldata = str_replace("####","[",$htmldata);
//$htmldata = str_replace("#####","]",$htmldata);

return $newArr;
}

function paragraph($text)
{
    //echo $text;exit;
    $text = str_replace("[","||||",$text);
    $text = str_replace("]","|||||",$text);
    $text = str_replace("</w:p>","]",$text);
    
    $arr=explode(']', $text);
   // echo "<pre>"; print_r($arr);
    $val = "";
    
    if(count($arr)>1){
    	foreach ($arr as $key => $value)
      { 
        $value=strip_tags($value);
        if($value!=''){
          //$val .= "<p>".$value."</p>";
          $val .= $val."</br>";
        }
      }
    }
    else
    {
      $val=strip_tags($text);
      $val=rtrim($val,']');
      //$val .= "<p>".$val."</p>";
      $val .= $val."</br>";
    }
    //echo "STR<br>".$val;
    $val = str_replace("|||||","]",$val);
    $val = str_replace("||||","[",$val);
    //exit;
    //echo "END<br>".$val."<br>";
    //return strip_tags($val);
    return $val;
    //return strip_tags($text);


}

   /* readDocx function 
   * 
   *  Read a docx file and return the string
   *  @param $filepath - path of the docx file 
   *  @param $newArr - xml data output as string
   */

function readDocx($filePath) {
    // Create new ZIP archive
    $zip = new ZipArchive;
    $dataFile = 'word/document.xml';
    // Open received archive file
    if (true === $zip->open($filePath)) {
        // If done, search for the data file in the archive
        if (($index = $zip->locateName($dataFile)) !== false) {
            // If found, read it to the string
            $data = $zip->getFromIndex($index);
            // Close archive file
            $zip->close();
            // Load XML from a string
            // Skip errors and warnings
            $xml = DOMDocument::loadXML($data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
            // Return data without XML formatting tags
			//echo $xml->saveXML();exit;
            $contents = explode('\n',strip_tags($xml->saveXML()));
            $text = '';
            foreach($contents as $i=>$content) {
                $text .= $contents[$i];
            }
            return  $xml->saveXML();
        }
        $zip->close();
    }
    // In case of failure return empty string
    return "";
}


  /* read_docx_special_character_function function 
   * 
   *  Return value as per encoding of data based on OS
   *  @param $value - data from docx read
   *  @return $value - data from docx read
   */ 

	function read_docx_special_character_function($value){
	 
	    if (getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows')
                        {
                            $value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
							$value = isset($value) ? html_entity_decode($value,ENT_QUOTES,"UTF-8") : '';
                        }   else   {
                            $value=html_entity_decode(htmlentities($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES , 'ISO-8859-1');
                             //$value = isset($value) ? ((mb_detect_encoding($value) == "ISO-8859-1") ? iconv("ISO-8859-1", "UTF-8", $value) : $value) : '';
							//$value = isset($value) ? html_entity_decode($value,ENT_QUOTES,"UTF-8") : '';
                        }
						$value =str_replace("\0", "", $value);
		 return $value;
	 }	

   /**
    * Function windowsFix
    *
    * @param
    * @return
    */    
   function windowsFix($str)
   {
      if (getOS($_SERVER['HTTP_USER_AGENT']) != 'Windows')
      {
        $str=utf8_decode($str);
      }
      return $str;
   }

	 
   /* writeXlsxVenereDev2 function 
   * 
   *  Read a docx file and return the string
   *  @param $data - data in array which xlsx will contain 
   *  @param $file_path - path of file where xlsx will be created
   *  @param $anchor_cols - default null 
   */ 
 
	function writeXlsxVenereDev2($data,$file_path,$anchor_cols=null)
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
        $columns_required = array('G','H','I','J','K','L');
        $rowCount=0;
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            //echo "<pre>"; print_r ($row); echo "</pre>"; exit;
            $language = "fr_FR";//$row[0]; //exit;
            //echo "<br />";
            $col_k_value = ""; echo $k;
            foreach ($row as $key => $value)
            {	
				
							
				$value = write_docx_special_character_function($value,trim($language));
				
                
                if(in_array($col,$columns_required) && $rowCount > 0 && $key < sizeof($row)-1)
                {
                   $col_k_value .= ($key < sizeof($row)-2) ? $col.($rowCount+1).",CHAR(10),CHAR(10)," : $col.($rowCount+1).",CHAR(10),CHAR(10)"; // create formula for last row
                }
                
                
                
                /*if($key == sizeof($row)-1 &&  $rowCount > 0){ 
				      $value = "=CONCATENATE(".$col_k_value.")"; // concatenate formula which all concat specific cells of row
				      //echo "<br />";
			    } */ 
                
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                
                $col++;
            }
             //echo $col_k_value."<br />";
            $rowCount++;
        }

        //exit;

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle("sheet1");
        $objPHPExcel->getActiveSheet()->getStyle('1')->getFont()->setBold(true);
         
        // Save Excel 2007 file
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($file_path);
        
       
        @chmod($file_path, 0777) ; 
        
        if(file_exists($file_path))
            return true ;
            
    }
    
   /* write_docx_special_character_function function 
   * 
   *  Write XSLX Delivery file with 2 sheet
   *  @param $value - data from $final_array 
   *  @param $language - language of docx file 
   *  @return $value - data from $final_array
   */ 
    
    function write_docx_special_character_function($value,$language){
		//echo $language;
		if($language == "fr_FR" ){ // French language
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
			return $value;	    
	    }else if($language == "de_DE"){ // German language
			   $value = mb_convert_encoding($value, mb_internal_encoding());
			   //$value = str_replace("é","",$value);
			   
			return $value;
		}else if($language == "en_UK"){ // Norwegian language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "es_ES"){ // Spanish language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else if($language == "it_IT"){ // italic language
				$value = mb_convert_encoding($value, mb_internal_encoding());
			return $value;	
		}else{
		   return $value;	
		}		
		
	 }
	 
  /* is_array_empty function 
   * 
   *  Check the array is empty or not
   *  @param $a - data in array
   *  @return true if no data & false if data is there
   */ 
	 
	 
	 function is_array_empty($a){
       foreach($a as $elm)
          if(!empty($elm)) return false;
            return true;
     }
    
    
?>
