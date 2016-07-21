<?  
ini_set('display_errors',0);
ini_set('display_startup_errors',0);
error_reporting(-1);
//header("Location:http://edit-place.com");
//exit;
/*$Mydir = 'CLIENTS/'; ### OR MAKE IT 'yourdirectory/';

foreach(glob($Mydir.'*', GLOB_ONLYDIR) as $dir) {
   // $dir = str_replace($Mydir, '', $dir);
    echo '"'.$dir.'/",';
}
exit;*/

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="robots" content="noindex" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>File Manager</title>
<meta name="robots" content="noindex" />
<style type="text/css" title="currentStyle">
			@import "http://www.datatables.net/release-datatables/media/css/demo_page.css"; @import "/media/css/header.ccss";
			@import "http://www.datatables.net/release-datatables/media/css/demo_table.css";
		</style>
		
<script type="text/javascript" language="http://www.datatables.net/release-datatables/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="http://www.datatables.net/release-datatables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {

			} );
			
			
		</script>
<style type="text/css">
*
{
	font-family:trebuchet ms,Verdana, Arial, Helvetica, sans-serif;
	font-size:11px;
}
tr:hover
{
	background-color:#666666;
	color:#FFFFFF;
	cursor:pointer;
}
</style>		
</head>

<body>

<?php
set_time_limit(10000);

include 'calculate.directory.class.php';

/* Path to Directory - IMPORTANT: with '/' at the end */

//$directory = 'upload/KORBEN/BREAL/'; 
//'/home/sites/site1/','/home/sites/site2/','/home/sites/site3/','/home/sites/site4/','/home/sites/site5/',

