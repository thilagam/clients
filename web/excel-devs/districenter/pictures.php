<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/common_functions.php");
include_once(INCLUDE_PATH."/header.php");
//include_once(INCLUDE_PATH."/left-menu.php");
?>

<div class="span12 content">			
	<h3 class="heading"><? echo "DISTRICENTER  :: ".$_REQUEST['reference'] ?> </h1>    
<? if($_REQUEST['reference']) { ?>
	<div id="links">
<?		
$reference = $_REQUEST['reference'];
function getDistricenterClientReferenceImages($reference = NULL, $client_image_path)
{
    $ref_directory = glob($client_image_path . "/*", GLOB_ONLYDIR);
    $loop = 0;
    if (count($ref_directory) > 0) {
        foreach ($ref_directory as $index => $folder) {
            $img_directory = $folder;
            $files = glob($img_directory . "/". $reference . "{*.jpg,*.jpeg,*.JPG,*.JPEG}", GLOB_BRACE);
            if (count($files) > 0) {
                foreach ($files as $file) {
                    $string = basename($file);
                    $img = $file;
                    $img = str_replace($_SERVER['DOCUMENT_ROOT'], "", $img);
                    $reference_images .= '<a href="' . SITE_URL . $img . '" data-gallery=""><img class="img-polaroid" src="' . $img . '" width=150 height=250/></a>';
                }
            }
            continue;
        }
    }
    echo $reference_images;
}
getDistricenterClientReferenceImages($reference, DISTRICENTER_IMAGE_PATH);

?>
	</div>
<? } else{ echo '<div class="alert alert-error">                               
					Reference/Client Missed in URL.
				</div>';
		}
?>
</div>
<div id="blueimp-gallery" class="blueimp-gallery">
    <!-- The container for the modal slides -->
    <div class="slides"></div>
    <!-- Controls for the borderless lightbox -->
    <h3 class="title"></h3>
    <a class="prev">&lsaquo;</a>
    <a class="next">&rsaquo;</a>
    <a class="close">&times;</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
    <!-- The modal dialog, which will be used to wrap the lightbox content -->
    <div class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body next"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left prev">
                        <i class="glyphicon glyphicon-chevron-left"></i>
                        Previous
                    </button>
                    <button type="button" class="btn btn-primary next">
                        Next
                        <i class="glyphicon glyphicon-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
