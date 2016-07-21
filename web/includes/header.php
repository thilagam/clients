<!DOCTYPE html>
<html lang="en" ng-app>
  <head>
    <meta charset="utf-8">
    <title>Edit-place :: Clients Info</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
	  .container-fluid {
		padding-left: 1px;		
	}
	.navbar-inverse .navbar-inner {
		padding-left: 20px;
	}

    </style>
    <script src="/js/jquery_ext.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <script src="/js/prefixfree-1.0.7.js"></script>
<script>
$(document).ready(function(){
	$("#nallamenu h3").click(function(){
		if($(this).attr('class') != 'sub')
		{
			//slide up all the link lists
			$("#nallamenu ul ul").slideUp();
			//slide down the link list below the h3 clicked - only if its closed
			if(!$(this).next().is(":visible"))
			{
				$(this).next().slideDown();
			}
		}
	})
	$("#nallamenu h3.sub").click(function(){
		//slide up all the link lists
		$("#nallamenu ul ul ul.subitems").slideUp();//alert('sub1');
		//slide down the link list below the h3 clicked - only if its closed
		if(!$(this).next().is(":visible"))
		{
			$(this).next().slideDown();
		}
	})
<?php if($_SERVER["PHP_SELF"] != '/excel-devs/spec.php') { ?>
	$('.modal-header').append('<h4>'+($('.heading:first').text())+'</h4>') ;
	$('.heading:first').append('&nbsp;<a href="javascript:void(0);" class="label label-info" onclick="loadSpec();">spec</a>') ;
	$('.heading:last').append('&nbsp;<a href="javascript:void(0);" class="label label-info" onclick="loadProcessDev();">process dev</a>') ;
<?php } ?>
})

function loadSpec()
{
	$('#specInfo').modal('show');
}
function loadProcessDev()
{
	$('#specInfoTech').modal('show');
}
</script>
<script src="/js/bootstrap.min.js"></script>

<!--<script src="http://static.scripting.com/github/bootstrap2/js/bootstrap-transition.js"></script>-->
<script src="http://static.scripting.com/github/bootstrap2/js/bootstrap-modal.js"></script>

    <link href="/css/bootstrap-responsive.css" rel="stylesheet">
	<!--<link href="/css/ddsmoothmenu-v.css" rel="stylesheet">-->
	<link href="/css/menu.css" rel="stylesheet">
	<link href="/css/custom.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->   
	<link rel="stylesheet" href="/css/blueimp-gallery.min.css">
	<link rel="stylesheet" href="/css/bootstrap-image-gallery.css">
  </head>

  <body>



<!-- Spec modal -->

<style>#specInfo{padding:0 10px 0 15px;min-width:950px!important;left:32%!important;}.modal {border: 8px solid rgba(0, 0, 0, 0.5);box-shadow: none;}</style>

<div id="specInfo" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
	<div class="modal-header">
		<a href="#" class="close" data-dismiss="modal">&times;</a>
	</div>
	<div class="modal-body well">
<?php
    function specInfo($uri)
    {
		$parse_url = parse_url($uri);
		$specFile = str_replace(array('/excel-devs/','/','.php'), array('','.',''), $parse_url['path']);
		
		$specFile_nfo = simplexml_load_file(SPEC_FLDR . $specFile);
		
		$specAttachments = array_filter(array_unique(explode("|", $specFile_nfo->files))) ;
		
		foreach($specAttachments as $specAttachment)
			$spec_attachments[$specFile][] = trim($specAttachment) ;
		
		return array('owner'=>$specFile_nfo->owner, 'date_of_launch'=>$specFile_nfo->date_of_launch, 'last_modified_date'=>$specFile_nfo->last_modified_date, 'info'=>base64_decode($specFile_nfo->info), 'usage'=>base64_decode($specFile_nfo->usage),'techdesc'=>base64_decode($specFile_nfo->techdesc), 'files'=>specFiles($spec_attachments) );
    }

    function specFiles($spec_attachments)
    {
		$atth = '<div id="attachments">';
		
		foreach($spec_attachments as $folder=>$specs)
			foreach($specs as $spec_attachment)
				$atth .= '<div><a href="'.SITE_URL.'/excel-devs/download_spec.php?file='.$spec_attachment.'&folder='.$folder.'">'.$spec_attachment.'</a></div>';
			
		$atth .= '</div>';
		
		return $atth;
    }
    
$specData = specInfo($_SERVER['REQUEST_URI']);


?>
		<div class="span3">
			<span2><b>Owner</b> : </span2><?=$specData['owner']?><?php //print_r($_SERVER['REQUEST_URI']); ?>
		</div>
		<div class="span3">
			<span2><b>Date of launch</b> : </span2><?=$specData['date_of_launch']?>
		</div>
		<div class="span3">
			<span2><b>Last updated date</b> : </span2><?=$specData['last_modified_date']?>
		</div>
		<div class="span3">
			<h5>Attachments</h5>
			<p><?=$specData['files']?></p>
		</div>
		<div class="span9">
			<h5>Specification</h5>
			<p><![CDATA[  ]]> <?=$specData['info']?> </p>
		</div>
		<div class="span9">
			<h5>Usage</h5>
			<p><![CDATA[  ]]><?=$specData['usage']?></p>
		</div>

	</div>
	<!--<div class="modal-footer"></div>-->
</div>
<!-- Spec modal -->

<!-- Spec Tech modal -->
<style>#specInfoTech{padding:0 10px 0 15px;min-width:950px!important;left:32%!important;}.modal {border: 8px solid rgba(0, 0, 0, 0.5);box-shadow: none;}</style>

<div id="specInfoTech" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="windowTitleLabel" aria-hidden="true">
	<div class="modal-header">
		<a href="#" class="close" data-dismiss="modal">&times;</a>
	</div>
	<div class="modal-body well">
		<div class="span9">
		    <h5>Technical Description</h5>
			<p><![CDATA[  ]]><?=$specData['techdesc']?></p>
		</div>
	</div>
</div>	
<!-- Spec Tech modal -->

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="/index.php">Edit-place Clients</a>
          <a class="brand" href="http://clients.edit-place.com/excel-devs/spec.php">| Spec Manager |</a>
		  <? if(!preg_match("/view-pictures.php/", $_SERVER['SCRIPT_NAME'])):?>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              <a href="/logout.php" class="navbar-link">Logout</a>
            </p>
          </div><!--/.nav-collapse -->
		  <?endif;?>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row-fluid">       
