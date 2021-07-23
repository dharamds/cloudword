<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<link rel="stylesheet" href="<?=base_url()?>/public/public/assets/css/site-demos.css">
<div class="container-fluid">
	<div class="row mr-0">

	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?=$this->lang->line("new_project")?> </h3>
				</div>
				<div class="flex-col-md-6 text-right">
					<!-- <a class="btn btn-primary" href="<?php //echo base_url();?>admin/projects/create"> New Project </a>  -->
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
								<form id="createform" method="post" >
									<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
									<fieldset title="Step 1">
									<!-- <legend class="block-heading mb-4"></legend> -->
										<div class="form-group row">
											<label for="fieldname" class="col-md-4 control-label text-right"><?=$this->lang->line("project_name")?></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="project_name" id="project_name" placeholder="<?=$this->lang->line("project_name")?>" onkeyup="addslug(this.value)">
												<span id="project_name_msg" class="errmsg" style="color: red;"></span>
											</div>
										</div>

										<div class="form-group row">
											<label for="fieldemail" class="col-md-4 control-label text-right"><?=$this->lang->line("slug")?></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="slug" id="slug" placeholder="<?=$this->lang->line("slug")?>">
												<span id="slug_msg" class="errmsg" style="color: red;"></span>
											</div>
										</div>
										<div class="form-group row">
											<label for="fieldemail" class="col-md-4 control-label text-right"><?=$this->lang->line("project_url")?></label>
											<div class="col-md-7">
												<input type="text" class="form-control" name="url" id="url" placeholder="<?=$this->lang->line("project_url")?>">
												<span id="project_url_msg" class="errmsg" style="color: red;"></span>
											</div>
										</div>
									
									</fieldset>
									<div class="row">
										<div class="col-sm-11">
											<input style="float: right;" id="submitform" type="submit" class="finish btn-primary btn-primary-lms btn" value="<?=$this->lang->line("submit")?>" />
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
<script src="<?=base_url()?>/public/public/assets/js/jquery.validate.min.js"></script>
<script src="<?=base_url()?>/public/public/assets/js/additional-methods.min.js"></script>
<script type="text/javascript">
	jQuery.validator.setDefaults({
  			debug: true,
  			success: "valid"
		});

	$("#createform").validate({
		  rules: {
		    url: {
		      required: true,
		      url: true
		    },
		    project_name:{
		    	required: true
		    },
		    slug:{
		    	required: true
		    }
		  }
		});
		function validateform(){
			var project_name = $("#project_name").val();
			var slug = $("#slug").val();
			if(project_name == ""){
				$("#project_name_msg").html("<?=$this->lang->line("project_name_blank")?>")
				return false;
			}else if(slug == ""){
				$("#project_name_msg").html("");
				$("#slug_msg").html("<?=$this->lang->line("project_slug_blank")?>");
				return false;
			}else{
				$("#project_name_msg").html("");
				$("#slug_msg").html("");
				return true;
			}
		}
			$("#createform").on('submit', function(e){
				        e.preventDefault();
				        if(validateform() == true){
				    	$.ajax({
				           url:"<?php echo base_url();?>admin/projects/save",
						   beforeSend:function(data){
								//$('#submitform').prop('disabled', true);
						   },
				           type:"post",
				            data: new FormData(this),
				            dataType: 'json',
				            contentType: false,
				            cache: false,
				            processData:false,
				                    success:function(data){
				                        if(data.status == "success"){
				                            alert(data.msg);
				                            window.location.replace("<?php echo base_url();?>admin/projects/");
											$('#submitform').prop('disabled', false);
				                        }else{
				                        	 $("#cover-spin").hide();
				                        	 $(".errmsg").html("");
				                        	$.each(data.error_data, function (key, val) {
										        $("#"+key).html(val);
										    });
										    $('#submitform').prop('disabled', false);
				                        }

				                     }
				                });
				    	}
				   	 });


			function addslug(fff){
					string = fff.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g,'_');
					$("#slug").val(string);
			}
</script>
<?php $this->load->view("admin/layout/footer_new");?>