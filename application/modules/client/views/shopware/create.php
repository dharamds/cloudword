<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<link rel="stylesheet" href="https://jqueryvalidation.org/files/demo/site-demos.css">
<div class="container-fluid">
	<div class="row mr-0">

		<div class="row">                    
			<div class="col-md-12">
				<div class="filter-container flex-row">
					<div class="flex-col-md-6">
						<h3 class="filter-content-title"><?= $this->lang->line("new_shop_project") ?> </h3>
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
				<!-- <div class="panel-heading brd-0 m-0 pt-0"></div> -->
				<div class="panel-body pt-0">
					<div class="wizard-panel">

						<div class="row">
							<div class="col-md-8 col-md-offset-2">
								<form id="createform" method="post" action="<?php echo base_url();?>client/shopware/save" enctype="multipart/form-data">
									<p style="color: red"><?php echo isset($error_data["errors"]) ? $error_data["errors"] : '' ;  ?></p>
									<p style="color: green"><?php echo isset($msg) ? $msg : '' ;  ?></p>
									<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
									<fieldset title="Step 1">
										<!-- <legend class="block-heading mb-4"></legend> -->
										<div class="form-group row">
											<?php
											$ver = 6;
											if(isset($pdata["version"])){
												$ver = $pdata["version"];
											}
											?>
											<label for="fieldemail" class="col-md-4 control-label text-right"> <?= $this->lang->line("shopware_project_version") ?>  </label>
											<div class="col-md-7">
												<select id="version" name="version" class="form-control" onchange="checkversion(this.value)">
													<option value="6" <?php if($ver == 6){ echo "selected"; }?>>V6</option>
													<option value="5" <?php if($ver == 5){ echo "selected"; }?>>V5</option>
												</select>	
												<span id="version_msg" style="color: red"><?php echo isset($error_data["version"]) ? $error_data["version"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldname" class="col-md-4 control-label text-right"><?= $this->lang->line("shopware_proj_name") ?><span class="text-danger">*</span></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="project_name" id="project_name" placeholder="<?= $this->lang->line("shopware_proj_name") ?>" value="<?php echo isset($pdata["project_name"]) ? $pdata["project_name"] : "";  ?>" data-errormessage-value-missing="Please input something">
												<span id="project_name_msg" style="color: red"> <?php echo isset($error_data["project_name"]) ? $error_data["project_name"] : "";  ?> </span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldname" class="col-md-4 control-label text-right"><?= $this->lang->line("shopware_proj_url") ?><span class="text-danger">*</span></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="url" id="url" placeholder="<?= $this->lang->line("shopware_proj_url") ?>" value="<?php echo isset($pdata["url"]) ? $pdata["url"] : "";  ?>">
												<span id="url_msg" style="color: red"><?php echo isset($error_data["url"]) ? $error_data["url"] : "";  ?></span>
											</div>
										</div>
										
										<div class="form-group row">
											<label for="fieldemail" class="col-md-4 control-label text-right"><span id="username">
												<?= $ver == 6 ? $this->lang->line("access_key_id") : $this->lang->line("username");  ?> 

												<?= $this->lang->line("access_key_id") ?></span><span class="text-danger">*</span></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="key_id" id="key_id" placeholder="<?= $this->lang->line("access_key_id_msg") ?>" value="<?php echo isset($pdata["key_id"]) ? $pdata["key_id"] : "";  ?>">
												<span id="key_id_msg" style="color: red"><?php echo isset($error_data["key_id"]) ? $error_data["key_id"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldemail" class="col-md-4 control-label text-right"><span id="api_key">
												<?= $ver == 6 ? $this->lang->line("secret_access_key") : $this->lang->line("api_key");  ?>	
											</span><span class="text-danger">*</span></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="access_key" id="access_key" placeholder="<?= $this->lang->line("secret_access_key_msg") ?>" value="<?php echo isset($pdata["access_key"]) ? $pdata["access_key"] : "";  ?>">
												<span id="access_key_msg" style="color: red"><?php echo isset($error_data["access_key"]) ? $error_data["access_key"] : "";  ?></span>
											</div>
										</div>
										<div class="form-group row" id="shopware_5_instruction" style="<?= $ver == 5 ? "display: show;" : "display: none;";  ?> ">
											<p>Shopware 5 API Key instruction :</p>
<p>Click on <strong><strong>&ldquo;Configuration&rdquo;</strong></strong>&nbsp;from menu bar then click on <strong><strong>&ldquo;User administration&rdquo;</strong></strong>&nbsp;from dropdown list, then you can click <strong><strong>&ldquo;Add User&rdquo; </strong></strong>&nbsp;or <strong><strong>&ldquo;Edit User&rdquo; </strong></strong>then check API access as <strong><strong>&ldquo;Enabled&rdquo; &nbsp;</strong></strong>then you have to &ldquo;<strong><strong>save/ update&rdquo; </strong></strong>user , make sure that generated API key you have to use it in this form as <strong><strong>&ldquo;API Key&rdquo;.</strong></strong></p>
										</div>
										<div class="form-group row" id="shopware_6_instruction" style="<?= $ver == 6 ? "display: show;" : "display: none;";  ?>" >
											<p>Shopware 6 instruction :</p>
<p><strong><strong>&nbsp;</strong></strong>Click on <strong><strong>&ldquo;Settings&rdquo;</strong></strong>&nbsp;from side bar then click &ldquo;<strong><strong>System=&gt;Integrations&rdquo; &nbsp;</strong></strong>then you can add integration or update integration <strong><strong>&ldquo;select Roles&rdquo; </strong></strong>then click on <strong><strong>&ldquo;Create New API Key &ldquo;</strong></strong>&nbsp;then copy those <strong><strong>&ldquo;Access Key ID&rdquo;</strong></strong>&nbsp;&amp; <strong><strong>&ldquo;Secret Access Key&rdquo;</strong></strong>&nbsp;and <strong><strong>save/update</strong></strong>&nbsp;integration, those copied you have to provide it in Cloud Service World Dashboard.</p>
										</div>
									</fieldset>
									<div class="form-group row">
										<div class="input-group">
											<div class="col-md-11 text-right">
												<a href="<?= base_url('client/shopware') ?>" class="btn btn-default waves-effect"><?php echo $this->lang->line("close") ?></a>
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
<!-- <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
 --><script type="text/javascript">
 function checkversion(ver){
 	var key_idmsg = '';
 	var access_keymsg = '';
 	if(ver == 5){
 		$("#username").html("<?php echo $this->lang->line("username") ?>")
		$("#api_key").html("<?php echo $this->lang->line("api_key") ?>");
		$("#key_id").attr("placeholder","<?php echo $this->lang->line("username") ?>")
		$("#access_key").attr("placeholder","API Key")

		$("#shopware_5_instruction").show();
		$("#shopware_6_instruction").hide();
		

 	}else if(ver == 6){
 		$("#username").html("<?php echo $this->lang->line("access_key_id") ?>")
		$("#api_key").html("<?php echo $this->lang->line("secret_access_key") ?>")
		$("#key_id").attr("placeholder","<?php echo $this->lang->line("access_key_id") ?>")
		$("#access_key").attr("placeholder","<?php echo $this->lang->line("secret_access_key") ?>")
		$("#shopware_5_instruction").hide();
		$("#shopware_6_instruction").show();
		key_idmsg += "<?= $this->lang->line("shopware_proj_key_id_msg");?>";
		access_keymsg += "<?= $this->lang->line("shopware_proj_access_key_msg");?>";

 	}
 }

 function goBack() {
  window.history.back();
}
$(function () {

	var key_idmsg = '';
 	var access_keymsg = '';
 	ver = $("#version").val();
 	if(ver == 5){
 		key_idmsg += "<?= $this->lang->line("shopware_proj_username_msg");?>";
		access_keymsg += "<?= $this->lang->line("shopware_proj_api_key_msg");?>";
 	}else{
 		key_idmsg += "<?= $this->lang->line("shopware_proj_key_id_msg");?>";
		access_keymsg += "<?= $this->lang->line("shopware_proj_access_key_msg");?>";
 	}

	 $("#createform").validate({
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
	 	  },
	 	  messages: {
	 	  	project_name:{
	 	  		required: "<?= $this->lang->line("shopware_proj_name_msg");?>"
	 	  	},
	 	  	url: {
	 	      required: "<?= $this->lang->line("shopware_proj_url_msg");?>",
	 	      url: "<?= $this->lang->line("Please enter valid URL");?>"
	 	    },
	 	    key_id:{
	 	    	required: key_idmsg
	 	    },
	 	    access_key:{
	 	    	required: access_keymsg
	 	    },
	 	    version:{
	 	    	required: "<?= $this->lang->line("This field is required");?>"
	 	    }
	 	  }
	 	});
		// function validateform(){
		// 	$("#createform").on('submit', function(e){
		// 		        e.preventDefault();
		// 		        if(validateform() == true){
		// 		    	$.ajax({
		// 		           url:"<?php //echo base_url();?>client/shopware/save",
		// 		           type:"post",
		// 		            data: new FormData(this),
		// 		            dataType: 'json',
		// 		            contentType: false,
		// 		            cache: false,
		// 		            processData:false,
		// 		                    success:function(data){
		// 		                        if(data.status == "success"){
		// 		                            alert(data.msg);
		// 		                            window.location.replace("<?php echo base_url();?>client/projects/")
		// 		                        }else{
		// 		                        	alert(data.msg);
		// 		                        }     
		// 		                     }
		// 		                });
		// 		    	}
		// 		   	 });


	})
</script>
<?php $this->load->view("client/layout/footer_new");?>