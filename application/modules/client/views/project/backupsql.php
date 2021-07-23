<?php $this->load->view("client/layout/header");?>
<div class="pcoded-content">
						<div class="page-header card">
							<div class="row align-items-end">
								<div class="col-lg-8">
									<div class="page-header-title">
										<i class="feather icon-inbox bg-c-blue"></i>
										<div class="d-inline">
											<h5>testing</h5>
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
											<li class="breadcrumb-item"><a href="#!">FTP Data List</a>
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
												<a href="javascript:" onclick="bulkbackup()" style="float: right;" class="btn btn-danger btn-round waves-effect waves-light">Bulk Backup</a>
											</div>
											<div class="card-block">
												<div class="table-responsive dt-responsive">
													<input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
													<table id="dom-jqry" class="table table-striped table-bordered nowrap">
														<thead>
															<tr>
																<th><input type="checkbox" name="selectall" id="selectall" class="form-control"></th>
																<th>Sr No</th>
																<th>File or Folder Name</th>
																<th>Type</th>
																<th>Action</th>
															</tr>
														</thead>
														<tbody>
															<tr>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															</tr>
														</tbody>
														<tfoot>
															<tr>
																<th></th>
																<th>Sr No</th>
																<th>File or Folder Name</th>
																<th>Type</th>
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
<?php $this->load->view("client/layout/footer");?>