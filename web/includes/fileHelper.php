<?php
/**
 * Include Files Here
 * */
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
/**
 * FileHelper Lib is a PHP-functions library. It is collection of commonly used functions 
 * for file related works
 * PHP versions 4 and 5
 *
 * @package    Edit-place
 * @copyright  Edit-place
 * @license    Edit-place
 * @version    1.0
 * @category   Library Class
 * @author 	   Vinayak Kadolkar
 */
class FileHelper
{


	/**
	* Function upload 
	* function used to upload lbm related Files 
	* 
	* @package clientsEditPlace
	* @author Vinayak
	* @param  string $file  
	* @param string $path path where it needs to upload 
	* @param array $options configs for upload
	* @retuen boolean true/false
	*/
	function upload($file,$path,$options){
		
		$target_file    =   $path."/".$options['name'];
		$fileInfo=pathinfo($file);
		if(isset($option['ext']) && $option['ext']!=$fileInfo['ext']){
			$status['flag']=false;
			$status['error']="Extension not supported";
			return $status;
		}
		//Check and Rename the File 
		if(isset($options['ren']) && isset($options['ren_name']) && $options['ren_name']!=''){
			if (file_exists($target_file)) {
				if(!rename($target_file,$options['ren_name'])){
					$status['error']= "Sorry, file rename Error.";
					$status['flag']=false;
					return $status;
				}
				
			}	
		}
		
		// Check if file already exists
		if (file_exists($target_file)) {
			$status['error']= "Sorry, file already exists.";
			$status['flag']=false;
			return $status;
		}
		
		//print_r(pathinfo($file));exit;
		if(move_uploaded_file($file, $target_file)){
			chmod($target_file, 0777);
			$status['success']="File Uploaded";
			$status['flag']=true;
			return $status;
		}else{
			$status['error']="File not uploaded";
			$status['flag']=false;
			return $status;
		}
	}

	/**
	* Function writeXlsx
	* function used to create Writer file 
	* 
	* @package clientsEditPlace
	* @author Vinayak
	* @param  array $data  
	* @param  array $filepath
	* @return array $anchor_cols
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
					    
				}
			
                $objPHPExcel->getActiveSheet()->setCellValue($col.($rowCount+1), $value);
                if((strstr($value, "http://clients.edit-place.com/excel-devs/") && ($col=='E')) || (in_array($col, $anchorCols)))
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
}

?>