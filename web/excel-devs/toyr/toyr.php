<?php
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');

define("INCLUDE_PATH",ROOT_PATH."/includes");

require_once(INCLUDE_PATH.'/basiclib.php');
include_once(INCLUDE_PATH."/config_path.php");
require_once(INCLUDE_PATH."/PHPWord.php");
include_once(TOYR_PATH."/dbfunctions.php");

/**
* bnp class used to process bnp dev related things 
* bnp dev is template creation and update dev 
* usage is mainly docx and xlsx 
* PHP versions 5
*
* @package    Edit-place
* @copyright  Edit-place
* @license    Edit-place
* @version    1.0
* @category   Library Class
* @author 	  Vinayak Kadolkar
*/
class Toyr
{	
	public $table='cl_toyr_articles';
	public $dbfunctions;

	public function __construct(){
		$this->dbfunctions=new dbfunctions();
	}

	
	/**
	 * Function createArticle
	 *
	 * @param
	 * @return
	 */		
	public function createArticle($data,$allData,$file)
	{
		//$otherData=json_encode(array($this->escape_quote($allData[4]),$this->escape_quote($allData[6]),$this->escape_quote(utf8_encode($allData[10])),$this->escape_quote(utf8_encode($allData[15])),$this->escape_quote(utf8_encode($allData[14]))),JSON_UNESCAPED_UNICODE);
		//echo $this->escape_quote(utf8_encode($allData[10]));
		//echo "<pre>"; print_r($allData); exit;
		$insertData=array(
							//'toyr_article_id'=>$this->escape_quote($data[1]),
							'toyr_skn'=>$this->escape_quote($data[2]),
							'toyr_uid'=>$this->escape_quote($data[3]),
							'toyr_pid'=>$this->escape_quote($data[4]),
							'toyr_url'=>$this->escape_quote($data[5]),
							'toyr_marque'=>$this->escape_quote($data[6]),
							'toyr_create_date'=>date('Y-m-d H:i:s')
						);

	
		
		$fields=implode(',', array_keys($insertData));
		$values="	
					'".$this->escape_quote($allData[1])."',
					'".$this->escape_quote($allData[2])."',
					'".$this->escape_quote($allData[3])."',
					'".$this->escape_quote($allData[4])."',
					'".$this->escape_quote($allData[5])."',
					'".date('Y-m-d H:i:s')."'
				";
		$this->dbfunctions->mysql_insert($this->table,$fields,$values);
	}

	/**
	 * Function createTemplates
	 *
	 * @param
	 * @return
	 */		
	public function createTemplates($data,$path)
	{
		
	}

	

	/**
	 * Function docxColumns
	 *
	 * @param
	 * @return
	 */		
	public function docxColumns($array_data,$hd,$titles,$lang)
	{
		
	   $array_data1=array();
	   $array_data1[1]= $array_data[0];
	   $array_data1[2]= $array_data[1];
	   $array_data1[3]= $array_data[2];
	   $array_data1[4]= $array_data[3];
	   $array_data1[5]= $array_data[4];
	   $array_data1[6]= $array_data[5];
	   $array_data1[7]= $array_data[6];
	   $array_data1[8]= $array_data[7];
	   $array_data1[9]= $array_data[8];
	   

	  
	  // if($hd == 1) { $array_data1[1]=0; } else if($hd == 2) { $array_data1[1]="Article ID"; } else { $array_data1[1]=""; }   
	   
	   //if($hd == 1) { $array_data1[10]=9; } else if($hd == 2) { $array_data1[10]="Paragraphe 1"; } else { $array_data1[10]=''; }
	   //if($hd == 1) { $array_data1[11]=10; } else if($hd == 2) { $array_data1[11]="Paragraphe 2 "; } else { $array_data1[11]=''; }
	  
	   //if($hd == 1) { $array_data1[12]=11; } else if($hd == 2) { $array_data1[12]="Paragraphe 3"; } else { $array_data1[12]=''; }
	   //if($hd == 1) { $array_data1[13]=12; } else if($hd == 2) { $array_data1[13]="Paragraphe 4 "; } else { $array_data1[13]=''; }
	    
	  // if($hd == 1) { $array_data1[14]=13; } else if($hd == 2) { $array_data1[14]="Paragraphe 5"; } else { $array_data1[14]=''; }
	   
	   
	   return $array_data1;
	}
	

	/**
	 * Function nextArticleId
	 *
	 * @param
	 * @return
	 */		
	public function nextArticleId()
	{
		$sql="SELECT `auto_increment` 
			  FROM INFORMATION_SCHEMA.TABLES
			  WHERE table_name = '".$this->table."'";
		$id=$this->dbfunctions->mysql_qry($sql,1);
		$id=mysql_fetch_array($id);
		return $id['auto_increment'];
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
		//$str=utf8_decode($str);
		return str_replace("'","''",$str);
	}
}

?>