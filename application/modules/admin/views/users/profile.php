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
	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title" align="left"><?= $this->lang->line("up_profile");?></h3>
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
						<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-block">
										<form id="userCreateForm" method="post" enctype="form-data/multipart">
											<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
											<input type="hidden" name="euser_id" id="euser_id">
											<div class="form-group row" align="center">
												<div class="col-lg-12">
												<div class="input-group text-center" align="center">
												<?php if($getclient->img == ""){
													?>
													<img height="100" width="100" src="<?= base_url() ?>public/public/assets/img/default_user.png" id="userprofile" name="userprofile">
												<?php	
												}else{
													?>
													<img height="100" width="100" src="<?= base_url() ?>uploads/user/profile/<?=$getclient->img?>" id="userprofile" name="userprofile">
													<?php
												}
												?>
												<input type="file" name="userprofilec" id="userprofilec" style="display: none">
												<a href="javascript:" onclick="$('#userprofilec').click();"><i class="flaticon-edit"></i></a>
												</div>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("First_Name");?> </label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="f_name" id="f_name" placeholder="<?= $this->lang->line("First_Name");?>" value="<?= $getclient->fname?>">
													<span style="color: red;" class="f_name_msg"></span>
												</div>
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("Last_Name");?> </label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="l_name" id="l_name" placeholder="<?= $this->lang->line("Last_Name");?>" value="<?= $getclient->lname?>">
													<span style="color: red;" class="l_name_msg"></span>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("Phone");?></label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="phone" id="phone" placeholder="<?= $this->lang->line("Phone");?>" value="<?= $getclient->phone?>">
													<span style="color: red;" class="phone_msg"></span>
												</div>
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("Email");?> </label>
												<div class="col-sm-4">
													<input type="text" class="form-control" name="email" id="email" placeholder="<?= $this->lang->line("Email");?>" value="<?= $getclient->email?>" readonly>
													<span style="color: red;" class="email_msg"></span>
												</div>
											</div>
											<div class="form-group row">
												<!-- <label class="col-sm-2 col-form-label"><?= $this->lang->line("Date_of_Birth");?></label>
												<?php
												/*$bb = explode("-",$getclient->birth);
												$bbb=$bb[1]."/".$bb[2]."/".$bb[0];*/
												?>
												<div class="col-sm-4">
													<input type="date" id="dob"  name="dob" class="form-control" placeholder="<?= $this->lang->line("Date_of_Birth");?>" value="<?= $getclient->birth ?>">
													<span style="color: red;" class="dob_msg"></span>
												</div> -->
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("Address");?></label>
												<div class="col-sm-4">
													<textarea id="address"  name="address" class="form-control" placeholder="<?= $this->lang->line("Address");?>"><?= $getclient->address?> </textarea>
													<span style="color: red;" class="address_msg"></span>
												</div>

												<label class="col-sm-2 col-form-label"><?= $this->lang->line("landmark");?> </label>
												<div class="col-sm-4">
													<textarea id="landmark"  name="landmark" class="form-control" placeholder="<?= $this->lang->line("landmark");?>"><?= $getclient->landmark?></textarea>
													<span style="color: red;" class="landmark_msg"></span>
												</div>

											</div>
											<div class="form-group row">
												
												
												<label class="col-sm-2 col-form-label"><?= $this->lang->line("City");?></label>
												<div class="col-sm-4">
													<input type="text" id="city"  name="city" class="form-control" placeholder="<?= $this->lang->line("City");?>" value="<?= $getclient->city?>">
													<span style="color: red;" class="city_msg"></span>
												</div>

												<label class="col-sm-2 col-form-label"><?= $this->lang->line("Zip_Code");?> </label>
												<div class="col-sm-4">
													<input type="text" id="zipcode"  name="zipcode" class="form-control" placeholder="<?= $this->lang->line("Zip_Code");?>" value="<?= $getclient->zipcode?>">
													<span style="color: red;" class="zipcode_msg"></span>
												</div>
											</div>

											<fieldset class="border p-2">
												<h3> <?= $this->lang->line("company_information");?></h3>
   												<hr>
												<div class="form-group row">
													<div class="col-sm-12">
														<input type="checkbox" name="check_company" id="check_company" onclick="showcompany_block();" <?=($getclient->is_company == 'yes')?'checked="checked"':'';?>  > <?= $this->lang->line("user_company_sure");?>
													</div>	
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_name");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_name"  name="company_name" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_name");?>" value="<?=$getclient->company_name;?>" readonly>
														<span style="color: red;" class="company_name_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_vat_number");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_vat_number"  name="company_vat_number" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_vat_number");?>" value="<?=$getclient->company_vat_number;?>" readonly>
														<span style="color: red;" class="company_vat_number_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_street");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_street"  name="company_street" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_street");?>" value="<?=$getclient->company_street;?>" readonly>
														<span style="color: red;" class="company_street_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_town");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_town"  name="company_town" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_town");?>" value="<?=$getclient->company_town;?>" readonly>
														<span style="color: red;" class="company_town_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_zipcode");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_zipcode"  name="company_zipcode" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_zipcode");?>" value="<?=$getclient->company_zipcode;?>" readonly>
														<span style="color: red;" class="company_zipcode_msg errmsg"></span>
													</div>
													<label class="col-sm-2 col-form-label text-right"><?= $this->lang->line("company_country");?> </label>
													<div class="col-sm-4">
														<input type="text" id="company_country"  name="company_country" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_country");?>" value="<?=$getclient->company_country;?>" readonly>
														<span style="color: red;" class="company_country_msg errmsg"></span>
													</div>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label  text-right"><?= $this->lang->line("company_responsible_person");?></label>
													<div class="col-sm-4">
														<input type="text" id="company_responsible_person"  name="company_responsible_person" class="form-control chkcompany" placeholder="<?= $this->lang->line("company_responsible_person");?>" value="<?=$getclient->company_responsible_person;?>" readonly>
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
														<input type="hidden" name="old_logo" value="<?=$getclient->company_logo;?>" />	
														<?php if($getclient->company_logo != ''){ ?>

															<img src="<?=base_url().'uploads/company_logo/'.$getclient->company_logo; ?>" id="company_logo_image" name="company_logo_image" width="100" height="100">

														<?php }else{ ?>

															<img src="<?php echo base_url() ?>public/public/assets/img/default_user.png" id="company_logo_image" name="company_logo_image" width="100" height="100">

														<?php } ?>	
													</div>
												</div>
											</fieldset>
											
											<div class="form-group row">
												<div class="input-group">
													<div class="col-sm-6" id="usererrormsg" style="color: red;"></div>
													<div class="col-sm-6 text-right">
														<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit");?></button>
														<button type="button" class="btn btn-default waves-effect" data-dismiss="modal"><?= $this->lang->line("close");?></button>
													</div>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						
					</div>
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#wrap').hide('fast');
                $('#userprofile').attr('src', e.target.result);
                $('#wrap').show('fast');
            }       
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#userprofilec").change(function(){
        readURL(this);
    });


    /*COMPANY*/
    function showcompany_block(){
		if($("#check_company").is(':checked') == true){
			$(".chkcompany").removeAttr("readonly");
			$("#company_logo").removeAttr("disabled");
			
		}else{
			$(".chkcompany").attr("readonly",true);
			$("#company_logo").attr("disabled");

		}	
	}
							
	function readURLCom(input) {
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

    $("#company_logo").change(function(){
        readURLCom(this);
    });

    //init
	showcompany_block();
	/*COMPANY*/


	$("#userCreateForm").on('submit', function(e){
	        e.preventDefault();
	    	$.ajax({
	           url:"<?php echo base_url();?>admin/users/update_profile",
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
	                        	$("#usererrormsg").html(data.msg);
	                        	swal(data.msg, {
									title: "<?= $this->lang->line("oops") ?>",
									type: "error",
									timer: 3000
								})
	                        }
	                            
	                     }
	                });
	   	 });
</script>
<?php $this->load->view("admin/layout/footer_new");?>