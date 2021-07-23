<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("reset_password");?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/css/iofrm-theme9.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/fonts/flaticon/flaticon.css">
    <link rel="shortcut icon" href="<?= base_url(); ?>public/public/assets/img/favcon.png"/>
    <style>
        .inputStyle{
          color:#000 !important ;  
      }
  </style>

</head>
<body style="background-image:url('<?php echo base_url();?>public/public/front/img/banner-bg.png');">
    <div class="form-body">
        <div class="row ">
          <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-2">
            <div class="website-logo-inside text-center">
                <a href="javascript:void(0)">
                 <div class="logo"> 
                    <h1>Cloud Service World</h1>
                </div> 
            </a>
        </div>

        <?php if($pageActionStatus == 'notexpired'){?>
        <div class="form-holder">
            <div class="form-content">                  
                <div class="form-items">
                  <div class="form-title-holder">
                    <h2 class="title"><?php echo $this->lang->line("set_password");?></h2>
                </div>
                <div class="page-links" >
                    <strong style="color: red;" id="loginerror"><?= $error_login;  ?></strong>
                </div>

                <div class="loginBox">
                    <form id="resetpassword_form" action="#" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        
                        <div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="flaticon-user"></i></div>
                                </div>
                                <input  class="form-control inputStyle" type="email" name="username" id="username" placeholder="<?php echo $this->lang->line("email")?>" >
                            </div>  
                            <span style="color:red;" id="username_error"></span>
                        </div> 

                        
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="flaticon-password"></i></div>
                            </div>                              
                            <input class="form-control inputStyle" type="password" name="password" id="password" placeholder="<?php echo $this->lang->line("Password")?>" >
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="flaticon-password"></i></div>
                            </div>                              
                            <input class="form-control inputStyle" type="password" name="confirm_password" placeholder="<?php echo $this->lang->line("confirm_password")?>" >
                        </div>

                        <div class="form-button">
                            <div class="text-center">
                                <button id="submit" type="submit" class="ibtn"><?php echo $this->lang->line("submit");?></button> 
                            </div>
                        </div>

                    </form>
                </div>    

            </div>
        </div>
        <?php }else{ ?>
            <div class="text-center"><h2> <?php echo $this->lang->line("This link is expired")?></h2></div>
        <?php } ?>    

    </div>


<script type="text/javascript" src="<?= base_url('public/public/assets/js/jquery-1.10.2.min.js') ?>"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/jquery.validate.min.js"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/popper.min.js"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/bootstrap.min.js"></script>

<script type="text/javascript">

$(document).ready(function(){
  


    $("#resetpassword_form").validate({
			rules: {
				'username': {
					required: true,
					remote   : {
                        url:"<?php echo base_url();?>client/login/getCheckUserNameExistForSetPass",
					 	type: 'POST',
					 	data: { secrete_key:'<?php echo $secrete_key; ?>' }
					 }
				},
				'password': {
					required: true,
					minlength : 8
				},
				'confirm_password': {
					equalTo:"#password"
				},

			},
			messages: {
				"username": {
					required: '<?php echo $this->lang->line("Please enter username");?>',
					remote: '<?php echo $this->lang->line("Please enter valid username");?>'
				},
				"password": {
					required: '<?php echo $this->lang->line("Please enter new password");?>',
					minlength : '<?php echo $this->lang->line("Password must be minimum 8 characters/numbers.");?>'
				},
				'confirm_password': {
					equalTo: '<?php echo $this->lang->line("Confirmed password not matched with password");?>'
				}
			},
			errorPlacement: function (error, element) {
				error.insertAfter(element.parent('.input-group'));
			},
			submitHandler: function () { // for demo	
				submitForm();
				return false;
			}
		});


        var submitForm = function (){ 
            $('.message').html('');
            $.ajax({
                url:"<?php echo base_url();?>client/login/get_set_password",
                method: "POST",
                data : $('#resetpassword_form').serialize(),
                dataType: 'json',
                beforeSend: function () {
                    //$(".loader").show();
                    $("#submit").prop('disabled', true);
                },
                success: function (result) {
                    if(result.code == 200)
                    {
                        $('.loginBox').html('<div class="alert alert-success" role="alert">'+result.message+'</div>');
                        setTimeout(function(){ window.location.href = "<?php echo base_url();?>client/login" }, 5000);
                    }else{
                        //$(".loader").hide();
                        $("#submit").prop('disabled', false);
                    }
                }
            });

            return false;
	    }



});


    
</script>


</body>
</html>


