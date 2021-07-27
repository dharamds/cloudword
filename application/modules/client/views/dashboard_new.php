<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">
      <div class="row">                    
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-6">
                  <h3 class="filter-content-title"><?= $this->lang->line("dashboard")?></h3>
               </div>
               <div class="flex-col-md-6 text-right">
               <a class="btn btn-primary" id="request_update" href="javascript:void(0);">
			   <?= $this->lang->line("Request_For_Extra_Space");?>
                     
                  </a>  
               </div>
            </div>
         </div>        
      </div>

      <?php if (isset($this->session->success_msg) && !empty($this->session->success_msg) ): ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="alert alert-success" role="alert">
                 <?= $this->session->success_msg ?>
               </div>
            </div>        
         </div>
      <?php endif ?>

      <?php if($isplanactive == 'expired'){ ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="filter-container flex-row">
                  <div class="flex-col-md-12">
                     <h3 class="filter-content-title" style="color:red;"><?= $this->lang->line("Your Plan has been Expired");?>, <a href="<?php echo base_url();?>client/dashboard/view_plan"> <u><?= $this->lang->line("Click here to get a new plan");?></u> </a></h3>
                  </div>
                 
               </div>
            </div>        
         </div>
      <?php }else if($this->session->userdata("cash_advance_flag") == 1){ ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="filter-container flex-row">
                  <div class="flex-col-md-12">
                     <h3 class="filter-content-title" style="color:orange;"><?= $this->lang->line("free_trial_msg")."  ".displayDate($this->session->userdata("cash_advance_expiry_date"),false);?>, <a href="<?php echo base_url();?>client/dashboard/view_plan"> <u><?= $this->lang->line("Click here to get a new plan");?></u> </a></h3>
                  </div>
                 
               </div>
            </div>        
         </div>
      <?php } ?>

      <?php if($isplanactive == 'noplansubcribed'){ ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="filter-container flex-row">
                  <div class="flex-col-md-12">
                     <h3 class="filter-content-title" style="color:red;"><?= $this->lang->line("You dont have any plan");?>, <a href="<?php echo base_url();?>client/dashboard/view_plan"> <u><?= $this->lang->line("Click here to get a new plan");?></u> </a></h3>
                  </div>
                 
               </div>
            </div>        
         </div>
      <?php } ?>


      <?php if( $this->session->flashdata('notallow') == true){ ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="filter-container flex-row">
                  <div class="flex-col-md-12">
                     <h3 class="filter-content-title" style="color:red;"> <?= $this->lang->line("cant_access")?> </h3>
                  </div>
                 
               </div>
            </div>        
         </div>
      <?php } ?>


      <div class="row">
         <div class="col-sm-12">
            <div class="boxbg" style="padding: 15px 25px 25px;">

               <div class="row">
                  <?php if($this->session->userdata("role_type") == "reseller") { ?>
                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dactive-user">
                     <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/active-user.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("active_user")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $user_count?>
                           </span>
                           <span class="tiledetail">
                           <a href="<?= base_url();?>client/users"> 
                           <?= $this->lang->line("view_all_user")?>
                           </a>
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div>
                        <div class="wave"></div> -->
                     </div>
                  </div>
                  <?php } ?>

                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dproject">
                     <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/server-projects.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("projects")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $project_count?>
                           </span>
                           <span class="tiledetail">
                              <?php if($ifplanexpirecss && in_array('7', $allowmodule)){ ?>
                                 <a href="<?= base_url();  ?>client/project"> 
                              <?php } ?> 
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
                     <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/sql-backup.png"> </div>
                        <div class="tile-heading"><span><?= $this->lang->line("bkp_ftp")?> </span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $ftp_count?>
                           </span>
                           <span class="tiledetail"> 
                           <!-- <a  onclick="getBaseUrl('coursemodules')"  href="javascript:void(0)">  -->
                           <?= $this->lang->line("bkp_list")?>
                           <!-- </a>  -->
                           </span>
                        </div>
                        <!-- <div class="tile-footer">
                        </div> -->
                     </div>
                  </div>
               

                     <!-- </div>

                     <div class="row">
                  -->

                  <div class="col-md-4">
                     <div class="info-tile tile-ocean-green__ dbackupsql">
                     <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/sql-restore.png"> </div>
                        <div class="tile-heading"><span> <?= $this->lang->line("bkp_sql")?></span></div>
                        <div class="tile-body">
                           <span class="tilecount">
                           <?= $sql_count?>
                           </span>
                           <span class="tiledetail">
                           <!-- <a onclick="getBaseUrl('questions')" href="javascript:void(0)">  -->
                        <?= $this->lang->line("sql_list")?>
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
                     <div class="tile-icon"> <img src="<?=base_url()?>public/public/assets/img/ftp-restore.png"> </div>
                        <div class="tile-heading">
                           <span class="tilecount"> 
                           <?= $this->lang->line("ftp_space_used")?>
                           </span>
                        </div>
                        <div class="tile-body pl-0">
                           <span id="ftpspace">
                           <?= $this->general->convert_size($ftp_used)?>/<?= $this->general->convert_size($ftp_unused)?>
                           </span>
                           <span class="tiledetail">
                           <!-- <a  onclick="getBaseUrl('lessons')"  href=""> 
                           </a>  -->
                           </span>
                           <span class="request_btn_ftp">
                              
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
                           <span><?= $this->lang->line("dbspace_used")?></span>
                        </div>
                        <div class="tile-body pl-0">
                           <span id="sqlspace" class="tilecount">
                        <?= $this->general->convert_size($db_used)?>/<?= $this->general->convert_size($db_unused)?>
                           </span>
                           <span class="tiledetail">
                           <!-- <a  onclick="getBaseUrl('lessons')"  href=""> 
                           </a>  -->
                           </span>
                           <span class="request_btn_db">
                              
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

