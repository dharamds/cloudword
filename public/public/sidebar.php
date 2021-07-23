<div id="wrapper">
    <div id="layout-static">
        <div class="static-sidebar-wrapper sidebar-default">
            <div class="static-sidebar">
                <div class="sidebar">
                    <div class="widget stay-on-collapse sidebar-for-media" id="widget-sidebar">
                        <nav role="navigation" class="widget-body">
                            <ul class="acc-menu">
                                <li class="hasChild open">
                                    <a href="<?php bs('/users/auth'); ?>">
                                        <i class="flaticon-speedometer"></i> <span><?php echo $this->lang->line('users_sidebar_label_dashboard'); ?></span>
                                    </a>
                                    
                                </li>
                            </ul>
                            <?php echo $sidebar_menus; ?>
                            <ul class="acc-menu">
                                <?php if ($this->ion_auth->is_admin()) { ?>
                                    <!-- <li>
                                        <a href="javascript:void(0)">
                                            <i class="flaticon-checklist"></i><span>Setup Menu</span><span
                                                class="badge badge-teal"></span>
                                        </a>
                                        <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('users/menus') ?>">
                                                    <i class="circle"></i> Manage Menus
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('users/menus/save_menu') ?>">
                                                    <i class="circle"></i> Add New Menu
                                                </a>
                                            </li>
                                        </ul>
                                    </li> -->
                               <li><a href="javascript:;"><i class="flaticon-mind"></i><span><?php echo $this->lang->line('users_sidebar_label_general_modules'); ?></span></a>
                                        <ul class="acc-menu">

                                            <!-- Category sidebar => -->
                                            <li><a href="javascript:;"> <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_manage_cat'); ?> 
                                                </a>
                                                <ul class="acc-menu">

                                                    <li>
                                                        <a href="<?= base_url('general_modules/category/') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_view_cat'); ?>  
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('general_modules/category/add') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_add_cat'); ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <!-- Tags sidebar => -->
                                            <li><a href="javascript:;"> <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_manage_tag'); ?>  </a>
                                                <ul class="acc-menu">
                                                    <li>
                                                        <a href="<?= base_url('general_modules/tags/') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_view_tag'); ?> 
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('general_modules/tags/add') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_add_tag'); ?> 
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <!-- Skills sidebar => -->
                                            <li><a href="javascript:;"> <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_manage_pages'); ?>  </a>
                                                <ul class="acc-menu">

                                                    <li>
                                                        <a href="<?= base_url('pages') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_view_pages'); ?> 
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('pages/add') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_add_page'); ?> 
                                                        </a>
                                                    </li>


                                                </ul>
                                            </li>
                                            <!-- Email Template sidebar =>  -->
                                            <li><a href="javascript:;"> <i class="ti ti-angle-right"></i> <?php echo $this->lang->line('users_sidebar_label_email_templates'); ?> </a>
                                                <ul class="acc-menu">
                                                    <li>
                                                        <a href="<?= base_url('email_templates/email_templates_list') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_view_templates'); ?> 
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('email_templates/add_email_templates') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_add_template'); ?> 
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li><a href="javascript:;"> <i class="ti ti-angle-right"></i> Certificate Template</a>
                                                <ul class="acc-menu">
                                                    <li>
                                                        <a href="<?= base_url('certificate_templates/certificate_templates_list') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_view_templates'); ?> 
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="<?= base_url('certificate_templates/add_certificate_templates') ?>">
                                                            <i class="ti ti-angle-right"></i><?php echo $this->lang->line('users_sidebar_label_add_template'); ?> 
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                            <?php /*        
                                    <li>
                                        <a href="javascript:;"><i class="ti ti-settings"></i><span>Setup</span></a>
                                        <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('site_config') ?>">
                                                    <i class="ti ti-angle-right"></i><span>General</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('site_config/Login_setup') ?>">
                                                    <i class="ti ti-angle-right"></i><span>Login</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                   
                                    <li>
                                        <a href="javascript:;"><i class="fa fa-wrench"></i><span>Social Login</span></a>
                                        <ul class="acc-menu">
                                            <li>
                                                <a href="<?= base_url('site_config/fb_config') ?>">
                                                    <i class="ti ti-facebook"></i> Facebook Config
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('site_config/twitter_config') ?>">
                                                    <i class="ti  ti-twitter"></i> Twitter Config
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('site_config/google_config') ?>">
                                                    <i class="ti  ti-google"></i> Google Config
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('site_config/insta_config') ?>">
                                                    <i class="ti ti-instagram"></i> Instagram Config
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= base_url('site_config/linkedin_config') ?>">
                                                    <i class="ti ti-linkedin"></i> Linkedin Config
                                                </a>
                                            </li>

                                        </ul>
                                    </li>
                                     */?>
                                   

                                <?php } ?>
                            </ul>
                        </nav>
                    </div>
                    <!-- <div class="sidebar-footer">
                        <h6>© 2019 LMS</h6>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- Sidebar Ends -->


        <div class="static-content-wrapper">
            <div class="static-content">



                <div class="page-content">
                    <ol class="breadcrumb">
                        <?php if (isset($breadcrumb)): foreach ($breadcrumb as $item): ?>
                                <li class="<?php echo $item['class']; ?>">
                                    <?php if ($item['link']): ?>
                                        <a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a>
                                    <?php else: ?>
                                        <span><?php echo $item['title']; ?></span>
                                    <?php endif; ?>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>


                        <?php
                        $string = $_SERVER['PHP_SELF'];
                        $split = explode('index.php/', $string, 2);
                        $param = $split[1];
                        if (isset($breadcrumb)) {
                            $i = 0;
                            foreach ($breadcrumb as $key => $val) {
                                if ($key == 1) {
                                    ?>
                                    <!-- <a class="faourite-btn" href="javascript:void(0)" id="makeFav" data-title="<?php //echo $val['title'] ?>"  data-param="<?php //echo $param ?>" ><span class="ti-star"></span></a> -->
                                        <?php
                                    }
                                    $i++;
                                }
                            }
                            ?>

                    </ol>

                    

                    <script>
                        $(document).ready(function () {
                            var title = $('#makeFav').data('title');
                            var param = $('#makeFav').data('param');
                            $.ajax({
                                url: "<?php echo bs('favorites/favorite/checkFavStatus'); ?>",
                                context: document.body,
                                type: 'post',
                                dataType:'json',
                                data: {'title' : title,'param' : param, '<?php echo $this->security->get_csrf_token_name()?>' : '<?php echo $this->security->get_csrf_hash();?>' },
                                success: function (result) {
                                    if (result.active) {
                                        $('#makeFav').addClass('active');
                                    } else {
                                        $('#makeFav').removeClass('active');
                                    }
                                }});

                            $(document).on("click", "#makeFav", function () {

                                $.ajax({
                                    url: "<?php echo bs(); ?>favorites/favorite/make_fav",
                                    type: 'post',
                                    data: {'title' : title,'param' : param, '<?php echo $this->security->get_csrf_token_name()?>' : '<?php echo $this->security->get_csrf_hash();?>' },
                                    dataType: 'json',
                                    success: function (data) {
                                        if (data.message == 'inserted') {
                                            $('#makeFav').addClass('active');
                                        } else {
                                            $('#makeFav').removeClass('active');
                                        }
                                    }
                                });
                            });
                        });
                
            /**
            *@getBaseUrl 
            *@ This function use to set url to select menu bar selected to set value in public\assets\js\application.js file
            *@ params urlname
            *@ return void()
            */            
                    function getBaseUrl(urlname) {
                        localStorage.setItem("childUrl",'<?php echo base_url();?>'+urlname);
                    }
                    $(document).ready(function(){
                        $('ul.acc-menu a').bind("contextmenu",function(e){
                            localStorage.setItem("childUrl", $(this).attr('href'));
                         });
                    });
                  
            /*END*/        
                  
                  
            </script>   
