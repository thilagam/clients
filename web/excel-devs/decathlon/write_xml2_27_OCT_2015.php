<?php

/* Updated on 13 OCT 2015 with new specification */


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
            $xls1Arr  = sxlsxRead($_FILES['excel_file']['tmp_name']) ;
            $xlsE1 = 0;
        }
        else
        {
            $xls1Arr  = sxlsRead($_FILES['excel_file']['tmp_name']) ;
            $xlsE1 = 1;
        }
        
	// Creating xml array from excel
        $rowCount = 1 ;
        foreach ($xls1Arr[0][0] as $excel)
        {
            foreach ($excel as $k=>$v)
            {
                $xml_array[$rowCount][$k-1] = $v ;
            }
            $rowCount++;
        }
    	
	// Setting title, content, kw, seo title, meta description and link references from post data
    	$titre_reference=($_POST['titre_reference']-1);
    	$contenu_reference=($_POST['contenu_reference']-1);
    	$keywords_reference=($_POST['keywords_reference']-1);
    	$seotitle_reference=($_POST['seotitle_reference']-1);
    	$metadesc_reference=($_POST['metadesc_reference']-1);
    	$linkdex_reference=($_POST['linkdex_reference']-1);
    	
	// Array for xml elements
    	$reference_array=array('titre'=>$titre_reference,'contenu'=>$contenu_reference,'keywords'=>$keywords_reference,'seotitle'=>$seotitle_reference,'metadesc'=>$metadesc_reference,'linkdex'=>$linkdex_reference);
    	
        writeXML($xml_array,$reference_array);
        exit;
    }
    else
    {
        header("Location:".DECATHLON_URL."/index2.php?msg=file_error");
    }
}		
else
{
	exit;//header("Location:index2.php?msg=file_error");
}

function writeXML($xml_array,$reference_array)
{
	$date=date('Ymd');
	
	$pattern = DECATHLON_XML_FILE_PATH2.'/decathlon_'.$date.'*.xml';
	$arraySource = glob($pattern);
	$xml_count=count($arraySource);
	
	$time_extract=date("d/m/Y H:i:s",time());	
	$xml_dir=DECATHLON_XML_FILE_PATH2;
	$file_name="decathlon_".$date."_".($xml_count+1);
	$ext=".xml"; 
	$xml_file=$file_name.$ext;
	$xmldoc=$xml_dir."/".$xml_file;


	unset($xml_array[1]);
	 
	if($fp=fopen($xmldoc,'w+'))
	{			
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss version=\"2.0\" xmlns:excerpt=\"http://wordpress.org/export/1.2/excerpt/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:wp=\"http://wordpress.org/export/1.2/\">
<channel>
";
		foreach($xml_array as $xmlArr)
		{
		    if(!empty($xmlArr[$reference_array['titre']]))
		    {
			$xml .= "<item>
					<title>".$xmlArr[$reference_array['titre']]."</title>
					<dc:creator>chloe.bataille@fr.decathlon.com</dc:creator>
					<description></description>
					<content:encoded>
						<![CDATA[
						".modifyCellContent($xmlArr[$reference_array['contenu']])."
						]]>
					</content:encoded>
					<excerpt:encoded><![CDATA[]]></excerpt:encoded>
					<wp:comment_status>open</wp:comment_status>
					<wp:ping_status>open</wp:ping_status>
					<wp:post_name>".$xmlArr[$reference_array['titre']]."</wp:post_name>
					<wp:status>draft</wp:status>
					<wp:menu_order>0</wp:menu_order>
					<wp:post_type>page</wp:post_type>
					<wp:is_sticky>0</wp:is_sticky>
					<!-- SEO Yoast -->
						<wp:postmeta>
							<wp:meta_key>_yoast_wpseo_focuskw</wp:meta_key>
							<wp:meta_value><![CDATA[".$xmlArr[$reference_array['keywords']]."]]></wp:meta_value>
							</wp:postmeta>
						<wp:postmeta>
							<wp:meta_key>_yoast_wpseo_title</wp:meta_key>
							<wp:meta_value><![CDATA[".$xmlArr[$reference_array['seotitle']]."]]></wp:meta_value>
							</wp:postmeta>
						<wp:postmeta>
							<wp:meta_key>_yoast_wpseo_metadesc</wp:meta_key>
							<wp:meta_value><![CDATA[".$xmlArr[$reference_array['metadesc']]."]]></wp:meta_value>
						</wp:postmeta>
						<wp:postmeta>
							<wp:meta_key>_yoast_wpseo_linkdex</wp:meta_key> 
							<wp:meta_value><![CDATA[".$xmlArr[$reference_array['linkdex']]."]]></wp:meta_value>
						</wp:postmeta>
			</item>
			";
		    }
		}
		$xml .= "</channel>
		</rss>" ;	
		fwrite($fp,$xml);	
		fclose($fp);
		chmod(@$xmldoc,0777);//exit($xml);
	}
	else
		header("Location:".DECATHLON_URL."/index2.php?client=DECATHLON&msg=error");	

	//return $xmldoc;
	if (file_exists($xmldoc)) {
		header("Location:".DECATHLON_URL."/index2.php?client=DECATHLON&msg=success&file=".$file_name);	
	   
	} else {
	  //echo 'ERROR: Could not save XML.';
	 header("Location:".DECATHLON_URL."/index2.php?client=DECATHLON&msg=error");	
	}

}