$directory=array("CLIENTS/LAHALLE/20150128-chaussures/","CLIENTS/LAHALLE/20150128-vetements/","CLIENTS/LAHALLE/20150129-vetements/","CLIENTS/LAHALLE/20150130-vetements/","CLIENTS/LAHALLE/20150131-vetements/","CLIENTS/LAHALLE/20150204-vetements/","CLIENTS/LAHALLE/20150206-vetements/","CLIENTS/LAHALLE/20150206-vetements-2/","CLIENTS/LAHALLE/20150207-vetements/","CLIENTS/LAHALLE/20150210-vetements/","CLIENTS/LAHALLE/20150211-chaussures/","CLIENTS/LAHALLE/20150211-vetements/","CLIENTS/LAHALLE/20150212-chaussures/","CLIENTS/LAHALLE/20150212-vetements/","CLIENTS/LAHALLE/20150213-vetements/","CLIENTS/LAHALLE/20150217-vetements/","CLIENTS/LAHALLE/20150218-vetements/","CLIENTS/LAHALLE/20150219-vetements/","CLIENTS/LAHALLE/20150220-vetements/","CLIENTS/LAHALLE/20150226-chaussures/","CLIENTS/LAHALLE/20150226-vetements/","CLIENTS/LAHALLE/20150227-vetements/","CLIENTS/LAHALLE/20150302-vetements/","CLIENTS/LAHALLE/20150303-vetements/","CLIENTS/LAHALLE/20150304/","CLIENTS/LAHALLE/20150305-vetements/","CLIENTS/LAHALLE/20150306-vetements/","CLIENTS/LAHALLE/20150309-chaussures/","CLIENTS/LAHALLE/20150309-vetements/","CLIENTS/LAHALLE/20150310-chaussures/","CLIENTS/LAHALLE/20150310-vetements/","CLIENTS/LAHALLE/20150312-vetements/","CLIENTS/LAHALLE/20150313-vetements/","CLIENTS/LAHALLE/20150316-vetements/","CLIENTS/LAHALLE/20150317-vetements/","CLIENTS/LAHALLE/20150318-vetements/","CLIENTS/LAHALLE/20150319-vetements/","CLIENTS/LAHALLE/20150320-vetements/","CLIENTS/LAHALLE/20150323/","CLIENTS/LAHALLE/20150323-vetements/","CLIENTS/LAHALLE/20150324-vetements/","CLIENTS/LAHALLE/20150325-vetements/","CLIENTS/LAHALLE/20150326-chaussures/","CLIENTS/LAHALLE/20150327-vetements/","CLIENTS/LAHALLE/20150330-vetements/","CLIENTS/LAHALLE/20150331- chaussures/","CLIENTS/LAHALLE/20150331-vetements/","CLIENTS/LAHALLE/20150401-vetements/","CLIENTS/LAHALLE/20150402-vetements/","CLIENTS/LAHALLE/20150403-vetements/","CLIENTS/LAHALLE/20150407/","CLIENTS/LAHALLE/20150407-vetements/","CLIENTS/LAHALLE/20150408-vetements/","CLIENTS/LAHALLE/20150410/","CLIENTS/LAHALLE/20150413-vetements/","CLIENTS/LAHALLE/20150415/","CLIENTS/LAHALLE/20150415-chaussures/","CLIENTS/LAHALLE/20150415-vetements/","CLIENTS/LAHALLE/20150416-vetements/","CLIENTS/LAHALLE/20150417-vetements/","CLIENTS/LAHALLE/20150420-vetements/","CLIENTS/LAHALLE/20150421-chaussures/","CLIENTS/LAHALLE/20150421-vetements/","CLIENTS/LAHALLE/20150422-vetements/","CLIENTS/LAHALLE/20150423-chaussures/","CLIENTS/LAHALLE/20150429-vetements/","CLIENTS/LAHALLE/20150429-vetements-2/","CLIENTS/LAHALLE/20150430-vetements/","CLIENTS/LAHALLE/20150504-vetements/","CLIENTS/LAHALLE/20150505/","CLIENTS/LAHALLE/20150505-vetements/","CLIENTS/LAHALLE/20150511-vetements/","CLIENTS/LAHALLE/20150513-vetements/","CLIENTS/LAHALLE/20150519-vetements/","CLIENTS/LAHALLE/20150520/","CLIENTS/LAHALLE/20150522- vetements- 2/","CLIENTS/LAHALLE/20150526-vetements/","CLIENTS/LAHALLE/20150527/","CLIENTS/LAHALLE/20150527-vetements/","CLIENTS/LAHALLE/20150529/","CLIENTS/LAHALLE/20150529-vetements/","CLIENTS/LAHALLE/20150602-vetements/","CLIENTS/LAHALLE/20150603/","CLIENTS/LAHALLE/20150603-vetements/","CLIENTS/LAHALLE/20150605-vetements/","CLIENTS/LAHALLE/20150608-vetements/","CLIENTS/LAHALLE/20150609-vetements/","CLIENTS/LAHALLE/20150610-vetements/","CLIENTS/LAHALLE/20150616-vetements/","CLIENTS/LAHALLE/20150617-vetements/","CLIENTS/LAHALLE/20150618-vetements/","CLIENTS/LAHALLE/20150619 - chaussures/","CLIENTS/LAHALLE/20150622-vetements/","CLIENTS/LAHALLE/20150623-vetements/","CLIENTS/LAHALLE/20150624-vetements/","CLIENTS/LAHALLE/20150625-vetements bis/","CLIENTS/LAHALLE/20150626-vetements/","CLIENTS/LAHALLE/20150629-vetements/","CLIENTS/LAHALLE/20150630-vetements/","CLIENTS/LAHALLE/20150701-vetements/","CLIENTS/LAHALLE/20150702-vetements/","CLIENTS/LAHALLE/20150703-chaussures/","CLIENTS/LAHALLE/20150707-vetements/","CLIENTS/LAHALLE/20150708-vetements/","CLIENTS/LAHALLE/20150709-vetements/","CLIENTS/LAHALLE/20150710-vetements/","CLIENTS/LAHALLE/20150713-chaussures/","CLIENTS/LAHALLE/20150713-vetements/","CLIENTS/LAHALLE/20150715-vetements/","CLIENTS/LAHALLE/20150717-vetements/","CLIENTS/LAHALLE/20150720-vetements/","CLIENTS/LAHALLE/20150721-chaussures/","CLIENTS/LAHALLE/20150721-vetements/","CLIENTS/LAHALLE/20150722-vetements/","CLIENTS/LAHALLE/20150723-vetements/","CLIENTS/LAHALLE/20150724-vetements/","CLIENTS/LAHALLE/20150725-vetements/","CLIENTS/LAHALLE/20150727-vetements/","CLIENTS/LAHALLE/20150728-vetements/","CLIENTS/LAHALLE/20150729-vetements/","CLIENTS/LAHALLE/20150730-vetements/","CLIENTS/LAHALLE/20150731- chaussures/","CLIENTS/LAHALLE/20150731-vetements/","CLIENTS/LAHALLE/20150803-vetements/","CLIENTS/LAHALLE/20150804-vetements/","CLIENTS/LAHALLE/20150805-vetements/","CLIENTS/LAHALLE/20150806-vetements/","CLIENTS/LAHALLE/20150807/","CLIENTS/LAHALLE/20150807-vetements/","CLIENTS/LAHALLE/20150810-vetements/","CLIENTS/LAHALLE/20150811-vetements/","CLIENTS/LAHALLE/20150812-vetements/","CLIENTS/LAHALLE/20150813-vetements/","CLIENTS/LAHALLE/20150814-vetements/","CLIENTS/LAHALLE/20150818-vetements/","CLIENTS/LAHALLE/20150819-vetements/","CLIENTS/LAHALLE/20150820-vetements/","CLIENTS/LAHALLE/20150821-vetements/","CLIENTS/LAHALLE/20150825-vetements/","CLIENTS/LAHALLE/20150826-vetements/","CLIENTS/LAHALLE/20150826-vetements-bis/","CLIENTS/LAHALLE/20150827 - chaussures b/","CLIENTS/LAHALLE/20150827 -vetements supplÃ©ment/","CLIENTS/LAHALLE/20150827 -vetements supplément/","CLIENTS/LAHALLE/20150827- chaussures/","CLIENTS/LAHALLE/20150827-vetements/","CLIENTS/LAHALLE/20150828-vetements/","CLIENTS/LAHALLE/20150831-vetements/","CLIENTS/LAHALLE/20150901- chaussures bis/","CLIENTS/LAHALLE/20150901-chaussures/","CLIENTS/LAHALLE/20150901-vetements/","CLIENTS/LAHALLE/20150901-vetements bis/","CLIENTS/LAHALLE/20150902-vetements/","CLIENTS/LAHALLE/20150903-vetements/","CLIENTS/LAHALLE/20150908-chaussures/","CLIENTS/LAHALLE/20150908-vetements/","CLIENTS/LAHALLE/20150915-vetements/","CLIENTS/LAHALLE/20150917 bis/","CLIENTS/LAHALLE/20150917- chaussures/","CLIENTS/LAHALLE/20150917-vetements/","CLIENTS/LAHALLE/20150918-vetements/","CLIENTS/LAHALLE/20150922-vetements/","CLIENTS/LAHALLE/20150923-vetements/","CLIENTS/LAHALLE/20150924-vetements/","CLIENTS/LAHALLE/20150929-vetements/","CLIENTS/LAHALLE/20150930-vetements/","CLIENTS/LAHALLE/20151001-vetements/","CLIENTS/LAHALLE/20151002- chaussures/","CLIENTS/LAHALLE/20151002-vetements/","CLIENTS/LAHALLE/20151006-vetements/","CLIENTS/LAHALLE/20151008-vetements/","CLIENTS/LAHALLE/20151009-vetements/","CLIENTS/LAHALLE/20151012- chaussures/","CLIENTS/LAHALLE/20151012- chaussures bis/","CLIENTS/LAHALLE/20151012-vetements/","CLIENTS/LAHALLE/20151013-vetements/","CLIENTS/LAHALLE/20151014-vetements/","CLIENTS/LAHALLE/20151015- chaussures/","CLIENTS/LAHALLE/20151015-vetements/","CLIENTS/LAHALLE/20151019-vetements/","CLIENTS/LAHALLE/20151020-vetements/","CLIENTS/LAHALLE/20151022-vetements/","CLIENTS/LAHALLE/20151023- chaussures/","CLIENTS/LAHALLE/20151023-vetements/","CLIENTS/LAHALLE/20151026-vetements/","CLIENTS/LAHALLE/20151027-vetements/","CLIENTS/LAHALLE/20151028-vetements/","CLIENTS/LAHALLE/20151029/","CLIENTS/LAHALLE/20151029-vetements/","CLIENTS/LAHALLE/20151030-vetements/","CLIENTS/LAHALLE/20151102-chaussures/","CLIENTS/LAHALLE/20151102-vetements/","CLIENTS/LAHALLE/20151103-vetements/","CLIENTS/LAHALLE/20151106-chaussures/","CLIENTS/LAHALLE/20151106-vetements/","CLIENTS/LAHALLE/20151109-vetements/","CLIENTS/LAHALLE/20151112- chaussures/","CLIENTS/LAHALLE/20151112-vetements/","CLIENTS/LAHALLE/20151113-vetements/","CLIENTS/LAHALLE/20151116-vetements/","CLIENTS/LAHALLE/20151117-chaussures/","CLIENTS/LAHALLE/20151117-vetements/","CLIENTS/LAHALLE/20151119-vetements/","CLIENTS/LAHALLE/20151123-vetements/","CLIENTS/LAHALLE/20151126-vetements/","CLIENTS/LAHALLE/20151203-chaussures/","CLIENTS/LAHALLE/20151203-vetements/","CLIENTS/LAHALLE/20151204-vetements/","CLIENTS/LAHALLE/20151208-vetements/","CLIENTS/LAHALLE/20151210-vetements/","CLIENTS/LAHALLE/20151215-vetements/","CLIENTS/LAHALLE/20151216-vetements/","CLIENTS/LAHALLE/20151217-vetements/","CLIENTS/LAHALLE/20151218-vetements/","CLIENTS/LAHALLE/20151228-vetements/","CLIENTS/LAHALLE/20151229 - chaussures/","CLIENTS/LAHALLE/20151229 - sacs/","CLIENTS/LAHALLE/20151229-vetements/","CLIENTS/LAHALLE/20151711-chaussures/","CLIENTS/LAHALLE/20160104-vetements/","CLIENTS/LAHALLE/20160105 - sacs/","CLIENTS/LAHALLE/20160106 - sacs/","CLIENTS/LAHALLE/20160106-vetements/","CLIENTS/LAHALLE/20160107 - SDM/","CLIENTS/LAHALLE/20160107 - sacs/","CLIENTS/LAHALLE/20160108 - chaussures/","CLIENTS/LAHALLE/20160111 - chaussures/","CLIENTS/LAHALLE/20160111-vetements/","CLIENTS/LAHALLE/20160112-vetements/","CLIENTS/LAHALLE/20160113-vetements/","CLIENTS/LAHALLE/20160115-vetements/","CLIENTS/LAHALLE/20160120-vetements/","CLIENTS/LAHALLE/20160122-vetements/","CLIENTS/LAHALLE/20160123-vetements/","CLIENTS/LAHALLE/20160125-vetements/","CLIENTS/LAHALLE/20160126-vetements/","CLIENTS/LAHALLE/20160127 - SDM/","CLIENTS/LAHALLE/20160127 - chaussures/","CLIENTS/LAHALLE/20160127-vetements/","CLIENTS/LAHALLE/20160128 - sac/","CLIENTS/LAHALLE/20160129 - HAV descriptif court rattrapage images/","CLIENTS/LAHALLE/20160129 - HAV descriptif long rattrapage images/","CLIENTS/LAHALLE/20160129-vetements/","CLIENTS/LAHALLE/20160201-vetements/","CLIENTS/LAHALLE/20160202-vetements/","CLIENTS/LAHALLE/20160203-vetements/","CLIENTS/LAHALLE/20160204-vetements/","CLIENTS/LAHALLE/20160208-vetements/","CLIENTS/LAHALLE/20160209-vetements/","CLIENTS/LAHALLE/20160210 - HAC descriptif court rattrapage/","CLIENTS/LAHALLE/20160210 - HAC descriptif long rattrapage/","CLIENTS/LAHALLE/20160210 - HAV descriptif court rattrapage/","CLIENTS/LAHALLE/20160210 - HAV descriptif long rattrapage/","CLIENTS/LAHALLE/20160211-vetements/","CLIENTS/LAHALLE/20160212-chaussures/","CLIENTS/LAHALLE/20160212-vetements/","CLIENTS/LAHALLE/20160215-vetements/","CLIENTS/LAHALLE/20160216 HAC/","CLIENTS/LAHALLE/20160216 HAC Rattrapage/","CLIENTS/LAHALLE/20160216 SDM/","CLIENTS/LAHALLE/20160216 SDM Rattrapage/","CLIENTS/LAHALLE/20160216-vetements/","CLIENTS/LAHALLE/20160218 - chaussures/","CLIENTS/LAHALLE/20160218-vetements/","CLIENTS/LAHALLE/20160219-vetements/","CLIENTS/LAHALLE/20160222 - chaussures/","CLIENTS/LAHALLE/20160222-vetements/","CLIENTS/LAHALLE/20160223-vetements/","CLIENTS/LAHALLE/20160224-vetements/","CLIENTS/LAHALLE/20160225-vetements/","CLIENTS/LAHALLE/20160229-HAC/","CLIENTS/LAHALLE/20160229-SDM/","CLIENTS/LAHALLE/20160301_vetements/","CLIENTS/LAHALLE/20160302-HAC/","CLIENTS/LAHALLE/20160302-SDM/","CLIENTS/LAHALLE/20160302_vetements/","CLIENTS/LAHALLE/20160307_vetements/","CLIENTS/LAHALLE/20160308-HAC/","CLIENTS/LAHALLE/20160308-SDM/","CLIENTS/LAHALLE/20160308_vetements/","CLIENTS/LAHALLE/20160309_vetements/","CLIENTS/LAHALLE/20160311_vetements/","CLIENTS/LAHALLE/20160314_vetements/","CLIENTS/LAHALLE/20160315_vetements/","CLIENTS/LAHALLE/20160316_vetements/","CLIENTS/LAHALLE/20160317_vetements/","CLIENTS/LAHALLE/20160318_vetements/","CLIENTS/LAHALLE/20160321 - HAV rattrapage/","CLIENTS/LAHALLE/20160322_vetements/","CLIENTS/LAHALLE/20160323 - chaussures SDM/","CLIENTS/LAHALLE/20160323_vetements/","CLIENTS/LAHALLE/20160324_vetements/","CLIENTS/LAHALLE/20160325_vetements/","CLIENTS/LAHALLE/20160329_vetements/","CLIENTS/LAHALLE/2016033066 - HAC/","CLIENTS/LAHALLE/20160330_vetements/","CLIENTS/LAHALLE/20160331_HAC/","CLIENTS/LAHALLE/20160331_SDM/","CLIENTS/LAHALLE/20160331_sac/","CLIENTS/LAHALLE/20160331_vetements/","CLIENTS/LAHALLE/20160401 - HAV rattrapage/","CLIENTS/LAHALLE/20160401_vetements/","CLIENTS/LAHALLE/20160404_vetements/","CLIENTS/LAHALLE/20160405_vetements/","CLIENTS/LAHALLE/20160406_vetements/","CLIENTS/LAHALLE/20160407_vetements/","CLIENTS/LAHALLE/20160408_vetements/","CLIENTS/LAHALLE/20160411_vetements/","CLIENTS/LAHALLE/20160412-HAC/","CLIENTS/LAHALLE/20160412_vetements/","CLIENTS/LAHALLE/20160413_vetements/","CLIENTS/LAHALLE/20160414_vetements/","CLIENTS/LAHALLE/20160415 - HAV rattrapage images/","CLIENTS/LAHALLE/20160415_vetements/","CLIENTS/LAHALLE/20160418_vetements/","CLIENTS/LAHALLE/20160419-SDM/","CLIENTS/LAHALLE/20160419-Sac/","CLIENTS/LAHALLE/20160419_vetements/","CLIENTS/LAHALLE/20160420_vetements/","CLIENTS/LAHALLE/20160421 - HAC CHAUSSURES RATTRAPAGE/","CLIENTS/LAHALLE/20160421 - HAC MARO RATTRAPAGE/","CLIENTS/LAHALLE/20160421 - HAC SDM RATTRAPAGE/","CLIENTS/LAHALLE/20160422_vetements/","CLIENTS/LAHALLE/20160425_vetements/","CLIENTS/LAHALLE/20160426_vetements/","CLIENTS/LAHALLE/20160427-HAC (161)/","CLIENTS/LAHALLE/20160427_vetements/","CLIENTS/LAHALLE/20160428-HAC (161)/","CLIENTS/LAHALLE/20160502_vetements/","CLIENTS/LAHALLE/20160503-HAC (161)/","CLIENTS/LAHALLE/20160504-HAC (161)/","CLIENTS/LAHALLE/20160504-HAV rattrapage maillots/","CLIENTS/LAHALLE/20160504_vetements/","CLIENTS/LAHALLE/20160506-HAC (161)/","CLIENTS/LAHALLE/20160509_vetements/","CLIENTS/LAHALLE/20160510_vetements/","CLIENTS/LAHALLE/20160512-HAC (161)/","CLIENTS/LAHALLE/20160512_vetements/","CLIENTS/LAHALLE/20160513_vetements/","CLIENTS/LAHALLE/20160516_vetements/","CLIENTS/LAHALLE/20160517_vetements/","CLIENTS/LAHALLE/20160518-HAC (161)/","CLIENTS/LAHALLE/20160523_vetements/","CLIENTS/LAHALLE/20160524_vetements/","CLIENTS/LAHALLE/20160525_vetements/","CLIENTS/LAHALLE/20160526_vetements/","CLIENTS/LAHALLE/20160527-SDM (161)/","CLIENTS/LAHALLE/20160527_vetements/","CLIENTS/LAHALLE/20160530_vetements/","CLIENTS/LAHALLE/20160601_vetements/","CLIENTS/LAHALLE/20160602_vetements/","CLIENTS/LAHALLE/2060419_HAC 161/","CLIENTS/LAHALLE/HAV court rattrapage le 08.03/","CLIENTS/LAHALLE/HAV long rattrapage le 08.03/","CLIENTS/LAHALLE/Rattrapage EP HAC (161)/","CLIENTS/LAHALLE/Rattrapage EP HAV (161)/","CLIENTS/LAHALLE/SDM - 20160205/","CLIENTS/LAHALLE/chaussures - 20160205/","CLIENTS/LAHALLE/images/","CLIENTS/LAHALLE/ref urgentes 18 11/","CLIENTS/LAHALLE/sac - 20160205/","CLIENTS/LAHALLE/xml/");


