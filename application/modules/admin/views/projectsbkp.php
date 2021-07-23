<?php $this->load->view("client/layout/header");?>
<div class="pcoded-content">

						<div class="page-header card">
							<div class="row align-items-end">
								<div class="col-lg-8">
									<div class="page-header-title">
										<i class="feather icon-inbox bg-c-blue"></i>
										<div class="d-inline">
											<h5>Project List</h5>
										</div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="page-header-breadcrumb">
										<ul class=" breadcrumb breadcrumb-title">
											<li class="breadcrumb-item">
												<a href="index.html"><i class="feather icon-home"></i></a>
											</li>
											<li class="breadcrumb-item"><a href="<?php echo base_url();?>client/dashboard">Dashboard</a>
											</li>
											<li class="breadcrumb-item"><a href="#!">Project List</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>

						<div class="pcoded-inner-content">
							<div class="main-body">
								<div class="page-wrapper">
									<div class="page-body">
										<div class="card">
											<div class="card-header">
												<h5>Projects</h5>
												<a href="<?php echo base_url();?>client/projects/create" style="float: right;" class="btn btn-danger btn-round waves-effect waves-light">New Project</a>
											</div>
											<div class="card-block">
												<div class="table-responsive dt-responsive">
													<table id="dom-jqry" class="table table-striped table-bordered nowrap">
														<thead>
															<tr>
																<th>Name</th>
																<th>Slug</th>
																<th>Status</th>
																<th>Created Date</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php
															if($project_count > 0){
																foreach ($projects as $proj){				
															?>
															<tr>
																<td><?= $proj->project_name?></td>
																<td><?= $proj->slug?></td>
																<td><?= $proj->status?></td>
																<td><?= $proj->added_date?></td></td>
																<td>
																	<a href="javascript:" class="btn btn-primary btn-round waves-effect waves-light" onclick='showftp(<?php echo json_encode($proj);?>)'>Setup FTP</a>&nbsp;
																	<a href="javascript:" onclick='showmysql(<?php echo json_encode($proj);?>)' class="btn btn-info btn-round waves-effect waves-light">Setup Mysql</a>
																	&nbsp;
																	<a href="<?php echo base_url(); ?>client/projects/listftp/<?php echo base64_encode($proj->project_id);?>" class="btn btn-danger btn-round waves-effect waves-light">Backup FTP</a>
																	<a href="<?php echo base_url(); ?>client/projects/listftp/<?php echo base64_encode($proj->project_id);?>" class="btn btn-danger btn-round waves-effect waves-light">Backup MySQL</a>
																</td>
															</tr>
															<?php
																}
															}	
															?>
														</tbody>
														<tfoot>
															<tr>
																<th>Name</th>
																<th>Slug</th>
																<th>Status</th>
																<th>Created Date</th>
																<th>Action</th>
															</tr>
														</tfoot>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="styleSelector">
							</div>
						</div>
					</div>
					<div class="modal fade" id="ftp-Modal" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Setup of FTP Server</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="row">
											<div class="col-sm-12">
												<div class="card">
													<div class="card-header">
														<h5>Create Project</h5>
													</div>
													<div class="card-block">
														<form id="ftpform" method="post">
															<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
															<input type="hidden" name="ftp_id" id="ftp_id">
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Protocol Type </label>
																<div class="col-sm-10">
																	
																	<select name="protocol_type" id="protocol_type" class="form-control">
																		<option value="">Select Protocol</option>
																		<option value="ftp">FTP</option>
																		<option value="sftp">SFTP</option>
																	</select>
																	<span class="protocol_type_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Host Name</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="hostname" id="hostname" placeholder="Enter Host Name">
																	<span class="hostname_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">User Name</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="username" id="username" placeholder="Enter User Name">
																	<span class="username_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Password</label>
																<div class="col-sm-10">
																	<input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
																	<span class="password_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2"></label>
																<div class="col-sm-10">
																	<button type="submit" class="btn btn-primary m-b-0">Submit</button>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal fade" id="mysql-Modal" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Setup of Mysql Server</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div class="row">
											<div class="col-sm-12">
												<div class="card">
													<div class="card-header">
														<h5>Setup Mysql</h5>
													</div>
													<div class="card-block">
														<form id="mysqlform" method="post">
															<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
															<input type="hidden" name="mysql_id" id="mysql_id">
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Database Name </label>
																<div class="col-sm-10">
																	
																	<input type="text" class="form-control" name="mdatabase_name" id="mdatabase_name" placeholder="Enter Database Name">

																	<span class="mdatabase_name_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Host Name</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="mhostname" id="mhostname" placeholder="Enter Host Name">
																	<span class="mhostname_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">User Name</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="musername" id="musername" placeholder="Enter User Name">
																	<span class="musername_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Password</label>
																<div class="col-sm-10">
																	<input type="password" class="form-control" name="mpassword" id="mpassword" placeholder="Enter Password">
																	<span class="mpassword_msg"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2"></label>
																<div class="col-sm-10">
																	<button type="submit" class="btn btn-primary m-b-0">Submit</button>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<script type="text/javascript">
					function showftp(data){
							console.log(data);
					$("#ftp_id").val(data.ftp_id);
					$("#protocol_type").val(data.protocol_type)
					$("#hostname").val(data.hostname)
					$("#username").val(data.username)
					$("#password").val(data.password)
					$("#ftp-Modal").modal("show");

						}

						function showmysql(data){
							console.log(data);
					$("#mysql_id").val(data.mysql_id);
					$("#mdatabase_name").val(data.mdatabase_name)
					$("#mhostname").val(data.mhostname)
					$("#musername").val(data.musername)
					$("#mpassword").val(data.mpassword)
					$("#mysql-Modal").modal("show");

						}

					function validateftp(){
					var protocol_type = $("#protocol_type").val();
					var hostname = $("#hostname").val();
					var username = $("#username").val();
					var password = $("#password").val();
					if(protocol_type == ""){
							$(".protocol_type_msg").html("Please enter Protocol type");
							return false;
					}else if(hostname){
							$(".hostname_msg").html("Please enter Host name");
							return false;
					}else if(username){
							$(".username_msg").html("Please enter User name");
							return false;
					}else if(password){
							$(".password_msg").html("Please enter Password");
							return false;
					}else{
							$(".protocol_type_msg").html("");
							$(".hostname_msg").html("");
							$(".username_msg").html("");
							$(".password_msg").html("");
							//return true;
					}
					}
					function validatemysql(){
					var mdatabase_name = $("#mdatabase_name").val();
					var mhostname = $("#mhostname").val();
					var musername = $("#musername").val();
					var mpassword = $("#mpassword").val();
					if(protocol_type == ""){
							$(".mdatabase_name_msg").html("Please enter Database Name");
							return false;
					}else if(hostname){
							$(".mhostname_msg").html("Please enter Host name");
							return false;
					}else if(username){
							$(".musername_msg").html("Please enter User name");
							return false;
					}else if(password){
							$(".mpassword_msg").html("Please enter Password");
							return false;
					}else{
							$(".mdatabase_name_msg").html("");
							$(".mhostname_msg").html("");
							$(".musername_msg").html("");
							$(".mpassword_msg").html("");
							//return true;
					}
					}
					$("#ftpform").on('submit', function(e){
				        e.preventDefault();
				        validateftp();
				    	$.ajax({
				           url:"<?php echo base_url();?>client/projects/set_ftp",
				           type:"post",
				            data: new FormData(this),
				            dataType: 'json',
				            contentType: false,
				            cache: false,
				            processData:false,
				                    success:function(data){
				                        if(data.status == "success"){
				                            alert(data.msg)
				                            location.reload();
				                        }     
				                     }
				                });
				   	 });


					</script>
					<script type="text/javascript">
						$("#mysqlform").on('submit', function(e){
				        e.preventDefault();
				        validatemysql();
				    	$.ajax({
				           url:"<?php echo base_url();?>client/projects/set_mysql",
				           type:"post",
				            data: new FormData(this),
				            dataType: 'json',
				            contentType: false,
				            cache: false,
				            processData:false,
				                    success:function(data){
				                        if(data.status == "success"){
				                            alert(data.msg)
				                            location.reload();
				                        }     
				                     }
				                });
				   	 });
					</script>
					<?php $this->load->view("client/layout/footer");?>