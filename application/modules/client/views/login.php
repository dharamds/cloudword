<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/css/iofrm-theme9.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>public/public/assets/fonts/flaticon/flaticon.css">
    <link rel="shortcut icon" href="<?= base_url(); ?>public/public/assets/img/favcon.png"/>
    <style>
        
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
                    <!-- <img class="logo-size" src="<?php // base_url(); ?>public/public/assets/img/logo_admin.png" alt=""> -->
                </div> 
            </a>
        </div>
        <div class="form-holder">
            <div class="form-content">                  
                <div class="form-items">
                  <div class="form-title-holder">
                    <h2 class="title"><?php echo $this->lang->line("client_login")?></h2>
                    <div class="subtitle"><?php echo $this->lang->line("loginmsg")?></div>

                </div>
                <div class="page-links">
                    <strong style="color: red;"><?= $error_login;  ?></strong>
                </div>

                <form id="login_form" onsubmit="return validate();" action="<?php echo base_url();?>client/login/login_set" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div>
                        <div class="input-group mb-4">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><i class="flaticon-user"></i></div>
                        </div>
                        <input  class="form-control inputStyle" type="email" name="username" id="username" placeholder="<?php echo $this->lang->line("Email")?>" required>
                    </div>
                    <div class="input-group mb-4">
                      <div class="input-group-prepend">
                        <div class="input-group-text"><i class="flaticon-password"></i></div>
                    </div>                              
                    <input class="form-control inputStyle" type="password" name="password" placeholder="<?php echo $this->lang->line("password")?>" required>
                </div>
            </div>                                                        <div class="form-button">
              <div class="text-center">
                <button id="submit" type="submit" class="ibtn"><?php echo $this->lang->line("login")?></button> 
            </div>
            <div class="text-center forgot mt-2">
                <a href="<?= base_url('client/login/forgot_password'); ?>">
                    <?php echo $this->lang->line("forgot_password")?>
                </a>      
            </div>
        </div>
    </form>

</div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
    function validate(){
        var username = $("#username").val();
        var password = $("#password").val();
        var emailval = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ ;
            if(username == ""){
                $("#username_error").html("<?php echo $this->lang->line("email_blank")?> ")
                return false;
            }else if(password == ""){
                $("#username_error").html("");
                $("#password_error").html("<?php echo $this->lang->line("password_blank"); ?> ");
                return false;
            }else if(!emailval.test(username)){
                $("#password_error").html("");
                $("#username_error").html("<?php echo $this->lang->line("email_not_proper"); ?>");
                return false;
            }else{
                $("#username_error").html("");
                $("#password_error").html("");
                return true;
            }
    }
    </script>
<script type="text/javascript" src="<?= base_url('public/public/assets/js/jquery-1.10.2.min.js') ?>"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/popper.min.js"></script> 
<script src="<?= base_url(); ?>public/public/assets/js/bootstrap.min.js"></script>
</body>
</html>


