
<?php
/*$s1="The city offers a great number of museums including the MUSEUM1, MUSEUM2 and MUSEUM3 providing the opportunity to learn more about what made CITY what it is today.";
$s2="The city offers a great number of museums including the MUSEUM1, MUSEUM2 and MUSEUM3 providing the opportunity to learn more about what made CITY what it is today.";
echo get_matching_percentage($s1,$s2,$min_sequence_size=5);


function get_matching_percentage($client_text,$competitor_text,$min_sequence_size=5)
{
	$client_words = explode(" ", $client_text);
	$no_of_client_text_words =sizeof($client_words);
	$no_of_common_seq_words = 0;
	$i=0;
	$word_matches = '';
	while (($i+$min_sequence_size) <= $no_of_client_text_words )
	{
		$temp_sequence = array_slice($client_words, $i, $min_sequence_size);//$client_words($i,$min_sequence_size)." ";
		echo "<br>";
		$temp_sequence = implode(" ",$temp_sequence);
		$temp_sequence_size = $min_sequence_size;
		if (strpos($temp_sequence,$competitor_text) ) //
		{
			while(in_array($competitor_text,$temp_sequence))
			{
				if (($i+$temp_sequence_size+1) >$ $no_of_client_text_words)
				{
				break;
				}
					$temp_sequence = $client_words($i,$temp_sequence_size+1)." ";					
					$temp_sequence_size += 1;				
					$word_matches += $temp_sequence;
					$i += $temp_sequence_size;
					$no_of_common_seq_words += $temp_sequence_size;

			}
		}
		else
		{
			$i += 1;
		}
	}
	return (($no_of_common_seq_words / $no_of_client_text_words) * 100);
}
*/




$s1="After a long day of exploring a sightseeing then a";
$s2="After a long day of exploring a sightseeing then a";

echo get_matching_percentage($s1,$s2,$min_sequence_size=5);

function get_matching_percentage($client_text,$competitor_text,$min_sequence_size)
{
 $client_words = explode(" ", $client_text);
 $no_of_client_text_words =sizeof($client_words);
 $no_of_common_seq_words = 0;
 $i=0;
 $word_matches = '';
 while (($i+$min_sequence_size) <= $no_of_client_text_words )
 {
 	
	echo $i+$min_sequence_size."::".$no_of_client_text_words."<br>";
  $temp_sequence = array_slice($client_words, $i, $min_sequence_size);//$client_words($i,$min_sequence_size)." ";
  $temp_sequence = implode(" ",$temp_sequence);
  //echo "<br>";
  $temp_sequence_size = $min_sequence_size;
  //echo $temp_sequence.":".$competitor_text;exit;
  if(strpos($competitor_text,$temp_sequence))
  {
  	//echo "sd";exit;
   while(strpos($competitor_text,$temp_sequence))
   {
   
    if (($i+$temp_sequence_size+1) > $no_of_client_text_words)
    {
		//echo "break";
    	break;
    }
	 $temp_sequence = implode(" ",array_slice($client_words, $i, $min_sequence_size));     
      $temp_sequence_size += 1;    
	
    $word_matches .= $temp_sequence." ";
    $i += $temp_sequence_size;
    $no_of_common_seq_words += $temp_sequence_size;
   }
  }
  else
  {
   $i += 1;
  }
 }
 /*echo "----------------------------------------<br/>";
 echo $no_of_common_seq_words;
 echo("<br/>!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! <br/>");
 echo($no_of_client_text_words);
 echo("******************************    <br/>");
 echo($word_matches."\n<br/>");*/
 echo $no_of_common_seq_words;
 return (($no_of_common_seq_words / $no_of_client_text_words) * 100 );
}



?>