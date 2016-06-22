<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>
<div class="span10 content">				
	<h2 class="heading">DISTRICENTER
		<span class="pull-right">
			<form action="" method="GET" class="form-inline">
				<input type="text" name="reference" placeholder="Search reference.." value="<?=$_REQUEST['reference']; ?>" class="span8">
				<input type="hidden" name="client" value="<?=$_REQUEST['client']; ?>">
				<button class="btn" type="submit"><i class="icon-search"></i></button>
			</form>
		</span>
	</h2>
<?php
$reference = $_REQUEST['reference'];

function getDistricenterClientReferences($check_reference = NULL, $client_image_path, $url, $client)
{
    $refs = glob($client_image_path . "/*", GLOB_ONLYDIR);
    //print_r($refs);exit;
    $loop = 0;
    foreach ($refs as $index => $folder) {
        $img_directory = $folder;
        $img_directory_name = basename($img_directory);
        $reference_directories = glob($img_directory . "/$check_reference{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);

		usort($reference_directories, "sorted") ;
        $references_text .= '<div class="row-fluid" id="'.$img_directory_name.'"><div class="span12"><h4 class="heading">' . $img_directory_name . '</h4>';
		
        if (count($reference_directories) > 0)
        {
            $reference_array = array();
            foreach ($reference_directories as $image) {
                $image = basename($image) ;
              
                //$s = getLahalleShoeRefFrmImg($client, $image) ;
                $s = explode("-", $image);
                if ($s[0] && strstr($image, "-")) {
					//echo $s[0];
                    $reference = $s[0];
                    $reference_array[$reference] = $img_directory_name;
                }
            }
          // 
            ksort($reference_array);
             ///print_r($reference_array);exit;
             if(!empty($reference_array)){
				foreach ($reference_array as $reference => $value)
				{
					$pathinfo = pathinfo($reference);
					$reference=$pathinfo['filename'];
					//echo substr($reference, 0, 1);
					$replaced=substr($reference, 1);
					$part=($reference[0] == '0') ? $replaced:$reference;
					//$part=ltrim($reference, '0');
					$references_text .= '<a target="DISTRICENTER" href="' . $url . '/pictures.php?client=DISTRICENTER&reference=' . $part . '"><span class="badge">' . $reference . '</span></a>&nbsp;';
				}
			}
        }
        else
            $references_text .= '<span class="label label-important">No References Found</span>';
	
        $references_text .= '</div></div>';
    }
    echo $references_text;
}

getDistricenterClientReferences($reference, DISTRICENTER_IMAGE_PATH, DISTRICENTER_URL, 'DISTRICENTER') ;
?>	
</div>
<?php
include_once(INCLUDE_PATH."/footer.php");   
?>
