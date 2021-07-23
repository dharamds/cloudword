<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("add_new_plan")?></h3>
				</div>
				<div class="flex-col-6 text-right">
					<a class="btn btn-primary"  onclick="backToList()">
						<?= $this->lang->line("back");?>
					</a> 
				</div>
				<div>
					<?php echo validation_errors(); ?>
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<div class="row mt-4">
							<div class="col-lg-10 col-lg-offset-1">
								<div class="card">
									<div class="card-block">
										<form id="planaddform" action="<?= base_url();?>admin/plan/add/<?= $id ?>" method="post" enctype="multipart/form-data">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<div class="form-group row">
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("plan_name")?> <span class="text-danger">*</span></label>
												<div class="col-sm-6">
													<input type="text" value="<?= $plandata->name ?? '' ?>" class="form-control" name="name" placeholder="<?= $this->lang->line("Enter Plan Name") ?>">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("description")?> <span class="text-danger">*</span></label>
												<div class="col-sm-6">
													<textarea class="form-control" name="description" placeholder=" <?= $this->lang->line("Enter Description")?> " ><?= $plandata->description ?? '' ?></textarea>
												</div>
											</div>
											<div class="form-group row">
												<div class="input-group">
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("ftp_space_limit")?><span class="text-danger">*</span></label>
												<div class="col-sm-3">
													<input type="text" value="<?= $plandata->ftp_space_limit ?? '' ?>" placeholder="<?= $this->lang->line("ftp_space_limit")?>" name="ftp_space_limit" class="form-control"/>
												</div>
												<div class="col-sm-3">
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
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("db_space_limit")?><span class="text-danger">*</span></label>
												<div class="col-sm-3">
													<input type="text" value="<?= $plandata->sql_space_limit ?? '' ?>" placeholder="<?= $this->lang->line("db_space_limit")?>" name="sql_space_limit" class="form-control"/>
												</div>
												<div class="col-sm-3">
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
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("price") ?><span class="text-danger">*</span></label>
												<div class="col-sm-6">
													<input type="text" value="<?= $plandata->price ?? '' ?>" placeholder="<?= $this->lang->line("price")?>" name="price" class="form-control"/>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-3 col-form-label"><?= $this->lang->line("time_period")?></label>
												<div class="col-sm-3">
													<input type="text" value="<?= $plandata->time_period ?? '' ?>" placeholder="<?= $this->lang->line("time_period")?>" name="time_period" id="time_period" class="form-control userscount"/>
												</div>
												<div class="col-sm-3">
													<?php $period = $plandata->period ?? 0; ?>
													<select id="period" name="period" class="form-control">
															<option <?php if($period == "month") echo "Selected"; ?> value="month"><?= $this->lang->line("Months")?></option>
															<option <?php if($period == "year") echo "Selected"; ?> value="year"><?= $this->lang->line("Years")?></option>
													</select>	
												</div>
											</div>
											<div class="form-group row">
					                           <label class="col-sm-3 col-form-label"><?= $this->lang->line("icon")?></label>
					                           <div class="col-md-3">
					                            <img src="<?= (isset($plandata->icon) && !empty($plandata->icon)) ? base_url('uploads/plan/'.$plandata->icon) : '' ?>" id="img_home" style="<?= (isset($plandata->icon) && !empty($plandata->icon)) ? '' : 'display: none;' ?>" width="50" height="50">
					                           </div>
					                           <div class="col-md-3  text-right">
					                              <button type="button" class="btn btn-primary" onclick="$('#icon').trigger('click');"><?= $this->lang->line("upload_icon")?></button>
					                              <input  type="file" name="icon" id="icon" style="display: none;">
					                           </div>
					                        </div>
					                        <div class="form-group row">
					                           <label class="col-md-3 col-form-label"><?= $this->lang->line("assgn_modules")?></label>
					                           <div class="col-lg-9 col-md-9">
					                           	<?php
					                           	$planmodules = $plandata->modules ?? 0;
					                           	$pm =  $planmodules != 0 ? explode(",", $planmodules) : array();
					                           		foreach($moduledata as $r) {
					                           		 		?>
					                           		<div class="col-sm-6">
					                           			<input type="checkbox" name="modules[]" id="module_ids" value="<?= $r->module_id ?>" class="form-check-input" <?php echo in_array($r->module_id,$pm) ? "checked" :"";  ?> > <label class="form-check-label" for="module_ids"><?=  $this->lang->line(ucfirst($r->module_name)) ?></label> 
					                           		</div>
					                           		 		<?php

					                           		 } 
					                           	?>
					                           		
					                           </div>
					                        </div>

											<div class="form-group row">
												<div class="col-sm-12">
													<h3 class="filter-content-title" style="margin: 0;"><?= $this->lang->line("additional_details")?></h3>
												</div>	
					                        </div>

											<div id="additinalInfoBox">
												<?php 
													$count = 1; 
												 if(!empty($additinalInfo)){	
													foreach ($additinalInfo as $ky => $infodata) {
												?>		
													<div class="form-group row" id="infoboxrow<?php echo $count; ?>">
														<div class="col-sm-3">
															<textarea name="additinalInfo[<?php echo $count; ?>][key]" placeholder="<?= $this->lang->line("key_feature")?>" rows="2" cols="4" class="form-control"><?= $infodata->key_feature; ?></textarea>
														</div>
														<div class="col-sm-6">
															<textarea name="additinalInfo[<?php echo $count; ?>][val]" placeholder="<?= $this->lang->line("short_description")?>" rows="2" cols="4" class="form-control"><?= $infodata->short_description; ?></textarea>
														</div>
														<button class="btn btn-danger " type="button" onclick="removeAddInfoRow(<?php echo $count; ?>)"><i class="flaticon-trash"></i></button>
													</div>	
												<?php
													$count++;	
													} 

												 }else{	
												?>
													<div class="form-group row">
														<div class="col-sm-4">
															<textarea name="additinalInfo[<?php echo $count; ?>][key]" placeholder="<?= $this->lang->line("key_feature")?>" rows="2" cols="6" class="form-control"></textarea>
														</div>
														<div class="col-sm-5">
															<textarea name="additinalInfo[<?php echo $count; ?>][val]" placeholder="<?= $this->lang->line("short_description")?>" rows="2" cols="4" class="form-control"></textarea>
														</div>
													</div>

												<?php
												 }
												?>

											</div>
											<div class="form-group row">
												<div class="col-sm-9 text-right">
													<button type="button" id="addMoreInfoBtn" class="btn btn-primary"><?= $this->lang->line("add_more")?></button>
												</div>
											</div>
											<div class="form-group row">
												<div class="input-group">
													<div class="col-sm-6 text-right">
														<button type="button" class="btn btn-default waves-effect" onclick="backToList()"><?= $this->lang->line("close");?></button>
														<button type="submit" class="btn btn-primary m-b-0"><?php echo $this->lang->line("submit")?></button>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- <div class="panel-footer"></div> -->
				</div>
			</div>
		</div>
	</div>

	</div>
