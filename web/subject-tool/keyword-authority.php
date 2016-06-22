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
						Keywords Authority
					</h3>  
				
						<div class="span6" style="margin-left:0px">
							<table id="mastertable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th width="70%">SELECT STRATEGIC KWS</th>
									<th width="30%">
										<select name="comp" style="margin-bottom:0">
											<option value="Option1">Option1</option>
											<option value="Option2">Option2</option>
										</select>
									</th>
								</tr>
								<tr>
									<th>KW</th>
									<th>Rank</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option11"> Kw1
									</label>
									</td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option12"> Kw2
									</label></td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option13"> Kw3
									</label></td>
									<td>100</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td width="70%"><button class="btn btn-success btn-mini addtowhitelist" type="button">+</button> <button class="btn btn-danger btn-mini addtoblacklist" type="button">+</button></td>
									<td width="30%" align="right"><button class="btn btn-mini btn-primary loadmore" type="button">Load More</button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="whitetable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>White List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option11"> Kw1
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="blacktable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>Black List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option12"> Kw2
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
				
						<div class="clear" style="height:20px"></div>
				
						<div class="span6" style="margin-left:0px">
							<table id="mastertable2" class=" table-striped table authority_table" width="100%">
								<tr>
									<th width="70%">SELECT STRATEGIC KWS</th>
									<th width="30%">
										<select name="comp" style="margin-bottom:0">
											<option value="Option1">Option1</option>
											<option value="Option2">Option2</option>
										</select>
									</th>
								</tr>
								<tr>
									<th>KW</th>
									<th>Rank</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="inlineCheckbox1" value="option21"> Kw1
									</label>
									</td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option22"> Kw2
									</label></td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option23"> Kw3
									</label></td>
									<td>100</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td width="70%"><button class="btn btn-success btn-mini addtowhitelist" type="button">+</button> <button class="btn btn-danger btn-mini addtoblacklist" type="button">+</button></td>
									<td width="30%" align="right"><button class="btn btn-mini btn-primary loadmore" type="button">Load More</button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="whitetable2" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>White List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option22"> Kw2
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="blacktable2" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>Black List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option21"> Kw1
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
				
						<div class="clear" style="height:20px"></div>
				
					<!--	<div class="span6" style="margin-left:0px">
							<table id="mastertable3" class=" table-striped table authority_table" width="100%">
								<tr>
									<th width="70%">SELECT STRATEGIC KWS</th>
									<th width="30%">
										<select name="comp" style="margin-bottom:0">
											<option value="Option1">Option1</option>
											<option value="Option2">Option2</option>
										</select>
									</th>
								</tr>
								<tr>
									<th>KW</th>
									<th>Rank</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="inlineCheckbox1" value="option31"> Kw1
									</label>
									</td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option32"> Kw2
									</label></td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option33"> Kw3
									</label></td>
									<td>100</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td width="70%"><button class="btn btn-success btn-mini addtowhitelist" type="button">+</button> <button class="btn btn-danger btn-mini addtoblacklist" type="button">+</button></td>
									<td width="30%" align="right"><button class="btn btn-mini btn-primary loadmore" type="button">Load More</button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="whitetable3" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>White List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option32"> Kw2
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="blacktable3" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>Black List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option31"> Kw1
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
						
						<div class="clear" style="height:20px"></div>
						
						<div class="span6" style="margin-left:0px">
							<table id="mastertable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th width="70%">SELECT STRATEGIC KWS</th>
									<th width="30%">
										<select name="comp" style="margin-bottom:0">
											<option value="Option1">Option1</option>
											<option value="Option2">Option2</option>
										</select>
									</th>
								</tr>
								<tr>
									<th>KW</th>
									<th>Rank</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option11"> Kw1
									</label>
									</td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option12"> Kw2
									</label></td>
									<td>100</td>
								</tr>
								<tr>
									<td><label class="checkbox inline">
										<input type="checkbox" id="" value="option13"> Kw3
									</label></td>
									<td>100</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td width="70%"><button class="btn btn-success btn-mini addtowhitelist" type="button">+</button> <button class="btn btn-danger btn-mini addtoblacklist" type="button">+</button></td>
									<td width="30%" align="right"><button class="btn btn-mini btn-primary loadmore" type="button">Load More</button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="whitetable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>White List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option11"> Kw1
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
						
						<div class="span3">
							<table id="blacktable1" class=" table-striped table authority_table" width="100%">
								<tr>
									<th>Black List</th>
								</tr>
								<tr>
									<td>
									<label class="checkbox inline">
										<input type="checkbox" id="" value="option12"> Kw2
									</label>
									</td>
								</tr>
							</table>
							<table width="100%">
								<tr>
									<td><button class="btn btn-danger btn-mini removelist" type="button"><i class="icon-remove"></i></button></td>
								</tr>
							</table>
						</div>
				
						<div class="clear" style="height:20px"></div> -->
						
				
				</div>
            </div>
        </div>
    </div>
	</div>
	<script>
		$(document).on("click",".addtowhitelist",function(){
			var master_id = $(this).closest(".span6").find("table").attr("id");
			var text = "";
			var whitelist_id = "whitetable"+master_id.substring('11');
			var blacklist_id = "blacktable"+master_id.substring('11');
			var whitelistarray = new Array();
			
			$("#"+whitelist_id+" input[type=checkbox]").each(function(index,element){
				whitelistarray.push($(element).val());
			})

			$("#"+master_id+" input[type=checkbox]:checked").each(function(index,element){
				if($.inArray($(element).val(), whitelistarray)===-1)
				{
					text += '<tr><td><label class="checkbox inline"><input type="checkbox" id="" value="'+$(element).val()+'">'+$.trim($(element).closest("td").text())+'</label></td></tr>';
					whitelistarray.push($(element).val());
				}
			})
			
			$("#"+blacklist_id+" input[type=checkbox]").each(function(index,element){
				
				if($.inArray($(element).val(), whitelistarray)!==-1)
				{
					$(element).closest("tr").remove();
				}
				
			})
			
			$("#"+whitelist_id).append(text);
			
		})
		
		$(document).on("click",".addtoblacklist",function(){
			var master_id = $(this).closest(".span6").find("table").attr("id");
			var text = "";
			var whitelist_id = "blacktable"+master_id.substring('11');
			var blacklist_id = "whitetable"+master_id.substring('11');
			var whitelistarray = new Array();
			
			$("#"+whitelist_id+" input[type=checkbox]").each(function(index,element){
				whitelistarray.push($(element).val());
			})
						
			$("#"+master_id+" input[type=checkbox]:checked").each(function(index,element){
				if($.inArray($(element).val(), whitelistarray)===-1)
				{
					text += '<tr><td><label class="checkbox inline"><input type="checkbox" id="" value="'+$(element).val()+'">'+$.trim($(element).closest("td").text())+'</label></td></tr>';
					whitelistarray.push($(element).val());
				}
			})
			
			$("#"+blacklist_id+" input[type=checkbox]").each(function(index,element){
				
				if($.inArray($(element).val(), whitelistarray)!==-1)
				{
					$(element).closest("tr").remove();
				}
				
			})
			$("#"+whitelist_id).append(text);
			
		})
		
		$(document).on("click",".removelist",function(){
		
			var id = $(this).closest(".span3").find("table").attr("id");
			
			$("#"+id+" input[type=checkbox]:checked").each(function(index,element){
				$(element).closest("tr").remove();
			})
			
		})
		
		$(document).on("click",".loadmore",function(){
		
			var id = $(this).closest(".span6").find("table").attr("id");
			
			$("#"+id).append('<tr><td><label class="checkbox inline"><input type="checkbox" id="" value="ghjghj">ghjhgj</label></td><td>452</td></tr>');
			
		})
		
	</script>
</body>
</html>