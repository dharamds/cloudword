<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<style type="text/css">
	#cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
	from {-webkit-transform:rotate(0deg);}
	to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
	from {transform:rotate(0deg);}
	to {transform:rotate(360deg);}
}

#cover-spin::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:black;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}
.btn + .btn{ margin-left: 3px; padding: 5px 6px;}

	.checksystem{}
	/*.checksystem .modal-header{background: #fff;}*/
	.checksystem .modal-header .close{background: #d74545; width: 34px; height: 34px; background: #d74545; opacity: 1;
	border-radius: 50%; top: -10px; right: -10px; line-height: 10px; padding-top: 0px; font-size: 19px;}
	.checksystem .modal-header .close span{line-height: 4px;}
	.checksystem .modal-body .systemurl{border: 1px solid #eee; padding:10px 15px; border-radius: 30px; margin-top: 10px;}
	.checksystem .modal-body .systemstatus .btn.btn-danger{border-radius: 32px;  height: 50px; min-width: 50px;}
	.checksystem .modal-body .systemstatus .btn.btn-success{border-radius: 32px;  height: 50px; min-width: 50px;}
	.checksystem .modal-body .systemstatus .btn.btn-lg{padding: 6px 20px;}

</style>
<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("project_list") ?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
					<a class="btn btn-primary" href="<?php echo base_url();?>admin/projects/create">
						<?= $this->lang->line("new_project") ?>
					</a> 
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<!-- <div class="panel-heading panel-button">
						<div class="flex-row">
							<div class="flex-col-auto">
								
							</div>
						</div>
					</div> -->
					<div class="panel-body no-padding p-0">
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<!-- <th>
										<label class="checkbox-tel"><input type="checkbox" class="select_all"></label>
									</th> -->
									<th style="width:5%">#</th>
									<th style="width:10%"><?= $this->lang->line("name")?>  </th>
									<th style="width:10%"><?= $this->lang->line("project_url")?>    </th>
									<th style="width:10%"><?= $this->lang->line("folder")?>    </th>
									<th style="width:5%"><?= $this->lang->line("status")?> </th>
									<th style="width:10%"><?= $this->lang->line("created")?> </th>
									<th style="width:10%"><?= $this->lang->line("project_size")?> </th>
									<th style="width:50%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if($project_count > 0){
									$srcnt = 0;
									foreach ($projects as $proj){				
										?>
										<tr>
											<!-- <td><input type="checkbox" name="project_ids[]" id="project_id_<?= $proj->project_id ?>" value="<?= $proj->project_id ?>"></td> -->
											<td><?= ++$srcnt; ?></td>
											<td><?= $proj->project_name?></td>
											<td><?= $proj->projecturl?></td>
											<td><?= $proj->folder_name?></td>
											<td><?= $proj->status?></td>
											<td><?= $proj->added_date?></td></td>
											<td><?php	
										$currentFile = "./projects/".$proj->folder_name."/";
        								$size = $this->general->get_local_directory_size($currentFile);
        								echo $this->general->convert_size($size);
										?>
										</td>
											<td>
												<a href="javascript:" class="btn btn-primary" style="min-width: 40px;" data-toggle="tooltip" data-placement="top" title="Edit" onclick='updateproj(<?php echo json_encode($proj);?>)'> <i class="flaticon-pencil-1"></i></a>

												<a href="javascript:" class="btn btn-primary" style="min-width: 40px;" onclick='showftp(<?php echo json_encode($proj);?>)' data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("set_ftp")?>'><i class="flaticon-ftp"></i> <!-- <?= $this->lang->line("set_ftp")?> --></a>

												<a href="javascript:" style="min-width: 40px;" onclick='showmysql(<?php echo json_encode($proj);?>)' class="btn btn-info " data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("set_sql")?>'><i class="flaticon-servers"></i></a> <!-- <?= $this->lang->line("set_sql")?> -->

												<?php if($proj->ftp_status == 1){  ?>

													<!-- <a href="<?php //echo base_url(); ?>admin/projects/listftp/<?php //echo base64_encode($proj->project_id);?>" class="btn btn-danger"><?php // $this->lang->line("bkp_ftp")?></a> -->

													<a href="javascript:" style="min-width: 40px;" onclick='backupftp(<?php echo $proj->project_id;?>)' class="btn btn-danger" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("bkp_ftp")?>'><i class="flaticon-server-from-client"></i><!-- <?= $this->lang->line("bkp_ftp")?> --></a>

													<a href="javascript:" style="min-width: 40px;" onclick='backupftpbackground(<?php echo $proj->project_id;?>)' class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Backup Project in Background"><i class="flaticon-cloud-storage"></i></a>

												<?php } ?>


												<?php if($proj->sql_status){ ?>	

													<a href="javascript:" style="min-width: 40px;" onclick="sqlbkp(<?=$proj->project_id;?>)" class="btn btn-danger " data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("bkp_sql")?>'><i class="flaticon-database"></i><!-- <?= $this->lang->line("bkp_sql")?> --></a>
												
												<?php  } ?>


												<a href="<?= base_url()?>admin/optimizations/alive_system/<?= base64_encode($proj->project_id)?>"  class="btn btn-info" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("chk_sys")?>' style="min-width: 40px;" ><i class="flaticon-desktop-computer"></i> <!-- <?= $this->lang->line("chk_sys")?> --></a>

												<a href="javascript:" onclick="checksystemloadtime('<?php echo base64_encode($proj->project_id);?>')" class="btn btn-info" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("chk_sys_load_time")?>' style="min-width: 40px;" ><i class="flaticon-sand-clock-1"></i><!-- <?= $this->lang->line("chk_sys_load_time")?> --></a>
	
												<a href="javascript:" onclick='openpassmodal(<?= $proj->project_id?>)' class="btn btn-danger" style="min-width: 40px;" data-toggle="tooltip" data-placement="top" title="Delete"> <i class="flaticon-trash"></i> </a>
											</td>
										</tr>
										<?php
									}
								}	
								?>
							</tbody>
						</table>
					</div>
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>
<div class="modal fade" id="password-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("cnfirm_identity")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="passwordform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="delproj_id" id="delproj_id">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("password")?></label>
											<div class="col-sm-9">
												<input type="password" name="passwordverify" id="passwordverify" placeholder="<?= $this->lang->line("password")?>" class="form-control">
												<span style="color: red;" class="passwordverify_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="passvererrormsg" style="color: red;"></div>
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
<div class="modal fade" id="proj-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("up_proj_title")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="projform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="proj_id" id="proj_id">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("project_name")?></label>
											<div class="col-sm-9">
												<input type="text" name="proj_name" id="proj_name" placeholder="<?= $this->lang->line("project_name")?>" class="form-control">
												<span style="color: red;" class="proj_name_msg errmsg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("project_url")?></label>
											<div class="col-sm-9">
												<input type="text" name="proj_url" id="proj_url" placeholder="<?= $this->lang->line("project_url")?>" class="form-control">
												<span style="color: red;" class="proj_url_msg  errmsg"></span>
											</div>
										</div>
										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="projerrormsg" style="color: red;"></div>
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
<div class="modal fade" id="ftp-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("setup_ftp_server")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="ftpform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="ftp_id" id="ftp_id">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("domain_url")?></label>
											<div class="col-sm-9">
												<input type="text" name="domain_url" id="domain_url" placeholder="<?= $this->lang->line("domain_url")?>" class="form-control" onkeyup="updatepath(this.value)">
												<span>e.g. http://www.example.com/</span>
												<span style="color: red;" class="domain_url_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("domain_url_path")?> </label>
											<div class="col-sm-9">
												<input type="text" name="root_path" id="root_path" placeholder="<?= $this->lang->line("domain_url_path")?>" class="form-control">
												<span>e.g. http://www.example.com/testing</span>
												<span style="color: red;" class="root_path_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("protocol_type") ?></label>
											<div class="col-sm-9">
												
												<select name="protocol_type" id="protocol_type" class="form-control" onchange="addport(this.value)">
													<option value=""><?= $this->lang->line("select_protocol") ?></option>
													<option value="ftp">FTP</option>
													<option value="sftp">SFTP</option>
												</select>
												<span style="color: red;" class="protocol_type_msg"></span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("port_no") ?></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="port_no" id="port_no" placeholder="<?= $this->lang->line("port_no") ?>">
												<span style="color: red;" class="portno_msg"></span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("hostname") ?></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="hostname" id="hostname" placeholder="<?= $this->lang->line("hostname") ?>">
												<span style="color: red;" class="hostname_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("username") ?></label>
											<div class="col-sm-9">
												<input type="text" class="form-control" name="username" id="username" placeholder="<?= $this->lang->line("username") ?>">
												<span  style="color: red;" class="username_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("password") ?></label>
											<div class="col-sm-9">
												<input type="password" class="form-control" name="password" id="password" placeholder="<?= $this->lang->line("password") ?>">
												<small><em id="ftp_pw_note"></em><br></small>
												<span style="color: red;" class="password_msg"></span>
												<input type="hidden" id="is_ftp_set" value="">
											</div>

										</div>
									
										<div class="form-group row">
											<div class="input-group">
											<div class="col-sm-6" id="ftperrormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit") ?></button>
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $this->lang->line("close") ?></button>
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