</div>
<?php $this->load->view("admin/layout/footer_new");?>
<script type="text/javascript">
	 function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#wrap').hide('fast');
                
                $("#img_home").attr('src',e.target.result);
                
                $('#wrap').show('fast');
                $("#img_home").show();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#icon").change(function(){
        readURL(this);
    });


	
	$("#planaddform").validate({
	rules: {
		name: "required",
		description: "required",
		data_limit: {
			required: true,
			number: true
		},
		ftp_space_limit: {
			required: true,
			number: true
		},
		sql_space_limit: {
			required: true,
			number: true
		},
		price: {
			required: true,
			number: true
		}
	},
	messages: {
		name: "<?= $this->lang->line("name_blank")?>",
		description: "<?= $this->lang->line("Please enter description")?>",
		data_limit:{
		required: "<?= $this->lang->line("Please enter limit")?>",
		number: "<?= $this->lang->line("Please enter numbers only")?>"
    	},
		ftp_space_limit:{
		required: "<?= $this->lang->line("Please enter ftp space limit")?>",
		number: "<?= $this->lang->line("Please enter numbers only")?>"
    	},
		sql_space_limit:{
		required: "<?= $this->lang->line("Please enter sql space limit")?>",
		number: "<?= $this->lang->line("Please enter numbers only")?>"
    	},
		price:{
			required: "<?= $this->lang->line("Please enter price")?>",
			number: "<?= $this->lang->line("Please enter numbers only")?>"
    	} 
	}
	});
	

	function backToList() {
		window.location.replace("<?= base_url()?>admin/plan");
	}


	var cnt = <?php echo $count+1; ?>;

	$('#addMoreInfoBtn').click(function(){
		//alert('gfgghfghfg');
		$("#additinalInfoBox").append('<div class="form-group row" id="infoboxrow'+cnt+'"><div class="col-sm-4"><textarea name="additinalInfo['+cnt+'][key]" placeholder="<?= $this->lang->line("key_feature")?>" rows="2" cols="4" class="form-control"></textarea></div><div class="col-sm-5"><textarea name="additinalInfo['+cnt+'][val]" placeholder="<?= $this->lang->line("short_description")?>" rows="2" cols="4" class="form-control"></textarea></div><button type="button" class="btn btn-danger deleteplan" onclick="removeAddInfoRow('+cnt+')"><i class="flaticon-trash"></i></button></div>');
		cnt++;

	});

	function removeAddInfoRow(rowid){
		if(rowid > 0){
			$('#infoboxrow'+rowid).remove();
		}
	}




</script>