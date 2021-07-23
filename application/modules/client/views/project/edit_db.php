<?php $this->load->view("client/layout/header_new"); ?>
<?php $this->load->view("client/layout/sidebar"); ?>
<div class="container-fluid">
	<div class="row mr-0">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url('client/project')?>"><?=$this->lang->line("projects") ?></a></li>
          		<li class="breadcrumb-item"><a href="<?= base_url('client/project/'.$project_data->project_id)?>"><?=$this->lang->line("backups") ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"><?=$this->lang->line("edit_database") ?></a></li>
				
			</ol>
		</nav>
		<div id="cover-spin"></div>
		<div class="row">
			<div class="col-md-12">
				<div class="filter-container flex-row">
					<div class="flex-col-md-6">
						<h3 class="filter-content-title"><?=$this->encryption->decrypt($project_data->project_name) ?></h3>
					</div>
					<div class="flex-col-md-6 text-right"> </div>
				</div>
			</div>
		</div>
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">

				<li role="presentation" class="active"><a href="#db_edit" aria-controls="db_edit" role="tab" data-toggle="tab"><?=$this->lang->line("db_backup") ?></a></li>

			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				
				<div role="tabpanel" class="tab-pane active" id="db_edit">
					<div data-widget-role="role1">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default panel-grid" style="visibility: visible;"> 

									<div class="panel-body no-padding p-0">
										<div id="memListTable_wrapper" class="dataTables_wrapper form-inline no-footer">
											<div class="row">
												<div class="col-lg-12"><h3><?=$this->lang->line("edit_database") ?></h3></div>
											</div>
											<div style="margin-top:30px">
												<form class="addftp" id="editdbform">

													<input type="hidden" name="project_id" value="<?=$project_data->project_id ?>">
													<input type="hidden" name="mysql_id" value="<?= $db_data->mysql_id ?>">

													<div class="row">
														<label for="d_caption" class="col-lg-2 col-form-label"><?=$this->lang->line("caption") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="caption" id="d_caption" placeholder="<?=$this->lang->line("caption") ?>" value="<?= $this->encryption->decrypt($db_data->caption) ?>" required>
														</div>
													</div>

													<div class="row">
														<label for="d_hostname" class="col-lg-2 col-form-label"><?=$this->lang->line("hostname") ?></label>
														<div class="col-lg-3">

															<input type="text" class="form-control" name="hostname" id="d_hostname" placeholder="<?=$this->lang->line("hostname") ?>" value="<?= $this->encryption->decrypt($db_data->mhostname) ?>" required>
														</div>
<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("white_list_CW_server_IP") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span></div>
														<label for="d_port_no" class="col-lg-1 col-form-label"><?=$this->lang->line("port_no") ?></label>
														<div class="col-lg-1">
															<input type="text" class="form-control" name="port_no" id="d_port_no" placeholder="<?=$this->lang->line("port_no") ?>" value="<?= $db_data->port_no ?>">
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("please_enter_custom_port_db") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="d_username" class="col-lg-2 col-form-label"><?=$this->lang->line("username") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="username" id="d_username" placeholder="<?=$this->lang->line("username") ?>" value="<?= $this->encryption->decrypt($db_data->musername) ?>" required>
														</div>
													</div>
													<div class="row">
														<label for="d_password" class="col-lg-2 col-form-label"><?=$this->lang->line("password") ?></label>
														<div class="col-lg-7">
															<input type="password" class="form-control" name="password" id="d_password" placeholder="******" value="" >
														</div>
													</div>
													<div class="row">
														<div class="col-lg-9 text-right">
															<a href="<?= base_url('client/project/manage_backup/' . $project_data->project_id) ?> " class="btn btn-secondary" >Cancel</a>
															<button type="submit" class="btn btn-primary">Submit</button>
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
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	

	$("#editdbform").on('submit', function(e){
        
        e.preventDefault();
 
    	$.ajax({
			url:"<?php echo base_url(); ?>client/project/update_db",
			type:"post",
			beforeSend:function(){
				$("#cover-spin").show()
			},
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData:false,
            success:function(data){
                if(data.status == "success"){
                     swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
    											 location.replace("<?=base_url('client/project/manage_backup/' . $project_data->project_id) ?>");
											});
                   
                }else{
                	swal(data.msg, {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
                }
                $("#cover-spin").hide()     
             }
        });
    	
   	 });

</script>

<?php $this->load->view("client/layout/footer_new"); ?>