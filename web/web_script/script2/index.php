<?php
ini_set('max_execution_time',0);
ini_set('memory_limit', '2048M');
ini_set('upload_max_filesize', '200M');
ini_set('max_input_time',"-1");
?>

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
include_once("class.imageresize.php");
$clients = array("LEBONMARCHE","KORBEN","LAHALLE"); // client Url
$imgext = array("JPG","jpg","PNG","png","gif","GIF");  //Image Type Allowed with Extension

function totalNoImages($folder){
		return count(scandir($folder)) - 2;
	}	
?>
</head>
<body>
<form action="" method="post">
<p>
Select Client: <br /><select name="clientlist" id="clientlist" onchange="imageFolders(this.value)">
<option value="">Select</option>
<?php for($i=0; $i<sizeof($clients); $i++){ ?>
     <?php if($_GET["p"] == $clients[$i]) { ?>     
	<option value="<?php echo $clients[$i] ?>" selected><?php echo $clients[$i] ?></option>
     <?php } else { ?>
	<option value="<?php echo $clients[$i] ?>"><?php echo $clients[$i] ?></option>
     <?php } ?>  
<?php }  ?>
</select> </p>
<p>Select Folders: <br /><select name="clientlist_folders" id="clientlist_folders"> 
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
</select> </p>

<div style="width:20%">
<p><input type="submit" value="Compress All Folder of Client" name="compress_all" /></p>
<p style="text-align:center"> or </p> 
<p><input type="submit" value="Compress Specific Folder of Client" name="compress"/></p>
</div>

</form>
<?php
$i=0;
if(isset($_POST['compress'])){ ?>
  <br /><div style="width:60%"><h4>Results</h4>
<?php

  $old_size = 0;
  $new_size = 0;

  if ($handle = opendir($_GET['p']."/".$_POST['clientlist_folders']."/")) {
	  
         while (false !== ($entry = readdir($handle))) {
			    $ext = pathinfo($_GET['p']."/".$_POST['clientlist_folders']."/".$entry, PATHINFO_EXTENSION);
	            if($entry != "." && $entry != ".." && in_array($ext,$imgext)){
					 "File Name:- $entry, ";
					if(is_executable($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)){ 
					    $old_size=$old_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    if(intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)/1000) > 700){
							//echo $entry." ".filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    $lv = new WebImageCompress($_GET['p']."/".$_POST['clientlist_folders']."/".$entry, "COPY_TEMP/".$_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
                        $lv->checkWH();
                        //echo ",Old File Size in Byte :-".intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/cmprsd_".$entry))/1000;
                        //echo "<br />";
                            $new_size=$new_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
                        }else{
                            $new_size=$new_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    }
				    }else{
						echo "File Name:- $entry, Permission Deny <br />";
					}	
					
                    $i++;
                }
            }		
	    }
	    
   echo "-----------------------------------------------------------<br />";
   echo "Folder Name :- ".$_POST['clientlist_folders']."<br />";
   echo "Folder Size Before Compressed in Byte:-".($old_size/1000)."KB<br />";
   echo "Folder Size After Compressed in Byte:-".($new_size/1000)."KB<br />";
   echo "-----------------------------------------------------------<br />";
  	    
	echo "<br />Total Number of Files ".$i;    ?>
  </div>
<?php } ?>

<?php
$i=0;
if(isset($_POST['compress_all'])){ echo "compress_all"; ?>
  <br /><div style="width:60%"><h4>Results</h4>
<?php

  $old_size = 0;
  $new_size = 0;
if ($handleF = opendir($_GET['p']."/")) {
while (false !== ($entryF = readdir($handleF))) {
if($entryF != "." && $entryF != ".."){
  if ($handle = opendir($_GET['p']."/".$entryF."/")) {
	 
          while (false !== ($entry = readdir($handle))) {
			    $ext = pathinfo($_GET['p']."/".$_POST['clientlist_folders']."/".$entry, PATHINFO_EXTENSION);
	            if($entry != "." && $entry != ".." && in_array($ext,$imgext)){
					 "File Name:- $entry, ";
					if(is_executable($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)){ 
					    $old_size=$old_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    if(intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry)/1000) > 200){
							//echo $entry." ".filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    $lv = new WebImageCompress($_GET['p']."/".$_POST['clientlist_folders']."/".$entry, $_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
                        $lv->checkWH();
                        //echo ",Old File Size in Byte :-".intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/cmprsd_".$entry))/1000;
                        //echo "<br />";
                            $new_size=$new_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
                        }else{
                            $new_size=$new_size+filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    }
				    }else{
						echo "File Name:- $entry, Permission Deny <br />";
					}	
					
                    $i++;
                }
            }	
	    }
	    
	       echo "-----------------------------------------------------------<br />";
	       echo "Folder Name :- ".$entryF."<br />";
	       echo "Size Before Compressed in Byte:-".($old_size/1000)."KB<br />";
           echo "Size After Compressed in Byte:-".($new_size/1000)."KB<br />";
           echo "-----------------------------------------------------------<br />";
           
	}
  }
}
  

  	    
	echo "<br />Total Number of Files ".$i;    ?>
  </div>
<?php } ?>

</body>
</html>

