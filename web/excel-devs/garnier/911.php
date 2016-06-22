<?php
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("GARNIER_PATH", ROOT_PATH."/excel-devs/garnier/");
	
	function o_docxToTxt($path, $outpath)
    {
        if (!file_exists($path))
            return -1;
        $zh = zip_open($path);
        $content = "";
        while (($entry = zip_read($zh))){
            $entry_name = zip_entry_name($entry);
            if (preg_match('/word\/document\.xml/im', $entry_name)){
                $content = zip_entry_read($entry, zip_entry_filesize($entry));
                break;
            }
        }
        $text_content = "";exit(htmlentities($content));
        if ($content){
            $xml = new XMLReader();
            $xml->XML($content);
            while($xml->read()){//echo $xml->name . "<br>";//print_r($xml);
                if ($xml->name == "w:t" && $xml->nodeType == XMLReader::ELEMENT){
					
					//if ($xml->name == "w:b")
						//$text_content .= "<b>";
					
					//echo $xml->name . "<br>";
					
                    //$text_content .= $xml->readInnerXML();
                    $text_content .= $xml->readOuterXml();
                    $space = $xml->getAttribute("xml:space");
                    if ($space && $space == "preserve")
                        $text_content .= " ";
                        
					//if ($xml->name == "w:b")
						//$text_content .= "</b>";
						
                }
                if (($xml->name == "w:p" || $xml->name == "w:br" || $xml->name == "w:cr") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\n";
                if (($xml->name == "w:tab") && $xml->nodeType == XMLReader::ELEMENT)
                    $text_content .= "\t";
            }exit($text_content);
            file_put_contents($outpath, $text_content);
            return 0;
        }
        return -1;
    }echo "<pre>";
    o_docxToTxt(GARNIER_PATH.'123.docx', GARNIER_PATH.'123.txt');
?>
