<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");


if(isset($_GET['action']) && $_GET['action']=='download' && isset($_GET['file']))
{
	downloadXML($_GET['file']);
}
else if(isset($_GET['action']) && $_GET['action']=='view' && isset($_GET['file']))
{
	viewXML($_GET['file']);	
	exit;
}

if(isset($_POST['upload']))
{
	
	
    $file1  =   pathinfo($_FILES['excel_file']['name']) ;
    
    if(($file1['extension'] == 'xlsx') || ($file1['extension'] == 'xls'))
    {
        if($file1['extension'] == 'xlsx')
        {
            $xls1Arr  = oxlsxRead($_FILES['excel_file']['tmp_name']) ;
            $xlsE1 = 0;
        }
        else
        {
            $xls1Arr  = oxlsRead($_FILES['excel_file']['tmp_name']) ;
            $xlsE1 = 1;
        }
        
        $rowCount = 1 ;
        foreach ($xls1Arr[0][0] as $excel)
        {
            foreach ($excel as $k=>$v)
            {
                $xml_array[$rowCount][$k-1] = $v ;
            }
            $rowCount++;
        }
    	
    	$sas_reference=($_POST['sas_reference']-1);
    	$name_reference=($_POST['name_reference']-1);
    	$short_reference=($_POST['short_reference']-1);
    	$long_reference=($_POST['long_reference']-1);
    	$comp_reference=($_POST['comp_reference']-1);
    	
    	$reference_array=array('sas'=>$sas_reference,'name'=>$name_reference,'short_desc'=>$short_reference,'long_desc'=>$long_reference,'composition'=>$comp_reference);

	/*	foreach($xml_array as $key => $val){
			$sasid=$val[$reference_array['sas']];
			
			//$sasid=	str_replace(",","",$sasid);
			if($sasid=='5179'){
				echo "HERE";
				echo convert($val[$reference_array['long_desc']]);
				exit;
			}
		}*/

    	
        writeXML($xml_array,$reference_array);
        exit;
    }
    else
    {
        header("Location:".CAROLL_URL."/create-xml.php?msg=file_error");
    }
    
	//print_r($reference_array);exit;
    
    /*echo "<pre>";print_r($xml_array);   echo "</pre>";exit;
    
	require_once(INCLUDE_PATH."/reader.php");	
	
	//print_r($_FILES);exit;
	if(($_FILES['excel_file']['type']=='application/x-msexcel' || $_FILES['excel_file']['type']=='application/vnd.ms-excel' || $_FILES['excel_file']['type']=='application/xls' || $_FILES['excel_file']['type']=='application/msword' ))
	{	
		$data2 = new Spreadsheet_Excel_Reader();
		$data2->setOutputEncoding('Windows-1252');
		$data2->read($_FILES['excel_file']['tmp_name']);
		//echo $data2->dump(TRUE,TRUE);exit;		
		//echo "Number of sheets: " .sizeof($data2->sheets) . "\n";exit;
		
		//echo "<pre>";	print_r($data2->sheets);echo "</pre>";exit('mmm');
		
		$sheets=sizeof($data2->sheets);
		for($i=0;$i<$sheets;$i++)
		{
			if($data2->sheets[$i]['numRows'])	
			{
				$x=1;
				$z=1;
				while($x<=$data2->sheets[$i]['numRows']) {
					$y=1;
					while($y<=$data2->sheets[$i]['numCols']) {
					
							//if($x==1)
							$xml_array[$x][$y-1]=isset($data2->sheets[$i]['cells'][$x][$y])?$data2->sheets[$i]['cells'][$x][$y]:'';
							$y++;
					}
					$x++;
				}			
			}
		}
        echo "<pre>";print_r($xml_array);   echo "</pre>";exit;
		//echo "<pre>";print_r(array_filter($reference_array));	echo "</pre>";exit;
		writeXML($xml_array,$reference_array);
		exit;*/	
}		
else
{
	exit;//header("Location:create-xml.php?msg=file_error");
}

