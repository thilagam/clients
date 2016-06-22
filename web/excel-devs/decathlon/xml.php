<?php
$xmlData = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
<rss version=\"2.0\"
	xmlns:excerpt=\"http://wordpress.org/export/1.2/excerpt/\"
	xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"
	xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\"
	xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
	xmlns:wp=\"http://wordpress.org/export/1.2/\"
>

<channel>
" ;
foreach($xmls as $xml) :

$xmlData .= "<item>
		<title>{$xml[1]}</title>
		<dc:creator>chloe.bataille@fr.decathlon.com</dc:creator>
		<description></description>
		<content:encoded>
			<![CDATA[
			<p> {$xml[2]} </p>
			]]>
		</content:encoded>
		<excerpt:encoded><![CDATA[]]></excerpt:encoded>
		<wp:comment_status>open</wp:comment_status>
		<wp:ping_status>open</wp:ping_status>
		<wp:post_name>{$xml[1]}</wp:post_name>
		<wp:status>pending</wp:status>
		<wp:menu_order>0</wp:menu_order>
		<wp:post_type>page</wp:post_type>
		<wp:is_sticky>0</wp:is_sticky>
			<wp:postmeta>
				<wp:meta_key>_yoast_wpseo_focuskw</wp:meta_key>
				<wp:meta_value><![CDATA[{$xml[3]}]]></wp:meta_value>
				</wp:postmeta>
			<wp:postmeta>
				<wp:meta_key>_yoast_wpseo_title</wp:meta_key>
				<wp:meta_value><![CDATA[{$xml[4]}]]></wp:meta_value>
				</wp:postmeta>
			<wp:postmeta>
				<wp:meta_key>_yoast_wpseo_metadesc</wp:meta_key>
				<wp:meta_value><![CDATA[{$xml[5]}]]></wp:meta_value>
			</wp:postmeta>
			<wp:postmeta>
				<wp:meta_key>_yoast_wpseo_linkdex</wp:meta_key>
				<wp:meta_value><![CDATA[{$xml[6]}]]></wp:meta_value>
			</wp:postmeta>
</item>" ;
endforeach ;
$xmlData .= "


</channel>
</rss>" ;

// Saving xml file

$fp = fopen($file_path,"w");
fwrite($fp,$xmlData);
fclose($fp);
chmod($file_path, 0777);
?>
