<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
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
					<h3 class="filter-content-title"><?= $this->lang->line("site_setting");?></h3>
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
					<!-- <div class="panel-heading panel-button">
						<div class="flex-row">
							<div class="flex-col-auto">
								
							</div>
						</div>
					</div> -->
					<div class="panel-body no-padding p-0">
						<div class="table-responsive">
						<table id="usrtable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<th>
										<label class="checkbox-tel"><input type="checkbox" class="select_all"></label>
									</th>
									<th><?=$this->lang->line("name")?></th>
									<th><?=$this->lang->line("value")?></th>
									<th><?=$this->lang->line("action")?></th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if(!empty($settings) > 0){
									$cnt = 1;
									$rm = array("reseller_id","setting_id","updated_date");

									foreach($settings as $set => $data){		
										if(in_array($set,$rm)){
											continue;
										}else{


										?>
										<tr>
											<td><?= $cnt ?></td>
											<td id="key_<?=$cnt?>"><?= $this->lang->line($set) ?></td>
											<td id="value_<?=$cnt?>"><?=  $data?></td>
											<td>
												<a href="javascript:" onclick='update_setting(<?=$cnt?>,"<?= $set ?>","<?= !empty($data) ? $data : 0 ?>")'><i class="flaticon-edit"></i> </a>
											</td>
										</tr>
										<?php
										$cnt++;

										}
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
										<input type="hidden" name="keydata" id="keydata">
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
	function update_setting(numb,keydata,$val){
		var get = $("#key_"+numb).html();
		$("#keydata").val(keydata);
		$("#Update_label").html(get);
		var htdef = '';
		if(keydata == "currency"){
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
		if($val != "0"){
			$("#name_value").val($val);
		}
		$("#setting-modal").modal("show");

	}

					$("#projform").on('submit', function(e){
				        e.preventDefault();
				        var name_value = $("#name_value").val();
				        if(name_value != ""){
				        	$("#projerrormsg").html("");
				    	$.ajax({
				           url:"<?php echo base_url();?>client/settings/update",
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
												$("#name_value_msg").html("");
				                            	location.reload();
											})


				                            
				                        }else{
				                        	$("#name_value_msg").html(data.msg);
				                        		swal(data.msg, {
                                                  title: "<?= $this->lang->line("oops")?>",
                                                  type: "error",
                                                  timer: 3000
                                                });
				                        }
				                        $("#cover-spin").hide()     
				                     }
				                });
				    	}else{
				    		$("#name_value_msg").html("<?php echo $this->lang->line("value_blank"); ?>");
				    	}
	});

</script>
<?php $this->load->view("client/layout/footer_new");?>
