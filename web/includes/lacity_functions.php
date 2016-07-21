<?php
include_once("common_functions.php");

//Korben fucntions to get referece images

function getLacityReferenceImages($client,$reference=NULL)
{
	$client_image_path=LACITY_IMAGE_PATH."/";
	
	$ref_directory=glob($client_image_path."*", GLOB_ONLYDIR);
    //echo "<pre>";print_r($ref_directory);exit;
    
	$loop=0;
	if(count($ref_directory)>0)
	{
		foreach($ref_directory as $index=>$folder)
		{
			$img_directory = $folder;
			
			$files = glob($img_directory."/*/".$reference."{*.jpg,*.jpeg,*.JPG,*.JPEG,*.png}", GLOB_BRACE);
			
			if(count($files)>0)	
			{		
				foreach($files as $file)
				{	
					$string = basename($file);
					$img=$file;
					$img=str_replace($_SERVER['DOCUMENT_ROOT'],"",$img);
					
			
			//echo $img;exit;
				
					$reference_images.='<a href="http://clients.edit-place.com'.$img.'" data-gallery="">
								<img class="img-polaroid" src="'.$img.'" width=150 height=250/>
							</a>';
				
				}
			}		
			continue;
		}
	}	
	echo $reference_images;
	//exit;

}
function getLacityReferences($client,$check_reference=NULL)
{
	$client_image_path=LACITY_IMAGE_PATH."/";
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR); 	
	
	
	$loop=0;	
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;			
		$img_directory_name=basename($img_directory);
		
		$reference_directories = glob($img_directory."/$check_reference*", GLOB_ONLYDIR);
		
		usort($reference_directories, function($a, $b) {
			return filemtime($a) < filemtime($b);
		}); 	
		
		//echo "<pre>";print_r($reference_directories);		
		$references_text.='<div class="row-fluid">
							<div class="span12">
								<h4 class="heading">'.$img_directory_name.'</h4>
			';   
		
		if(count($reference_directories)>0)
		{
			$reference_array=array();
			foreach($reference_directories as $reference)
			{
				$reference=basename($reference);
				
				if($client)	
				{	
					$reference = substr($reference,0,7);
				}	
					
				$reference_array[$reference]=$img_directory_name;		
			}
			foreach($reference_array as $reference=>$value)
			{
				if($client)
					$references_text.='<a target="lacity" href="'.SITE_URL.'/excel-devs/lacity/view-pictures.php?client='.$client.'&reference='.$reference.'"><span class="badge">'.$reference.'</span></a>&nbsp;';
				else	
					$references_text.='<a target="lacity" href="'.SITE_URL.'/excel-devs/lacity/index.php?client='.$img_directory_name.'"><span class="badge badge-info">'.$reference.'</span></a>&nbsp;';
			}	
		}	
		else
		{
			$references_text.='<span class="label label-important">No References Found</span>';	
		}		
		
		$references_text.='		</div>
						</div>';		
		
		
	}
	echo $references_text;
	//exit;


}

/*Korben fucntions to get all references except given folder 
if ALL folders TRUE then getting all references other than given folder
if ALL false getting references of a given folder*/ 

function getALLlacityReferencesExceptGivenFolder($folder_id,$all_folders=TRUE)
{
	$client_image_path=LACITY_IMAGE_PATH."/";
	
	$refs=glob($client_image_path."*", GLOB_ONLYDIR); 	
	
	//echo '<pre>';//print_r($refs);exit($client_image_path);
	$loop=0;
	foreach($refs as $index=>$folder)
	{
		$img_directory = $folder;
		$img_directory_name=basename($img_directory);
		//exit($img_directory.'--'.$img_directory_name);
		if(($img_directory_name!=$folder_id && $all_folders ) || ($img_directory_name==$folder_id && !$all_folders))
		{
			$reference_directories = glob($img_directory."/*", GLOB_ONLYDIR);
			
			//echo $img_directory."/*" ; print_r($reference_directories);
			usort($reference_directories, function($a, $b) {
				return filemtime($a) < filemtime($b);
			});
			
			if(count($reference_directories)>0)
			{
				//$reference_array=array();
				foreach($reference_directories as $reference)
				{
					$reference=basename($reference);
					
					//if($client)	
					{	
						$reference = substr($reference,0,7);
					}	
						
					$reference_array[]=$reference;
					$reference_array_folder_names[$reference]=$img_directory_name;
				}				
			}
		}	
	}	
	$reference_array=array_values(array_unique($reference_array));
	
	return array($reference_array,$reference_array_folder_names);
	//exit;
}

//function to get all folders  list a client to generate Writer files
function getLaCityFoldersList()
{
	$client_folder_path=LACITY_IMAGE_PATH."/";//exit($client_folder_path);	
	$all_directory=glob($client_folder_path."*", GLOB_ONLYDIR);	
	
	if(count($all_directory))
	{
		usort($all_directory, "sortedarr");
		
		return $all_directory;
	}
	

}


    function sortedarr($a, $b)
    {
        if (filemtime($a) == filemtime($b)) {
            return 0;
        }        
        return (filemtime($a) > filemtime($b)) ? -1 : 1 ;
    }
