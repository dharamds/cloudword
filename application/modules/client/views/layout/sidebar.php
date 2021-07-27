
<?php
 $ifplanexpire = $this->general->check_if_plan_expire();
 $ifplanexpirecss = '';
 if($ifplanexpire == 'expired' || $ifplanexpire == 'noplansubcribed'){ $ifplanexpirecss = ' noclick ';}


//print_r($ifplanexpire); exit;
 $allowmodule = $this->general->check_for_allow_module();
 //echo '<pre>';
 //print_r($allowmodule);
 //exit;



?>
<div id="wrapper">
    <div id="layout-static">
        <div class="static-sidebar-wrapper sidebar-default">

            <ul class="sidebarprofile">
                <li class="dropdown toolbar-icon-bg">
                    <a href="#" class="dropdown-toggle username" data-toggle="dropdown">

                            <?php
                            $icn = $this->session->userdata("icon");
                            if($icn !=""){
                                ?>
                                <img src="<?php echo base_url() ?>uploads/user/profile/thumbnail/<?=$icn?>" class="img-responsive img-circle" width="200" alt="">
                                <?php
                            }else{
                                ?>
                                <img src="<?php echo base_url() ?>public/public/assets/img/default_user.png" class="img-responsive img-circle" width="200" alt="">
                                <?php
                            }
                            ?>                            
                    </a>
                    <span class="username"><?php echo $this->session->userdata("fname")." ".$this->session->userdata("lname");?></span>
                    <ul class="dropdown-menu userinfo arrow">
                        <li>
                            <a href="<?php echo base_url();?>client/users/profile">
                                <i class="flaticon-user-2"></i><span> <?php echo $this->session->userdata("fname")." ".$this->session->userdata("lname");?></span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a onclick="$('#changepassword-Modal').modal('show');" href="javascript:">
                                <i class="flaticon-password"></i><span><?php echo $this->lang->line("change_password")?></span>
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= base_url();?>client/login/logout">
                                <i class="flaticon-logout-2"></i><span><?php echo $this->lang->line("sign_out")?></span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="static-sidebar">
                <div class="sidebar">
                    <div class="widget stay-on-collapse sidebar-for-media" id="widget-sidebar">
                        <nav role="navigation" class="widget-body">
                            <ul class="acc-menu">
                                <li class="<?php echo $page == "dashboard" ?'active' :'';?>">
                                    <a href="<?= base_url();?>client/dashboard">
                                        <i class="flaticon-dashboard"></i> <span><?php echo $this->lang->line("dashboard")?></span>
                                    </a>
                                </li>
                            </ul>
                            <?php if($this->session->userdata("role_type") == "reseller") {
                                ?>
                            <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "users" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-user-2"></i> <span><?php echo $this->lang->line("customers")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('client/users') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("customer")." ".$this->lang->line("list")?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('client/users/create') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("create")." ".$this->lang->line("customer")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "plan" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-planning"></i> <span><?php echo $this->lang->line("plans")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('client/plan') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("plan_list")?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('client/plan/add') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("create_plan")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul>
							<ul class="acc-menu">
                                <li class="<?php echo $page == "subscriptions" ?'active' :'';?>">
                                    <a href="<?= base_url('client/plan/subscription_details') ?>">
                                        <i class="flaticon-task"></i> <span><?php echo $this->lang->line("plan_subscription_details")?></span>
                                    </a>
                                </li>
                            </ul>
                            <?php
                                }
                            ?>
                            <ul class="acc-menu">
                                
								
								<li class="hasChild <?php echo $page == "updateRequest" ? 'active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-planning"></i> <span><?php echo $this->lang->line("space_update_requests")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url();?>client/updateRequest">
                                                    <i class="circle"></i> <?php echo $this->lang->line("space_update_requests")?>
                                                </a>
                                            </li>
											
											<?php if($this->session->userdata("role_type") == 'reseller'): ?>
                                            <li>
                                                <a href="<?= base_url('client/updateRequest/customer_request') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("customer_space_update_requests")?>
                                                </a>
                                            </li>
										<?php endif; ?>

									   </ul>
                                </li>
								
								
                            </ul>

                           
                            <ul class="acc-menu <?php echo $ifplanexpirecss; echo in_array('7', $allowmodule) ?'':' noaccess'; ?>">
                                <li class="<?php echo $page == "projects" ?'active' :'';?>">
                                    <a href="<?= base_url();?>client/project">
                                        <i class="flaticon-task"></i> <span><?php echo $this->lang->line("projects")?></span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="acc-menu <?php echo $ifplanexpirecss; echo in_array('5', $allowmodule) ?'':' noaccess'; ?>">
                                <li class="hasChild <?php echo $page == "shopware" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-shopping-bag"></i> <span><?php echo $this->lang->line("shopware_overview")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('client/shopware/create/') ?>">
                                                    <i class="circle"></i><?php echo $this->lang->line("add_project_details")?></span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('client/shopware/') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("sales_overview")?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('client/shopware/add_api_key') ?>">
                                                    <i class="circle"></i> <?= $this->lang->line("api_key")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul>
                            <!-- <ul class="acc-menu <?php //echo $ifplanexpirecss; echo in_array('6', $allowmodule) ?'':' noaccess';?>">
                                <li class="hasChild <?php //echo $page == "api_modules" ?'open' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-api-3"></i> <span><?php //echo $this->lang->line("api_modules")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?php // base_url('client/ApiModules/modules/') ?>">
                                                    <i class="circle"></i> <?php //echo $this->lang->line("add_api_credentials")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul> -->

                           
                           <!--  <ul class="acc-menu <?php //echo $ifplanexpirecss; ?>">
                                <li class="<?php //echo $page == "userapi" ?'open' :'';?>">
                                    <a href="<?php// base_url();?>client/userapi">
                                        <i class="flaticon-api-2"></i> <span><?php //echo $this->lang->line("manage_api")?></span>
                                    </a>
                                </li>
                            </ul> -->

                            <?php if($this->session->userdata("role_type") == "reseller") {
                                ?>
                                <ul class="acc-menu">
                                    <li class="<?php echo $page == "site_setting" ?'active' :'';?>">
                                        <a href="<?= base_url();?>client/settings">
                                            <i class="flaticon-api"></i> <span><?php echo $this->lang->line("site_setting")?></span>
                                        </a>
                                    </li>
                                </ul>
                            <?php } ?>  
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- Sidebar Ends -->
<div class="static-content-wrapper">
    <div class="static-content">
        <div class="page-content">
            <ol class="breadcrumb">
                <li class="">
                    <span></span>
                </li>
            </ol>




