<?php $this->load->view("client/layout/header");?>
	<div class="pcoded-content">

						<div class="page-header card">
							<div class="row align-items-end">
								<div class="col-lg-8">
									<div class="page-header-title">
										<i class="feather icon-clipboard bg-c-blue"></i>
										<div class="d-inline">
											<h5>Create Project</h5>
										</div>
									</div>
								</div>
								<div class="col-lg-4">
									<div class="page-header-breadcrumb">
										<ul class=" breadcrumb breadcrumb-title">
											<li class="breadcrumb-item">
												<a href="<?php echo base_url();?>client/dashboard"><i class="feather icon-home"></i></a>
											</li>
											<li class="breadcrumb-item"><a href="<?php echo base_url();?>client/projects">Projects</a>
											</li>
											<li class="breadcrumb-item">
												<a href="<?php echo base_url();?>client/projects/create">New Project</a>
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
										<div class="row">
											<div class="col-sm-12">
												<div class="card">
													<div class="card-header">
														<h5>Create Project</h5>
													</div>
													<div class="card-block">
														<form id="main" method="post" action="<?php echo base_url();?>client/projects/save" >
															<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Project Name</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="project_name" id="project_name" placeholder="Enter Project Name">
																	<span class="messages"></span>
																</div>
															</div>
															<div class="form-group row">
																<label class="col-sm-2 col-form-label">Project Slug</label>
																<div class="col-sm-10">
																	<input type="text" class="form-control" name="slug" id="slug" placeholder="Enter Slug">
																	<span class="messages"></span>
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

								</div>
							</div>
						</div>
					</div>

<?php $this->load->view("client/layout/footer");?>