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
	<h2 class="heading">BASH
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
getClientReferences($reference, BASH_IMAGE_PATH, BASH_URL, 'BASH') ;
?>	
</div>
<?php
include_once(INCLUDE_PATH."/footer.php");   
?>