$op =' <html><body>

<div id="demo">

<table border="1" id="example" cellpadding="2"  width="55%" align="center">
<tr><th colspan="5" align="center"> FILE MANAGER</th></tr>
<tr><th>S.No</th><th>Directory</th><th>Size</th><th>Folders</th><th>Files</th></tr>
';
if($_REQUEST['p']=='1')
{
$d = "/home/sites/site6/web/".$_REQUEST['path'];
////////////////////////
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));

while($it->valid()) {

    if (!$it->isDot()) {
		$size_in = 'GB';
		/* Number of decimals to show */
		$decimals = 2;
		$directory_size = new Directory_Calculator;
		/* Initialize Class */
		$directory_size->size_in = $size_in;
		$directory_size->decimals = $decimals;
		$array = $directory_size->size($_REQUEST['path'].$it->getSubPath()."/");
		if($array['size'] > 3)
		{
			echo $size="<p style='color:#FF0000; font-weight:bold'>".$array['size']."</p>";
		}
		else
		{
			$size=$array['size']." ".$size_in;
		}
		$op .= "<tr><td align='center'>$i</td><td><a href='filesize.php?p=1&path=".$_REQUEST['path'].$it->getSubPath()."/'>". $it->getSubPath()."</td><td>".$size." ".$size_in."</td><td> ".$array['folders']." </td><td> ".$array['files']." </td> </tr>";
        
    }

    $it->next();
}
///////////////////////////
	
	//get all image files with a .jpg extension.
	$files = glob($d."*.*");
	$i=1;
	foreach($files as $key => $value)
	{
		$size_in = 'GB';
		/* Number of decimals to show */
		$decimals = 2;
		$directory_size = new Directory_Calculator;
		/* Initialize Class */
		$directory_size->size_in = $size_in;
		$directory_size->decimals = $decimals;
		$array = $directory_size->size($value); // return an array with: size, total files & folders
		if($array['size'] > 3)
		{
			$size="<p style='color:#FF0000; font-weight:bold'>".$array['size']." ".$size_in."</p>";
		}
		else
		{
			$size=$array['size']." ".$size_in;
		}
		$op .= "<tr><td align='center'>$i</td><td>".$value."</td><td>".$size."</td><td> ".$array['folders']." </td><td> ".$array['files']." </td> </tr>";
		$i++;
	}
}
else
{
	$i=1;
	
	foreach($directory as $key => $value)
	{
		/* Calculate size in: B (Bytes), KB (Kilobytes), MB (Megabytes), GB (Gigabytes) */
		$size_in = 'GB';
		/* Number of decimals to show */
		$decimals = 2;
		$directory_size = new Directory_Calculator;
		/* Initialize Class */
		$directory_size->size_in = $size_in;
		$directory_size->decimals = $decimals;
		$array = $directory_size->size($value); // return an array with: size, total files & folders		
		if($array['size'] > 0.4)
		{
			$size="<p style='color:#FF0000; font-weight:bold'>".$array['size']." ".$size_in."</p>";
		}
		else
		{
			$size=$array['size']." ".$size_in;
		}
		$op .= "<tr><td align='center'>$i</td><td>".$value."</td><td>".$size."</td><td> ".$array['folders']." </td><td> ".$array['files']." </td> </tr>";
		$i++;
	}
}	


echo $op .='</table></div>';
if($_GET['email'])
{
	$op .='</body></html>';
	$to = $_GET['email'];//"rakeshm@edit-place.com";
	$subject = "MEMORY STATUS IN LIVE SERVER ON ".date("d-m-Y");;
	$message = $op ;
	$from = "alert@edit-place.com";
	//$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
	$headers .= "From:".$from;//. "\r\n";
	//$headers .= "Cc:rakeshm@edit-place.com". "\r\n";
	mail($to,$subject,$message,$headers);
	//echo "email sent ";
}

?>

</body>
</html>