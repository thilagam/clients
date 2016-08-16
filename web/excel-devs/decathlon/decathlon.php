<?php
mb_internal_encoding('UTF-8');
ini_set('default_charset', 'UTF-8');
define("INCLUDE_PATH",ROOT_PATH."/includes");
require_once(INCLUDE_PATH.'/basiclib.php');
include_once(INCLUDE_PATH."/config_path.php");
require_once(INCLUDE_PATH."/PHPWord.php");
include_once(VOYAGES_PATH."/dbfunctions.php");
/**
 * Decathlon Lib is a PHP-functions library used in Decathlon Devs. It is collection of commonly used functions for Interrent devs
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
class Decathlon 
{
	public $table='cl_decathlon_articles';
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

		//echo "<pre>"; print_r($allData);

		$insertData=array(
							'decathlon_mot_attrib'=>$this->escape_quote($data[1]),
							'decathlon_url'=>$this->escape_quote($data[2]),
							'decathlon_page_position'=>$this->escape_quote($data[3]),
							'decathlon_remark'=>$this->escape_quote($data[4]),
							'decathlon_doc_name'=>$this->escape_quote($data[5]),
							'decathlon_meta_title'=>$this->escape_quote($data[6]),
							'decathlon_meta_desc'=>$this->escape_quote($data[8]),
							'decathlon_title'=>$this->escape_quote($data[10]),
							'decathlon_subtitle_1'=>$this->escape_quote($data[11]),
							'decathlon_para_1'=>$this->escape_quote($data[12]),
							'decathlon_subtitle_2'=>$this->escape_quote($data[13]),
							'decathlon_para_2'=>$this->escape_quote($data[14]),
							'decathlon_subtitle_3'=>$this->escape_quote($data[15]),
							'decathlon_para_3'=>$this->escape_quote($data[16]),
							'decathlon_concat'=>$this->escape_quote($data[17]),
							'decathlon_other_data'=>json_encode(array($this->escape_quote($data[18]),$this->escape_quote($data[19]),$this->escape_quote($data[20]))),
							'decathlon_create_date'=>date('Y-m-d H:i:s')
						);
		//echo "<pre>"; print_r($insertData);exit;
		
		$fields=implode(',', array_keys($insertData));
		$otherData='';
		if($this->escape_quote($allData[18])!='' && $this->escape_quote($allData[19])!='' && $this->escape_quote($allData[20]) )
		{
			$otherData=json_encode(array($this->escape_quote($allData[18]),$this->escape_quote($allData[19]),$this->escape_quote($allData[20])));
		}

		$values="	'".$this->escape_quote($allData[1])."',
					'".$this->escape_quote($allData[2])."',
					'".$this->escape_quote($allData[3])."',
					'".$this->escape_quote($allData[4])."',
					'".$this->escape_quote($allData[5])."',
					'".$this->escape_quote($allData[6])."',
					'".$this->escape_quote($allData[8])."',
					'".$this->escape_quote($allData[10])."',
					'".$this->escape_quote($allData[11])."',
					'".$this->escape_quote($allData[12])."',
					'".$this->escape_quote($allData[13])."',
					'".$this->escape_quote($allData[14])."',
					'".$this->escape_quote($allData[15])."',
					'".$this->escape_quote($allData[16])."',
					'".$this->escape_quote($allData[17])."',
					'".$otherData."',
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
