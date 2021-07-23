<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <title>Cloud World Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="icon" href="https://colorlib.com//polygon/admindek/files/assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>/public/bower_components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url();?>public/assets/pages/waves/css/waves.min.css" type="text/css" media="all">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/icon/feather/css/feather.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/icon/themify-icons/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/icon/icofont/css/icofont.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/icon/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/assets/css/pages.css">
</head>
<body themebg-pattern="theme1">
    <div class="theme-loader">
        <div class="loader-track">
            <div class="preloader-wrapper">
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="gap-patch">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class="login-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <form class="md-float-material form-material" onsubmit="return validate();" action="<?php echo base_url();?>client/login/login_set" method="post">
                    	<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">


                        <div class="text-center">
                            <h3>Cloud World</h3>
                        </div>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center txt-primary">Client Login</h3>
                                    </div>
                                </div>
                                <p class="text-muted text-center p-b-5">Login with your regular account</p>
                                <div class="form-group form-primary">
                                    <input type="email" name="username" id="username" class="form-control">
                                    <span class="form-bar" style="color: red;" id="username_error"></span>
                                    <label class="float-label">Email</label>
                                </div>
                                <div class="form-group form-primary">
                                    <input type="password" name="password" id="password" class="form-control">
                                    <span class="form-bar" id="password_error" style="color: red;"> </span>
                                    <label class="float-label">Password</label>
                                </div>
                                <div class="row m-t-25 text-left">
                                    <div class="col-12">
                                        <div class="checkbox-fade fade-in-primary">
                                            
                                        </div>
                                        <div class="forgot-phone text-right float-right">
                                            <a href="auth-reset-password.html" class="text-right f-w-600"> Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <input type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20" value="LOGIN">
                                    </div>
                                </div>
                                <p class="text-inverse text-left">Don't have an account?<a href="auth-sign-up-social.html"> <b>Register here </b></a>for free!</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
      </div>
    </section>
    <script type="text/javascript">
    function validate(){
    	var username = $("#username").val();
		var password = $("#password").val();
		var emailval = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/ ;
			if(username == ""){
				$("#username_error").html("Please enter email")
				return false;
			}else if(password == ""){
				$("#username_error").html("");
				$("#password_error").html("Please enter password");
				return false;
			}else if(!emailval.test(username)){
				$("#password_error").html("");
				$("#username_error").html("Please enter email in proper format");
				return false;
			}else{
				$("#username_error").html("");
				$("#password_error").html("");
				return true;
			}
    }
    </script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/jquery/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/popper.js/js/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>public/assets/pages/waves/js/waves.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>/public/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>public/assets/js/common-pages.js"></script>
    <script src="<?php echo base_url();?>public/assets/js/rocket-loader.min.js"></script>
</body>
</html>