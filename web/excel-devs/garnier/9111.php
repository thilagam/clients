<?php
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("GARNIER_PATH", ROOT_PATH."/excel-devs/garnier/");
	
error_reporting(0);
$zh = zip_open(GARNIER_PATH.'/123.docx');
while (($entry = zip_read($zh))){
	$entry_name = zip_entry_name($entry);
	if (preg_match('/word\/document\.xml/im', $entry_name))
		@$content['word'] = zip_entry_read($entry, zip_entry_filesize($entry));
	elseif (preg_match('/word\/_rels\/document\.xml\.rels/im', $entry_name))
		@$content['rel'] = zip_entry_read($entry, zip_entry_filesize($entry));
}

$relxml = new XMLReader();
$relxml->XML($content['rel']);
while($relxml->read()){
	//echo $relxml->name."<br>";
	if($relxml->name == "Relationship")
	{
		$relns[$relxml->getAttribute('Id')] = $relxml->getAttribute('Target') ;
	}
}
//echo "<pre>";print_r($relns);exit;

$text_content = "";
$xml = new XMLReader();
$xml->XML($content['word']);
while($xml->read()){
	if($xml->name == "w:p") { $text_content .= !empty($text_content) ? "</p><p>" : "<p>"; }
	
	if($xml->name == "w:hyperlink" && !in_array($xml->getAttribute('r:id'), $ridArr)) {
		$rid = $xml->getAttribute('r:id');
		$ridArr[] = $xml->getAttribute('r:id');
	}

	if($xml->name == "w:r") { unset($bold); }
	
	if($xml->name == "w:b") { $bold = 1; }
	
	if ($xml->name == "w:t" && $xml->nodeType == XMLReader::ELEMENT){
		if($rid) $text_content .= "<a href='{$relns[$rid]}'>";
		if($bold) $text_content .= "<strong>";
		
		$text_content .= $xml->readInnerXML();
		
		if($bold) { $text_content .= "</strong>"; unset($bold); }
		if($rid) { $text_content .= "</a>"; unset($rid); }
	}
}
$text_content .= "</p>";

exit($text_content) ;

?>
