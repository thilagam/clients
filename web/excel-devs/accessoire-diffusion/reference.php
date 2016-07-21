<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']); // Root path
define("INCLUDE_PATH",ROOT_PATH."/includes"); // Include files path

include_once(INCLUDE_PATH."/session.php"); // Session handling file
include_once(INCLUDE_PATH."/config_path.php"); // Configuration file (Devs writer file path, success/error messages and other configurations)
include_once(INCLUDE_PATH."/common_functions.php"); // Includes all common functions used for excel devs
include_once(INCLUDE_PATH."/header.php"); // Header file includes html header section with css/js links, common js/jquery script etc
include_once(INCLUDE_PATH."/left-menu.php"); // Clients list menu
?>
<div class="span10 content">				
	<h2 class="heading">ACCESSORIE DIFFUSION
		<span class="pull-right">
			<form action="" method="GET" class="form-inline">
				<input type="text" name="reference" placeholder="Search reference.." value="<?=$_REQUEST['reference']; ?>" class="span8">
				<input type="hidden" name="client" value="<?=$_REQUEST['client']; ?>">
				<button class="btn" type="submit"><i class="icon-search"></i></button>
			</form>
		</span>
	</h2>
<?php
$reference = $_REQUEST['reference']; // Reference number selected

// Printing all client references with corresponding images url for this client
getClientReferences($reference, ACCESSORIE_DIFFUSION_IMAGE_PATH, ACCESSORIE_DIFFUSION_URL, 'ACCESSORIE_DIFFUSION') ;
?>
</div>
<?php
include_once(INCLUDE_PATH."/footer.php");  // Footer html section  
?>
