<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']); // Root path
define("INCLUDE_PATH",ROOT_PATH."/includes"); // Include files path

include_once(INCLUDE_PATH."/config_path.php"); // Configuration file (Devs writer file path, success/error messages and other configurations)
include_once(INCLUDE_PATH."/common_functions.php"); // Includes all common functions used for excel devs
include_once(INCLUDE_PATH."/header.php"); // Header file includes html header section with css/js links, common js/jquery script etc
?>

<div class="span12 content">			
	<h3 class="heading"><? echo "ACCESSORIE DIFFUSION :: ".$_REQUEST['reference'] ?> </h1>    
<? if($_REQUEST['reference']) { ?>
	<div id="links">
<?		
$reference = $_REQUEST['reference']; // Reference number selected

// Showing product images for purticular client reference 
getClientReferenceImages($reference, ACCESSORIE_DIFFUSION_IMAGE_PATH);

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
include_once(INCLUDE_PATH."/footer.php"); // Footer html section
 ?>
