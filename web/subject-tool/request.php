<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include 'common_css.php'; ?>
	<?php include 'common_js.php'; ?>
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
            <li class="selected"><a href="request.php">Request</a></li>
            <li><a href="ongoing.php">On Going</a></li>
        </ul>
    </div>
    <div id="page-content-wrapper">
        <div class="main-content">
            <div class="row-fluid">
                <div class="span12">
					<h3 class="heading">
						New Request
					</h3>      
               <form class="form-horizontal" action="request_sucess.php">
				  <div class="control-group">
					<label class="control-label span5" for="inputEmail">Title</label>
					<div class="controls">
					  <input type="text"  class="span6" placeholder="Title">
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="site">Enter Your Site</label>
					<div class="controls">
					   <input type="text"  class="span6" id="site" placeholder="Enter Your Site">
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="Competetiors">Competetiors</label>
					<div class="controls">
					   <textarea type="text"  class="span6" id="Competetiors" placeholder="Competetiors"></textarea>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="Strategic words">Strategic words</label>
					<div class="controls">
					   <textarea type="text"  class="span6" id="Strategic words" placeholder="Strategic words"></textarea>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="combincations">Max.no.of combincations</label>
					<div class="controls">
					   <textarea type="text"  class="span6" id="combincations" placeholder="Max.no.of combincations"></textarea>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="country">Country</label>
					<div class="controls">
					  <select class="span4" id="country">
						<option>France</option>
						<option>France</option>
						<option>France</option>
						<option>France</option>
					  </select>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="urls">Max.no.of url's</label>
					<div class="controls">
					  <input type="text"  class="span6" placeholder="Max.no.of url's">
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="tor">Tor</label>
					<div class="controls">
					<label class="radio inline">
					<input type="radio" name="optionsRadios" id="tor" value="option1" checked>
					Yes
					</label>
					<label class="radio inline">
					<input type="radio" name="optionsRadios" id="tor1" value="option2">
					No
					</label>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="now">Process Now</label>
					<div class="controls">
					<label class="radio inline">
					<input type="radio" name="optionsRadios" id="now" value="option1" checked>
					Now
					</label>
					<label class="radio inline">
					<input type="radio" name="optionsRadios" id="now1" value="option2">
					Later
					</label>
					</div>
				  </div>
				  <div class="control-group">
					<label class="control-label" for="email">Email</label>
					<div class="controls">
					  <input type="text"  class="span6" id="email" placeholder="Email">
					</div>
				  </div>
				  
		
				  <div class="control-group">
					<div class="controls">
					  <button type="submit" class="btn btn-primary">Process Score</button>
					</div>
				  </div>
				</form>

				</div>
            </div>
        </div>
    </div>
	</div>
				
			
	
</body>
</html>