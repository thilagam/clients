<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php include 'common_css.php'; ?>
	<?php include 'common_js.php'; ?>
    <title>St Edit Place</title>
</head>
<body class="login_page">

	<div id="header" class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
			<img src="http://www.edit-place.com/images/edit-place_logo.png">
			</div>
		</div>
	</div>
	
	<div class="login_box">
		<form name="adminHP" method="post" id="login_form" action="request.php">

			<div class="top_b">Login into Edit Place SEO Home</div>

			<div class="cnt_b">
				<div class="formRow">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-user"></i></span><input type="text" name="log" id="log" class="loginfield" placeholder="Username/Email"/>
					</div>
				</div>

				<div class="formRow">
					<div class="input-prepend">
						<span class="add-on"><i class="icon-lock"></i></span><input name="pass" id="pass" type="password" class="loginfield" value="" placeholder="Password" />
					</div>
				</div>

				<label  id="checkmsg" class="label label-inverse"></label>

			</div>

			<div class="btm_b clearfix">

				<button class="btn btn-inverse pull-right" type="submit">Sign In</button>

			</div>

		</form>

	</div>
	<script>

    $(document).ready(function(){



        // boxes animation

        form_wrapper = $('.login_box');

        function boxHeight() {

            form_wrapper.animate({ marginTop : ( - ( form_wrapper.height() / 2) - 24) },400);

        };

        form_wrapper.css({ marginTop : ( - ( form_wrapper.height() / 2) - 24) });

        $('.linkform a,.link_reg a').on('click',function(e){

            var target	= $(this).attr('href'),

                    target_height = $(target).actual('height');

            $(form_wrapper).css({

                'height'		: form_wrapper.height()

            });

            $(form_wrapper.find('form:visible')).fadeOut(400,function(){

                form_wrapper.stop().animate({

                    height	 : target_height,

                    marginTop: ( - (target_height/2) - 24)

                },500,function(){

                    $(target).fadeIn(400);

                    $('.links_btm .linkform').toggle();

                    $(form_wrapper).css({

                        'height'		: ''

                    });

                });

            });

            e.preventDefault();

        });



        //* validation

        $('#login_form').validate({

            onkeyup: false,

            errorClass: 'error',

            validClass: 'valid',

            rules: {

                log: { required: true, minlength: 3 },

                pass: { required: true, minlength: 3 }

            },

            submitHandler: function(form) {

                var log = $("#log").val();

                var pass = $("#pass").val();

                var target_page = "/index/index?log="+log+"&pass="+pass+"&logintest=yes";

                $.post(target_page, function(data){   ///alert(data);

                    var data1 = $.trim(data);

                    if(data1 == 'failed')
                        $("#checkmsg").text('Identifiant ou mot de passe incorrect');
                    else
                    {
                        //$('#login_form').submit();
                        window.location.reload();
                    }




                });



            },



            highlight: function(element) {

                $(element).closest('div').addClass("f_error");

                setTimeout(function() {

                    boxHeight()

                }, 200)

            },

            unhighlight: function(element) {

                $(element).closest('div').removeClass("f_error");

                setTimeout(function() {

                    boxHeight()

                }, 200)

            },

            errorPlacement: function(error, element) {

                $(element).closest('div').append(error);

            }

        });

    });

</script>

</body>
</html>