//upload korben source w.r.t client and save the reference in config file.
function uploadLacitySource($client,$reference,$source_file=NULL,$valid=NULL)
{

	$client_source_path=constant($client."_SOURCE_PATH");
	$client_config_file=constant($client."_CONFIG_FILE");
	//$client_source_path=($client."_SOURCE_PATH");
	//$client_config_file=($client."_CONFIG_FILE");
	//
	if(!$valid)
	{
		$file_name = $client."_tmp.csv";
		$source_file_temp = $client_source_path."/$file_name";
//exit($client."--".$client_source_path.'--'.$client_config_file.'|'.$source_file_temp);	
		move_uploaded_file($source_file['csv_file']['tmp_name'], $source_file_temp);
		$arrayCsv = file($source_file_temp);
		//echo "<pre>";print_r($arrayCsv);exit;
		return $arrayCsv;
	}
	else if($valid==1)
	{
		$file_name = $client."_tmp.csv";
		$old_name2 = $client_source_path."/$file_name";
		
		if(file_exists($old_name2))
		{
			//current becomes old
			$old_name1 = $client_source_path."/$client.csv";
			$new_name1 = $client_source_path."/$client"."_".date("YmdHis").".csv";			
			if(file_exists($old_name1))
				rename($old_name1,$new_name1);
				
				
			//temp become new
			$new_name2 = $client_source_path."/$client.csv";
			rename($old_name2,$new_name2);
		
				
			//reference storing file updation
			$arr_ref_soc[basename($old_name1)] = $reference;
			$fp = fopen($client_config_file,"w");
			fwrite($fp,serialize($arr_ref_soc));
			fclose($fp);	
		}
	}

}
//Get All korben source files of a client
function getAllPreviousLacitySourceFiles($client)
{
	$client_source_path=constant($client."_SOURCE_PATH");
	$client_config_file=constant($client."_CONFIG_FILE");
	
	$pattern = $client_source_path.'/'.$client.'*.*';
	$arraySource = glob($pattern);	
	//sort($arraySource);
	usort($arraySource, function($a, $b) {
			return filemtime($a) < filemtime($b);
		});
	return $arraySource;
	
}
//Korben csv Process
function lacityCsvProcess($all_reference_array,$all_reference_array_folder_names,$folder_reference_array,$csvArray,$source_index)
{
	//$xlsArray[0][0]='Doublon';
	$xlsArray[0][0]='Reference prod';
	$xlsArray[0][1]='URL';
	//$xlsArray[0][3]='Comment';
	//echo "<pre>";print_r($csvArray);echo '<--###-->';print_r($folder_reference_array);exit;
    
    for($i=0;$i<count($csvArray[0]);$i++)
    {
    	if(in_array($i, array(6, 8, 9, 11, 13, 21)))
    	{
    		if($i!=($source_index-1))
    		{
    			$string=$csvArray[0][$i];
    			$string=lacityCleanString($string);
    			$xlsArray[0][]=$string;
    		}
    	}
    }
    $xlsArray[0][8]='Titre';
    $xlsArray[0][9]='Nb car';
    $xlsArray[0][10]='Descriptif';
    $xlsArray[0][11]='Nb car';
    $xlsArray[0][12]='Market Place';
    $xlsArray[0][13]='Nb car';
    
	$i=1;
	foreach($folder_reference_array as $reference)
	{
		$url = SITE_URL."/excel-devs/lacity/view-pictures.php?client=$client&reference=$reference";
		if(in_array($reference,$all_reference_array))
			$doublon = "DOUBLON (".$all_reference_array_folder_names[$reference].")";
		else 
			$doublon = '';
		//$comment='';
		
		if(array_key_exists($reference,$csvArray))
		{
    		//$xlsArray[$i][0]=$doublon;
    		$xlsArray[$i][0]=$reference;
    		$xlsArray[$i][1]=$url;
        }
		//$xlsArray[$i][3]=$comment;
		
		//echo $reference."<pre>";print_r($csvArray);exit;
		if(array_key_exists($reference,$csvArray))
		{
			for($z=0;$z<count($csvArray[$reference]);$z++)
			{
			    if(in_array($z, array(6, 8, 9, 11, 13, 21)))
                {
    				//echo $reference."<pre>";print_r($csvArray);exit;
    				if($z!=($source_index-1))
    				{
    					$string=$csvArray[$reference][$z];
    					$string=lacityCleanString($string);
    					$xlsArray[$i][]=$string;
    				}
                }
			}
		}
        $xlsArray[$i][8]='';
        $xlsArray[$i][9]='';
        $xlsArray[$i][10]='';
        $xlsArray[$i][11]='';
        $xlsArray[$i][12]='';
        $xlsArray[$i][13]='';
		
		$i++;
	}
	return $xlsArray;

}
function lacityCleanString($string)
{
	$string=str_replace("<br>"," ",$string);
	$string=str_replace("</br>"," ",$string);
	$string=str_replace("\r\n"," ",$string);
	$string=str_replace("\n"," ",$string);
	$string=str_replace("\r"," ",$string);
	$string=str_replace("\t"," ",$string);
	return utf8_decode($string);

}
?>
