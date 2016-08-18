<?php
include_once(LAHALLE2_PATH."/dbfunctions.php");
include_once(INCLUDE_PATH."/basiclib.php");
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
					//$svalue="<b>".$svalue."</b>";
					$svalue="<strong>".$svalue."</strong>";
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
					//$value=($value!='')?$value:'';
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
	 	$basiclib = new Basiclib();
	 	switch ($type) {
	 		case 1:
	 			//echo "<pre>"; print_r($rowData );exit;
			 	$common=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[2][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2])
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
			 				//$semiComArray[0]=strip_tags($rowData[$key-3][2]). "(".$validations[$key-3]." + ".$validations[$key-2]." Characters)";
			 				$semiComArray[0] = strip_tags($rowData[$key-3][2]);
			 				$rowCount++;
			 				$tempCommon=array_merge($common,$semiComArray);
			 				$tempRow[]=$lastString;
			 				$tempRow[]="text";
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
			 	//echo "<pre>";print_r($sheetRows);exit;
	 			break;
	 		
	 		case 2:
	 			/* Create Rows manually from inconsistant pattern  */		
	 			/* ROW1 */
	 			$cleansous1=$this->validateAndCleanHtml($rowData[9][2],$validations[8]);
	 			$cleansous2=$this->validateAndCleanHtml($rowData[10][2],$validations[9]);

	 			$cleansous3=$this->validateAndCleanHtml($rowData[12][2],$validations[11]);
	 			$cleansous4=$this->validateAndCleanHtml($rowData[13][2],$validations[12]);
	 			$cleansous5=$this->validateAndCleanHtml($rowData[14][2],$validations[13]);

	 			$cleansous6=$this->validateAndCleanHtml($rowData[16][2],$validations[15]);
	 			$cleansous7=$this->validateAndCleanHtml($rowData[17][2],$validations[16]);
	 			$cleansous8=$this->validateAndCleanHtml($rowData[18][2],$validations[17]);

	 			$cleansous9=$this->validateAndCleanHtml($rowData[20][2],$validations[19]);
	 			$cleansous10=$this->validateAndCleanHtml($rowData[21][2],$validations[20]);
	 			$cleansous11=$this->validateAndCleanHtml($rowData[22][2],$validations[21]);

	 			$cleansous12=$this->validateAndCleanHtml($rowData[24][2],$validations[23]);
	 			$cleansous13=$this->validateAndCleanHtml($rowData[25][2],$validations[24]);

			 	$row_1=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2]),
			 				//strip_tags($rowData[8][2])." ( ".$validations[8]." + ".$validations[9]." ) ",
			 				strip_tags($rowData[8][2]),
			 				1,
			 				1,
			 				1,
			 				1,
			 				1,
			 				strip_tags($rowData[8][2]),
			 				'', //empty
			 				'', //empty
			 				$cleansous1[0],
			 				$cleansous1[2],
			 				$cleansous2[0],
			 				$cleansous2[2],
			 				$cleansous1[1]." ".$cleansous2[1],
			 				"text"
			 			);
			 	
	 			/* ROW2 */		
	 			//$cleanString3=$this->validateAndCleanHtml($rowData[13][2],$validations[12]);
	 			//$cleanString4=$this->validateAndCleanHtml($rowData[14][2],$validations[13]);
	 			//$cleansous=$this->validateAndCleanHtml($rowData[12][2],$validations[11]);
	 			//echo "<pre>"; print_r($cleansous);exit;
			 	$row_2=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2]),
			 				//strip_tags($rowData[11][2])." ( ".$validations[11]." + ".$validations[12]." + ".$validations[13]." ) ",
			 				strip_tags($rowData[11][2]),
			 				2,
			 				2,
			 				3,
			 				1,
			 				1,
			 				strip_tags($rowData[11][2]),
			 				$cleansous3[0],
			 				$cleansous3[2], 
			 				$cleansous4[0],
			 				$cleansous4[2],
			 				$cleansous5[0],
			 				$cleansous5[2],
			 				"<h3>".ltrim($cleansous3[0],'ERR-')."</h3>".$cleansous4[1]." ".$cleansous5[1],
			 				"text"
			 			);
			 	
			 	/* ROW3 */		
			 	//$cleanString5=$this->validateAndCleanHtml($rowData[17][2],$validations[16]);
			 	//$cleansous2=$this->validateAndCleanHtml($rowData[16][2],$validations[15]);
			 	
	 			$row_3=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2]),
			 				//strip_tags($rowData[15][2])." ( ".$validations[15]." + ".$validations[16]." ) ",
			 				strip_tags($rowData[15][2]),
			 				2,
			 				2,
			 				3,
			 				2,
			 				2,
			 				strip_tags($rowData[15][2]),
			 				$cleansous6[0],
			 				$cleansous6[2], 
			 				$cleansous7[0],
			 				$cleansous7[2],
			 				$cleansous8[0],
			 				$cleansous8[2],
			 				"<h3>".ltrim($cleansous6[0],'ERR-')."</h3>".$cleansous7[1]." ".$cleansous8[1],
			 				"text"
			 			);
	 			
				/* ROW 4 */		
			 	//$cleanString6=$this->validateAndCleanHtml($rowData[20][2],$validations[19]);
			 	//$cleansous3=$this->validateAndCleanHtml($rowData[19][2],$validations[18]);
			 	
	 			$row_4=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2]),
			 				//strip_tags($rowData[18][2])." ( ".$validations[18]." + ".$validations[19]." ) ",
			 				strip_tags($rowData[19][2]),
			 				2,
			 				2,
			 				3,
			 				1,
			 				3,
			 				strip_tags($rowData[19][2]),
			 				$cleansous9[0],
			 				$cleansous9[2], 
			 				$cleansous10[0],
			 				$cleansous10[2], 
			 				$cleansous11[0],
			 				$cleansous11[2], 
			 				"<h3>".ltrim($cleansous9[0],'ERR-')."</h3>".$cleansous10." ".$cleansous11[1],
			 				"text"
			 			);
	 			
	 			/* ROW 5 */		
	 			//$cleanString7=$this->validateAndCleanHtml($rowData[22][2],$validations[21]);
	 			//$cleanString8=$this->validateAndCleanHtml($rowData[23][2],$validations[22]);
			 	$row_5=array(
			 				strip_tags($rowData[1][2]),
			 				//strip_tags($rowData[3][2]),
			 				"TUI",
			 				strip_tags($rowData[4][2]),
			 				strip_tags($rowData[5][2]),
			 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
			 				//strip_tags($rowData[7][2]),
			 				//strip_tags($rowData[21][2])." ( ".$validations[21]." + ".$validations[22]." ) ",
			 				strip_tags($rowData[23][2]),
			 				3,
			 				1,
			 				1,
			 				1,
			 				1,
			 				strip_tags($rowData[23][2]),
			 				'', //empty
			 				'', //empty
			 				$cleansous12[0],
			 				$cleansous12[2],
			 				$cleansous13[0],
			 				$cleansous13[2],
			 				$cleansous12[1]." ".$cleansous13[1],
			 				"text"
			 			);
			 	$sheetRows=array($row_1,$row_2,$row_3,$row_4,$row_5);	
			 	
	 			break;
	 			 /*
					* Author: Thilagam

					* Date of comment: 5/8/2016

					* https://trello.com/c/xt3oD2aE/139-tui-themes-input-output

					* https://trello.com/c/N6MwZ9mg/125-tui-characters-numbers-checking-dev-docx-to-xlsx

	 			 */
	 		case 3:
	 			$cleanString10 = $this->validateAndCleanHtml($rowData[9][2],$validations[8]);
	 			$cleanString11 = $this->validateAndCleanHtml($rowData[10][2],$validations[9]);
	 			$cleanString12 = $this->validateAndCleanHtml($rowData[11][2],$validations[10]);

	 			$clearString13 = $this->validateAndCleanHtml($rowData[13][2],$validations[12]);
	 			$clearString14 = $this->validateAndCleanHtml($rowData[14][2],$validations[13]);
	 			$clearString15 = $this->validateAndCleanHtml($rowData[15][2],$validations[14]);

	 			$clearString16 = $this->validateAndCleanHtml($rowData[17][2],$validations[16]);
	 			$clearString17 = $this->validateAndCleanHtml($rowData[18][2],$validations[17]);

	 			$clearString18 = $this->validateAndCleanHtml($rowData[20][2],$validations[19]);
	 			$clearString19 = $this->validateAndCleanHtml($rowData[21][2],$validations[20]);

	 			$row_1 = array(
	 				strip_tags($rowData[1][2]),
	 				//strip_tags($rowData[3][2]),
	 				"TUI",
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
	 				//strip_tags($rowData[7][2]),
	 				//"CodeGroupe autour du monde"."(".$validations[8]."+".$validations[9]."+".$validations[10]."caractères )",
	 				strip_tags($rowData[8][2]),
	 				1,
	 				2,
	 				2,
	 				1,
	 				1,
	 				strip_tags($rowData[8][2]),
	 				$cleanString10[0],
	 				$cleanString10[2],
	 				$cleanString11[0],
	 				$cleanString11[2],
	 				$cleanString12[0],
	 				$cleanString12[2],
	 				"<h3>".strip_tags($cleanString10[1])."</h3>".$cleanString11[1]." ".$cleanString12[1],
	 				"text"
				);
	 			$row_2 = array(
					strip_tags($rowData[1][2]),
	 				//strip_tags($rowData[3][2]),
	 				"TUI",
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
	 				//strip_tags($rowData[7][2]),	
	 				//"CodeGroupe autour du monde"."(".$validations[12]."+".$validations[13]."caractères )",
	 				strip_tags($rowData[12][2]),
	 				1,
	 				2,
	 				2,
	 				2,
	 				2,
	 				strip_tags($rowData[12][2]),
	 				$clearString13[0],
	 				$clearString13[2],
	 				$clearString14[0],
	 				$clearString14[2],
	 				$clearString15[0],
	 				$clearString15[2],
	 				"<h3>".strip_tags($clearString13[1])."</h3>".$clearString14[1]." ".$clearString15[1],
	 				"text"
				);
				$row_3 = array(
					strip_tags($rowData[1][2]),
	 				//strip_tags($rowData[3][2]),
	 				"TUI",
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
	 				//strip_tags($rowData[7][2]),	
	 				//"Nos destinations CodeGroupe"."(".$validations[15]."+".$validations[16]."caractères )",
	 				strip_tags($rowData[16][2]),
	 				2,
	 				1,
	 				1,
	 				1,
	 				1,
	 				strip_tags($rowData[16][2]),
	 				" ",
	 				" ",
	 				$clearString16[0],
	 				$clearString16[2],
	 				$clearString17[0],
	 				$clearString17[2],
	 				$clearString16[1]." ".$clearString17[1],
	 				"text"
				);

				$row_4 = array(
					strip_tags($rowData[1][2]),
	 				//strip_tags($rowData[3][2]),
	 				"TUI",
	 				strip_tags($rowData[4][2]),
	 				strip_tags($rowData[5][2]),
	 				$basiclib->normaliseUrlString(strip_tags(strtolower($rowData[6][2]))),
	 				//strip_tags($rowData[7][2]),	
	 				//"Nos destinations CodeGroupe"."(".$validations[18]."+".$validations[19]."caractères )",
	 				strip_tags($rowData[19][2]),
	 				3,
	 				1,
	 				1,
	 				1,
	 				1,
	 				strip_tags($rowData[19][2]),
	 				" ",
	 				" ",
	 				$clearString18[0],
	 				$clearString18[2],
	 				$clearString19[0],
	 				$clearString19[2],
	 				$clearString18[1] ." ".$clearString19[1],
	 				"text"

				);
				$sheetRows=array($row_1,$row_2,$row_3,$row_4);	
	 			break;
	 		
	 	}
	 	
	 //echo "<pre>"; print_r($sheetRows);exit;
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