<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>


<?php if($isplanactive == 'active'){ ?>

<div class="container-fluid">
   <div class="row mr-0">
      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-6">
                  <h3 class="filter-content-title"><?= ucfirst($plan_data->name) ?> </h3>
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
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/active-user.png"> </div>
                        <div class="tile-heading"><span>FTP</span></div>
                        <div class="tile-body">
                           <span class="tiledetail">
                              <a href="<?= base_url();?>admin/users"> 
                              <?= $this->lang->line("ftp_storage_allocated")?> 
                              </a>
                           </span>
                           <br>
                           <span class="tilecount">
                              <?= $ftp_storage ?>
                           </span>
                          
                        </div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dproject">
                        <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/server-projects.png"> </div>
                        <div class="tile-heading"><span>DB</span></div>
                        <div class="tile-body">
                           <span class="tiledetail">
                              <a href="<?= base_url();  ?>client/projects"> 
                              <?= $this->lang->line("db_storage_allocated")?>
                              </a> 
                           </span>
                           <br>
                           <span class="tilecount">
                              <?= $db_storage ?>
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
                           <span class="tiledetail"> 
                              <a  onclick="getBaseUrl('coursemodules')"  href="<?= base_url();  ?>client/Backup"> 
                              <?= $this->lang->line("bkp_list")?>
                              </a> 
                           </span>
                           <br>
                           <span class="tilecount">
                           0
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
                           <span class="tiledetail">
                              <a onclick="getBaseUrl('questions')" href=""> 
                              <?= $this->lang->line("sql_list")?>
                              </a>
                           </span>
                           <br>
                           <span class="tilecount">
                           0
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
                           44
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
                           ff
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

      <div class="row">
         <div class="col-md-6">
            <table id="usrtable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
                  <thead>
                     
                     <th colspan="2"> <?= $this->lang->line("Plan Details")?> </th>
                  </thead>
                  <tbody>
                     <tr>
                        
                        <td><?= $this->lang->line("Plan Name")?></td>
                        <td><?=$plandata['name'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Plan Description")?></td>
                        <td><?=$plandata['description'];?></td>
                     </tr>

                     <tr>
                       
                        <td><?= $this->lang->line("Plan Type")?></td>
                        <td><?=$subscription_details['plan_type'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Start date")?></td>
                        <td><?= displayDate($subscription_details['start_date'], false);?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Expiry date")?></td>
                        <td><?= displayDate($subscription_details['expiry_date'], false); ?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Plan Price per month")?></td>
                        <td><?=$plandata['price'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("FTP space limit")?></td>
                        <td><?=$plandata['ftp_space_limit'].' '.$plandata['ftp_unit'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("SQL space limit")?></td>
                        <td><?=$plandata['sql_space_limit'].' '.$plandata['db_unit'];?></td>
                     </tr>

                  </tbody>
            </table>
			<div class="row">
         <div class="col-sm-12">
               <div class="text-center">
                  <a class="btn btn-primary" href="<?php echo base_url();?>client/dashboard/view_plan">
                     <?= $this->lang->line("Upgrade_plan")?>
                  </a> 
               </div>
         </div>
      </div>  
         </div>

         <div class="col-md-6">
            <table id="usrtable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
                  <thead>
                     
                     <th colspan="2"> <?= $this->lang->line("Payment Details")?> </th>
                  </thead>
                  <tbody>
                     <tr>
                        
                        <td><?= $this->lang->line("Payer Email")?></td>
                        <td><?=$payment_info['payer_email'];?></td>
                     </tr>
                     <tr>
                        
                        <td><?= $this->lang->line("Payer id")?></td>
                        <td><?=$payment_info['payer_id'];?></td>
                     </tr>
                     <tr>
                        <td><?= $this->lang->line("First Name")?></td>
                        <td><?=$payment_info['first_name'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Last Name")?></td>
                        <td><?=$payment_info['last_name'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Address")?></td>
                        <td>
                        <?= $payment_info['address_name'].', '.$payment_info['address_street'].', '.$payment_info['address_city'].', '.$payment_info['address_state'].', '.$payment_info['address_country_code'].', '.$payment_info['address_zip'];
                        ?>
                        </td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Taxation id")?></td>
                        <td><?=$payment_info['txn_id'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Currency")?></td>
                        <td><?=$payment_info['mc_currency'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Ammount paid")?></td>
                        <td><?=$payment_info['payment_gross'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Payment Status")?></td>
                        <td><?=$payment_info['payment_status'];?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Payment Date")?></td>
                        <td><?= displayDate($payment_info['payment_date']);?></td>
                     </tr>

                     <tr>
                        
                        <td><?= $this->lang->line("Plan Name")?></td>
                       
                          <td><?=$plandata['name'];?></td>
                     </tr>

                     <tr>
                        <td><?= $this->lang->line("Plan Type")?></td>
                       <td><?=$subscription_details['plan_type'];?></td>
                     </tr>


                  </tbody>
            </table>
         </div>
      </div>

	 

   </div>
</div>
<script type="text/javascript">
   $(document).ready(function(){
               $.ajax({
                       url:"<?php echo base_url();?>client/dashboard/getsizes",
                       type:"get",
                       dataType: 'json',
                       success:function(data){
                                       $("#ftpspace").html(data.ftpsize+" <br> "+data.ftpmsg);
                                       $("#sqlspace").html(data.sqlsize+" <br> "+data.dbmsg);    
                                 }
                            });

                     });
</script>


<?php }else{ ?>


<div class="container-fluid">
   <div class="row mr-0">
      
      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-12">
                  <h3 class="filter-content-title text-center"> <?= $this->lang->line("Your Plan has been Expired")?> </h3>
               </div>
               
            </div>
         </div>
      </div>

      <div class="row">
         <div class="col-sm-12">
               <div class="text-center">
                  <a class="btn btn-primary" href="<?php echo base_url();?>client/dashboard/view_plan">
                     <?= $this->lang->line("Buy a new plan")?>
                  </a> 
               </div>
         </div>
      </div>  


   </div>
</div>





<?php } ?>




<?php $this->load->view("client/layout/footer_new");?>

