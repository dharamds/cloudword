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
					<h3 class="filter-content-title"><?= $this->lang->line("contact_reply");?></h3>
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

										<form id="replyForm" method="post">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<input type="hidden" name="contact_id" value="<?= $contact_id ?>">
											<fieldset class="border p-2">
	   											<h3> <?= $this->lang->line("reply_msg");?> </h3>
	   											<hr>

												<div class="form-group row">
													<label class="col-sm-12 col-form-label"><?= $this->lang->line("message");?> <span class="text-danger">*</span></label>
													<div class="col-sm-12">
														<textarea class="form-control" name="message" id="message" required=""></textarea>
														<span style="color: red;" class="errmsg"></span>
													</div>
												</div>

											</fieldset>
											
											<div class="form-group row">
												<div class="input-group">
													<div class="col-sm-6" id="errormsg" style="color: red;"></div>
													<div class="col-sm-6 text-right">
														<button type="button" class="btn btn-default waves-effect" onclick="backToList()"><?= $this->lang->line("close");?></button>
														<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("send");?></button>							
													</div>
												</div>
											</div>

										</form>
									</div>
								</div>
							</div>

							<div class="col-sm-11 col-sm-offset-0">
								<div class="card">
									<div class="card-block">
										<h3><?= $this->lang->line("old_replies");?></h3>

										<?php if (!empty($contactdata)): ?>
											<table class="table">
												<thead>
													<th><?= $this->lang->line("admin");?></th>
													<th><?= $this->lang->line("message");?></th>
													<th><?= $this->lang->line("date");?></th>
												</thead>
												<tbody>
													<?php foreach ($contactdata as $value): 

														$cldata = $this->db->where("client_id",$value->client_id)->get('client')->row();
														?>
														<tr>
															<td><?= $cldata->fname." ".$cldata->lname ?></td>
															<td><?= $value->reply_msg ?></td>
															<td><?= displayDate($value->date) ?></td>
														</tr>
													<?php endforeach ?>
												</tbody>
											</table>

										<?php else: ?>
											<span class="text-secondary"><?= $this->lang->line("no_old_replies");?></span>
										<?php endif ?>
										


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
	


	$("#replyForm").on('submit', function(e){
        e.preventDefault();

        var btn  = $(this).find('button[type="submit"]')

    	$.ajax({
			url:"<?php echo base_url();?>admin/contacts/reply/save",
			type:"post",
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend:function () {
            	$(btn).html('Please wait...')
            	$("#cover-spin").show();
            },
            success:function(data){
            	$("#cover-spin").hide();
                if(data.status == "success"){

                    swal(data.msg, {
						title: "<?= $this->lang->line("great") ?>",
						type: "success",
						buttons: true,
						timer: 3000
					}).then(() => {
						$("#errormsg").html("");
                    	window.location.replace("<?= base_url()?>/admin/contacts");
					})

                    
                }else{
                	 
                	swal(data.msg, {
						title: "<?= $this->lang->line("oops") ?>",
						type: "error",
						buttons: true,
						timer: 3000
					})
                }
                     
             }
        });
   	});

	 
		
	function backToList() {
		window.location.replace("<?= base_url()?>admin/contacts");
	}
	
</script>
<?php $this->load->view("admin/layout/footer_new");?>