<?php
/** 
*  Compress image to there Lower Resolution Like HD, Full HD. 
*  Image with Half HD and less Resoultion will compressed only when size goes above 500KB.
*  @author     Lavanya Pant
*  @copyright  Edit-Place
*  @version    1.0
*  @since      3 Aug 13, 2015
*/	
?>
<!DOCTYPE html>
<html>
<head>
<title>Compressing Image Script</title>
<?php
include_once("class.imageresize.php");
$clients = array("LEBONMARCHE","KORBEN","LAHALLE"); // client Url
$imgext = array("JPG","jpg","PNG","png","gif","GIF");  //Image Type Allowed with Extension
?>
</head>
<body>
<?php
$i=0;
if(isset($_GET['p']) && isset($_GET['f'])) {
if($_GET['f'] != "all"){ echo "compress";  ?>
  <br /><div style="width:60%"><h4>Results Compress Specific Folder of Clients</h4>
<?php

  $old_size = 0;
  $new_size = 0;
if (is_dir($_GET['p']."/".$_GET['f']."/")) {
  if ($handle = opendir($_GET['p']."/".$_GET['f']."/")) {
	  
         while (false !== ($entry = readdir($handle))) {
			    $ext = pathinfo($_GET['p']."/".$_GET['f']."/".$entry, PATHINFO_EXTENSION);
	            if($entry != "." && $entry != ".." && in_array($ext,$imgext)){
					 "File Name:- $entry, ";
					if(is_executable($_GET['p']."/".$_GET['f']."/".$entry)){ 
					    $old_size=$old_size+filesize($_GET['p']."/".$_GET['f']."/".$entry);
					    if(intval(filesize($_GET['p']."/".$_GET['f']."/".$entry)/1000) > 700){
							//echo $entry." ".filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    $lv = new WebImageCompress($_GET['p']."/".$_GET['f']."/".$entry, $_GET['p']."/".$_GET['f']."/".$entry);
                        $lv->checkWH();
                        //echo ",Old File Size in Byte :-".intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/cmprsd_".$entry))/1000;
                        //echo "<br />";
                            $new_size=$new_size+filesize($_GET['p']."/".$_GET['f']."/".$entry);
                        }else{
                            $new_size=$new_size+filesize($_GET['p']."/".$_GET['f']."/".$entry);
					    }
				    }else{
						echo "File Name:- $entry, Permission Deny <br />";
					}	
					
                    $i++;
                }
            }		
	    }
  	    
	    
   echo "-----------------------------------------------------------<br />";
   echo "Folder Name :- ".$_GET['f']."<br />";
   echo "Folder Size Before Compressed in Byte:-".($old_size/1000)."KB<br />";
   echo "Folder Size After Compressed in Byte:-".($new_size/1000)."KB<br />";
   echo "-----------------------------------------------------------<br />";
  	    
	echo "<br />Total Number of Files ".$i;    
  }else{	
	echo "No Directory Found";
  }?>	  
  </div>
<?php } ?>

<?php
$i=0;
if(isset($_GET['p']) && isset($_GET['f']) && $_GET['f'] == "all"){ ?>
  <br /><div style="width:60%"><h4>Results Compress All Folder of Clients</h4>
<?php

  $old_size = 0;
  $new_size = 0;
if ($handleF = opendir($_GET['p']."/")) {
while (false !== ($entryF = readdir($handleF))) {
if($entryF != "." && $entryF != ".."){
  if ($handle = opendir($_GET['p']."/".$entryF."/")) {
	 
          while (false !== ($entry = readdir($handle))) {
			    $ext = pathinfo($_GET['p']."/".$entryF."/".$entry, PATHINFO_EXTENSION);
	            if($entry != "." && $entry != ".." && in_array($ext,$imgext)){
					 "File Name:- $entry, ";
					if(is_executable($_GET['p']."/".$entryF."/".$entry)){ 
					    $old_size=$old_size+filesize($_GET['p']."/".$entryF."/".$entry);
					    if(intval(filesize($_GET['p']."/".$entryF."/".$entry)/1000) > 700){
							//echo $entry." ".filesize($_GET['p']."/".$_POST['clientlist_folders']."/".$entry);
					    $lv = new WebImageCompress($_GET['p']."/".$entryF."/".$entry, $_GET['p']."/".$entryF."/".$entry);
                        $lv->checkWH();
                        //echo ",Old File Size in Byte :-".intval(filesize($_GET['p']."/".$_POST['clientlist_folders']."/cmprsd_".$entry))/1000;
                        //echo "<br />";
                            $new_size=$new_size+filesize($_GET['p']."/".$entryF."/".$entry);
                        }else{
                            $new_size=$new_size+filesize($_GET['p']."/".$entryF."/".$entry);
					    }
				    }else{
						echo "File Name:- ".$_GET['p']."/$entryF/$entry, Permission Deny <br />";
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
<?php } } else {
   echo "Get Parameters can't be Empty";
 } ?>
</body>
</html>

