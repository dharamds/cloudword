<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<link rel="stylesheet" href="https://jqueryvalidation.org/files/demo/site-demos.css">
<div class="container-fluid">
<div class="row mr-0">

	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("edit_shopware_project")?> </h3>
				</div>
				<div class="flex-col-md-6 text-right">
					<!-- <a class="btn btn-primary" href="<?php //echo base_url();?>client/projects/create"> New Project </a>  -->
				</div>
			</div>
		</div>        
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="panel panel-default panel-grid">
				<div class="panel-heading brd-0 m-0 pt-0"></div>
				<div class="panel-body pt-0">
					<div class="wizard-panel">

						<div class="row">
							<div class="col-md-8 col-md-offset-2">

								<form id="updateform" method="post" action="<?php echo base_url();?>client/shopware/update" enctype="multipart/form-data">
									<p style="color: red"><?php echo isset($error_data["errors"]) ? $error_data["errors"] : '' ;  ?></p>
									<p style="color: green"><?php echo isset($msg) ? $msg : '' ;  ?></p>
									<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
									<input type="hidden" name="sproject_id" id="sproject_id" value="<?= $projdata->sproject_id?>">
								<fieldset title="Step 1">
									<!-- <legend class="block-heading mb-4"></legend> -->
										<div class="form-group row">
											<label for="fieldname" class="col-md-3 control-label"><?= $this->lang->line("shopware_proj_name") ?><span class="text-danger">*</span></label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="project_name" id="project_name" placeholder="<?= $this->lang->line("shopware_proj_name") ?>" value="<?php echo isset($pdata["project_name"]) ? $pdata["project_name"] : $this->encryption->decrypt($projdata->project_name);  ?>">
												<span id="project_name_msg" style="color: red"> <?php echo isset($error_data["project_name"]) ? $error_data["project_name"] : "";  ?> </span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldname" class="col-md-3 control-label"><?= $this->lang->line("shopware_proj_url") ?><span class="text-danger">*</span></label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="url" id="url" placeholder="<?= $this->lang->line("shopware_proj_url") ?>" value="<?php echo isset($pdata["url"]) ? $pdata["url"] : $this->encryption->decrypt($projdata->url);  ?>">
												<span id="url_msg" style="color: red"><?php echo isset($error_data["url"]) ? $error_data["url"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldemail" class="col-md-3 control-label"><?= $this->lang->line("access_key_id") ?><span class="text-danger">*</span></label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="key_id" id="key_id" placeholder="<?= $this->lang->line("access_key_id_msg") ?>" value="<?php echo isset($pdata["key_id"]) ? $pdata["key_id"] : $this->encryption->decrypt($projdata->key_id);  ?>">
												<span id="key_id_msg" style="color: red"><?php echo isset($error_data["key_id"]) ? $error_data["key_id"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldemail" class="col-md-3 control-label"><?= $this->lang->line("secret_access_key")?><span class="text-danger">*</span></label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="access_key" id="access_key" placeholder="<?= $this->lang->line("secret_access_key_msg") ?>" value="<?php echo isset($pdata["access_key"]) ? $pdata["access_key"] : $this->encryption->decrypt($projdata->access_key);  ?>">
												<span id="access_key_msg" style="color: red"><?php echo isset($error_data["access_key"]) ? $error_data["access_key"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldemail" class="col-md-3 control-label"><?= $this->lang->line("shopware_project_version")?></label>
											<div class="col-md-8">
												<select id="version" name="version" class="form-control">
													<option <?php echo ($projdata->version) == 6?'selected': ''; ?> value="6">V6</option>
													<option <?php echo ($projdata->version) == 5?'selected': ''; ?> value="5">V5</option>
												</select>	
												<span id="version_msg" style="color: red"><?php echo isset($error_data["version"]) ? $error_data["version"] : "";  ?></span>
											</div>
										</div>
								</fieldset>
								<div class="form-group row">
									<div class="input-group">
										<div class="col-md-11 text-right">
											<a href="<?= base_url('client/shopware') ?>" class="btn btn-default waves-effect">
												<?php echo $this->lang->line("close") ?>		
											</a>
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
	</div>
</div>

</div>
</div>
<style>
	.help-block{
		color:red;
	}
</style>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
	 
	 $("#updateform").validate({
	 	  rules: {
	 	  	project_name:{
	 	  		required: true
	 	  	},
	 	  	url: {
	 	      required: true,
	 	      url: true
	 	    },
	 	    key_id:{
	 	    	required: true
	 	    },
	 	    access_key:{
	 	    	required: true
	 	    },
	 	    version:{
	 	    	required: true	
	 	    }
	 	  }
	 	});
</script>
<?php $this->load->view("client/layout/footer_new");?>