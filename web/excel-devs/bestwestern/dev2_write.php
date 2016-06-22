<?php
/**
 * Venere Theme delivery file xlsx contain all data of docx files data.
 *
 * PHP version 5
 * 
 * @package    ClientDevs
 * @author     Lavanya Pant
 * @copyright  Edit-Place
 * @version    1.0
 * @since      1.0 Aug 26
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
include_once(BESTWESTERN_PATH."/dbfunctions.php");

/**
 *	Code To Download Files after it gets Generated
 * */
if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
	if(pathinfo($_GET['file'], PATHINFO_EXTENSION)=='xlsx'){
		odownloadXLS($_GET['file'], BESTWESTERN_WRITER_FILE_PATH."/dev2/", "dev2.php") ;
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
		  $srcFile    =   BESTWESTERN_WRITER_FILE_PATH."/dev2/"."Bestwestern-" . date('ymd')."-".uniqid().".".$ext ;
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
            
			//echo "<pre>";print_r($docx_files); exit;
			
			$final_array = array(array("Article ID","Nom de l'attraction","ID attraction","Type","URL","Ville","Colonne1","Mot Cible 1","Mot cible 2","Mot cible 3","H1","H2 - 1","Paragraphe 1","H2 - 2","Paragraphe 2","H2 - 3","Paragraphe 3","Title","Meta Description"));
			$j=1; // 1st 5 columns will be filled by the 1st DOCX file and remaning will other docx files 3rd & 4th Column
			$check_empty = "";
						
			
			foreach($docx_files as $lkey=>$docx){
				//echo "<pre>"; print_r (process_xmlData(readDocx($docx)));  exit;
				$dummy_array = process_xmlData(readDocx($docx));
       // echo "<pre>"; print_r($dummy_array);exit;
				$sheet_3_array = array();
        $other_data = array();
				for($i=1;$i<=sizeof($dummy_array);$i++)
        {
					//$other_data = array();
					if($i==1)
          {
				      
				      $sql = "Select bwa_other_data from cl_bestwestern_articles where bwa_article_id=".$dummy_array[$i][1];
				      $result = $dbfunctions->mysql_qry($sql,1);				      
				      $other_data = mysql_fetch_row($result); 
              //echo "HIIIIIIIIIII<pre>"; print_r($other_data);
				      $other_data = json_decode(utf8_encode($other_data[0]),true);
             // echo "<pre>"; print_r($other_data);exit;
              $final_array[$j][$i] = $dummy_array[$i][1];            
          }
          else
          {
					  
					   $final_array[$j][$i] = ($i >= 8 || $i <= 16) ? modify_content($dummy_array[$i][1],$i) : $dummy_array[$i][1];  					
					}
				
	      }
        //echo "<pre>"; print_r($final_array[$j]);
        $finalData=array();
        $finalData[]=$final_array[$j][1]; //Article ID
        $finalData[]=$final_array[$j][3]; // nome de attractin
        $finalData[]=$final_array[$j][5]; //ID attraction    
        $finalData[]=$final_array[$j][2]; //type
        $finalData[]=(!empty($other_data))? windowsFix($other_data[0]):'';   //URL
        $finalData[]=$final_array[$j][4]; //ville 
        $finalData[]=(!empty($other_data))? windowsFix($other_data[1]):''; ;   //Colonne1
        $finalData[]=$final_array[$j][6];  // Mot Cible 1
        $finalData[]=$final_array[$j][7];  // Mot Cible 2
        $finalData[]=$final_array[$j][8];  // Mot Cible 3
        $finalData[]="<h1>".$final_array[$j][9]."</h1>";//(!empty($other_data))? windowsFix("<h1>".$other_data[2]."</h1>"):'';  //H1
        $finalData[]="<h2>".$final_array[$j][10]."</h2>"; //h2-1
        $finalData[]="<p>".$final_array[$j][11]."</p>"; //p1
        $finalData[]="<h2>".$final_array[$j][12]."</h2>";//h2-2
        $finalData[]="<p>".$final_array[$j][13]."</p>"; //p2
        $finalData[]="<h2>".$final_array[$j][14]."</h2>";//h2-3
        $finalData[]="<p>".$final_array[$j][15]."</p>"; //p3
        $finalData[]=(!empty($other_data))? windowsFix($other_data[4]):'';  //H1
        $finalData[]=(!empty($other_data))? windowsFix($other_data[3]):'';  //H1  
        $final_array[$j]=$finalData;
				$j++;
			}
			//exit;
			
			//echo "<pre>";print_r($final_array); exit;

            $rand="BestWestern-delivery-".uniqid()."-".date('d-m-y-h-m');
    	    $srcPath=BESTWESTERN_WRITER_FILE_PATH."/dev2/".$rand."/";
			$srcFile=BESTWESTERN_WRITER_FILE_PATH."/dev2/".$rand.".xlsx";
			
			//exit;
			
			writeXlsxVenereDev2($final_array,$srcFile);
         
		    
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

   /* modify_content function
   * 
   *  Modify content for column 8 10 12
   *  @param $value -  value of column cell
   *  @param $col - column number of input data array
   *  @return $value - return the $value with tags
   */	

function modify_content($value,$col){
	//$value = ($col == 10) ? "<h1>".$value."</h1>" : $value;		
 //  $value = ($col == 8 ) ? "<h1>".$value."</h1>" : $value;
	// $value = ($col == 9 || $col == 11 || $col == 13) ? "<h2>".$value."</h2>" : $value;
	// $value = ($col == 10 || $col == 12 || $col == 14) ? "<p>".$value."</p>" : $value;
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
$text = str_replace("[","####",$text);
$text = str_replace("]","#####",$text);

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

$ind=1;
$newArr=array();
foreach($matches as $key => $value){
	/* if($key != 1){ */ // Dont include second row of DOCX file as per specification
	$value = str_replace("</w:tc>","]",$value);
	$value = str_replace("<w:tc>","[",$value);
	//echo $text2;
	$pattern='/\[([^\]]*)\]/';
	//$htmldata.="<tr id='row_".$key."'>";
	preg_match_all($pattern, $value, $matches2);
	$arrData='';
	//$htmldata.="<td >".$ind."</td>";
	foreach($matches2[1] as $k=>$v){
		
		if($k > 0)
		$arrData[]=read_docx_special_character_function(trim(strip_tags($v)));
		//$htmldata.="<td >".trim(strip_tags($v))."</td>";
		
	}
	$arrData = str_replace("####","[",$arrData);
	$arrData = str_replace("#####","]",$arrData);
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
