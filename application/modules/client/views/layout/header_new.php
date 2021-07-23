 <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Cloud Service World</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="<?php echo base_url(); ?>public/public/assets/img/favcon.png"/>

        <link type='text/css' href='<?php echo base_url();?>public/public/assets/fonts/google_fonts.css' rel='stylesheet'>
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/fonts/flaticon/flaticon.css" rel="stylesheet">
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">        <!-- Font Awesome -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/fonts/themify-icons/themify-icons.css" rel="stylesheet">              <!-- Themify Icons -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/bootstrap-new-grid.css" rel="stylesheet">                                     <!-- Core CSS with all styles --->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/styles.css" rel="stylesheet">                                     <!-- Core CSS with all styles -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/custom.css" rel="stylesheet">

        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/codeprettifier/prettify.css" rel="stylesheet">                <!-- Code Prettifier -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/iCheck/skins/minimal/blue.css" rel="stylesheet">              <!-- iCheck -->
        <link type="text/css" href="<?= base_url();?>public/public/assets/css/animate.css" rel="stylesheet">              
        <!-- Animate css -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/switchery/switchery.css" rel="stylesheet">   		<!-- Switchery -->
        <link type="text/css" href="<?php echo base_url();?>public/public/assets/css/mystyle.css" rel="stylesheet">
        <!-- Custom Checkboxes / iCheck -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/iCheck/skins/flat/_all.css" rel="stylesheet">
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/iCheck/skins/square/_all.css" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo base_url();?>public/public/assets/css/jquery-confirm.min.css">

        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/jquery-ui.min-1.12.1.css" rel="stylesheet">
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"> 			
        <link rel="icon" href="<?php echo base_url();?>public/public/assets/img/favicon.ico" type="image/ico" sizes="16x16">
        <!-- jVectorMap -->

        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/build.css" rel="stylesheet"> 			
        <!-- jVectorMap -->
        <link type="text/css" href=" <?php echo base_url();?>public/public/assets/css/fancymetags.css" rel="stylesheet"> 
        <script type="text/javascript" src="<?= base_url() ?>public/public/assets/js/jquery-1.10.2.min.js"></script>    
        <script type="text/javascript" src="<?php echo base_url();?>public/public/assets/js/jquery.validate.min.js"></script> 
        <script type="text/javascript" src="<?= base_url() ?>public/public/assets/js/additional-methods.min.js"></script>
        <script type="text/javascript" src=" <?php echo base_url();?>public/public/assets/js/comman.js"></script>
    <a href="javascript:;" id="demoskylo"></a>
	<script src="https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.2/dist/jBox.all.min.js"></script>
