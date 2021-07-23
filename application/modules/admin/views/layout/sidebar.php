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
                        <a href="<?php echo base_url();?>admin/users/profile">
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
                        <a href="<?= base_url();?>admin/login/logout">
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
                                    <a href="<?= base_url();?>admin/dashboard">
                                        <i class="flaticon-dashboard"></i> <span><?php echo $this->lang->line("dashboard")?></span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="<?php echo $page == "resellers" ?'active' :'';?>">
                                    <a href="<?= base_url();?>admin/resellers/request">
                                        <i class="flaticon-dashboard"></i> <span><?php echo $this->lang->line("resellers_req")?></span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="<?php echo $page == "projects" ?'active' :'';?>">
                                    <a href="<?= base_url();?>admin/project">
                                        <i class="flaticon-task"></i> <span><?php echo $this->lang->line("projects")?></span>
                                    </a>
                                </li>
                            </ul>  
                            <ul class="acc-menu">
                                <li class="<?php echo $page == "updateRequest" ?'active' :'';?>">
                                    <a href="<?= base_url();?>admin/updateRequest">
                                        <i class="flaticon-task"></i> <span><?php echo $this->lang->line("space_update_requests")?></span>
                                    </a>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "users" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-user-2"></i> <span><?php echo $this->lang->line("users")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('admin/users') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("user_list")?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('admin/users/create') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("create_user")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="<?php echo $page == "subscriptions" ?'active' :'';?>">
                                    <a href="<?= base_url('admin/plan/subscription_details') ?>">
                                        <i class="flaticon-task"></i> <span><?php echo $this->lang->line("plan_subscription_details")?></span>
                                    </a>
                                </li>
                            </ul> 
                            <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "plan" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-planning"></i> <span><?php echo $this->lang->line("plans")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('admin/plan') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("plan_list")?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('admin/plan/add') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("create_plan")?>
                                                </a>
                                            </li>
                                        </ul>
                                </li>
                            </ul>
                             <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "queries" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-help"></i> <span><?php echo $this->lang->line("help")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                        <li>
                                            <a href="<?= base_url('admin/contacts') ?>">
                                                <i class="circle"></i> <?php echo $this->lang->line("customer_queries")?>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <ul class="acc-menu">
                                <li class="hasChild <?php echo $page == "settings" ?'open active' :'';?>">
                                    <a href="javascript:void(0)">
                                        <i class="flaticon-api"></i> <span><?php echo $this->lang->line("settings")?></span>
                                    </a>
                                    <ul class="acc-menu">
                                        <li>
                                                <a href="<?= base_url('admin/settings') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("site_setting")?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="<?= base_url('admin/settings/email_templates') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("email_templates")?>
                                                </a>
                                        </li>
                                        <li>
                                                <a href="<?= base_url('admin/settings/page_templates') ?>">
                                                    <i class="circle"></i> <?php echo $this->lang->line("page_templates")?>
                                                </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>

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




