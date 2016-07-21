<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include 'common_css.php'; ?>
	<?php include 'common_js.php'; ?>
	<link href="css/table-tools.css" rel="stylesheet" />
	<title>Request Page</title>
</head>
<body>
	<div class="">
		<div class="innerheader">
			<div class="container-fluid">
			<img src="http://www.edit-place.com/images/edit-place_logo.png">
			<div class="">
			</div>	
			</div>
		</div>
	</div>	
	<div id="wrapper">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li><a href="request.php">Request</a></li>
            <li class="selected"><a href="ongoing.php">On Going</a></li>
        </ul>
    </div>
    <div id="page-content-wrapper">
        <div class="main-content">
            <div class="row-fluid">
                <div class="span12">
					<h3 class="heading">
						Subject Data Center
					</h3>  
				
					<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
						<li class="active"><a href="#data" data-toggle="tab">Data Center</a></li>
						<li><a href="#subject" data-toggle="tab">Subject Center</a></li>
					</ul>
					
					<div id="my-tab-content" class="tab-content">
					
						<div class="tab-pane active" id="data">
							
							<div class="span6 keyword" >
								<div class="innerkeywords">
									<a href="keyword-authority.php"><button class="btn btn-large btn-primary" type="button">Keywords authority</button></a>
								</div>
							</div>
													
							<div class="span6 keyword">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Keywords potential</button>
								</div>
							</div>
							
							<div class="span6 keyword" style="margin-left:0px;margin-top:15px">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Plagiarism on/off site</button>
								</div>
							</div>
													
							<div class="span6 keyword" style="margin-top:15px">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">SEO and SMO activity</button>
								</div>
							</div>
							
							<div class="clear"></div>
							
							<div class="keyword new_key">
								<button class="btn btn-large btn-primary" type="button">Help Me Find New Keywords</button>
							</div>
							
							<div class="keyword new_key">
								<button class="btn btn-large btn-primary" type="button">My Strategic Keywords</button>
							</div>
							
						</div>
						
						<div class="tab-pane" id="subject">
							
							<div class="span6 keyword" >
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Subjects proposal - competitors</button>
								</div>
							</div>
													
							<div class="span6 keyword">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Subject proposal - Google News</button>
								</div>
							</div>
							
							<div class="span6 keyword" style="margin-left:0px;margin-top:15px">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Subjects proposal - reference sites</button>
								</div>
							</div>
													
							<div class="span6 keyword" style="margin-top:15px">
								<div class="innerkeywords">
									<button class="btn btn-large btn-primary" type="button">Subjects proposal - twitter</button>
								</div>
							</div>
							
							<div class="clear"></div>
							
							<div class="keyword new_key">
								<button class="btn btn-large btn-primary" type="button">Add keywords</button>
							</div>
							
							<div class="keyword new_key">
								<button class="btn btn-large btn-primary" type="button">My Strategic Keywords</button>
							</div>
							
						</div>
						
					</div>
				</div>
            </div>
        </div>
    </div>
	</div>
	<script language="JavaScript" type="text/javascript" src="js/bootstrap.js"></script>
	
</body>
</html>