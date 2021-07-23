<?php $this->load->view("client/layout/header");?>
<div class="pcoded-content">

						<div class="page-header card">
							<div class="row align-items-end">
								<div class="col-lg-8">
									<div class="page-header-title">
										<i class="feather icon-inbox bg-c-blue"></i>
										<div class="d-inline">
											<h5><?= $projectss->project_name ?></h5>
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
													<input type="hidden" name="rootfolder" id="rootfolder" value="<?php echo base64_encode($root_folder); ?>">
													<input type="hidden" name="project_id" id="project_id" value="<?php echo base64_encode($projectss->project_id); ?>">
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
															<?php
															if(count($list) > 0){
																$cnt = 1;
																foreach($list as $key => $val){
															?>
															<tr>
																<td><input type="checkbox" name="cheftpids[]" id="chk_<?=$key?>" value="<?=$key?>"></td>
																<td><?= $cnt ?></td>
																<td><?= $val ?></td>
																<td><?php 
																if(strpos($val,".") !== false){
																	$as = explode(".",$val)[1];
																	$fcheck = 0;
																	echo $as." File";  
																}else{
																	$fcheck = 1;
																	echo 'Folder';	
																}
																?></td>
																<td>
																	<?php if($fcheck == 1){
																		if($root_folder == "/"){


																			$rrr = base64_encode($projectss->project_id)."/".base64_encode($root_folder.$val."/");
																			}else{
																				$rrr = base64_encode($projectss->project_id)."/".base64_encode($root_folder."/".$val."/");
																			}
																		?>
																	<a href="<?php echo base_url(); ?>client/projects/listftp/<?php echo $rrr;?>" class="btn btn-primary btn-round waves-effect waves-light">View Directory Data</a>
																	<?php
																	}
																	?>
																</td>
															</tr>
															<?php
															$cnt++;
																}
																
															}	
															?>
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
					<script type="text/javascript">
						
						$("#selectall").click(function(){
						 var checkBox = document.getElementById("selectall");
						 	if(checkBox.checked == true){
							    $('input[name="cheftpids[]"]').each(function() {
					            this.checked = true;
					        	});
							  } else {
							    $('input[name="cheftpids[]"]').each(function() {
					            this.checked = false;
					        	});
							  }
					    });
					    function bulkbackup(){
					    	var rootfolder = $("#rootfolder").val();
					    	var project_id = $("#project_id").val();
					    	var folderids = new Array();
				            $("input[name='cheftpids[]']:checked").each(function() {
				                folderids.push($(this).val());
				            });
				             var x = folderids.toString();
				             var csrfName = $('.txt_csrfname').attr('name');
          					 var csrfHash = $('.txt_csrfname').val();
          					 console.log(csrfName+"  "+csrfHash);
          					 $.ajax({
          					 		url:"<?php echo base_url();?>client/projects/bulkftp",
          					 		method:"post",
          					 		data:{rootfolder:rootfolder,folderid:x,project_id:project_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
          					 		success:function(data){
          					 			console.log(data);
          					 		}
          					 });

					    }
					</script>
<?php $this->load->view("client/layout/footer");?>