<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

if($_REQUEST['compress'])
{
	$src = ROOT_PATH . "/CLIENTS/" . $_REQUEST['f'] . "/" . $_REQUEST['sf'] . "/" ;	
	$dest = ROOT_PATH . "/excel-devs/test/" ;
	$images = scandir($src);

	$b4compress = foldersize($src);
//$stat = stat(ROOT_PATH . "/upload/CAROLL/2013_10_03_CAROLL_ST_P01_JPEG_BD/Doudoune-Amarck-M101018U-C.jpg");

	foreach($images as $image) :
	
		if($image!='.' && $image!='..') :
			compress_image($src . $image, $src . $image, 20);
		endif;
	endforeach;
	$compressed = foldersize($src);

	exit($b4compress[0].'#'.$b4compress[1].'#'.$compressed[0].'#'.$compressed[1]) ;
}

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");

function compress_image1($src, $dest , $quality) 
{}

function compress_image($src, $dest , $quality) 
{
    $info = getimagesize($src);
	
	if(filesize($src)>409600)
	{
	    $quality = (409600/filesize($src))*100 ;
		
		/* if(filesize($src)>1024000)
			$quality=95;
		elseif(filesize($src)>716800)
			$quality=97;
		elseif(filesize($src)>512000)	
			$quality=98;
		else	
			$quality=99; */
		
		//echo $quality."--".filesize($src)."<br>";
		if ($info['mime'] == 'image/jpeg') 
		{
			$image = imagecreatefromjpeg($src);
			//compress and save file to jpg
			imagejpeg($image, $dest, $quality);
		}
		elseif ($info['mime'] == 'image/png') 
		{
			$image = imagecreatefrompng($src);
			//compress and save file to jpg
			imagepng($image, $dest, $quality);
		}
		else
		{
	//echo $src.'<br>';
			//die('Unknown image file format');
		}
	}
}

function foldersize($dir){
	$count_size = 0;
	$count = 0;
	$dir_array = scandir($dir);
	foreach($dir_array as $key=>$filename){
	
		$info = getimagesize($dir.$filename);
		if ($info['mime'] == 'image/jpeg') 
		{
			if($filename!=".." && $filename!="."){
				if(is_dir($dir."/".$filename)){
					$new_foldersize = foldersize($dir."/".$filename);
					$count_size = $count_size + $new_foldersize[0];
					$count = $count + $new_foldersize[1];
				}else if(is_file($dir."/".$filename)){
					$count_size = $count_size + filesize($dir."/".$filename);
					$count++;
				}
			}
		}
	}
	return array($count_size,$count);
}

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(ROOT_PATH . '/CLIENTS/', RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST,
    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
);//exit('kkk');
foreach ($iter as $path => $dir) {
    if ($dir->isDir()) {
        $folders[] = $path;
    }
}
$folders = str_replace(ROOT_PATH . '/CLIENTS/', '', $folders) ;
foreach($folders as $folder)
{
	if(!strstr($folder, '/'))
		$labels[] = $folder ;
}
sort($labels);
if($_REQUEST['f'])
{
	$iter1 = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator(ROOT_PATH . '/CLIENTS/' . $_REQUEST['f'] . '/', RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST,
		RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
	);
	foreach ($iter1 as $path => $dir) {
		if ($dir->isDir()) {
			$subfolders[] = str_replace(ROOT_PATH . '/CLIENTS/' . $_REQUEST['f'] . '/', '', $path);
		}
	}

	/*usort($subfolders, function($a, $b) {
		return filemtime($a) < filemtime($b);
	});*/
	sort($subfolders);
	foreach($subfolders as $subfolder)
	{
		for($i=0; $i<sizeof($labels_); $i++)
			$subfolder = str_replace(ltrim($labels_[$i], '&nbsp;&nbsp;&nbsp;&nbsp;') . '/', '&nbsp;&nbsp;&nbsp;&nbsp;', $subfolder) ;
			
		$labels_[] = $subfolder ;
	}
}
//echo '<pre>'; print_r($folders); print_r($subfolders); print_r($labels_); echo '<br>'; exit;
?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script>
	$(document).ready(function(){
		$('#folder').change(function(){
			var folder = $('#folder').val() ;
			if(folder!='')
				window.location = "http://clients.edit-place.com/excel-devs/compress.php?f="+folder;
		});
	});
	function compressi()
	{
		$('#msg').text('') ;
		$('#msg').removeAttr("class");
		var folder = $('#folder').val() ;
		var subfolder = $('#subfolder').val() ;
		if(folder!='' && subfolder!='')
		{
			$('#msgSection').show();
			$('#msg').text('Processing...') ;
			$("#msg" ).attr("class", "processing");
//alert("http://clients.edit-place.com/excel-devs/compress.php?compress=1&f="+folder+'&sf='+subfolder);
			$.post("http://clients.edit-place.com/excel-devs/compress.php?compress=1&f="+folder+'&sf='+subfolder, '', function(res){
			//alert(res);
				var res1 = res.split('#');
				//$('#msg').removeAttr( "class" );
				$("#msg" ).attr("class", "success");
				//$('#msg').text('') ;
				$('.success').html('<b>Images compressed successfully.</b><br><br>Number of images before compression : <b>'+res1[1]+'</b><br>Total size of images before compression : <b>'+res1[0]+' bytes</b><br>Number of compressed images : <b>'+res1[3]+'</b><br>Total size of images after compression : <b>'+res1[2]+' bytes</b>');
			});
		}
		else
		{
			$('#msg').text('Please select client/folder') ;
		}
	}
	</script>
	<style>
		#folder,#subfolder{width:200px;}
		.processing {
			border-color: #BCE8F1;
			background: url("progress_bar.gif") no-repeat scroll center center #D9EDF7;
			color: #000000!important;
			font-size: 8pt;
			font-weight: bold;
			padding: 8px;
			text-align: center;
		}
		#msg {
			color:red;
			border-radius: 4px;
			margin-bottom: 20px;
			text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
		}
		.success{color:green!important;font-weight: normal !important;}
	</style>

<div class="span10 content">    
    <h2 class="heading">Image compression dev</h2>        
    <div class="span11">
        <div class="alert alert-info">
            <strong>Image compression</strong>
        </div>
	<div class="alert alert-success" id="msgSection" style="display:none;">
            <strong id="msg"></strong>
        </div> 
        <div class="control-group">
            <label class="control-label">Client :</label>
            <div class="controls">
                <select id="folder">
                    <option value="">Select</option>
                    <?php foreach($labels as $label) : ?>
                        <option value="<?php echo $label; ?>" <?php if($_REQUEST['f'] == $label) { ?>selected<?php } ?>>
                            <?php echo $label ; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
	<?php if($labels_) : ?>
        <div class="control-group">
            <label class="control-label">Folder :</label>
            <div class="controls">                  
                <select id="subfolder">
                    <option value="">Select</option>
                    <?php $idx = 0 ; ?>
                    <?php foreach($labels_ as $label_) : ?>
                        <option value="<?php echo $subfolders[$idx]; ?>"><?php echo $label_; ?></option>
                        <?php $idx++; ?>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
        <div class="control-group">
            <div class="controls">                  
                <button type="submit" value="Compress" name="submit" class="btn btn-primary" onclick="compressi();">Compress</button>
            </div>
        </div>
    </div>
</div>
