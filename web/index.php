<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/korben_functions.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>

<div class="span10 content">			
	<div class="hero-unit">
		<h1>Welcome!</h1>            
	</div>
</div>


<?php
include_once(INCLUDE_PATH."/footer.php");   
 ?>
