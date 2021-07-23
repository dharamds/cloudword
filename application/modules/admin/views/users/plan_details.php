<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">
      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-6">
                  <h3 class="filter-content-title"><?= ucfirst($plan_data->name) ?> </h3>
               </div>
               <div class="flex-col-6 text-right">
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
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/active-user.png"> </div>
                        <div class="tile-heading"><span>FTP</span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $ftp_storage ?>
                           </span>
                           <span class="tiledetail">
                           <a href="<?= base_url();?>admin/users"> 
                           <?= $this->lang->line("ftp_storage_allocated")?> 
                           </a>
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dproject">
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/server-projects.png"> </div>
                        <div class="tile-heading"><span>DB</span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $db_storage ?>
                           </span>
                           <span class="tiledetail">
                           <a    href="<?= base_url();  ?>client/projects"> 
                             <?= $this->lang->line("db_storage_allocated")?>
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
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/sql-backup.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("bkp_ftp")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           0
                           </span>
                           <span class="tiledetail"> 
                           <a  onclick="getBaseUrl('coursemodules')"  href="<?= base_url();  ?>client/Backup"> 
                           <?= $this->lang->line("bkp_list")?>
                           </a> 
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                           </div> -->
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dbackupsql">
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/sql-restore.png"> </div>
                        <div class="tile-heading"><span> <?= $this->lang->line("bkpsql")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           0
                           </span>
                           <span class="tiledetail">
                           <a onclick="getBaseUrl('questions')" href=""> 
                           <?= $this->lang->line("sql_list")?>
                           </a>
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                           </div>
                           <div class="wave"></div> -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dspaceftp">
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/ftp-restore.png"> </div>
                        <div class="tile-heading">
                           <span class="tilecount"> 
                           
                           <?= $this->lang->line("ftp_space_used")?>
                           </span>
                        </div>
                        <div class="tile-body pl-0">
                           <span id="ftpspace">
                           <?= $storage_running["ftpsize"]." - ".$storage_running["ftpmsg"]; ?>
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
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/api-dashboard.png"> </div>
                        <div class="tile-heading">
                           <span><?= $this->lang->line("db_storage_allocated")?></span>
                        </div>
                        <div class="tile-body pl-0">
                           <span id="sqlspace" class="tilecount">
                            <?= $storage_running["sqlsize"]." - ".$storage_running["dbmsg"]; ?>
                           </span>
                           <span class="tiledetail">
                           <a  onclick="getBaseUrl('lessons')"  href=""> 
                           </a> 
                           </span>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm-6">
            <h2 align="center">Modules</h2>
            <table id="usrtable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
               <thead>
                  <th>
                     <?= $this->lang->line("sr_no");?>
                  </th>
                   <th>
                     <?= $this->lang->line("modules");?>
                  </th>
               </thead>
               <tbody>
                  <?php 
                  $cnt = 1;

                  foreach ($modules as $md) {
                     ?>
                     <tr>
                        <td><?= $cnt ?></td>
                        <td><?= $md->module_name ?></td>
                     </tr>
                     <?php
                     $cnt++;
                  }
                  ?>
               </tbody>
            </table>
         </div>
         <div class="col-sm-6">

         </div>
      </div>
   </div>
</div>
<?php $this->load->view("admin/layout/footer_new");?>