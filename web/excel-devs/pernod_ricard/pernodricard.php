<?php
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
define("INCLUDE_PATH",ROOT_PATH."/includes");
require_once(INCLUDE_PATH.'/basiclib.php');
include_once(INCLUDE_PATH."/config_path.php");
require_once(INCLUDE_PATH."/PHPWord.php");
include_once(BESTWESTERN_PATH."/dbfunctions.php");
/**
 * pernodricard Lib is a PHP-functions library used in pernod ricard Devs. It is collection of commonly used functions for Interrent devs
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
class pernodricard 
{
	public $keywords_table='cl_pernodricard_keywords';
	public $translations_table='cl_pernodricard_keywords_translations';
	public $charLimit=800;

	public $dbfunctions;

	public function __construct(){
		$this->dbfunctions=new dbfunctions();
	}

	/**
	 * Function insertKeywords
	 * insert all keywords in database
	 * @param
	 * @return
	 */		
	public function insertKeywords($data,$columns)
	{	
		$insertArray=array();
		$insertString="";
		//echo "<pre>"; print_r($data);
		foreach ($data as $key => $value) {
			//echo "<pre>"; print_r($value);exit;
			if($key!=0){
				foreach ($value as $cell => $cellvalue) {
					$cellvalue=trim($cellvalue);
					if(in_array($cell, $columns) && $cellvalue!=''){
						$hash=md5($cellvalue);
						$length=strlen($cellvalue);
						$cellvalue=$this->escapeString($cellvalue);
						$insertArray[]="('".$hash."','".$cellvalue."',".$length.",".$cell.")";

					}
				}
			}
		}
		$sql="INSERT IGNORE INTO ".$this->keywords_table."
				    (`key_hash`,`key_text`,`key_length`,`key_column`)
			  VALUES ".implode(',',$insertArray);
	    //echo $sql;
		if($this->dbfunctions->mysql_qry($sql,0))
		{
			$keywordsSql="SELECT * 
						  FROM ".$this->keywords_table."
						  WHERE `key_template_status`=0 
						    AND `key_status`=1";
			$data=$this->dbfunctions->mysql_qry($keywordsSql,1);			      	
			$templatesData=$this->createTemplates($data);
			return $templatesData;
		}else{
			return false;
		}
		
		//echo "<pre>"; print_r($sql);exit;
	}

	/**
	 * Function createTemplates
	 *
	 * @param
	 * @return
	 */		
	public function createTemplates($data,$missing=0)
	{	$rows=array();
		$set=array();
		$chars=0;
		$temp=array();
		$setCode=date('Ymdhis',strtotime('now'));
		if($missing==1){
			$setCode=$setCode."_m";
		}
		 
		$keywordIdArray=array();
		while ($row = mysql_fetch_array($data, MYSQL_NUM)){
			//$rows[]=$row;
			//echo "<pre>"; print_r($row);
			$keywordIdArray[]=$row[0];
			//echo $chars."<br>";
			if($chars<=$this->charLimit){
				$temp[]=$row[0];
				$temp[]=$row[2]; 
				$chars+=$row[3];
			}else{
				$set[]=$temp;
				$chars=0;
				$temp=array();
				$temp[]=$row[0];
				$temp[]=$row[2]; 
				$chars+=$row[3];
			}
		}
		//echo $chars."<=".$this->charLimit;
		if(!empty($temp)){
			$set[]=$temp;
		}
		//echo "<pre>"; print_r($set);exit;
		if(!empty($set)){
			/* Path details  */
			$rand="pernodricard-".rand(11,99)."-".$setCode;
		    $srcPath=PERNODRICARD_WRITER_FILE_PATH."/dev1/".$rand."/";
			$srcFile=PERNODRICARD_WRITER_FILE_PATH."/dev1/".$rand.".zip";
			
			if(!file_exists($srcPath)){
				mkdir($srcPath) ;
				chmod($srcPath,0777) ;		
			}
			//echo "<pre>"; print_r($set);exit;
			foreach ($set as $key => $value) {
				 $name_key=$setCode."_".$key;
				 $docxfile=$this->create_docx_file($value,$srcPath,$name_key);
			}
			if($missing==0){
				/* Update status of keywrods */	
				$updateQyr="UPDATE ".$this->keywords_table."
							SET `key_template_status` = 1
							WHERE key_id IN(".implode(',',$keywordIdArray) .")";	
				//echo $updateQyr;exit;
				$this->dbfunctions->mysql_qry($updateQyr,0);
			}
			return array($srcPath,$srcFile);
		}else{
			return false;
		}
	}

	/**
	 * Function missedTemplates
	 *
	 * @param
	 * @return
	 */		
	public function missedTemplates($ids)
	{
		
		$cond=array();
		foreach ($ids as $key => $value) {
			if($value[1]!=''){
				$cond[]=$value[1];
			}
		}
		//echo "<pre>"; print_r($cond);exit;
		$ids=implode("','",$cond);
		$keywordsSql="SELECT * 
						  FROM ".$this->keywords_table."
						  WHERE `key_id` IN ('".$ids."')
						    AND `key_status`=1";
		//echo $keywordsSql;exit;			
		$data=$this->dbfunctions->mysql_qry($keywordsSql,1);			      	
		$templatesData=$this->createTemplates($data,1);
		return $templatesData;
	}

	/* create_docx_file function
   * 
   *  This will create docx of each row of xlsx file.
   *  @param $header - top header content in array, 
   *  @param $header_2 - top 2nd row header content in array,
   *  @param $data - 1 line of row xlxs file in arary,
   *  @param $path - path of docx file to be created,
   *  @param $fill_colors - color for all cell in array
   *  @param $key - row number of Array
   *  
   */
	 
	 function create_docx_file($data,$path,$namekey){
	
	   // $header_color= array();
	   // $header_2_color= array();
	   // $data_color= array();
	   //echo "<pre>"; print_r($data);exit;
	   // $header_color = columns_color_included_for_docx($fill_colors[1]);
	   // $header_2_color = columns_color_included_for_docx($fill_colors[2]);
	   // $data_color = columns_color_included_for_docx($fill_colors[$key]);
	 	$header_color="9fe59f";
	 	$insertColor="FFFFFF";
	   
	    $PHPWord = new PHPWord();
        // document style orientation and margin 
        $sectionStyle = array('orientation' => 'landscape', 'marginLeft'=>600, 'marginRight'=>600, 'marginTop'=>600, 'marginBottom'=>600, 'colsNum' => 2);
        $section = $PHPWord->createSection($sectionStyle);

		// Define table style arrays
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80, 'width'=>100);

		// Define font style for first row
		$fontStyle1 = array('bold'=>true, 'align'=>'center');
		$fontStyle2 = array('bold'=>false, 'align'=>'center');


		// Add table style
		$PHPWord->addTableStyle('myOwnTableStyle', $styleTable);

		// Add table
		$table = $section->addTable('myOwnTableStyle');

       	$styleCell2Row = array('bgColor'=>'ffff66');
        $styleCell3RowData = array('align'=>'center');
        $styleCellRemaining = array('bgColor'=>'0070c0');
        $index=0;
        foreach ($data as $key => $value) {
        	if(($index%2)==0){
        		if($index==0){
        			//1st Row for article id
        			$table->addRow();
        			$col_bg_1 = array('bgColor'=>"fbce4e");
					$table->addCell(2000,$col_bg_1)->addText($this->write_to_docx("Article"), $fontStyle);
					$col_bg_1 = array('bgColor'=>"fbce4e");
					$table->addCell(13300,$col_bg_1)->addText($this->write_to_docx($namekey), $fontStyle2);
					$table->addRow();
        		}else{
        			//Empty Row for translation after every 2 iterations 
        			$table->addRow();
        			$col_bg_1 = array('bgColor'=>"b2edfb");
					$table->addCell(2000,$col_bg_1)->addText($this->write_to_docx("Translation"), $fontStyle);
					$col_bg_2 = array('bgColor'=>$insertColor);
					$table->addCell(13300,$col_bg_2)->addText($this->write_to_docx(""), $fontStyle2);
					//add new next row for iteration
					$table->addRow();
        		}
        		
        	}
        	$size=($index%2==1 && $index!=0)?13300:2000;
        	$myfontStyle=($index%2==1 && $index!=0)?$fontStyle2:$fontStyle;
			$col_bg_1 = array('bgColor'=>$header_color);
			$table->addCell($size,$col_bg_1)->addText($this->write_to_docx($data[$index]), $myfontStyle);
        	
        	$index++;
        	
        }
        //LAst Line 
        $table->addRow();
		$col_bg_1 = array('bgColor'=>"b2edfb");
		$table->addCell(2000,$col_bg_1)->addText($this->write_to_docx("Translation"), $fontStyle);
		$col_bg_2 = array('bgColor'=>$insertColor);
		$table->addCell(13300,$col_bg_2)->addText($this->write_to_docx(""), $fontStyle2);
		//add new next row for iteration
		

       	$file_name_docx = "pr-".$namekey;

		// Save File
		$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
		$objWriter->save($path.$file_name_docx.".docx");
		//exit; 
		 return $file_name_docx.".docx";
	 }	 

	  /* write_to_docx function
   * 
   *  This will encode character before writing in XSLX
   *  @param $value - final array data for writing in XLSX, 
   *  @return $value - double decode it and then pass to writerXlsxM6Dev1 function
   */
	  
	 function write_to_docx($value){
        //$value = iconv("ISO-8859-1", "UTF-8", $value);
       	//$value = html_entity_decode(htmlentities($value, ENT_COMPAT, 'UTF-8')); 
       	$value = str_replace("&rsquo;", "'", $value) ;
       	$basiclib=new basiclib();
       	if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
        	utf8_decode($value);	
        }	
        return $value;	 
	 }
	 

	/**
	 * Function escapeString
	 *
	 * @param
	 * @return
	 */		
	public function escapeString($str)
	{	
		$str = str_replace("&rsquo;", "'", $str) ;
		$str = str_replace("’", "'", $str) ;
		$str=addslashes($str);
		$basiclib=new basiclib();
		if($basiclib->getOS($_SERVER['HTTP_USER_AGENT']) == 'Windows'){
        	$str=utf8_decode($str);	
        }
		return $str;
	}
	
	/**
	 * Function insertTranslations
	 * insert all translation on the basis of keyword_id in database
	 * @param
	 * @return
	 */		
	public function insertTranslations($insertdata)
	{	
		$sql="INSERT IGNORE INTO ".$this->translations_table."
				    (`kw_key_id`, `kw_language`, `kw_translation`)
			  VALUES ".implode(',',$insertdata);
	    //echo $sql; //exit;
		if($this->dbfunctions->mysql_qry($sql,0))
		{
			return true;
		}else{
			return false;
		}
		
		//echo "<pre>"; print_r($sql);exit;
	}
	
	/* writeXlsxPernodRicard function
   * 
   *  This will writer data array to XLSX file.
   *  @param $data - all writing content in array, 
   *  @param $file_path where to writer XSLX file,
   *  
   */	 
	 
	public function writeXlsxPernodRicard($data,$columns,$file_path,$lang)
    {
	
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
        foreach ($data as $k=>$row)
        {
            $col = 'A';
            $header_colors=array();
            

		   //echo "Debug 4 $k<pre>"; print_r ($colors[$k]); echo "</pre>";	exit;
	       $header_colors = $colors[$k];
            
            
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
				
				$stylArr1 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'ff4949')));
							
				$stylArr2 = array('font' => array('name' => 'Arial', 'size' => '12', 'color' => array('rgb' => '000000'), 'bold' => true), 'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'f4a6a6')));
								
				if($rowCount> 0 && in_array($col,$columns)){
					//echo md5($value); 
					$sql = "SELECT clpk.key_id, clpk.key_text, clpk.key_hash, clpkt.kw_translation, clpkt.kw_language
							FROM `cl_pernodricard_keywords` clpk
							LEFT JOIN `cl_pernodricard_keywords_translations` clpkt ON clpk.key_id = clpkt.kw_key_id
							WHERE key_hash = '".md5(trim($value))."' and kw_language='".$lang."'";
					
					if($row = mysql_fetch_array($this->dbfunctions->mysql_qry($sql,1)))
					{						
							if(empty($row[3]) ||  empty($row['kw_translation'])){
								$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr2);
								$value = $row[0]."|".$row[1];
							}else{
								   $value = html_entity_decode(utf8_encode($row[3]));	
								//echo "<br />";
							}		
					}else{
							$objPHPExcel->getActiveSheet()->getStyle($col.($rowCount + 1)) -> applyFromArray($stylArr1);
							$value = $value;			
						
					}	
					
					//echo $value."<br />";
				}					
				
				$objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);

                $col++;
                
            }
            $rowCount++;
        }
        //exit;

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

}
