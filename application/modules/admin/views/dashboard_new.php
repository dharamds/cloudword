<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">

      <div class="row">                    
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-6">
                  <h3 class="filter-content-title"><?= $this->lang->line("dashboard")?></h3>
               </div>
               <div class="flex-col-md-6 text-right">
                  <!-- <a class="btn btn-primary" href="<?php //echo base_url();//?>admin/projects/create">
                     New Project
                  </a> --> 
               </div>
            </div>
         </div>        
      </div>

      <div class="row">
         <div class="col-sm-12">
            <div class="boxbg" style="padding: 15px 25px 25px;">

               <div class="row">
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dactive-user">
                     <div class="tile-icon"> <img src="../public/public/assets/img/active-user.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("active_user")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $user_count?>
                           </span>
                           <span class="tiledetail">
                           <a href="<?= base_url();?>admin/users"> 
                           <?= $this->lang->line("view_all_user")?>
                           </a>
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dproject">
                     <div class="tile-icon"> <img src="../public/public/assets/img/server-projects.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("projects")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $project_count?>
                           </span>
                           <span class="tiledetail">
                           <a    href="<?= base_url();  ?>admin/project"> 
                           <?= $this->lang->line("project_list")?>   
                           </a> 
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dbackupftp">
                     <div class="tile-icon"> <img src="../public/public/assets/img/sql-backup.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("bkp_ftp")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $ftp_count?>
                           </span>
                           <span class="tiledetail"> 
                           <!-- <a href="javascript:">  -->
                            <?php //= $this->lang->line("bkp_list")?>
                           <!-- </a>  -->
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div> -->
                     </div>
                  </div>
               </div>

               <div class="row mt-4">
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dbackupsql">
                        <!-- <div class="tile-icon"><i class="flaticon-discuss-issue"></i></div> -->
                        <div class="tile-icon"> <img src="../public/public/assets/img/sql-restore.png"> </div>
                        <div class="tile-heading"><span> <?= $this->lang->line("bkpsql")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $sql_count?>
                           </span>
                           <span class="tiledetail">
                           <!-- <a href="javascript:">  -->
                           <?php //= $this->lang->line("sql_list")?>
                           <!-- </a> -->
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dspaceftp">
                     <div class="tile-icon"> <img src="../public/public/assets/img/ftp-restore.png"> </div>
                        <div class="tile-heading">
                           <span> 
                           <?= $this->lang->line("ftp_space_used")?>
                           </span>
                        </div>
                        <div class="tile-body">
                           <span id="ftpspace" class="tilecount">
                           <?= $this->general->convert_size($ftp_used)?>

                           </span>
                           <span class="tiledetail">
                           <a  onclick="getBaseUrl('lessons')"  href=""> 
                           </a> 
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dspacesql">
                     <div class="tile-icon"> <img src="../public/public/assets/img/api-dashboard.png"> </div>
                        <div class="tile-heading">
                           <span><?= $this->lang->line("dbspace_used")?></span>
                        </div>
                        <div class="tile-body">
                           <span id="sqlspace" class="tilecount">
                              <?= $this->general->convert_size($db_used)?>
                           </span>
                           <span class="tiledetail">
                           <a  onclick="getBaseUrl('lessons')"  href=""> 
                           </a> 
                           </span>
                        </div>
                        <!-- <div class="tile-footer">                           
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
               </div>

            </div>
         </div>
      </div>

   </div>
</div>

<script type="text/javascript">
   $(document).ready(function(){
               

                     });
</script>


<?php $this->load->view("admin/layout/footer_new");?>