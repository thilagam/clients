<?php
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
define("INCLUDE_PATH",ROOT_PATH."/includes");
require_once(INCLUDE_PATH.'/basiclib.php');
include_once(INCLUDE_PATH."/config_path.php");
require_once(INCLUDE_PATH."/PHPWord.php");
include_once(LA_REDOUTE_PATH."/dbfunctions.php");
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
class laredoute 
{
	public $table='cl_laredoute_articles';
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
							'laredoute_keywords'=>$this->escape_quote($data[2]),
							'laredoute_url'=>$this->escape_quote($data[3]),
							'laredoute_meta_title'=>$this->escape_quote($data[4]),
							'laredoute_meta_description'=>$this->escape_quote($data[5]),
							'laredoute_title'=>$this->escape_quote($data[6]),
							'laredoute_seo_text'=>$this->escape_quote($data[7]),
							'laredoute_create_date'=>date('Y-m-d H:i:s')
						);
		
		$fields=implode(',', array_keys($insertData));
		$values="	'".$this->escape_quote($allData[1])."',
					'".$this->escape_quote($allData[2])."',
					'".$this->escape_quote($allData[3])."',
					'".$this->escape_quote($allData[4])."',
					'".$this->escape_quote($allData[5])."',
					'".$this->escape_quote($allData[6])."',
					'".date('Y-m-d H:i:s')."'
				";
		$this->dbfunctions->mysql_insert($this->table,$fields,$values);
		//echo "<pre>"; print_r($insertData);exit;

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
