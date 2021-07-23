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
													<input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

													<table id="dom-jqry" class="table table-striped table-bordered nowrap">
														<thead>
															<tr>
																<th>Sr No</th>
																<th>Project Name</th>
																<th>File Name</th>
																<th>Created Date</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<?php 
															$cnt = 1;
																foreach($backups as $bkp) {
																	?>
															<tr>
																<td><?= $cnt ?></td>
																<td><?= $bkp->project_name ?></td>
																<td><?= $bkp->file_name ?></td>
																<td><?= $bkp->added_date ?></td>
																<td><a href="javascript:" class="btn btn-primary btn-round waves-effect waves-light" onclick='restorebkp(<?=$bkp->backup_id;?>)'>Restore Backup</a></td>
															</tr>


															<?php
															$cnt++;
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
					<script type="text/javascript">
						function restorebkp(bkp_id){
							$chk = confirm("Areyou sure you want to restore this Backup");
							if($chk){
								 $.ajax({
          					 		url:"<?php echo base_url();?>client/backup/restore",
          					 		method:"post",
          					 		data:{bkp_id:bkp_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
          					 		success:function(data){
          					 			console.log(data);
          					 		}
          					 });

							}

						}

					</script>
					<?php $this->load->view("client/layout/footer");?>