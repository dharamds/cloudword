<?php $this->load->view("client/layout/header_new"); ?>
<?php $this->load->view("client/layout/sidebar"); ?>
<div class="container-fluid">
	<div class="row mr-0">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="<?= base_url('client/project')?>"><?=$this->lang->line("projects") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('client/project/manage_backup/'.base64_encode($project_data->project_id))?>"><?=$this->lang->line("backups") ?></a></li>
				<li class="breadcrumb-item active" aria-current="page"><a href="#"><?=$this->lang->line("new_connection") ?></a></li>
				
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
				<li role="presentation" class="<?=$fun == "ftp" ? 'active' : '' ?>"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?=$this->lang->line("ftp_backup") ?></a></li>
				<li role="presentation" class="<?=$fun == "db" ? 'active' : '' ?>"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=$this->lang->line("db_backup") ?></a></li>
			</ul>
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane <?=$fun == "ftp" ? 'active' : '' ?>" id="home">
					<div data-widget-role="role1">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default panel-grid" style="visibility: visible;"> 
									<div class="panel-body no-padding p-0">
										<div id="memListTable_wrapper" class="dataTables_wrapper form-inline no-footer">
											<div class="row">

												<div class="col-lg-12"><h3><?=$this->lang->line("add_newftp") ?></h3></div>
											</div>
											<div style="margin-top:30px">
												<form class="addftp" id="addftpform" enctype="multipart/form-data">
													<input type="hidden" name="project_id" id="project_id" value="<?=$project_data->project_id?>">
													<div class="row">
														<label for="f_caption" class="col-lg-2 col-form-label"><?=$this->lang->line("caption") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" id="f_caption" name="caption" placeholder="<?=$this->lang->line("caption") ?>" required>
														</div>
													</div>
													<div class="row">
														<label for="f_protocol_type" class="col-lg-2 col-form-label"><?=$this->lang->line("protocol_type") ?></label>
														<div class="col-lg-4">
															<select class="form-control" id="f_protocol_type" name="protocol_type" onchange="addport(this.value)">
																<option value=""><?=$this->lang->line("select_protocol") ?></option>
																<option value="ftp">FTP</option>
																<option value="sftp">SFTP</option>
															</select>
														</div>
														<label for="f_port_no" class="col-lg-1 col-form-label"><?=$this->lang->line("port_no") ?></label>
														<div class="col-lg-1">
															<input type="text" class="form-control" name="port_no" id="f_port_no" placeholder="<?=$this->lang->line("port_no") ?>" required>
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("please_enter_custom_port") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="f_hostname" class="col-lg-2 col-form-label"><?=$this->lang->line("hostname") ?></label>
														<div class="col-lg-6">
															<input type="text" class="form-control" name="hostname" id="f_hostname" placeholder="<?=$this->lang->line("hostname") ?>" required>
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("white_list_CW_server_IP") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="f_username" class="col-lg-2 col-form-label"><?=$this->lang->line("username") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="username" id="f_username" placeholder="<?=$this->lang->line("username") ?>" required>
														</div>
													</div>

													<div class="row">
														<label for="f_password" class="col-lg-2 col-form-label"><?=$this->lang->line("password") ?></label>
														<div class="col-lg-6">
															<input type="password" class="form-control" name="password" id="f_password" placeholder="******" required>
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("skip_password") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													
													<div class="row">
														<label for="rsa_file" class="col-lg-2 col-form-label"><?=$this->lang->line("rsa_file") ?></label>
														<div class="col-lg-6">
															<input type="file" class="form-control" name="rsa_file" id="rsa_file" >
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("select_file_format") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="f_remote_folder" class="col-lg-2 col-form-label"><?=$this->lang->line("remote_folder") ?></label>
														<div class="col-lg-6">
															<input type="text" class="form-control" name="remote_folder" id="f_remote_folder" placeholder="/public_html">
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("select_backup_directory") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="f_exclude_folder" class="col-lg-2 col-form-label"><?=$this->lang->line("exclude_folder") ?></label>
														<div class="col-lg-6">
															<input type="text" class="form-control" name="exclude_dir" id="f_exclude_folder" placeholder="log, cache" >
															<span><?=$this->lang->line("exclude_folder_text") ?></span>
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("select_excluded_directory") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>	
													</div>
													<div class="row">
														<div class="col-lg-9 text-right">
															<a href="<?=base_url('client/project/manage_backup/'.$project_data->project_id) ?> " class="btn btn-secondary" ><?=$this->lang->line("cancel") ?></a>
															<button type="submit" class="btn btn-primary"><?=$this->lang->line("submit") ?></button>
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
				<div role="tabpanel" class="tab-pane <?=$fun == "db" ? 'active' : '' ?>" id="profile">
					<div data-widget-role="role1">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default panel-grid" style="visibility: visible;"> 

									<div class="panel-body no-padding p-0">
										<div id="memListTable_wrapper" class="dataTables_wrapper form-inline no-footer">
											<div class="row">
												<div class="col-lg-12"><h3><?=$this->lang->line("add_new_db") ?></h3></div>
											</div>
											<div style="margin-top:30px">
												<form class="addftp" id="adddbform">
													<input type="hidden" name="project_id" value="<?=$project_data->project_id ?>">
													<div class="row">
														<label for="d_caption" class="col-lg-2 col-form-label"><?=$this->lang->line("caption") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="caption" id="d_caption" placeholder="<?=$this->lang->line("caption") ?>" required>
														</div>
													</div>

													<div class="row">
														<label for="d_hostname" class="col-lg-2 col-form-label"><?=$this->lang->line("hostname") ?></label>
														<div class="col-lg-3">

															<input type="text" class="form-control" name="hostname" id="d_hostname" placeholder="<?=$this->lang->line("hostname") ?>" required>
														</div>