<div class="modal fade checksystem" id="checksystem-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("check_sys") ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									
									<div class="row">
										<div class="col-sm-12 text-center"><b><?= $this->lang->line("project_url") ?></b></div>
										<div class="col-sm-10 col-sm-offset-1 text-center systemurl" id="systemurl"></div>
									</div>
									<br/>
									<div class="row">
										<div class="col-sm-12 text-center"><b><?= $this->lang->line("project_status") ?></b></div>
										<div class="col-sm-12 text-center mt-4 systemstatus" id="systemstatus"></div>
									</div>
									<br/>
									<br/>	
									
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>



<div class="modal fade checksystem" id="checksystemloadtime-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("check_sys_time") ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									
									<div class="row">
										<div class="col-sm-12 text-center"><b><?= $this->lang->line("project_url") ?></b></div>
										<div class="col-sm-10 col-sm-offset-1 text-center systemurl" id="timemodelsystemurl"></div>
									</div>
									<br/>
									<div class="row">
										<div class="col-sm-12 text-center"><b><?= $this->lang->line("project_status") ?></b></div>
										<div class="col-sm-12 text-center mt-4 systemstatus" id="timingdata"></div>
									</div>
									<br/>									
									<br/>	
									
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="mysql-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("setup_sql_server") ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-block">
									<form id="mysqlform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="mysql_id" id="mysql_id">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label"><?= $this->lang->line("db_name") ?> </label>
											<div class="col-sm-10">
												
												<input type="text" class="form-control" name="mdatabase_name" id="mdatabase_name" placeholder="<?= $this->lang->line("db_name") ?>">

												<span style="color: red;" class="mdatabase_name_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label"><?= $this->lang->line("hostname") ?></label>
											<div class="col-sm-10">
												<input type="text" class="form-control" name="mhostname" id="mhostname" placeholder="<?= $this->lang->line("hostname") ?>">
												<span style="color: red;" class="mhostname_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label"><?= $this->lang->line("username") ?></label>
											<div class="col-sm-10">
												<input type="text" class="form-control" name="musername" id="musername" placeholder="<?= $this->lang->line("username") ?>">
												<span style="color: red;" class="musername_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label"><?= $this->lang->line("password") ?></label>
											<div class="col-sm-10">
												<input type="password" class="form-control" name="mpassword" id="mpassword" placeholder="<?= $this->lang->line("password") ?>">
												
												<small><em id="sql_pw_note"></em><br></small>
												<span style="color: red;" class="mpassword_msg"></span>
												<input type="hidden" id="is_sql_set" value="">
											</div>
										</div>
										
										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="sqlerrormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit") ?></button>
												<button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button>
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