function writeXML($xml_array,$reference_array)
{
	
	$date=date('Ymd');
	
	$pattern = CAROLL_XML_PATH.'/modeles_ep_'.$date.'*.xml';
	$arraySource = glob($pattern);
	$xml_count=count($arraySource);
	
	$time_extract=date("d/m/Y H:i:s",time());	
	$xml_dir=CAROLL_XML_PATH;
	$file_name="modeles_ep_".$date."_".($xml_count+1);
	$ext=".xml"; 
	$xml_file=$file_name.$ext;
	$xmldoc=$xml_dir."/".$xml_file;	

	/* if(!is_dir($xml_dir))
		 mkdir($xml_dir, 0777,TRUE);
	 chmod($xml_dir,0777) */;
	unset($xml_array[1]);
	 
	if($fp=fopen($xmldoc,'w+'))
	{			
		fwrite($fp,"<?xml version='1.0' encoding='UTF-8' ?>\n");		
		fwrite($fp,"<modeles time_extract=\"$time_extract\">\n");
		
		foreach($xml_array as $model)
		{
			$reference=$model[$reference_array['name']];
			if(substr($reference,0,1)=='Z' || substr($reference,0,1)=='z')
				$long_desc_add_text="\n\n" ;//. ' Le mannequin mesure 1m80. ';
			else
				$long_desc_add_text="\n\n" . ' Le mannequin mesure 1m80 et porte une taille 36.';
			/*if(substr($reference,0,1)=='Z' || substr($reference,0,1)=='z')
				$long_desc_add_text="\n\n" . ' Le mannequin mesure 1m78 ';
			else
				$long_desc_add_text="\n\n" . ' Le mannequin mesure 1m78 et porte une taille 36.';*/
			
			$sasid=$model[$reference_array['sas']];
			
			$sasid=	str_replace(",","",$sasid);
		/*	if($sasid=='5179'){
				$longDesc=$model[$reference_array['long_desc']];
				$longDesc=convert($longDesc);
				echo $longDesc;exit
			}*/
			fwrite($fp,"<modele>\n");
			fwrite($fp,"<sasid>".$sasid."</sasid>\n");
			fwrite($fp,"<name>".$model[$reference_array['name']]."</name>\n");
			fwrite($fp,"<shortdesc><![CDATA[".convert($model[$reference_array['short_desc']])."]]></shortdesc>\n");			
			fwrite($fp,"<longdesc><![CDATA[".convert($model[$reference_array['long_desc']].$long_desc_add_text)."]]></longdesc>\n");
			fwrite($fp,"<compositionautre><![CDATA[".convert($model[$reference_array['composition']])."]]></compositionautre>\n");
			fwrite($fp,"<entretientexte><![CDATA[]]></entretientexte>\n");
			fwrite($fp,"</modele>\n");			
		}
		
		fwrite($fp,"</modeles>");	
		fclose($fp);
		chmod(@$xmldoc,0777);
	}
	else
		echo "unable to create";
	//return $xmldoc;
	if (file_exists($xmldoc)) {
		header("Location:".CAROLL_URL."/create-xml.php?client=CAROLL&msg=success&file=".$file_name);	
	   
	} else {
	  //echo 'ERROR: Could not save XML.';
	 header("Location:".CAROLL_URL."/create-xml.php?client=CAROLL&msg=error");	
	}

}
function convert($string)
{
	$string=stripslashes($string);
	$replace = array(utf8_decode('Æ')=>'AE', utf8_decode('æ')=>'ae',utf8_decode('þ')=>utf8_decode('b'),utf8_decode('œ').'"'=>'oe',utf8_decode('Â')=>'' );
	$string = strtr( $string, $replace );
	
	//$string=str_replace(array_keys($replace), $replace, $string); 
	$string=str_replace("�","'",$string);
	//$string=htmlspecialchars($string,ENT_XML1);
	//$string=htmlspecialchars($string, ENT_XML1,'UTF-8', true);
   // $string=htmlentities($string, ENT_XML1,'UTF-8', true);
    
	$string=utf8_encode($string);
	//$string = html_entity_decode(htmlentities($string, ENT_COMPAT, 'UTF-8'));
	
	return $string;
}



function downloadXML($filename)
{
	$filename=$filename.".xml";
	$path_file=CAROLL_XML_PATH."/".$filename;
	//echo $path_file;exit;
	if(file_exists($path_file))
	{	
		
		header("Content-type: text/xml");
		header("Content-Disposition: attachment; filename=$filename");
		ob_clean();
        flush();
		readfile("$path_file"); 
		exit;
	}	
	else
		header("Location:".CAROLL_URL."/create-xml.php");
}
function viewXML($file)
    {
        $file=$file.".xml";
		$path_file=CAROLL_XML_PATH."/".$file;
		//echo $path_file;exit;
		if(file_exists($path_file))
		{
		
			header ('Content-type: text/html; charset=utf-8');
			//Initialize the XML parser
           $parser=xml_parser_create();

            //Specify element handler
            xml_set_element_handler($parser,"start","stop");
            //Specify data handler
            xml_set_character_data_handler($parser,"char");

                 //Open XML file
            $fp=fopen($path_file,"r");

			//Read data
            while ($data=fread($fp,4096))
            {
                 //$data=utf8_encode($data);
                xml_parse($parser,$data,feof($fp)) or
                die (sprintf("XML Error: %s at line %d",
                xml_error_string(xml_get_error_code($parser)),
                xml_get_current_line_number($parser)));
            }
			//Free the XML parser
			xml_parser_free($parser);
		}	
    }
    //Function to use at the start of an element
    function start($parser,$element_name,$element_attrs)
    {
        echo "<b><u>$element_name </u></b>: ";
		
        switch($element_name)
        {
            case "NOTE":
                echo "-- Note --<br />";
                break;
            case "TO":
                echo "To: ";
                break;
            case "FROM":
                echo "From: ";
                break;
            case "HEADING":
                echo "Heading: ";
                break;
            case "BODY":
                echo "Message: ";
        }
    }

//Function to use at the end of an element
   function stop($parser,$element_name)
    {
        //echo "<br/>";
    }
    //Function to use when finding character data
    function char($parser,$data)
    {
        $data=htmlspecialchars_decode($data);
		echo $data."<br>";
    }

?>