function modifyCellContent($string)
{
    $content = "";
	$return = "";

    // Apply Paraghraph Tags 
	$string = "<p>".$string."</p>";
	$string = str_replace("<h2>","</p><h2>",$string);
	$string = str_replace("</h2>","</h2><p>",$string);

    // Applying link + label
	preg_match_all('/\[(.*)\]/', $string, $matches);

	if(sizeof($matches[0]) > 0){
	for($i=0;$i<sizeof($matches[0]);$i++){
		//echo "matched: " . $matches[0][$i]. "\n";
		$modified = explode("*",$matches[1][$i]);
		$modified_link_label = "<a href='".trim($modified[0])."'>".trim($modified[1])."</a>";
		$string = str_replace($matches[0][$i],$modified_link_label,$string);	
	  }	
    } 
    //echo $string;
    
	// Adding strong tag
	$string_new = "";
	$count_astric=1;
	for ($i=0; $i<strlen($string); $i++){
        if ($string[$i] == "*"){
           $string_new.= (($count_astric%2!=0) ? '<strong>' : '</strong>');
           $count_astric++;
        }else
           $string_new.= $string[$i];
    }
	
	//echo $string_new;
	
	return str_replace("<p></p>","",$string_new);
		
	// Applying link + label
	/*foreach(explode("*#*", $string) as $k=>$v)
		$content .= strstr($v,'##') ? ('<a href="'.substr($v,0,strpos($v, '#')).'">'.substr($v, (strpos($v,'#')+2), (strlen($v)-strpos($v, '#'))).'</a>') : $v;

    echo $content;
    exit; 

	// Adding strong tag
	foreach(explode("**", $content) as $k=>$v)
		$return .= ($k%2!=0 ? '<strong>' : '') . $v . ($k%2!=0 ? '</strong>' : '') ;

	return ($return); */
}

function convert($string)
{
	$string=stripslashes($string);
	$string=str_replace("�","'",$string);
	//$string=htmlspecialchars($string);
	$string=utf8_encode($string);
	
	return $string;
}
function downloadXML($filename)
{
	$filename=$filename.".xml";
	$path_file=DECATHLON_XML_FILE_PATH2."/".$filename;
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
		header("Location:".DECATHLON_URL."/index2.php");
}
function viewXML($file)
    {
        $file=$file.".xml";
		$path_file=DECATHLON_XML_FILE_PATH2."/".$file;
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
