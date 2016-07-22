<?php
include_once(LAHALLE2_PATH."/dbfunctions.php");
/**
 * Lahalle Lib is a PHP-functions library used in tui Devs. It is collection of commonly used functions for Tui devs
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


class Tui 
{

	/**
	 * Function getHeaders
	 *
	 * @param
	 * @return
	 */		
	public function getHeaders($data)
	{
		if(empty($data)){
	 		return false;
	 	}	
	 	$headers=array();
	 	foreach ($data as $key => $value) {
	 		$headers[]=$value[1];
	 	}

	 	return $headers;

	}

	/**
	 * Function getValidations
	 *
	 * @param
	 * @return
	 */		
	public function getValidations($headers)
	{
		if(empty($headers)){
	 		return false;
	 	}	
	 	$validationArray=array();
	 	foreach ($headers as $key => $value) {
	 		$match=array();
	 		preg_match('/(?![^()]*\))\(.*?(\d+)/', trim(strip_tags($value)) , $match);
	 		//echo "------------------<br>".$value;
	 		//echo "<pre>"; print_r($match);
	 		if(!empty($match) && isset($match[1])){
	 			$validationArray[]=$match[1];
	 		}else{
	 			$validationArray[]='';
	 		}
	 	}
	 	return $validationArray;

	}

	/**
	 * Function validate
	 *
	 * @param
	 * @return
	 */		
	public function validateAndCleanHtml($text,$count)
	{
		$chechString=strip_tags($text);
		//echo "<br>".$count."<br>";
		$charCount=$this->countChars(trim($chechString));
		$origText=$text;

		$text = str_replace("[","||||",$text);
		$text = str_replace("]","|||||",$text);

		$text = str_replace("</w:p>","]",$text);
		$text = str_replace("<w:p>","[",$text);

		$matches = array();
		$matches=explode(']',$text);
		//echo "<pre>"; print_r($matches);
		$ind=1;
		$newArr=array();
		$pointStart=0;
		foreach($matches as $key => $value){
			$value = strip_tags($value);
			$value=ltrim($value,'[');
			$stars=explode('**', $value);
			$sind=1;
			$newValue='';
			foreach ($stars as $skey => $svalue) {
				if($sind%2==0 && $sind!=1){
					$svalue="<b>".$svalue."</b>";
				}	
				$newValue.=$svalue;
				$sind++;
			}
			$value=$newValue;
			//$value=preg_replace('/(\*\*)(.[^*]+)(\*\*)/', "<b>$2</b>", $value);
			//echo "<pre>"; print_r($value);
			if(preg_match("/(\#\#)(.+)(\#\#)/", $value)){
				//echo "<br><pre>"; print_r($value);
				if($pointStart==0){
					$value="<ul>".$value;
					$value=preg_replace('/(\#\#)(.+)(\#\#)/', "<li>$2</li>", $value);
					$pointStart++;
				}else{
					$value=preg_replace('/(\#\#)(.+)(\#\#)/', "<li>$2</li>", $value);
					$pointStart++;
				}
			}else{
				if($pointStart>0)
				{
					$value="</ul>".$value;
					$pointStart=0;
				}else{
					$value=($value!='')?"<p>".$value."</p>":'';
				}
			}
			$newArr[]=$value;

		}
		//exit;
		$newString=implode(' ',$newArr);
		//echo $count;
		if( $count==0 || $charCount>$count || strip_tags($origText)=='' ){
			$origText="ERR-".$origText;
		}
		$origText=strip_tags($origText);
		//echo $newString;
		return array($origText,$newString,$charCount);
	}

	/**
	 * Function countChars
	 *
	 * @param
	 * @return
	 */		
	public function countChars($string)
	{	
		$string=str_replace('#','', $string);
		$string=str_replace('*','', $string);
		//echo $string."<br>";
		//return $this->mb_count_chars($string);
		return mb_strlen($string, 'utf8');
	}

	/**
	 * Function segregateRows
	 *
	 * @param
	 * @return
	 */		
	public function segregateRow($rowData,$validations,$type)
	{
		if(empty($rowData)){
	 		return false;
	 	}	
	 	$sheetRows=array();
	 	switch ($type) {
	 		case 1:
	 			//echo "<pre>"; print_r($rowData );exit;
			 	$common=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[2][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2])
			 			);
			 	$semiComArray=array(
			 				'',0,1,1,1,1
			 			);
			 	$index=1;
			 	$rowCount=1;
			 	$tempRow=array();
			 	$tempCommon=array();
			 	
			 	$lastString='';
			 	foreach ($rowData as $key => $value) {
			 		if($key>=8)
			 		{
			 			if($index%3==1 && $index!=1)
			 			{	
			 				if($index==4){
			 					$semiComArray[2]=2;
			 				}else{
			 					$semiComArray[2]=1;
			 				}
			 				$semiComArray[1]=$rowCount;
			 				$semiComArray[0]=strip_tags($rowData[$key-3][2]). "(".$validations[$key-3]." + ".$validations[$key-2]." Characters)";
			 				$rowCount++;
			 				$tempCommon=array_merge($common,$semiComArray);
			 				$tempRow[]=$lastString;
			 				$sheetRows[]=array_merge($tempCommon,$tempRow);
			 				//echo "<pre>"; print_r($sheetRows);
			 				//exit;
			 				$tempRow=array();
			 				$lastString='';
			 				$tempRow[]=strip_tags($value[2]);
			 				
			 			}
			 			else
			 			{	
			 				if($index!=1){
			 					//echo $validations[$key-1]."<br>";
			 					$cleanString=$this->validateAndCleanHtml($value[2],$validations[$key-1]);
			 					//echo "<pre>"; print_r($cleanString);
			 					//echo utf8_encode($cleanString[1]);exit;
			 					$tempRow[]=$cleanString[0];
			 					$tempRow[]=$cleanString[2];
			 					$lastString.=$cleanString[1];
			 				}else{
			 					$tempRow[]=strip_tags($value[2]);
			 				}
			 			}
			 			$index++;
			 		}
			 	}
	 			break;
	 		
	 		case 2:
	 					
	 			/* Create Rows manually from inconsistant pattern  */		
	 			/* ROW1 */
	 			$cleanString1=$this->validateAndCleanHtml($rowData[9][2],$validations[8]);
	 			$cleanString2=$this->validateAndCleanHtml($rowData[10][2],$validations[9]);
			 	$row_1=array(
			 				strip_tags($rowData[1][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2]),
			 				strip_tags($rowData[8][2])." ( ".$validations[8]." + ".$validations[9]." ) ",
			 				1,
			 				1,
			 				1,
			 				1,
			 				1,
			 				strip_tags($rowData[8][2]),
			 				'', //empty
			 				'', //empty
			 				$cleanString1[0],
			 				$cleanString1[2],
			 				$cleanString2[0],
			 				$cleanString2[2],
			 				$cleanString1[1]." ".$cleanString2[1]
			 			);
	 			/* ROW2 */		
	 			$cleanString3=$this->validateAndCleanHtml($rowData[13][2],$validations[12]);
	 			$cleanString4=$this->validateAndCleanHtml($rowData[14][2],$validations[13]);
	 			$cleansous=$this->validateAndCleanHtml($rowData[12][2],$validations[11]);
	 			//echo "<pre>"; print_r($cleansous);exit;
			 	$row_2=array(
			 				strip_tags($rowData[1][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2]),
			 				strip_tags($rowData[11][2])." ( ".$validations[11]." + ".$validations[12]." + ".$validations[13]." ) ",
			 				2,
			 				2,
			 				3,
			 				1,
			 				1,
			 				strip_tags($rowData[11][2]),
			 				$cleansous[0],
			 				$cleansous[2], 
			 				$cleanString3[0],
			 				$cleanString3[2],
			 				$cleanString4[0],
			 				$cleanString4[2],
			 				"<h3>".ltrim($cleansous[0],'ERR-')."</h3>".$cleanString3[1]." ".$cleanString4[1]
			 			);
			 	/* ROW3 */		
			 	$cleanString5=$this->validateAndCleanHtml($rowData[17][2],$validations[16]);
			 	$cleansous2=$this->validateAndCleanHtml($rowData[16][2],$validations[15]);
			 	
	 			$row_3=array(
			 				strip_tags($rowData[1][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2]),
			 				strip_tags($rowData[15][2])." ( ".$validations[15]." + ".$validations[16]." ) ",
			 				2,
			 				2,
			 				3,
			 				2,
			 				2,
			 				strip_tags($rowData[15][2]),
			 				$cleansous2[0],
			 				$cleansous2[2], 
			 				'',
			 				'',
			 				$cleanString5[0],
			 				$cleanString5[2],
			 				"<h3>".ltrim($cleansous2[0],'ERR-')."</h3>".$cleanString5[1]
			 			);
				/* ROW 4 */		
			 	$cleanString6=$this->validateAndCleanHtml($rowData[20][2],$validations[19]);
			 	$cleansous3=$this->validateAndCleanHtml($rowData[19][2],$validations[18]);
			 	
	 			$row_4=array(
			 				strip_tags($rowData[1][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2]),
			 				strip_tags($rowData[18][2])." ( ".$validations[18]." + ".$validations[19]." ) ",
			 				2,
			 				2,
			 				3,
			 				1,
			 				3,
			 				strip_tags($rowData[18][2]),
			 				$cleansous3[0],
			 				$cleansous3[2], 
			 				'',
			 				'',
			 				$cleanString6[0],
			 				$cleanString6[2],
			 				"<h3>".ltrim($cleansous3[0],'ERR-')."</h3>".$cleanString6[1]
			 			);
	 			/* ROW 5 */		
	 			$cleanString7=$this->validateAndCleanHtml($rowData[22][2],$validations[21]);
	 			$cleanString8=$this->validateAndCleanHtml($rowData[23][2],$validations[22]);
			 	$row_5=array(
			 				strip_tags($rowData[1][2]),
			 				strip_tags($rowData[3][2]),
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				strip_tags($rowData[6][2]),
			 				strip_tags($rowData[7][2]),
			 				strip_tags($rowData[21][2])." ( ".$validations[21]." + ".$validations[22]." ) ",
			 				3,
			 				1,
			 				1,
			 				1,
			 				1,
			 				strip_tags($rowData[21][2]),
			 				'', //empty
			 				'', //empty
			 				$cleanString7[0],
			 				$cleanString7[2],
			 				$cleanString8[0],
			 				$cleanString8[2],
			 				$cleanString7[1]." ".$cleanString8[1]
			 			);
			 	$sheetRows=array($row_1,$row_2,$row_3,$row_4,$row_5);	

	 			break;
	 		case 3:
	 			$cleanString10 = $this->validateAndCleanHtml($rowData[9][2],$validations[8]);
	 			$cleanString11 = $this->validateAndCleanHtml($rowData[10][2],$validations[9]);
	 			$cleanString12 = $this->validateAndCleanHtml($rowData[11][2],$validations[10]);
	 			$clearString13 = $this->validateAndCleanHtml($rowData[13][2],$validations[12]);
	 			$clearString14 = $this->validateAndCleanHtml($rowData[14][2],$validations[13]);
	 			$clearString15 = $this->validateAndCleanHtml($rowData[16][2],$validations[15]);
	 			$clearString16 = $this->validateAndCleanHtml($rowData[17][2],$validations[16]);
	 			$clearString17 = $this->validateAndCleanHtml($rowData[19][2],$validations[18]);
	 			$clearString18 = $this->validateAndCleanHtml($rowData[20][2],$validations[19]);
	 			
	 			$row_1 = array(
	 				strip_tags($rowData[1][2]),
	 				strip_tags($rowData[3][2]),
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				strip_tags($rowData[6][2]),
	 				strip_tags($rowData[7][2]),
	 				"CodeGroupe autour du monde"."(".$validations[8]."+".$validations[9]."+".$validations[10]."caractères )",
	 				1,
	 				2,
	 				2,
	 				1,
	 				1,
	 				strip_tags($rowData[8][2]),
	 				strip_tags($rowData[9][2]),
	 				strip_tags($cleanString10[2]),
	 				strip_tags($rowData[10][2]),
	 				$cleanString11[2],
	 				strip_tags($rowData[11][2]),
	 				$cleanString12[2],
	 				"<h3>".strip_tags($rowData[9][2])."</h3><p>".strip_tags($rowData[10][2])."</p><p>".strip_tags($rowData[11][12])."</p>"
				);


				$row_2 = array(
					strip_tags($rowData[1][2]),
	 				strip_tags($rowData[3][2]),
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				strip_tags($rowData[6][2]),
	 				strip_tags($rowData[7][2]),	
	 				"CodeGroupe autour du monde"."(".$validations[12]."+".$validations[13]."caractères )",
	 				1,
	 				2,
	 				2,
	 				2,
	 				2,
	 				strip_tags($rowData[12][2]),
	 				strip_tags($rowData[13][2]),
	 				$clearString13[2],
	 				" ",
	 				" ",
	 				strip_tags($rowData[14][2]),
	 				$clearString14[2],
	 				"<h3>".strip_tags($rowdata[13][2])."</h3><p>".strip_tags($rowData[14][2])."</p>"
				);

				$row_3 = array(
					strip_tags($rowData[1][2]),
	 				strip_tags($rowData[3][2]),
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				strip_tags($rowData[6][2]),
	 				strip_tags($rowData[7][2]),	
	 				"Nos destinations CodeGroupe"."(".$validations[15]."+".$validations[16]."caractères )",
	 				2,
	 				1,
	 				1,
	 				1,
	 				1,
	 				strip_tags($rowData[15][2]),
	 				" ",
	 				" ",
	 				strip_tags($rowData[16][2]),
	 				$clearString15[2],
	 				strip_tags($rowData[17][2]),
	 				$clearString16[2],
	 				"<p>".strip_tags($rowData[16][2])."</p><p>".strip_tags($rowData[17][2])."</p>"
				);

				$row_4 = array(
					strip_tags($rowData[1][2]),
	 				strip_tags($rowData[3][2]),
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				strip_tags($rowData[6][2]),
	 				strip_tags($rowData[7][2]),	
	 				"Nos destinations CodeGroupe"."(".$validations[18]."+".$validations[19]."caractères )",
	 				3,
	 				1,
	 				1,
	 				1,
	 				1,
	 				strip_tags($rowData[18][2]),
	 				" ",
	 				" ",
	 				strip_tags($rowData[19][2]),
	 				$clearString17[2],
	 				strip_tags($rowData[20][2]),
	 				$clearString18[2],
	 				"<p>".strip_tags($rowData[19][2])."</p><p>".strip_tags($rowData[20][2])."</p>"

				);
				$sheetRows=array($row_1,$row_2,$row_3,$row_4);	
	 			break;
	 		
	 	}
	 	
	 //	echo "<pre>"; print_r($sheetRows);exit;
	 	return $sheetRows;


	}
	 
	/**
	* Counts character occurences in a multibyte string
	* @param string $input UTF-8 data
	* @return array associative array of characters.
	*/
	function mb_count_chars($input) {
	    $l = mb_strlen($input, 'UTF-8');
	    $unique = array();
	    for($i = 0; $i < $l; $i++) {
	        $char = mb_substr($input, $i, 1, 'UTF-8');
	        if(!array_key_exists($char, $unique))
	            $unique[$char] = 0;
	        $unique[$char]++;
	    }
	    return $unique;
	}

}
?>