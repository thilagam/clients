<?php
ob_start();
define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']);
define("INCLUDE_PATH",ROOT_PATH."/includes");

include_once(INCLUDE_PATH."/session.php");
include_once(INCLUDE_PATH."/config_path.php");
include_once(INCLUDE_PATH."/header.php");
include_once(INCLUDE_PATH."/left-menu.php");
?>

<div class="span10 content">    
    <h2 class="heading">XLS/XLSX GROUP IT</h2>        
    <div class="span11">
        <div class="col-lg-12">
            <div class="alert alert-info"><strong>Step 1 : Select File to Find Groups</strong></div>

            <div class="jumbotron" id='step1'>
<form action='#' method="post" enctype="multipart/form-data" id='uploadfiles' name='upload'>
    <div id='error1' style="display: none;" class="alert alert-danger"></div>
    <div class="col-lg-12">
        <div class="col-lg-6">
            <input type="file" name="userfile[]"  class="btn btn-lg btn-info" >
        </div>
    </div>
    <span>
        <input class="btn btn-lg btn-warning right col-lg-3"  type="reset" value="Reset" /> 
        <span class="col-lg-1"></span>
        <input class="btn btn-lg btn-primary right col-lg-3"  type="submit" value="upload" />
    </span>
    <span id='progress' style="display:none;padding-left:30px;">
        <img style="width: 40px; height: 40px;" src="../../img/page-loader.gif" alt="loding"> Upload in progress..
    </span>
</form>
            </div>
            </div>
            <div class="col-lg-12">

    <div class="alert alert-info"><strong>Step 2 : Select Column to Group</strong></div>
<form method="post" enctype="multipart/form-data" action="#" id='compare' name="compare">

    <div class="jumbotron" id='step2' style="display:none;">
        
        <div id='error2' style="display: none;" class="alert alert-danger"></div>
        <div class="col-lg-12">
            <div class="col-lg-6">
            <label class="col-lg-3">File Column</label>
            <label id='origfilename1' class="col-lg-3"></label>
            <select  class="form-control col-lg-6" name='col_file1' id='col1'></select>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-6">
            <label class="col-lg-3">Percentage</label>
            <label id='percentage' class="col-lg-3"></label>
            <select  class="form-control col-lg-6" name='percentage' id='percentage'>
<?php for($i=10;$i<101;$i++){ ?><option value="<?=$i?>"><?=$i?></option><?php } ?>
	    </select>
	    <!--<input name="percentage" id="percentage" type="range" min="10" max="100" step="1" />-->
            </div>
        </div>
        <div class="col-lg-12">
            <input type="hidden" id='savedname1' name='filename1' value="">
        </div>
        <div class="col-lg-12"></div>
        <div class="clearfix"></div>

        <div class="col-lg-12">
            <span>
                <input class="btn btn-lg btn-warning right col-lg-3"  type="reset" value="Reset" /> 
                <span class="col-lg-1"></span>
                <input class="btn btn-lg btn-primary right col-lg-3"  type="submit" value="Process" />
            </span>
            <span id='progress2' style="display:none;padding-left:30px;">
                <img style="width: 40px; height: 40px;" src="../../img/page-loader.gif" alt="loding">Comparing in progress..
            </span>
        </div>
    </div>
</form>
            </div>
            <div class="col-lg-12">
                <div class="alert alert-info"><strong>Step 3 : Download Processed File</strong></div>
                
<div class="jumbotron" id='step3' style="display: none;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Download The File</h3>
        </div>
        <div class="panel-body">
            <label id='result_lines'></label>
            <a id='dwdcomp' href="#" target="_blank">Download processed File</a><a id='dwdcountercomp' href="#" target="_blank" style="margin-left:40px;">Download count File</a>
            <div style="margin-top: 20px;"></div>
            <button type="button" id='recompare' class="btn btn-info">Other Column Grouping</button>
            <button type="button" id='reset_page' class="btn btn-info">Extract Other Files</button>
        </div>
    </div>
    <div class="col-lg-12"></div>
</div>
            </div>
        </div>
    </div>
<style>
.col-lg-12, .col-lg-6{margin:0 0 10px 0;width: 100%;float: left;}select {margin: 0;}
select.col-lg-12, select.col-lg-6{width:220px;}
#divL{float:left;width:50%;}#divR{float:left;width:50%;}
#comp1,#comp2,#comp3,#comp4{display:inline;padding: 0 0 0 10px;}
</style>