<div class="modal fade" id="req-Modal" tabindex="-1" role="dialog">
   <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?= $this->lang->line("update_status")?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <div class="row">
                  <div class="col-sm-12">
                     <div class="card">                        
                        <div class="card-block">
                           <form id="reqform" method="post">
                              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                              <?php  if($role_id == 3){  ?>
                              <div class="form-group row">
                                 <label class="col-sm-3 col-form-label"> <?php echo $this->lang->line("no_of_customers")?> </label>
                                 <div class="col-sm-6">
                                    <input type="number" name="no_of_users" value="" id="no_of_users" class="form-control">
                                    <span style="color: red;" class="errmsg"></span>
                                 </div>
                              </div>
                              <?php } ?>

                              <div class="form-group row">
                                 <label class="col-sm-3 col-form-label"><?= $this->lang->line("ftp_space")?></label>
                                 <div class="col-sm-6">
                                    <input type="number" name="ftp_space"  id="ftp_space" class="form-control">
                                    <span style="color: red;" class="errmsg"></span>
                                 </div>
                                 <div class="col-sm-3">
                                    <select id="ftp_space_unit" name="ftp_space_unit" class="form-control">
                                       <option value="b" selected="">Bytes</option>
                                       <option value="kb">KB</option>
                                       <option value="mb">MB</option>
                                       <option value="gb">GB</option>
                                       <option value="tb">TB</option>
                                    </select>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label class="col-sm-3 col-form-label"><?= $this->lang->line("db_space")?></label>
                                 <div class="col-sm-6">
                                    <input type="number" name="db_space"  id="db_space" class="form-control">
                                    <span style="color: red;" class="errmsg"></span>
                                 </div>
                                 <div class="col-sm-3">
                                    <select id="db_space_unit" name="db_space_unit" class="form-control">
                                       <option value="b" selected="">Bytes</option>
                                       <option value="kb">KB</option>
                                       <option value="mb">MB</option>
                                       <option value="gb">GB</option>
                                       <option value="tb">TB</option>
                                    </select>
                                 </div>
                              </div>
                              
                              <div class="form-group row">
                                 <div class="input-group">
                                    <div class="col-sm-6" id="reqerrormsg" style="color: red;"></div>
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
   $(document).ready(function(){    

     $("#reqform").submit(function (e) {
         e.preventDefault()
         var formdata = new FormData(this)
         var ftp_space = $('#ftp_space').val(); 
         var db_space = $('#db_space').val(); 
         
        if(db_space == '' &&  ftp_space =="") {
            $('.errmsg').text("Please enter db space or Ftp space");         
        }else{

           $.ajax({
                 url:"<?php echo base_url();?>client/updateRequest/save_update_request",
                 type:"POST",
                 data : formdata,
                 processData : false,
                 contentType : false,
                 dataType: 'json',
                 beforeSend:function(){
                       $("#cover-spin").show()
                   },
                 dataType: 'json',
                 success:function(data){
                    $("#cover-spin").hide();
  
                    if (data.status == 'success') {
                       swal(data.msg, {
                          title: "<?= $this->lang->line("great") ?>",
                          type: "success",
                          timer: 3000
                       }).then(() => {                        
                          $("#req-Modal").modal("hide");
                       });
                    }
                    else{
                          $("#req-Modal").modal("hide");
                          swal(data.msg, {
                             title: "<?= $this->lang->line("oops") ?>",
                             icon: "error",
                             timer: 3000
                          })
  
                    }   
                 }
            });
        }

        
        
      })


      $(document).on('click','#request_update',function () {
         $("#req-Modal").modal("show");
      })

   });

   //
</script>


<?php $this->load->view("client/layout/footer_new");?>