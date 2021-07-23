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

</style>
<div class="container-fluid">
	<div class="row mr-0">
		<div id="cover-spin"></div>
		<div class="row">                    
			<div class="col-md-12">
				<div class="filter-container flex-row">
					<div class="flex-col-md-6">
						<h3 class="filter-content-title"><?= $this->lang->line("resellers_req") ?></h3>
					</div>
					
				</div>
			</div>        
		</div>
		<div data-widget-role="role1">
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<th ><?= $this->lang->line("sr_no")?></th>
									<th ><?= $this->lang->line("name")?></th>
									<th ><?= $this->lang->line("requested_date") ?></th>
									<th ><?= $this->lang->line("status")?></th>
									<th ><?= $this->lang->line("action")?></th>  
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($reseller_requestlist) > 0){
									$cnt = 1;
									foreach ($reseller_requestlist as $proj){				
										?>
										<tr>
											<td><?= $cnt ?></td>
											<td><?= $proj->full_name?></td>
											<td><?= displayDate($proj->updated_date) ?></td>
											<td><?= $proj->status?></td>
											<td><a href="javascript:" class="btn btn-primary" onclick="approve_reseller(<?= $proj->user_id ?>)" data-toggle="tooltip" data-placement="top" title="<?= $this->lang->line("Role as Reseller")?>" style="min-width: 40px;"><i class="flaticon-user-1"></i> <!-- <?= $this->lang->line("Role as Reseller")?> --> </a></td>
										</tr>
										<?php
										$cnt++;
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
<div class="modal fade" id="assignreseller-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("assign_reseller")?></h4>
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
										<input type="hidden" name="user_id" id="user_id">
										<div class="form-group row">
											<label class="col-sm-4 col-form-label"><?= $this->lang->line("no_of_users")?></label>
											<div class="col-sm-8">
												<input type="text" name="userscount" id="userscount" placeholder="<?= $this->lang->line("no_of_users")?>" class="form-control userscount">
												<span style="color: red;" class="userscount_msg"></span>
											</div>
										</div>
										<div class="form-group row">
												<div class="input-group">
												<label class="col-sm-4 col-form-label"><?= $this->lang->line("ftp_space_limit")?></label>
												<div class="col-sm-4">
													<input type="text" value="<?= $plandata->ftp_space_limit ?? '' ?>" placeholder="<?= $this->lang->line("ftp_space_limit")?>" name="ftp_space_limit" id="ftp_space_limit" class="form-control userscount"/>
												</div>
												<div class="col-sm-4">
													<?php $ftp_unit = $plandata->ftp_unit ?? 0; ?>
													<select id="ftp_unit" name="ftp_unit" class="form-control">
														<option value=""><?= $this->lang->line("ftp_unit")?></option>
														<option <?php if($ftp_unit == "kb") echo "Selected"; ?> value="kb">KB</option>
														<option <?php if($ftp_unit == "mb") echo "Selected"; ?> value="mb">MB</option>
														<option <?php if($ftp_unit == "gb") echo "Selected"; ?> value="gb">GB</option>
														<option <?php if($ftp_unit == "tb") echo "Selected"; ?> value="tb">TB</option>
													</select>
												</div>
												</div>
											</div>
											<div class="form-group row">
												<div class="input-group">
												<label class="col-sm-4 col-form-label"><?= $this->lang->line("db_space_limit")?></label>
												<div class="col-sm-4">
													<input type="text" value="<?= $plandata->sql_space_limit ?? '' ?>" placeholder="<?= $this->lang->line("db_space_limit")?>" name="sql_space_limit" id="sql_space_limit" class="form-control userscount"/>
												</div>
												<div class="col-sm-4">
													<?php $db_unit = $plandata->db_unit ?? 0; ?>
													<select id="db_unit" name="db_unit" class="form-control">
														<option value=""><?= $this->lang->line("db_unit")?></option>
														<option <?php if($db_unit == "kb") echo "Selected"; ?> value="kb">KB</option>
														<option <?php if($db_unit == "mb") echo "Selected"; ?> value="mb">MB</option>
														<option <?php if($db_unit == "gb") echo "Selected"; ?> value="gb">GB</option>
														<option <?php if($db_unit == "tb") echo "Selected"; ?> value="tb">TB</option>
													</select>
												</div>
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
        }
    });

    $("input.userscount").keypress(function(event) {
  		return /\d/.test(String.fromCharCode(event.keyCode));
	});

	$("#projform").on('submit', function(e){
		e.preventDefault();
		var userscount = $("#userscount").val();
		var ftp_space_limit = $("#ftp_space_limit").val();
		var ftp_unit = $("#ftp_unit").val();
		var sql_space_limit = $("#sql_space_limit").val();
		var db_unit = $("#db_unit").val();

		if(userscount == ""){
			swal("<?= $this->lang->line("enter_count_of_how_many_user_can_add_by_reseller") ?>", {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
		}else if(ftp_space_limit == ""){

			swal("<?= $this->lang->line("enter_FTP_space") ?>", {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
		}else if(ftp_unit == ""){
			swal("<?= $this->lang->line("select_FTP_unit") ?>", {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
		}else if(sql_space_limit == ""){

			swal("<?= $this->lang->line("enter_DB_space") ?>", {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
		}else if(db_unit == ""){

			swal("<?= $this->lang->line("select_db_unit") ?>", {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
		}else{
		    $.ajax({
		           url:"<?php echo base_url();?>admin/resellers/assign",
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
		}
	});
});


function approve_reseller(user_id){
	$("#user_id").val(user_id);
	$("#assignreseller-Modal").modal("show");
		
}

</script>

<?php $this->load->view("admin/layout/footer_new");?>