<div class="modal fade livestatusmodal" id="livestatusmodal-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Live Status of FTP Backup Process</h4>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="warning-msg">Please do not close browser till Backup process get done</div>
				<div class="progress backup-progress-bar" style="height:20px">
				    <div class="progress-bar" id="livestatuspercent" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;height:20px">
				      0%
				    </div>
				</div>
				<div class="alert alert-success" role="alert" id="successmsggg" style="display: none;">
  					<?= $this->lang->line("backup_success_taken") ?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<input type="hidden" name="cntperctotal" id="cntperctotal" >
						<input type="hidden" name="cntperccurrent" id="cntperccurrent" value="0">
						<table id="livestatustable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
							<thead>
								<th>#</th>
								<th><?= $this->lang->line("file_folder_name")?></th>
								<th><?= $this->lang->line("status")?></th>
							</thead>
							<tbody id="livestatustbody">
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" id="error_logfilecontent">

					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal fade prolivestatus" id="prolivestatus-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Backup Process is running in Background</h4>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="success-msg mt-4 mb-5" id="backlivestatus"></div>
				<div class="clearfix"><div></div></div>
			</div>
		</div>
	</div>
</div>





<div class="modal fade" id="mysqlbackup-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("bkp_sql") ?></h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-block" style="height:500px;overflow-x:hidden;overflow-y:auto;">
									<div id="processingWindow" style="display:none;">
										<h1 id="processHeading"><?= $this->lang->line("Processing") ?>..</h1>
										
										<div class="progress" style="height:20px;">
										  <div class="progress-bar progress-bar-striped active" id="probar" role="progressbar"
										  aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height:20px;">
										  </div>
										</div>

										<h3 id="showingDoneCount"></h3>
									</div>	

									<div id="showingProcessMsg">
									<div>	
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>






