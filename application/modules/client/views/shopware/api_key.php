<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<div class="container-fluid">
	<div class="row mr-0">
		<div class="row">                    
			<div class="col-md-12">
				<div class="filter-container flex-row">
					<div class="flex-col-md-6">
						<h3 class="filter-content-title"><?= $this->lang->line("add_shopware_url") ?> </h3>
					</div>
					<div class="flex-col-md-6 text-right">
						
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
								<form id="apikeyform" method="post" action="<?php echo base_url();?>client/shopware/save_api_key" enctype="multipart/form-data">
									<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
									<fieldset title="Step 1">
										<div class="form-group row">
											<label for="fieldname" class="col-lg-3 control-label text-right"><?= $this->lang->line("domain_url") ?><span class="text-danger">*</span></label>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="domain_url" id="domain_url" placeholder="<?= $this->lang->line("domain_url") ?>" value="<?= $domain_url?>" >
											</div>
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
<script type="text/javascript">

	$("#apikeyform").on('submit', function(e){
				        e.preventDefault();
				        var domain_url = $("#domain_url").val();
				        if(domain_url != ""){
				    	$.ajax({
				           url:"<?php echo base_url();?>client/shopware/save_key",
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
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
    											location.reload();
											}); 
				                        }else{
				                        	$("#passvererrormsg").html(data.msg);
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
				    							swal("<?php echo $this->lang->line("domain_blank")?>", {
												title: "<?= $this->lang->line("oops") ?>",
												type: "error",
												timer: 3000
											})
											return false;
				    	}
	});



</script>
<?php $this->load->view("client/layout/footer_new");?>