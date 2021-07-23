<?php $this->load->view("admin/layout/header_new"); ?>
<?php $this->load->view("admin/layout/sidebar"); ?>
<div class="container-fluid">
	<div class="row mr-0">
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb">
				<!--     <li class="breadcrumb-item"><a href="#">Projects</a></li> -->
				<li class="breadcrumb-item active" aria-current="page"><?= $this->lang->line("projects") ?></li>
			</ol>
		</nav>
		<div id="cover-spin"></div>
		<div class="row">
			<div class="col-md-12">
				<div class="filter-container flex-row">
					<div class="flex-col-6">
						<h3 class="filter-content-title"><?= $this->lang->line("project_list") ?></h3>
					</div>
					<div class="flex-col-6 text-right"> <a class="btn btn-primary" href="<?= base_url("/admin/project/create")?>"> <?= $this->lang->line("new_project") ?> </a> </div>
				</div>
			</div>
		</div>
		<div data-widget-role="role1">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default panel-grid" style="visibility: visible;">
						<div class="panel-body no-padding p-0">
							<div id="memListTable_wrapper" class="form-inline no-footer">
								
									<table id="memListTable" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info" style="margin-top:5px">
										<thead>
											<tr role="row">
												<th width="5%" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
												<th width="20%" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Name  : activate to sort column ascending"><?= $this->lang->line("name")?> </th>
												<th width="10%" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?= $this->lang->line("status")?> </th>
												<th width="68%" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Action  : activate to sort column ascending"><?= $this->lang->line("action")?> </th>
											</tr>
										</thead>
										<tbody>
											<?php
											if(count($projects) > 0){
												$cnt = 1;
											foreach ($projects as $prodata) {
												?>
												<tr role="row" class="odd">
													<td class="sorting_1"><?= $cnt ?></td>
													<td><?=  $prodata->project_name ?></td>
													<td><span class="badge badge-success"><?= $this->lang->line("active")?></span></td>
													<td>
														<a href="<?= base_url('admin/project/manage_backup/'.base64_encode($prodata->project_id)); ?>" class="btn btn-info "><?= $this->lang->line("manage_backup")?></a>
														<!-- <a href="#" class="btn btn-info "><?php // $this->lang->line("Website SEO check")?></a> -->
														<a href="<?= base_url('admin/Webperformance/seo_check/'.base64_encode($prodata->project_id)); ?>" class="btn btn-info "><?= $this->lang->line("Website SEO check")?></a>
														 <a href="<?= base_url('admin/optimizations/alive_system/'.base64_encode($prodata->project_id)); ?>" class="btn btn btn-primary" ><?= $this->lang->line("Website alive system")?></a>
														 <a href="<?= base_url('admin/Webperformance/check/'.base64_encode($prodata->project_id)); ?>" class="btn btn btn-primary"><?= $this->lang->line("Website Performance Check")?></a> 
														
														<a href="javascript:" class="btn btn-danger" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Edit" onclick='updateproj(<?php echo json_encode($prodata);?>)'><i class="flaticon-pencil-1"></i></a>
														<a class="btn btn-danger" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:" onclick='openpassmodal(<?= $prodata->project_id?>)'><i class="flaticon-trash"></i></a>
													</td>
												</tr>
												<?php
												$cnt++;
											}
											}
											?>
										</tbody>
									</table>
								
							</div>
						</div>
						<!-- <div class="panel-footer"></div> -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="password-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
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
											<span class="col-sm-6 "><?= $this->lang->line("please_enter")?> "<strong>delete</strong>" <?= $this->lang->line("keyword_to_confirm")?></span>
											<div class="col-sm-6">
												<input type="text" name="passwordverify" id="passwordverify" placeholder="******" class="form-control">
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
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("project_name")?><span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="proj_name" id="proj_name" placeholder="<?= $this->lang->line("project_name")?>" class="form-control">
												<span style="color: red;" class="proj_name_msg errmsg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("project_url")?><span class="text-danger">*</span></label>
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
<script type="text/javascript">
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip({container: 'body'})
    		$('#memListTable').DataTable( {
		        "language": {
		            "url": "<?php echo $this->lang->line("language_file")?>"
		        },
     	});
    		
	});
	function openpassmodal(projid){
		var chk = "<?php echo $this->lang->line("delete_confirm_proj_msg")?>";
		swal(chk, {
			  buttons: {
			    cancel: "<?php echo $this->lang->line("No")?>",
			    catch: {
			      text: "<?php echo $this->lang->line("Yes")?>",
			      value: "catch",
			    },
			  },
			})
			.then((value) => {
			  switch (value) {
			    case "defeat":
			      return false;
			      break;
			    case "catch":
				     $("#delproj_id").val(projid);
					 $("#password-Modal").modal("show");
				      break;
			 
			    default:
			     	return false;
			  }
			});
	}

	function updateproj(data){
		$("#proj_id").val(data.project_id);
		$("#proj_name").val(data.project_name);
		$("#proj_url").val(data.url);
		$("#proj-Modal").modal("show");
	}
	function putcronalivesystem(project_id){
			$.ajax({
		           url:"<?php echo base_url();?>admin/project/putcronalivesystem",
		           type:"post",
		           beforeSend:function(){
		           		$("#cover-spin").show()
		           },
			       data: {project_id:project_id},
			       dataType: 'json',
		           success:function(data){
		                        if(data.status == "success"){
		                            swal(data.msg, {
										title: "<?= $this->lang->line("great") ?>",
										type: "success",
										timer: 3000
									}).then(() => {
										location.reload();
									})
		                        }else if(data.status == "redirect"){
		                        	location.replace("<?php echo base_url("admin/optimizations/alive_system/") ?>"+data.alive_id);
		                    	}else{
		                        	$("#passvererrormsg").html(data.msg);
		                        	swal(data.msg, {
										title: "<?= $this->lang->line("oops") ?>",
										type: "error",
										timer: 3000
									})
		                        }
		                        $("#cover-spin").hide()     
		                     }
						});
	}

	$("#passwordform").on('submit', function(e){
        e.preventDefault();
        var passwordverify = $("#passwordverify").val();
        if(passwordverify != ""){
        	$("#passvererrormsg").html("");
    	$.ajax({
           url:"<?php echo base_url();?>admin/project/deleteproject",
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
								title: "<?= $this->lang->line("great") ?>",
								type: "success",
								timer: 3000
							}).then(() => {
								location.reload();
							})
                        }else{
                        	$("#passvererrormsg").html(data.msg);
                        	swal(data.msg, {
								title: "<?= $this->lang->line("oops") ?>",
								type: "error",
								timer: 3000
							})
                        }
                        $("#cover-spin").hide()     
                     }
                });
    	}else{
    		$("#passvererrormsg").html("<?php echo $this->lang->line("please_enter_keyword")?>");
    	}
	});
	$("#projform").on('submit', function(e){
				        e.preventDefault();
				        var proj_name = $("#proj_name").val();
				        if(proj_name != ""){
				        	$("#projerrormsg").html("");
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/project/update",
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
										timer: 3000
									}).then(() => {
										location.reload();
									})
		                        }else{
		                        	$("#projerrormsg").html(data.msg);
		                        	swal(data.msg, {
										title: "<?= $this->lang->line("oops") ?>",
										type: "error",
										timer: 3000
									})
		                        }
		                            
		                     }
		                });
			    	}else{
			    		$("#projerrormsg").html("<?php echo $this->lang->line("project_name_blank")?>");
			    	}
		});


</script>
<?php $this->load->view("admin/layout/footer_new"); ?>