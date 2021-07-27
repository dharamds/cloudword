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
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("create")." ".$this->lang->line("new_customer");?></h3>
				</div>
				<div class="flex-col-6 text-right">
					<a class="btn btn-primary"  onclick="goBack()">
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
										<form id="userCreateForm" method="post">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<input type="hidden" name="euser_id" id="euser_id">
											<fieldset class="border p-2">
   											<h3> <?= $this->lang->line("customer")." ".$this->lang->line("personal_details");?> </h3>
   											<hr>
   											<p style="color: red;" class="user_count_msg errmsg" ></p>
   											<p style="color: red;" class="ftp_space_not_available_msg errmsg" ></p>
   											<p style="color: red;" class="db_space_not_available_msg errmsg" ></p>
											<div class="form-group row">

												<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("First_Name");?><span class="text-danger">*</span> </label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="f_name" id="f_name" placeholder="<?= $this->lang->line("First_Name");?>">
													<span style="color: red;" class="f_name_msg errmsg"></span>
												</div>
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("Last_Name");?><span class="text-danger">*</span></label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="l_name" id="l_name" placeholder="<?= $this->lang->line("Last_Name");?>">
													<span style="color: red;" class="l_name_msg errmsg"></span>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("Phone");?><span class="text-danger">*</span></label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="phone" id="phone" placeholder="<?= $this->lang->line("Phone");?>">
													<span style="color: red;" class="phone_msg errmsg"></span>
												</div>
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("email");?><span class="text-danger">*</span> </label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="email" id="email" placeholder="<?= $this->lang->line("email");?>">
													<span style="color: red;" class="email_msg errmsg"></span>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("City");?><span class="text-danger">*</span></label>
												<div class="col-sm-4">
													<input type="text" id="city"  name="city" class="form-control" placeholder="<?= $this->lang->line("City");?>">
													<span style="color: red;" class="city_msg errmsg"></span>
												</div>
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("Address");?><span class="text-danger">*</span></label>
												<div class="col-sm-4">
													<textarea id="address"  name="address" class="form-control" placeholder="<?= $this->lang->line("Address");?>"></textarea>
													<span style="color: red;" class="address_msg errmsg"></span>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("Zip_Code");?><span class="text-danger">*</span> </label>
												<div class="col-sm-4">
													<input type="text" id="zipcode"  name="zipcode" class="form-control" placeholder="<?= $this->lang->line("Zip_Code");?>">
													<span style="color: red;" class="zipcode_msg errmsg"></span>
												</div>
											</div>
											</fieldset>
											<fieldset class="border p-2">
												<h3> <?= $this->lang->line("company_information");?></h3>
   												<hr>
												<div class="form-group row">
													<div class="col-sm-12">
														<input type="checkbox" name="check_company" id="check_company" onclick="showcompany_block();"> <?= $this->lang->line("customer_company_sure");?>
													</div>	
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_name");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_name"  name="company_name" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_name");?>" readonly>
														<span style="color: red;" class="company_name_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_vat_number");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_vat_number"  name="company_vat_number" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_vat_number");?>" readonly>
														<span style="color: red;" class="company_vat_number_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_street");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_street"  name="company_street" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_street");?>" readonly>
														<span style="color: red;" class="company_street_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_town");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_town"  name="company_town" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_town");?>" readonly>
														<span style="color: red;" class="company_town_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_zipcode");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_zipcode"  name="company_zipcode" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_zipcode");?>" readonly>
														<span style="color: red;" class="company_zipcode_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_country");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_country"  name="company_country" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_country");?>" readonly>
														<span style="color: red;" class="company_country_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
												<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_responsible_person");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_responsible_person"  name="company_responsible_person" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_responsible_person");?>" readonly>
														<span style="color: red;" class="company_responsible_person_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("upload_company_logo");?></label>
													<div class="col-sm-4">
														<input style="display: none;" type="file" id="company_logo"  name="company_logo" class="form-control" placeholder="<?= $this->lang->line("company_logo");?>" disabled>
														<button type="button" onclick='$("#company_logo").click();' class="btn btn-primary"><?= $this->lang->line("upload_company_logo");?></button>
														<span style="color: red;" class="company_logo_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_logo");?></label>
													<div class="col-sm-4">
														<img src="<?php echo base_url() ?>public/public/assets/img//company.jpg" id="company_logo_image" name="company_logo_image" width="100" height="100" style="display: none;">
													</div>
												</div>
											</fieldset>
											<fieldset class="border p-2">
												<h3><?= $this->lang->line("user")." ".$this->lang->line("roles");?></h3>
   												<hr>
												<div class="form-group row">
													<div class="col-sm-12">
														<?php
														
														foreach ($roles as $rl) {
															?>
														<label class="radio-inline">
															<input type="radio" id="user_role" name="user_role"  value="<?= $rl->role_id ?>" checked> <?= $rl->role_id == 2 ?$this->lang->line("customer") :$this->lang->line($rl->role_name); ?>  
														</label>
															<?php
														}
														?>
														<span style="color: red;" class="user_role_msg errmsg"></span>
													</div>
												</div>
											</fieldset>
											<fieldset class="border p-2">
   											<h3><?= $this->lang->line("Plan Details");?> </h3>
   											<hr>
   												<div class="form-group row">
													<div class="col-sm-12">
														<input type="checkbox" name="check_plan" id="check_plan" onclick="showplan_block();"> <?= $this->lang->line("user_plan_sure");?>
													</div>	
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("select_plan_customer");?></label>
													<div class="col-sm-4">
														<select id="plan_id" name="plan_id" class="form-control" onchange="getplandetails(this.value)" disabled>
															<option value=""><?= $this->lang->line("no_plan_selected");?></option>
															<?php
															foreach($planlist as $k) {
																?>
																	<option value="<?= $k->id ?>"><?= $k->name ?></option>
																<?php
															}
															?>
														</select>
													</div>
												</div>
												<div class="form-group" style="display: none;" id="plandata">
													
													<div class="row">
													<strong class="col-sm-4 col-form-label"><?= $this->lang->line("plan_name");?> : </strong>
													<span class="col-sm-2" id="plan_name"></span>
													</div>

													<div class="row">	
													<strong class="col-sm-4 col-form-label"><?= $this->lang->line("ftp_space_limit");?> : </strong>
													<span class="col-sm-2" id="ftp_space_limit"></span>
													</div>

													<div class="row">	
													<strong class="col-sm-4 col-form-label"><?= $this->lang->line("db_space_limit");?> : </strong>
													<span class="col-sm-2" id="db_space_limit"></span>
													</div>

													<div class="row">	
													<strong class="col-sm-4 col-form-label"><?= $this->lang->line("price");?> : </strong>
													<span class="col-sm-2" id="price"></span>
													</div>

													<div class="row">	
													<strong class="col-sm-4 col-form-label"><?= $this->lang->line("expiry_days");?> : </strong>
													<span class="col-sm-2" id="expiry_days"></span>
													</div>

												</div>

   											</fieldset>

											<div class="form-group row">
												<div class="input-group">
													<div class="col-sm-6" id="usererrormsg" style="color: red;"></div>
													<div class="col-sm-6 text-right">
														<button type="button" class="btn btn-default waves-effect" onclick="backToList()"><?= $this->lang->line("close");?></button>
														<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit");?></button>
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
<script type="text/javascript">
	function getplandetails(plan_id){
			if(plan_id != ""){
				$.ajax({
			           url:"<?php echo base_url();?>client/plan/details",
			           type:"post",
			           data: {plan_id:plan_id},
			           dataType:'json',
			           success:function(data){
		                        //console.log(data);
		                        $.each(data.data, function (key, val) {
							        $("#"+key).html(val);
							        //alert(key);
							    });
							    $("#plandata").show();
		                     }
		                });
			}else{
					$("#plan_name").html("");
					$("#ftp_space_limit").html("");
					$("#db_space_limit").html("");
					$("#price").html("");
					$("#expiry_days").html("");
					$("#plandata").hide();
			}
	}

	function showcompany_block(){
		if($("#check_company").is(':checked') == true){
			$(".chkcompany").removeAttr("readonly");
			$("#company_logo").removeAttr("disabled");
			
		}else{
			$(".chkcompany").attr("readonly",true);
			$("#company_logo").attr("disabled");

		}	
	}
	function showplan_block(){
		if($("#check_plan").is(':checked') == true){
			$("#plan_id").removeAttr("disabled");
		}else{
			$("#plan_id").val("");
			$("#plan_id").attr("disabled",true);
			$("#plan_name").html("");
						$("#ftp_space_limit").html("");
						$("#db_space_limit").html("");
						$("#price").html("");
						$("#expiry_days").html("");
						$("#plandata").hide();

		}
	}

	$("#userCreateForm").on('submit', function(e){
	        e.preventDefault();
	    	$.ajax({
	           url:"<?php echo base_url();?>client/users/save/create",
	           type:"post",
	           // beforeSend:function(){
	           // 		$("#cover-spin").show()
	           // },
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
									$("#usererrormsg").html("");
	                            window.location.replace("<?= base_url()?>client/users");
								});

	                            
	                        }else{
	                        	 $("#cover-spin").hide();
	                        	 $(".errmsg").html("");
	                        	$.each(data.error_data, function (key, val) {
							        $("."+key).html(val);
							    });
	                        }
	                             
	                     }
	                });
	   	 });

	 
											
	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#wrap').hide('fast');
                
                $("#company_logo_image").attr('src',e.target.result);
                
                $('#wrap').show('fast');
                $("#company_logo_image").show();
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function goBack() {
		window.history.back();
	}

	function backToList() {
		window.location.replace("<?= base_url()?>client/users");
	}

    $("#company_logo").change(function(){
        readURL(this);
    });
</script>
<?php $this->load->view("client/layout/footer_new");?>