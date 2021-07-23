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
					<h3 class="filter-content-title"><?=$this->lang->line("general_site_setting")?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
					
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<div class="table-responsive">
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
							<thead>
								<tr>
									<th>
										<label class="checkbox-tel"><input type="checkbox" class="select_all"></label>
									</th>
									<th><?=$this->lang->line("name")?></th>
									<th><?=$this->lang->line("slug")?></th>
									<th><?=$this->lang->line("value")?></th>
									<th><?=$this->lang->line("action")?></th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($settings) > 0){
									$cnt = 1;
									foreach ($settings as $set){				
										?>
										<tr>
											<td><?= $cnt ?></td>
											<td><?= ucfirst($set->name) ?></td>
											<td><?= $set->slug?></td>
											<td><?= $set->name_value?></td>
											<td>
												<a style="min-width: 40px;" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary" href="javascript:" onclick='update_setting(<?php echo json_encode($set);?>)'><i class="flaticon-edit"></i> </a>
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
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>
<!-- Update Setting Modal -->
<div class="modal fade" id="setting-modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?=$this->lang->line("up_setting")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="currenciesdef" style="display: none;"> 
					<?php foreach ($currencies as $cur) {
							echo '<option value="'.$cur->code.'">'.$cur->code.'('.$cur->currency_symbol.')'.'</option>';
					}
					?>
				</div>

				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="projform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="setting_id" id="setting_id">

										<div class="form-group row">
											<label class="col-sm-3 col-form-label" id="Update_label"></label>
											<div class="col-sm-9" id="name_valuedefine">

											</div>
										</div>
										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="projerrormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?=$this->lang->line("close")?></button>
												<button type="submit" class="btn btn-primary m-b-0"><?=$this->lang->line("up_setting")?></button>
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
	function update_setting(data){
		$("#setting_id").val(data.setting_id)
		$("#Update_label").html(data.name);
		var htdef = '';
		if(data.setting_id == 15){
			htdef += '<select id="name_value" name="name_value" class="form-control">';
			htdef += $("#currenciesdef").html();
			htdef +='</select>';
			$("#name_valuedefine").html(htdef);
		}else{
					htdef += '<div id="normalvalue">';
					htdef += '<input type="text" name="name_value" id="name_value" placeholder="<?php echo $this->lang->line("up_value"); ?>" class="form-control">';
					htdef += '<span style="color: red;" class="name_value_msg"></span>';
					htdef += '</div>';
					$("#name_valuedefine").html(htdef)
		}
		$("#name_value").val(data.name_value);
		$("#setting-modal").modal("show");

	}


	$(function () {

		$("#projform").on('submit', function(e){
	        e.preventDefault();

	        var name_value = $("#name_value").val();
	        if(name_value != ""){
	        	$("#projerrormsg").html("");

	    	$.ajax({
	           url:"<?php echo base_url();?>admin/settings/update",
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
	                    	$("#name_value_msg").html(data.msg);
	                    	swal(data.msg, {
								title: "<?= $this->lang->line("oops") ?>",
								type: "error",
								timer: 3000
							})
	                    }
	                       
	                 }
	            });
	    	}else{
	    		$("#name_value_msg").html("<?php echo $this->lang->line("value_blank"); ?>");
	    	}
		});
	})
	
</script>
<?php $this->load->view("admin/layout/footer_new");?>