<script type="text/javascript">
	$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip({container: 'body'})
    $('#memListTable').DataTable( {
        "language": {
            "url": "<?php echo $this->lang->line("language_file")?>"
        }
     });
	});

function addport(vv){
	if(vv !=""){
		if(vv == "ftp"){
			$("#port_no").val(21);
		}else if(vv == "sftp"){
			$("#port_no").val(22);
		}
	}
}



	function sqlbkp(proid){

		$("#showingDoneCount").html("");
		$("#processingWindow").hide();
		$('#probar').css("width","0%").html("");

		$("#showingProcessMsg").html('<div class="form-group row"><div class="col-sm-12"><div> <?= $this->lang->line("Are you sure to start backup now") ?> </div><br><button type="button" onclick="startbkp('+proid+');" class="btn btn-primary m-b-0"><?= $this->lang->line("Start Backup") ?></button><button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button></div></div>');
		$("#mysqlbackup-Modal").modal({backdrop: 'static', keyboard: false});
	}



	function startbkp(proid){

		$('#showingProcessMsg').html('<p><?=$this->lang->line("Starting Backup Process");?></p>');
		$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Collecting database information. Please wait");?></p>');
		$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Creating database connection");?></p>');
		$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Collection table information");?></p>');


		$.ajax({
			url:"<?php echo base_url();?>admin/projects/startbkp",
			method:"post",
			data:{proid:proid},
			dataType:'json',
			success:function(data){
				
				//alert(data);

				if(data.status == 'failed'){
					$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Above error occur while connecting to database");?> </p>');
					$('#showingProcessMsg').prepend('<p style="color:red;">'+data.msg+'</p>');
					$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Please retry");?> </p>');
				}

				if(data.status == 'success'){

					//return false;

					//alert(data.path);
					var filepath = data.path;
					var file_name = data.file_name;

					$('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Database connection succeesfull");?></p>');
					$('#showingProcessMsg').prepend('<p> <?=$this->lang->line("Counting number of table available to Backup, Please wait sometime");?></p>');
					$('#showingProcessMsg').prepend('<p style="color:green;">'+data.msg+'</p>');
					$('#showingProcessMsg').prepend('<p style="color:green;"><?=$this->lang->line("Total no of table to backup is");?> '+data.tablescount+'</p>');

					if(data.tablescount > 0){
						
						$('#processingWindow').show();
						$('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Listing Database tables");?></p>');
					    $('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Creating database backup one by one it might be take some time");?> </p>');

					    $.each(data.tables, function (tableKey, tableName) {
					    	var tKey = tableKey+1;	

					    	setTimeout(function(){ 
					    		
					    		$('#showingProcessMsg').prepend('<p>'+tableName+' <?=$this->lang->line("backup started");?></p>');

						    	$.ajax({
									url:"<?php echo base_url();?>admin/projects/backuptableonebyone",
									method:"post",
									data:{table:tableName,filepath:filepath,file_name:file_name,tKey:tKey,tablescount:data.tablescount,proid:proid},
									dataType:'json',
									async: false,
									success:function(res){
										
										//alert(res);

										if(res.status == 'failed'){
											$('#showingProcessMsg').prepend('<p style="color:red;">'+res.msg+'</p>');
										}

										if(res.status == 'success'){
											$('#showingProcessMsg').prepend('<p style="color:green;">'+res.msg+'</p>');

											var per = ((tKey/data.tablescount)*100).toFixed(0);
											$('#probar').css("width",""+per+"%");
											$('#probar').html(""+per+"% <?=$this->lang->line("Completed");?>");

											if(tKey == data.tablescount){	
												$('#showingDoneCount').html(tKey+ ' <?=$this->lang->line("of");?> ' +data.tablescount+ ' <?=$this->lang->line("done");?>');
												$('#showingDoneCount').prepend('<p style="color:green;"><?=$this->lang->line("Backup completed successfully");?></p>');
												$("#showingDoneCount").prepend('<div class="form-group row"><div class="col-sm-12"><div> <button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button> </div></div>');
											}else{
												$('#showingDoneCount').html(tKey+ ' <?=$this->lang->line("of");?> ' +data.tablescount+ ' <?=$this->lang->line("done");?>');
											}


											
										}
										
										
									}
								});


					    	 }, 2000);

					        
					    		

					    });






					}




				}




				
			}
		});
			






	
	}















	function updateproj(data){
		$("#proj_id").val(data.project_id);
		$("#proj_name").val(data.project_name);
		$("#proj_url").val(data.projecturl);
		$("#proj-Modal").modal("show");
	}
		function openpassmodal(projid){
		var chk = confirm("<?php echo $this->lang->line("delete_confirm_proj_msg")?>");
		if(chk){
			$("#delproj_id").val(projid);
			$("#password-Modal").modal("show");
		}
	}
			function showftp(data){
					
					//console.log(data);
					$("#ftp_id").val(data.ftp_id);
					$("#domain_url").val(data.url);
					$("#root_path").val(data.root_path);
					$("#protocol_type").val(data.protocol_type);
					$("#hostname").val(data.hostname);
					$("#username").val(data.username);
					$("#autobkphrs").val(data.auto_backup_hours);
					$("#port_no").val(data.port_no);
					
					//$("#password").val(data.password)
					if(data.ftp_status == 1){
						$("#ftp_pw_note").html('<?php echo $this->lang->line("ftp_pw_note")?>');
						$("#is_ftp_set").val(1);	
					}else{
						$("#ftp_pw_note").html('');
						$("#is_ftp_set").val(0);
					}
					$("#ftp-Modal").modal("show");
					
			}

			function checksystem(proid){
				$.ajax({
					url:"<?php echo base_url();?>admin/projects/checksystem/"+proid,
					type:"get",
					beforeSend:function(){
						$("#cover-spin").show();
					},
					dataType:"json",
					success:function(data){
						
						console.log(data);
						$("#systemurl").html(data.url);
						$("#checksystem-Modal").modal("show");
						if(data.http_code == 200 || data.http_code == 301){
							$("#systemstatus").html('<button class="btn btn-lg btn-success"><i class="flaticon-check"></i>&nbsp;&nbsp;<?php echo $this->lang->line("success")?></button>');
						}else{
							$("#systemstatus").html('<button class="btn btn-lg btn-danger"><i class="flaticon-close"></i>&nbsp;&nbsp;<?php echo $this->lang->line("failure")?></button>');
						};  
						
						$("#cover-spin").hide();

					}
				});
			}	
			function checksystemloadtime(proid){
				$.ajax({
					url:"<?php echo base_url();?>admin/projects/checksystem/"+proid,
					type:"get",
					beforeSend:function(){
						$("#cover-spin").show();
					},
					dataType:"json",
					success:function(data){
						
						console.log(data);
						$("#timemodelsystemurl").html(data.url);
						$("#checksystemloadtime-Modal").modal("show");
						if(data.http_code == 200 || data.http_code == 301){

							$("#timingdata").html('<div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Connect_Time");?> </b></div><div class="col-sm-6">'+data.connect_time+'</div></div><div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Name_Lookup_Time");?>  </b></div><div class="col-sm-6">'+data.namelookup_time+'</div></div><div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Pre_Transfer_Time");?>  </b></div><div class="col-sm-6">'+data.pretransfer_time+'</div></div><div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Redirect_Time");?> </b></div><div class="col-sm-6">'+data.redirect_time+'</div></div><div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Start_Transfer_Time");?> </b></div><div class="col-sm-6">'+data.starttransfer_time+'</div></div><div class="row"><div class="col-sm-6"><b><?php echo $this->lang->line("Total_Time");?> </b></div><div class="col-sm-6">'+data.total_time+'</div></div>');
						}else{
							$("#timingdata").html('<button class="btn btn-lg btn-danger"><i class="flaticon-close"></i>&nbsp;&nbsp;<?php echo $this->lang->line("failure");?></button>');
						};  
						$("#cover-spin").hide();

					}
				});
			}	


			function updatepath(val){
				$('#root_path').val(val)
			}

			function showmysql(data){
					//console.log(data);
					$("#mysql_id").val(data.mysql_id);
					$("#mdatabase_name").val(data.mdatabase_name);
					$("#mhostname").val(data.mhostname);
					$("#musername").val(data.musername);
					//$("#mpassword").val(data.mpassword);
					$("#mautobkphrs").val(data.mauto_backup_hours);
					
					if(data.sql_status == 1){
						$("#sql_pw_note").html('<?php echo $this->lang->line("ftp_pw_note")?>');
						$("#is_sql_set").val(1);	
					}else{
						$("#sql_pw_note").html('');
						$("#is_sql_set").val(0);
					}
					$("#mysql-Modal").modal("show");

			}

			function validateftp(){
					var protocol_type = $("#protocol_type").val();
					var hostname = $("#hostname").val();
					var username = $("#username").val();
					var password = $("#password").val();
					var domain_url = $("#domain_url").val();
					var root_path = $("#root_path").val();
					if(domain_url == ""){
							$(".domain_url_msg").html("<?php echo $this->lang->line("domain_blank")?>");
							return false;
					}else if(root_path == ""){
							$(".domain_url_msg").html("")
							$(".root_path_msg").html("<?php echo $this->lang->line("domain_url_path_path")?>");
							return false;
					}else if(protocol_type == ""){
							$(".domain_url_msg").html("");
							$(".root_path_msg").html("");
							$(".protocol_type_msg").html("<?php echo $this->lang->line("protocol_type_blank")?>");
							return false;
					}else if(hostname == ""){
							$(".domain_url_msg").html("")
							$(".root_path_msg").html("")
							$(".protocol_type_msg").html("")
							$(".hostname_msg").html("<?php echo $this->lang->line("hostname_blank")?>");
							return false;
					}else if(username == ""){
							$(".domain_url_msg").html("")
							$(".root_path_msg").html("")
							$(".protocol_type_msg").html("")
							$(".hostname_msg").html("")
							$(".username_msg").html("<?php echo $this->lang->line("username_blank")?>");
							return false;
					}else if(password == ""){

							if($("#is_ftp_set").val() == 1){
								return true;
							}
							
							$(".domain_url_msg").html("")
							$(".root_path_msg").html("")
							$(".protocol_type_msg").html("")
							$(".hostname_msg").html("")
							$(".username_msg").html("") 
							$(".password_msg").html("<?php echo $this->lang->line("password_blank")?>");
							return false;
					}else{
							$(".protocol_type_msg").html("");
							$(".hostname_msg").html("");
							$(".username_msg").html("");
							$(".password_msg").html("");
							return true;
					}
			}
			function validatemysql(){
					var mdatabase_name = $("#mdatabase_name").val();
					var mhostname = $("#mhostname").val();
					var musername = $("#musername").val();
					var mpassword = $("#mpassword").val();
					if(mdatabase_name == ""){
							$(".mdatabase_name_msg").html("<?php echo $this->lang->line("dbname_blank")?>");
							return false;
					}else if(mhostname == ""){
							$(".mdatabase_name_msg").html("")
							$(".mhostname_msg").html("<?php echo $this->lang->line("hostname_blank")?>");
							return false;
					}else if(musername ==""){
							$(".mdatabase_name_msg").html("")
							$(".mhostname_msg").html("")
							$(".musername_msg").html("<?php echo $this->lang->line("username_blank")?>");
							return false;
					}else if(mpassword == ""){

							if($("#is_sql_set").val() == 1){
								return true;
							}

							$(".mdatabase_name_msg").html("")
							$(".mhostname_msg").html("")
							$(".musername_msg").html("") 
							$(".mpassword_msg").html("<?php echo $this->lang->line("password_blank")?>");
							return false;
					}else{
							$(".mdatabase_name_msg").html("");
							$(".mhostname_msg").html("");
							$(".musername_msg").html("");
							$(".mpassword_msg").html("");
							$(".domain_url_msg").html("");
							$(".root_path_msg").html("");
							return true;
					}
			}
					$("#ftpform").on('submit', function(e){
				        e.preventDefault();
				        if(validateftp() == true){
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/projects/set_ftp",
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
				                    	$("#cover-spin").hide()   
				                        if(data.status == "success"){
				                            swal(data.msg, {
												title: "<?= $this->lang->line("great") ?>",
												type: "success",
												buttons: true,
												timer: 3000
											}).then(() => {
												location.reload();
											})

				                        }else{
				                        	$("#ftperrormsg").html(data.msg);
				                        	swal(data.msg, {
												title: "<?= $this->lang->line("oops") ?>",
												type: "error",
												buttons: true,
												timer: 3000
											})
				                        }
				                          
				                     }
				                });
				    	}
				   	 });
					$("#projform").on('submit', function(e){
				        e.preventDefault();
				        var proj_name = $("#proj_name").val();
				        if(proj_name != ""){
				        	$("#projerrormsg").html("");
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/projects/update",
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
				                    	$("#cover-spin").hide() 
				                        if(data.status == "success"){
				                            swal(data.msg, {
												title: "<?= $this->lang->line("great") ?>",
												type: "success",
												buttons: true,
												timer: 3000
											}).then(() => {
												location.reload();
											})
				                        }else{
				                        	$("#projerrormsg").html(data.msg);
				                        	swal(data.msg, {
												title: "<?= $this->lang->line("oops") ?>",
												type: "error",
												buttons: true,
												timer: 3000
											})
				                        }
				                            
				                     }
				                });
				    	}else{
				    		$("#projerrormsg").html("<?php echo $this->lang->line("project_name_blank")?>");
				    	}
				   	 });
					$("#passwordform").on('submit', function(e){
				        e.preventDefault();
				        var passwordverify = $("#passwordverify").val();
				        if(passwordverify != ""){
				        	$("#passvererrormsg").html("");
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/projects/deleteproject",
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
				                    	$("#cover-spin").hide() 
				                        if(data.status == "success"){
				                            swal(data.msg, {
												title: "<?= $this->lang->line("great") ?>",
												type: "success",
												buttons: true,
												timer: 3000
											}).then(() => {
												location.reload();
											})
				                        }else{
				                        	$("#passvererrormsg").html(data.msg);
				                        	swal(data.msg, {
												title: "<?= $this->lang->line("oops") ?>",
												type: "error",
												buttons: true,
												timer: 3000
											})
				                        }
				                            
				                     }
				                });
				    	}else{
				    		$("#passvererrormsg").html("<?php echo $this->lang->line("password_blank")?>");
				    	}
				   	 });