<script type="text/javascript">
$(function()
{
    // Variable to store your files
    var files=new Array();

    // Add events
    $('input[type=file]').on('change', prepareUpload);
    $('#uploadfiles').on('submit', uploadFiles);
    //$('#compare').on('submit', submit_compare);
    var index=0;
    // Grab the files and set them to our variable
    function prepareUpload(event)
    {   
        //if($(this).attr("id")!='upload'){
            //return;
        //}
        
        files[index] = event.target.files;
        index++;
    }

    // Catch the form submit and upload the files
    function uploadFiles(event)
    {
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening

        // START A LOADING SPINNER HERE
        //alert(files);
        $('#progress').show('slow');
        // Create a formdata object and add the files
        var data = new FormData();
        var validerror=false;
        var ind=0;
        $.each(files, function(key, value)
        {   ind++;  
            
            $.each(value, function(key2,value2){
                
                data.append(key, value2);
                    
                
            }); 
                
        });
        if(ind<1){
            validerror=true;
        }
        
        if(validerror){
            //alert('error');
            $('#progress').hide('slow');
            $('#error1').show('slow');
            $('#error1').html('Chose both Files to Upload');
            return;
        }
       // console.log('data: ' + data);
        $.ajax({
            url: '<?=MISC_GROUP_TOOL_PATH?>groupupload.php?files',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            success: function(data, textStatus, jqXHR)
            {
                if(typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    
                    $('#progress').hide('slow');
                $('#error1').show('slow');
                $('#error1').html(data.error);
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                $('#progress').hide('slow');
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            }
        });
    }

    function submitForm(event, data)
    {
        // Create a jQuery object from the form
        $form = $(event.target);
        
        // Serialize the form data
        var formData = $form.serialize();
        //console.log(data.files);
        // You should sterilise the file names
        $.each(data.files, function(key, value)
        {       //alert(value);
            //  $.each(value,function(key2,value2){
                    formData = formData + '&filenames[]=' + value;

        });

        $.ajax({
            url: '<?=MISC_GROUP_TOOL_PATH?>groupupload.php',
            type: 'POST',
            data: formData,
            cache: false,
            dataType: 'json',
            success: function(data, textStatus, jqXHR)
            {
                if(typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    console.log('SUCCESS: ' + data.success);
                }
                else
                {   
                    
                        
                    //alert('HERE');
                    
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                    
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                $('#progress').hide('slow');
                console.log('ERRORS: ' + textStatus);
            },
            complete: function()
            {
                $('#progress').hide('slow');
                // STOP LOADING SPINNER
                $('#step1').hide('slow');
                $('#step2').show('slow');
                console.log(data.filename1);
                $("#origfilename1").html(data.filename1);
                //$("#origfilename2").html(data.filename2);
                $("#savedname1").val(data.savedfilename1);
                //$("#savedname2").val(data.savedfilename2);
                
                $.each(data.columns_sheet1, function(key, value)
                {   
                     $('#col1').append($('<option>').text(key).attr('value', key));     
                    
                });
                
            }
        });
    }
    
    $('#reset_page').click(function() {
        location.reload();
    });
    
    $('#recompare').click(function() {
        $('#step3').hide('slow');
        $('#step2').show('slow');
    });
    
    
    //function submit_compare(){
    $('#compare').submit(function (e) { 
            
            e.stopPropagation(); // Stop stuff happening
            e.preventDefault(); // Totally stop stuff happening
            $('#progress2').show('slow');
            var vfile1=$('#savedname1').val();
            
            var vcol1=$('#col1').val();
            
            
            var formData = $(this).serializeArray();
        /*var valData = {   
                            file1:$( "input[name$='filename1']" ).val(),
                            file2:$( "input[name$='filename2']" ).val(),
                            col1:$( "input[name$='col_file1']" ).val(),
                            col2:$( "input[name$='col_file2']" ).val(),
                            valoperation:$( "input[name$='compare_type']" ).val(),
                            valoptions:$( "input[name$='options']" ).val()
                            };*/ 
                            
            //alert(formData);
            //var data = new array();
             
            $.ajax({
                url : "<?=MISC_GROUP_TOOL_PATH?>groupie.php",
                type: "POST",
                data : formData,
                cache: false,
                dataType: 'json',
                success: function(data, textStatus, jqXHR)
                {
//alert(data.error);
                    console.log(data);
                    if(data.error!=''){
                        $('#error2').show('slow');
                        $('#error2').html(data.error);
                    }else{
                    //data - response from server
                    console.log('SUCCESS: ' + data.success);
                    console.log('File : ' + data.file);
                    $('#step2').hide('slow');
                    $('#step3').show('slow');
                    //$('#result_lines').html('There are '+data.lines+' Lines Present in Result ');
                    if(data.file!=''){
                        $("#dwdcomp").attr("href", data.file)
			$("#dwdcountercomp").attr("href", data.counts_file)
                    }else{
                        $("#dwdcomp").hide();
                    }
                    }
                    
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    console.log('ERRORS: ' + textStatus);
                },
            complete: function()
            {
                $('#progress2').hide('slow');
            }
            
        });
        
    });
});
</script>

<?php
include_once(INCLUDE_PATH."/footer.php");
?>
