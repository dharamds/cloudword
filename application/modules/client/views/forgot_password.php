<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("forgot_password");?></title>
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
        <div class="form-holder">
            <div class="form-content">                  
                <div class="form-items">
                  <div class="form-title-holder">
                    <h2 class="title"><?php echo $this->lang->line("forgot_password")?></h2>
                </div>
                <div class="page-links" >
                    <strong style="color: red;" id="loginerror"><?= $error_login;  ?></strong>
                </div>

                <div class="loginBox">
                    <form id="forgotpassword_form" onsubmit="return validate();" action="#" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <div>
                            <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><i class="flaticon-user"></i></div>
                            </div>
                            <input  class="form-control inputStyle" type="email" name="username" id="username" placeholder="<?php echo $this->lang->line("email")?>" required>
                            
                        </div>  
                        <span style="color:red;" id="username_error"></span>
                        </div> 

                        <div class="form-button">
                            <div class="text-center">
                                <button id="submit" type="submit" class="btn ibtn pl-2"><?php echo $this->lang->line("submit");?></button> 
                            </div>

                            
                        </div>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <a href="<?= base_url('client/login'); ?>"> 
                    <?php echo $this->lang->line("Back to Login?");?>
                    </a>    
                </div>

</div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    function validate(){
        var username = $("#username").val();
        var emailval = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ ;
            if(username == ""){
                $("#username_error").html("<?php echo $this->lang->line("email_blank")?> ")
                return false;
            }else if(!emailval.test(username)){
                $("#loginerror").html("");
                $("#username_error").html("<?php echo $this->lang->line("email_not_proper");?>");
                return false;
            }else{
                $("#username_error").html("");
                $("#loginerror").html("");
                //alert('heer');
                $.ajax({
                    url:"<?php echo base_url();?>client/login/get_send_forgot_password_link",
                    method: "POST",
                    data : $('#forgotpassword_form').serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $("#submit").prop('disabled', true);
                    },
                    success: function (result) {
                        //alert(result);
                        if(result.code == 200)
                        {
                            $("#loginerror").html("");    
                            $('.loginBox').html('<div class="alert alert-success" role="alert">'+result.message+'</div>');
                        }else{
                            $("#submit").prop('disabled', false);
                            $("#loginerror").html(result.message);
                            
                        }
                    }
                });

                return false;
               
            }
    }
    </script>
<script type="text/javascript" src="<?= base_url('public/public/assets/js/jquery-1.10.2.min.js') ?>"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/popper.min.js"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/bootstrap.min.js"></script>
</body>
</html>


