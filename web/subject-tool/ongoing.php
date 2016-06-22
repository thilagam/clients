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
						On Going
					</h3>  
					<table class="table table-bordered table-striped display" id="ongoing">
						<thead>
							<tr>
								<th>Title</th>
								<th>Url</th>
								<th>Comp Urls</th>
								<th>Email</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<tr>
							<td><a href="data_subject.php">RAKESH TESTING</a></td>
							<td>WWW.EDIT-PLACE.COM</td>
							<td>http://www.makemytrip.com</td>
							<td>rakeshm@edit-place.com</td>
							<td>Download</td>
						</tr>
						<tr>
							<td><a href="data_subject.php">RAKESH TESTING</a></td>
							<td>WWW.EDIT-PLACE.COM</td>
							<td>http://www.makemytrip.com</td>
							<td>rakeshm@edit-place.com</td>
							<td>Download</td>
						</tr>
						<tr>
							<td><a href="data_subject.php">RAKESH TESTING</a></td>
							<td>WWW.EDIT-PLACE.COM</td>
							<td>http://www.makemytrip.com</td>
							<td>rakeshm@edit-place.com</td>
							<td>Download</td>
						</tr>
						</tbody>
					</table>
				</div>
            </div>
        </div>
    </div>
	</div>
	<script language="JavaScript" type="text/javascript" src="js/jquery.dataTables.js"></script>
	<script>
		$(document).ready(function(){
			$("#ongoing").dataTable();
		})
	</script>
</body>
</html>