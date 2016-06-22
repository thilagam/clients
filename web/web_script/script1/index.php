<!DOCTYPE html>
<html>
<head>
<title>Compressing Image Script</title>
<script language="javascript" type="text/javascript">
  function imageFolders(lv){
	  document.location.href="?p="+lv;	  
	}  
</script>
<?php
include_once("class.ImageCompress.php");
$clients = array("LEBONMARCHE","KORBEN"); // client Url
$imgext = array("JPG","jpg","PNG","png","gif","GIF");  //Image Type Allowed

function totalNoImages($folder){
		return count(scandir($folder)) - 2;
	}	
?>
</head>
<body>
<form action="" method="post" >
Select Client: <select name="clientlist" id="clientlist" onchange="imageFolders(this.value)">
<option value="">Select</option>
<?php for($i=0; $i<sizeof($clients); $i++){ ?>
<?php if($_GET["p"] == $clients[$i]) { ?>     
	<option value="<?php echo $clients[$i] ?>" selected><?php echo $clients[$i] ?></option>
     <?php } else { ?>
	<option value="<?php echo $clients[$i] ?>"><?php echo $clients[$i] ?></option>
     <?php } ?><?php }  ?>
</select><br />
Select Folders: <select name="clientlist_folders" id="clientlist_folders">
<option value="">Select</option>
<?php 
if(!empty($_GET["p"])){
if ($handle = opendir($_GET['p']."/")) {
            while (false !== ($entry = readdir($handle))) {
	            if($entry != "." && $entry != ".."){
                    if($_POST["clientlist_folders"] == $entry) 
                         echo "<option value='$entry' selected>$entry (".totalNoImages($_GET['p']."/".$entry).")</option>";
                    else
                         echo "<option value='$entry'>$entry (".totalNoImages($_GET['p']."/".$entry).")</option>";
                }
            }		
	    }
	}
?>
</select><br />
<input type="submit" value="Compress" name="compress"/>
</form>
<?php
$i=0;
if(isset($_POST['compress'])){ ?>
  <br /><div style="width:60%"><h4>Results <span style="font-size:12px">( Will Read only Images with extension "JPG","jpg","PNG","png","gif","GIF" )</span></h4>
<?php

  $old_size = 0;
  $new_size = 0;

  if ($handle = opendir($_GET['p']."/".$_POST['clientlist_folders']."/")) {
            while (false !== ($entry = readdir($handle))) {
				$ext = pathinfo($_GET['p']."/".$_POST['clientlist_folders']."/".$entry, PATHINFO_EXTENSION);
	            if($entry != "." && $entry != ".." && in_array($ext,$imgext)){
					//echo "File Name:- $entry, ";
					if(is_executable($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)){
					    $old_size=$old_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
						if(filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)/1000 >  400) { 
					         $lv = new ImageCompress($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
                             $lv->reduceDpi();
                             //echo ",Old File Size in Byte :-".intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry))/1000;
                             //echo "<br />";
					    }
                        $new_size=$new_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
				    }else{
						echo "File Name:- $entry, Permission Deny <br />";
					}	
					$i++;
                }
            }		
	    }
  
   echo "Folder Size Before Compressed in Byte:-".($old_size/1000)."KB<br />";
   echo "Folder Size After Compressed in Byte:-".($new_size/1000)."KB<br />";
  	    
	echo "<br />Total Number of Files ".$i;    ?>
  </div>
<?php } ?>

</body>
</html>

