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
					<h3 class="filter-content-title"><?= $this->lang->line("edit_page_templates");?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
					<a class="btn btn-primary"  onclick="return window.history.back()">
						<?= $this->lang->line("back");?>
					</a> 
				</div>
			</div>
		</div>        
	</div>

	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<div class="row mt-3">
							<div class="col-sm-11 col-sm-offset-0">
								<div class="card">
									<div class="card-block">

										<form id="editForm" method="post">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

											<input type="hidden" name="page_id" id="page_id" value="<?= $page_templates->page_id ?>">
											<fieldset class="border p-2">
	   											<h3> <?= $this->lang->line("edit_page_templates");?> </h3>
	   											<hr>

	   											<div class="form-group row">
													<label class="col-sm-4 col-form-label"><?= $this->lang->line("page_title");?><span class="text-danger">*</span> </label>
													<div class="col-sm-4">
														<input type="text" class="form-control" value="<?= $page_templates->title ?>" name="subject" required>
														<span style="color: red;" class="errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-12 col-form-label"><?= $this->lang->line("page_content");?><span class="text-danger">*</span> </label>
													<div class="col-sm-12">
														<textarea class="form-control ckeditor" name="message" id="message"><?= !empty($page_templates->html_template) ? $page_templates->html_template : '' ?></textarea>
														<input type="hidden" name="message_body">
														<span style="color: red;" class="errmsg"></span>
													</div>
												</div>

												<div class="form-group row">
													<div class="input-group">
														<div class="col-sm-6" id="errormsg" style="color: red;"></div>
														<div class="col-sm-6 text-right">
															<a href="<?= base_url('admin/settings/page_templates') ?>" class="btn btn-default waves-effect"><?= $this->lang->line("close");?></a>
															<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("update");?></button>							
														</div>
													</div>
												</div>

											</fieldset>
											
											

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


<script type="text/javascript">
	
	$(function () {
		initCkeditor();
	})

	$("#editForm").on('submit', function(e){
        e.preventDefault();
        //console.log(CKEDITOR.instances.message.getData())
        $('#message').val(CKEDITOR.instances.message.getData())

        if (CKEDITOR.instances.message.getData() == '') {

        	swal('<?= $this->lang->line("required_field");?>', {
				title: "<?= $this->lang->line("oops") ?>",
				type: "error",
				timer: 3000
			})
        	return false
        }

    	$.ajax({
			url:"<?php echo base_url();?>admin/settings/page_template_update",
			type:"post",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend:function () {
            	$("#cover-spin").show();
            },
            success:function(data){
            	$("#cover-spin").hide();
                if(data.status == "success"){
                    swal(data.msg, {
						title: "<?= $this->lang->line("great") ?>",
						type: "success",
						timer: 3000
					}).then(() => {
						window.location.replace("<?= base_url()?>/admin/settings/page_templates");
					})
                    
                }else{
                	
                	 swal(data.msg, {
						title: "<?= $this->lang->line("oops") ?>",
						type: "error",
						timer: 3000
					})

                }

            }

        });

   	});

	 
		
	function backToList() {
		window.location.replace("<?= base_url()?>admin/settings/page_templates");
	}
	
</script>
<?php $this->load->view("admin/layout/footer_new");?>