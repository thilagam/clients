<?php
/*
	file: inline_function.php

	This file defines a function which hacks two strings so they can be
	used by the Text_Diff parser, then recomposes a single string out of
	the two original ones, with inline diffs applied.

	The inline_diff code was written by Ciprian Popovici in 2004,
	as a hack building upon the Text_Diff PEAR package.
	It is released under the GPL.

	There are 3 files in this package: inline_example.php, inline_function.php, inline_renderer.php
*/

if (!defined('TEXT_ROOT')) {
	define('TEXT_ROOT', dirname(__FILE__) . '/');

	// for the following two you need Text_Diff from PEAR installed
	include_once TEXT_ROOT . 'Text/Diff.php';
	include_once TEXT_ROOT . 'Text/Diff/Renderer.php';
	include_once TEXT_ROOT . 'Text/Diff/Renderer/unified.php';

	// this is my own renderer
	include_once TEXT_ROOT . 'inline_renderer.php';
}

//include_once 'Text/Diff/Mapped.php';

function inline_diff($text1, $text2, $nl) {

	// create the hacked lines for each file
	$htext1 = chunk_split($text1, 1, "\n");
	$htext2 = chunk_split($text2, 1, "\n");

	// convert the hacked texts to arrays
	// if you have PHP5, you can use str_split:
	/*
	$hlines1 = str_split(htext1, 2);
	$hlines2 = str_split(htext2, 2);
	*/
	// otherwise, use this code
	for ($i=0;$i<strlen($text1);$i++) {
		$hlines1[$i] = substr($htext1, $i*2, 2);
	}
	for ($i=0;$i<strlen($text2);$i++) {
		$hlines2[$i] = substr($htext2, $i*2, 2);
	}

/*
	$text1 = str_replace("\n",$nl,$text1);
	$text2 = str_replace("\n",$nl,$text2);
*/
	$text1 = str_replace("\n"," \n",$text1);
	$text2 = str_replace("\n"," \n",$text2);

	$hlines1 = explode(" ", $text1);
	$hlines2 = explode(" ", $text2);

	// create the diff object
	$diff = new Text_Diff($hlines1, $hlines2);

	/*$diff1 = $diff->getDiff();
	//$op ='';print_r($diff1);//echo sizeof($diff1);

	for ($i=0; $i < sizeof($diff1); $i++)
	{
	   if (strcasecmp(implode(' ', $diff1[$i]->orig), implode(' ', $diff1[$i]->final)) == 0)
	      $op[] = '<del>' . implode(' ', $diff1[$i]->orig) . '</del><ins>' . implode(' ', $diff1[$i]->final) . '</ins>' ;
	   else
	      $op[] = implode(' ', $diff1[$i]->orig) ;
	}*/

	//return implode(' ', $op);

	// you can add 4 other parameters, which will be the ins/del prefix/suffix tags
	$renderer = new Text_Diff_Renderer_inline(50000);
	//$renderer = new Text_Diff_Renderer();

	ob_start();
	echo $renderer->render($diff);
	$opcontent = ob_get_contents();
	ob_end_clean();
	return $opcontent;

	//return $renderer->render($diff);
}

?>