</script>
<script type="text/javascript">
					$("#mysqlform").on('submit', function(e){
				        e.preventDefault();
				        validatemysql();
				        if(validatemysql() == true){
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/projects/set_mysql",
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
				                    	$("#cover-spin").hide()
				                        if(data.status == "success"){
				                            swal(data.msg, {
												title: "<?= $this->lang->line("great") ?>",
												type: "success",
												buttons: true,
												timer: 3000
											}).then(() => {
												location.reload();
											})
				                        }else{
				                        	swal(data.msg, {
												title: "<?= $this->lang->line("oops") ?>",
												type: "error",
												buttons: true,
												timer: 3000
											})
				                        	
				                        }
				                            
				                     }
				                });
				    	}
				   	 });
					</script>
					<script type="text/javascript">
					function backupftp(project_id){
						var processing = "Processing";
						var successmsg = "Success";
						$("#livestatusmodal-Modal").modal({backdrop: 'static', keyboard: false});

						$.ajax({
				           url:"<?php echo base_url();?>admin/projects/backupftp",
				           type:"post",
				            data: {project_id:project_id},
				            dataType: 'json',
				            success:function(dat){
				            	if(dat.status == "success"){
				            	var tbodyhtml = '';	
				            	var dd = dat.data;
				            	console.log(dd);
				            	var cnt = 1;
				            	var error_logfile = dat.error_logfile;
				            	var ftpbkp_id = dat.ftpbkp_id;
				            	$.each(dd, function(key, value ){
				            		var val = value.split("/");
				            		var temp = val[val.length-1];
  									if(temp =="." || temp ==".." || temp =="/.." || temp =="/."){
				            			tbodyhtml += "";
				            		}else{
				            			tbodyhtml += '<tr class="inprogress" id="status_id_'+key+'">';
				            			tbodyhtml += '<td>'+cnt+'</td>';
				            			tbodyhtml += '<td>'+value+'</td>';
				            			tbodyhtml += '<td id="st_'+key+'">'+processing+'</td>';
				            			tbodyhtml += '</tr>';
				            			cnt++;
				            		}
								});
								cnt = cnt-1;
				            	$("#livestatusmodal-Modal").modal({backdrop: 'static', keyboard: false});
				            	$("#livestatustbody").html(tbodyhtml);
				            	$("#cntperctotal").val(cnt);
				            	for(var j=0;j<dd.length;j++){
				            		var val = dd[j].split("/");
				            		var temp = val[val.length-1];
				            		if(temp == "." || temp ==".." || temp =="/.." || temp =="/."){
				            			continue;
				            		}else{
				            			var folddata = temp;
				            			var cntperccurrent = parseInt($("#cntperccurrent").val());
				            			$.ajax({
								           url:"<?php echo base_url();?>admin/projects/ftploopdata",
								           type:"post",
								            timeout:72000000,
								            data: {project_id:project_id,localfolder:dat.localrootfolder,remotefolder:dat.remoterootfolder,fileorfolder:folddata,indexid:j,error_logfile:error_logfile,ftpbkp_id:ftpbkp_id},
								            dataType: 'json',
								            success:function(resdata){
								            	console.log(resdata);
								            	if(resdata.status== "success"){
								            		var cntperccurrent = parseInt($("#cntperccurrent").val());
								            		var clc = cntperccurrent + 1;
								            		console.log(clc);
								            		$("#cntperccurrent").val(clc);
								            		var perc = Math.round((clc/cnt) * 100);
								            		console.log(perc);
								            		$("#livestatuspercent").html(perc+"%");
								            		$("#livestatuspercent").attr("aria-valuenow",perc);
								            		$("#livestatuspercent").css("width","calc("+perc+"% - 6px)");
								            		console.log(j+"ss");
								            		$("#status_id_"+resdata.indexid).removeClass("inprogress").addClass("success");
								            		$("#st_"+resdata.indexid).html(successmsg);
								            		if(perc == 100){
								            			readlogfile(error_logfile)
								            			$("#successmsggg").show();
								            // 			$.ajax({
												        //    url:"<?php //echo base_url();?>admin/projects/zipproject",
												        //    type:"post",
												        //     data: {project_id:project_id,startdate:dat.startdate,error_logfile:error_logfile},
												        //     dataType: 'json',
												        //     success:function(resp){
												        //     	if(resp.status == 'success'){
												            		 
												        //     		 $("#successmsggg").show()
												        //     	}
												        //     }
												        // });
								            		}
								            	}
								            }
								        });
				            		}
				            	}	
				            	}else{
				            		swal(dat.msg, {
										title: "<?= $this->lang->line("oops") ?>",
										type: "error",
										buttons: true,
										timer: 3000
									})
				            	}
				            }
				        });
					}

						function readlogfile(error_logfile){
							   						$.ajax({
												           url:"<?php echo base_url();?>admin/projects/logfile",
												           type:"post",
												            data: {perror_logfile:error_logfile},
												            success:function(resp){
												            	if(resp.status == 'success'){
												            		 
												            		$("#error_logfilecontent").html(resp.data);
												            	}
												            }
												        });

							  
						}

						function backupftpbackground(project_id){
							var r = confirm("Are you sure you want to take FTP Backup in Background process");
							if(r){
								$.ajax({
							           url:"<?php echo base_url();?>admin/projects/putreqcron",
							           type:"post",
							            data: {project_id:project_id},
							            dataType: 'json',
							            success:function(dat){
							            	if(dat.status == "success"){
							            		$("#prolivestatus-Modal").modal("show");
							            		$("#backlivestatus").html(dat.msg);
							            	}
							            }
							        });
							}
						}


					</script>

<!-- .container-fluid -->
<?php $this->load->view("admin/layout/footer_new");?>