<link href="https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.2/dist/jBox.all.min.css" rel="stylesheet">

    <!-- Load jQuery -->
    <style>
        .noclick {pointer-events:none;opacity:0.4;}   
        .noaccess {pointer-events:none;opacity:0.4;}   
        .success-noty { background-color: #8bc34a;  color: white; }
        .error-noty { background-color: #dd191d; color:white; }
        .error { color: red; }
        #topnav .navbar-nav li.select_language{ margin-top: 14px; }
        #topnav .navbar-nav li.select_language .form-control{ border-radius: 0; height: 32px; }
        .custom-select{ position: relative; display: inline-block; }
        .custom-select::before{
            position: absolute; z-index: 11; content: ""; border-top: 6px solid #888; border-right: 6px solid transparent;
            border-left: 6px solid transparent; top: 15px; right: 8px; pointer-events: none;
        }

        .custom-select select{
            -moz-appearance: none;  
            -webkit-appearance: none;
            appearance: none;  
            width: 100%; 
            /*background: transparent !important; */
            padding: 0px 30px 0px 5px; 
            border: solid 1px #fff;
        } 
        /*For Ie*/
        .custom-select select::-ms-expand{
            display:none;
        }
        select{
            background:none; 
            color:#777; 
            /*font-size:16px; */
            text-align:left; 
            line-height:26px;  
            height:28px;
            padding:0 4px;
            box-sizing: border-box;
        }
        /*select option{padding:2px 5px; font-size: 14px;}*/
    </style>
    <link type="text/css" href=" <?php echo base_url();?>public/public/assets/plugins/form-select/select2.css" rel="stylesheet">

</head>
<body class="animated-content">
    <div class="modal fade" id="changepassword-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><?= $this->lang->line("change_password")?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                        <div class="col-sm-12">
                            <div class="card">                              
                                <div class="card-block">
                                    <form id="cpasswordform" method="post">
                                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= $this->lang->line("old_password")?></label>
                                            <div class="col-sm-9">
                                                <input type="password" name="oldpasswordverify" id="oldpasswordverify" placeholder="<?= $this->lang->line("old_password")?>" class="form-control">
                                                <span style="color: red;" class="oldpasswordverify_msg"></span>
                                            </div>
                                        </div> 

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= $this->lang->line("new_password")?></label>
                                            <div class="col-sm-9">
                                                <input type="password" name="cpasswordverify" id="cpasswordverify" placeholder="<?= $this->lang->line("new_password")?>" class="form-control">
                                                <span style="color: red;" class="cpasswordverify_msg"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= $this->lang->line("confirm_password")?></label>
                                            <div class="col-sm-9">
                                                <input type="password" name="ccpasswordverify" id="ccpasswordverify" placeholder="<?= $this->lang->line("confirm_password")?>" class="form-control">
                                                <span style="color: red;" class="ccpasswordverify_msg"></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="input-group">
                                                <div class="col-sm-6" id="cpassvererrormsg" style="color: red;"></div>
                                            <div class="col-sm-6 text-right">
                                                <button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit")?></button>
                                                <button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close")?></button>
                                            </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $("#cpasswordform").on('submit', function(e){
            e.preventDefault();
            var cpasswordverify = $("#cpasswordverify").val(); 
            var ccpasswordverify = $("#ccpasswordverify").val(); 
            var oldpasswordverify = $("#oldpasswordverify").val(); 

            if (oldpasswordverify == '') {
                
                 swal("<?php echo $this->lang->line("old_password_blank")?>", {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
                return false;

            }

            if(cpasswordverify == ""){
                 swal("<?php echo $this->lang->line("new_password_blank")?>", {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
            }else if(ccpasswordverify == ""){
                 swal("<?php echo $this->lang->line("confirm_password_blank")?>", {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
            }else if(ccpasswordverify != cpasswordverify){
                 swal("<?php echo $this->lang->line("matching_password_blank")?>", {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
            }else{
            $.ajax({
               url:"<?php echo base_url();?>client/users/change_password",
               type:"post",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData:false,
                success:function(data){
                    console.log(data);
                    if(data.status == "success"){
                        $("#cpasswordverify, #ccpasswordverify, #oldpasswordverify").val("")
                        
                                        swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
                                                $("#changepassword-Modal").modal("hide");
                                            });
                        
                    }else{
                        
                        swal(data.msg, {
                                                  title: "<?= $this->lang->line("oops")?>",
                                                  type: "error",
                                                  timer: 3000
                                                });
                    }
                        
                 }
            });
            }
         });

     function change_language(langvar){
        $.ajax({
           url:"<?php echo base_url();?>client/dashboard/change_language",
           type:"post",
            data: {langvar:langvar},
            dataType: 'json',
                    success:function(data){
                       if(data.status == "success"){
                           location.reload();
                       }    
                     }
                });

     }

     function logout_client(){
        var chk = "<?php echo $this->lang->line("logout_sure")?>";
        swal(chk, {
              buttons: {
                cancel: "<?php echo $this->lang->line("No")?>",
                catch: {
                  text: "<?php echo $this->lang->line("Yes")?>",
                  value: "catch",
                },
              },
            })
            .then((value) => {
              switch (value) {
                case "defeat":
                  return false;
                  break;
                case "catch":
                 location.replace("<?= base_url('client/login/logout') ?>");
                  break;
                default:
                 return false;
              }
            });
    }


    </script>
    <header id="topnav" class="navbar navbar-default navbar-fixed-top" role="banner">
        <div class="logo-area">
            <span id="trigger-sidebar" class="toolbar-trigger toolbar-icon-bg">
                <a data-toggle="tooltips" data-placement="right" title="Toggle Sidebar">
                    <span  class="icon-bg">
                        <i class="ti ti-menu"></i>
                    </span>
                </a>
            </span>
            <a class="navbar" href="<?php echo base_url('client/dashboard');?>">
                <h5>CLOUD SERVICE WORLD</h5> 
               <!-- <img src="<?php //echo base_url() ?>/public/public/assets/img/logo_admin.png" class="img-responsive" /> -->
            </a>
        </div><!-- logo-area -->
        <ul class="nav navbar-nav toolbar pull-right">
            <?php if($this->session->userdata('role_type') == 'client') {
                ?>
            <li class="">
                <a class="plan" href="<?php echo base_url(); ?>client/dashboard/requestreseller" target="_blank"><strong><?php echo $this->lang->line("become_reseller")?> </strong></a>
            </li>
        <?php } ?>
        	<li class="">
                
                <?php 
                $ifplanexpire = $this->general->check_if_plan_expire();
                if($ifplanexpire == 'noplansubcribed' ){ 
                ?>
                    <a class="plan" href="<?php echo base_url(); ?>client/dashboard/view_plan/"><span><?= $this->lang->line("Get a new plan") ?> </span></a>

                <?php }else if($ifplanexpire == "expired"){ ?>
                    <a class="plan" href="<?php echo base_url(); ?>client/dashboard/client_plan/<?= $this->session->userdata("plan_id") ?>"><span><?= $this->lang->line("upgrade_plan") ?> </span></a>
                <?php }else{ ?>
                    <a class="plan" href="<?php echo base_url(); ?>client/dashboard/client_plan/<?= $this->session->userdata("plan_id") ?>"><span><?= $this->session->userdata("plan_name") ?> </span></a>

                <?php } ?>    
                


            </li>
            <li class="dropdown toolbar-icon-bg select_language">
                <span class="custom-select">
                <select class="form-control" onchange="change_language(this.value)" >
                    <option value="en" <?php if($this->session->userdata('lang') == 'en') echo 'selected="selected"'; ?> >English</option>
                    <option value="ger" <?php if($this->session->userdata('lang') == 'ger') echo 'selected="selected"'; ?>>Deutsch</option>
                </select>
                </span>
            </li>

            <li class="dropdown toolbar-icon-bg">
                <a href="javascript:" onclick="logout_client()" data-toggle="tooltip" title="Sign Out"><i style="color: white;" class="flaticon-logout"></i></a>
            </li>

            <!-- <li class="dropdown toolbar-icon-bg">
                <input type="hidden" id="notificationArr" value="">
                <input type="hidden" id="messageArr" value="">
                <a href="#" class="hasnotifications dropdown-toggle" data-toggle='dropdown'><span id="notificationIcon" class="icon-bg"><i class="ti ti-bell"></i></span><span id="notificationCount" class="badge badge-deeporange">0</span></a>
                <div class="dropdown-menu notifications arrow">
                    <div class="topnav-dropdown-header">
                        <span><?= $this->lang->line("notifications")?></span>
                    </div>
                    <div class="scroll-pane notificationPanel">
                        <ul class="media-list scroll-content">

                            <li class="media notification-message " >
                                <div class="alert alert-light-warning m-0 pt-5 pb-5 text-center alertNotification"><?= $this->lang->line("no_notifications")?></div>
                            </li>

                        </ul>
                    </div>
                    <div class="topnav-dropdown-footer">
                        <a href="<?php echo base_url() ?>notifications"><?= $this->lang->line("see_all_notifications")?></a>
                    </div>
                </div>
            </li> -->

            


        </ul>
    </header>
    


