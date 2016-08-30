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
    <h2 class="heading">Xls tag search</h2>        
    <div class="span11">
        <div class="col-lg-12">
            <div class="alert alert-info"><strong>Step 1 : XLS/XLSX Tags - Select Files to Find Tags</strong></div>
           
            <div class="jumbotron" id='step1'>
                <form action='#' method="post" enctype="multipart/form-data" id='uploadfiles' name='upload'>
                    <div class="col-lg-12">
                        <span>
                            <div id='error1' style="display: none;" class="alert alert-danger"></div>
                            <input type="file" name="userfile[]"  class="btn btn-lg btn-info" >
                            <input class="btn btn-lg btn-warning right col-lg-3"  type="reset" value="Reset" /> 
                            <span class="col-lg-1"></span>
                            <input class="btn btn-lg btn-primary right col-lg-3"  type="submit" value="upload" />
                        </span>
                        <span id='progress' style="display:none;padding-left:30px;">
                            <img style="width: 40px; height: 40px;" src="../../img/page-loader.gif" alt="loding"> Upload in progress..
                        </span>
                    
                    </div>
                </form>                
            </div>
            </div>
            <div class="col-lg-12">
                <div class="alert alert-info"><strong>Step 2 : Select Column to Extract Tags</strong></div>
                <form method="post" enctype="multipart/form-data" action="#" id='compare' name="compare">
                 <div class="jumbotron" id='step2' style="display: none;">
                    <div id='error2' style="display: none;" class="alert alert-danger"></div>
                    <div class="col-lg-12">
                        <label class="col-lg-3">File Column</label>
                        <label id='origfilename1' class="col-lg-3"></label>
                        <select  class="form-control" name='col_file1' id='col1'></select>
                    </div>
                        <input type="hidden" id='savedname1' name='filename1' value="">

                    <div class="col-lg-12">
                        <span>
                            <input class="btn btn-lg btn-warning right col-lg-3"  type="reset" value="Reset" /> 
                            <span class="col-lg-1"></span>
                            <input class="btn btn-lg btn-primary right col-lg-3"  type="submit" value="Get Tags" />
                        </span>
                        <span id='progress2' style="display: none;padding-left:30px;">
                            <img style="width: 40px; height: 40px;" src="../../img/page-loader.gif" alt="loding">Tag extraction in progress..
                        </span>
                    </div>
                    
                    </form>
        
                 </div>
            </div>
            <div class="col-lg-12">
                <div class="alert alert-info"><strong>Step 3 : Download Comparison File</strong></div>
                
                <div class="jumbotron" id='step3' style="display: none;">
                   <div class="alert alert-success">
                        <a id='dwdcomp' href="#" target="_blank">Download Tag Extracted File</a>
                   </div>
                   <button type="button" id='recompare' class="btn btn-info">Other Column Extraction</button>
                   <button type="button" id='reset_page' class="btn btn-info">Extract Other Files</button>
                </div>
            </div>
        </div>
    </div>
<style>
    .col-lg-12, .col-lg-6{margin:0 0 10px 0;width: 100%;float: left;}select {margin: 0;}
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
            files[index] = event.target.files;
            index++;
        }
    
        // Catch the form submit and upload the files
        function uploadFiles(event)
        {
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening
    
            // START A LOADING SPINNER HERE
            $('#progress').show('slow');
            // Create a formdata object and add the files
            var data = new FormData();
            var validerror=false;
            var ind=0;
            $.each(files, function(key, value)
            {
                ind++;
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
                url: 'tagupload.php?files',
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
                formData = formData + '&filenames[]=' + value;
            });
    
            $.ajax({
                url: 'tagupload.php',
                type: 'POST',
                data: formData,
                cache: false,
                dataType: 'json',
                success: function(data, textStatus, jqXHR)
                {
                    //alert(data);
                    if(typeof data.error === 'undefined')
                    {
                        // Success so call function to process the form
                        console.log('SUCCESS: ' + data.success);
                    }
                    else
                    {
                        // Handle errors here
                        console.log('ERRORS: ' + data.error);//alert('HERE');
                        
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
                 
                $.ajax({
                    url : "tagextract.php",
                    type: "POST",
                    data : formData,
                    cache: false,
                    dataType: 'json',
                    success: function(data, textStatus, jqXHR)
                    {   
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