<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("white_list_CW_server_IP") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
														<label for="d_port_no" class="col-lg-1 col-form-label"><?=$this->lang->line("port_no") ?></label>
														<div class="col-lg-1">
															<input type="text" class="form-control" name="port_no" id="d_port_no" placeholder="<?=$this->lang->line("port_no") ?>">
														</div>
														<div class="col-lg-1">
<span class="tooltip" title="<?=$this->lang->line("please_enter_custom_port_db") ?>" style="opacity: 1;"><img src="<?php echo base_url('assets/images/icons8-help-50.png');?>"> </span>
														</div>
													</div>
													<div class="row">
														<label for="d_username" class="col-lg-2 col-form-label"><?=$this->lang->line("username") ?></label>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="username" id="d_username" placeholder="<?=$this->lang->line("username") ?>" required>
														</div>
													</div>
													<div class="row">
														<label for="d_password" class="col-lg-2 col-form-label"><?=$this->lang->line("password") ?></label>
														<div class="col-lg-7">
															<input type="password" class="form-control" name="password" id="d_password" placeholder="******">
														</div>
													</div>
													<div class="row">
														<div class="col-lg-9 text-right">
															<a href="<?=base_url('client/project/manage_backup/' . $project_data->project_id) ?> " class="btn btn-secondary" ><?=$this->lang->line("cancel") ?></a>
															<button type="submit" class="btn btn-primary"><?=$this->lang->line("submit") ?></button>
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
					$("#addftpform").on('submit', function(e){
				        e.preventDefault();
				    	$.ajax({
				           url:"<?php echo base_url(); ?>client/project/save_ftp",
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
											  buttons: true,
											  timer: 3000
											});

				                            location.replace("<?=base_url('client/project/manage_backup/' . $project_data->project_id) ?>");
				                        }else{
				                        	$("#passvererrormsg").html(data.msg);
				                        	swal(data.msg, {
				                            	title: "<?= $this->lang->line("oops")?>",
				                              type: "error",
											  buttons: true,
											  timer: 3000
											});
				                        }
				                        $("#cover-spin").hide()     
				                     }
				                });
				   	 });

					$("#adddbform").on('submit', function(e){
				        e.preventDefault();
				 
				        	
				    	$.ajax({
				          url:"<?php echo base_url(); ?>client/project/save_db",
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
                                            buttons: true,
                                            timer: 3000
                                          }).then(() => {
                                          location.replace("<?=base_url('client/project/manage_backup/' . $project_data->project_id) ?>");
                                   		 });
				                           
				                        }else{
				                        	swal(data.msg, {
				                            	title: "<?= $this->lang->line("oops")?>",
				                              type: "error",
											  buttons: true,
											  timer: 3000
											});
				                        }
				                        $("#cover-spin").hide()     
				                     }
				                });
				    	
				   	 });
					function addport(vv){
						if(vv !=""){
							if(vv == "ftp"){
								$("#f_port_no").val(21);
							}else if(vv == "sftp"){
								$("#f_port_no").val(22);
							}
						}
					}


</script>

<?php $this->load->view("client/layout/footer_new"); ?>