  	<?php
		$this->load->view("front/header");
		$plan_type = array("","daily","weekly","yearly");
	?>
  	<!-- Banner section -->
  	<section class="banner inner-banner">
  		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-text">
						<h1>Project</h1>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard </p>
					</div>  
				</div>
				<div class="col-md-6">
				<div class="banner-image">
					<img src="<?= base_url();?>public/public/front/img/cloud-backup.png" alt="">
				</div>
				</div>
			</div>
   		</div>
  	</section>
	<!-- Banner section end -->
	<section class="inner-section">
		<form onsubmit="return validateform(this.submited);" action="<?php echo base_url('pricing/buy/'.$plan_id);?>" method="post" enctype="form-data/multipart">
			<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
			<input type="hidden" name="submitaction" id="submitaction">
		 <div class="container">
			 <div class="row">
				 <div class="col-md-12">
					 <div class="content-box">
						<div class="page-title">
							<h1>Plan List <span>Subscribe your plan </span></h1>
						</div>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
					</div>
				 </div>
			 </div>
			<div class="services-row pricing-row">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<div><strong><?= $this->lang->line("plan_name");?>:</strong> <?= $plandata["name"];?> </div>
							<div><strong><?= $this->lang->line("plan_description");?>:</strong> <?= $plandata["description"];?> </div>
							<div><strong><?= $this->lang->line("price");?>:</strong>  <?= $plandata["price"];?> <strong><?= $currency?></strong> </div>
							<div><strong><?= $this->lang->line("Plan Duration");?>:</strong> <?= $plandata["time_period"].' '.$plandata["period"];?> </div>
							<div><strong><?= $this->lang->line("Will Expire on");?> :</strong> <?= $expiry_date;?> </div>
						</div>
					</div>
					<input type="hidden" id="plan_price" value="<?=$plandata["price"]?>">
					<input type="hidden" id="price" name="price" value="<?=$plandata["price"]?>">
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("First_Name");?><span style="color:red">*</span></label>
							<input type="text" class="form-control" id="f_name" name="f_name" placeholder="<?= $this->lang->line("First_Name");?>"  value="<?= isset($data["f_name"]) ? $data["f_name"] : '';?>">
							<span style="color: red;" id="f_name_msg"><?= isset($error_data["f_name"]) ? $error_data["f_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Last_Name");?><span style="color:red">*</span></label>
							<input type="text" class="form-control" id="l_name" name="l_name" placeholder="<?= $this->lang->line("Last_Name");?>" value="<?= isset($data["l_name"]) ? $data["l_name"] : '';?>">
							<span style="color: red;" id="l_name_msg"><?= isset($error_data["l_name"]) ? $error_data["l_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Phone");?><span style="color:red">*</span></label>
							<input type="text" class="form-control" id="phone" name="phone" placeholder="<?= $this->lang->line("Phone");?>"  value="<?= isset($data["phone"]) ? $data["phone"] : '';?>">
							<span style="color: red;" id="phone_msg"><?= isset($error_data["phone"]) ? $error_data["phone"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Email");?><span style="color:red">*</span></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?= $this->lang->line("Email");?>"  value="<?= isset($data["email"]) ? $data["email"] : '';?>">
							<span style="color: red;" id="email_msg"><?= isset($error_data["email"]) ? $error_data["email"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Password");?><span style="color:red">*</span></label>
							<input type="password" class="form-control" id="password" name="password" placeholder="<?= $this->lang->line("Password");?>" value="<?=isset($data["password"])?$data["password"]:'';?>">
							<span style="color: red;" id="password_msg"><?= isset($error_data["password"]) ? $error_data["password"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("CPassword");?><span style="color:red">*</span></label>
							<input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="<?= $this->lang->line("CPassword");?>" value="<?=isset($data["cpassword"])?$data["cpassword"]:'';?>">
							<span style="color: red;" id="cpassword_msg"><?= isset($error_data["cpassword"]) ? $error_data["cpassword"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Address");?><span style="color:red">*</span></label>
							<textarea class="form-control" id="address" name="address" placeholder="<?= $this->lang->line("Address");?>"><?= isset($data["address"]) ? $data["address"] : '';?></textarea>
							<span style="color: red;" id="address_msg"><?= isset($error_data["address"]) ? $error_data["address"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("City");?><span style="color:red">*</span></label>
							<input type="text" class="form-control" id="city" name="city" placeholder="<?= $this->lang->line("City");?>" value="<?= isset($data["city"]) ? $data["city"] : '';?>">
							<span style="color: red;" id="city_msg"><?= isset($error_data["city"]) ? $error_data["city"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Zip_Code");?><span style="color:red">*</span></label>
							<input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="<?= $this->lang->line("Zip_Code");?>" minlength="5" maxlength="6" value="<?= isset($data["zipcode"]) ? $data["zipcode"] : '';?>" onkeypress="return /\d/.test(String.fromCharCode(event.keyCode || event.which))">
							<span style="color: red;" id="zipcode_msg"><?= isset($error_data["zipcode"]) ? $error_data["zipcode"] : '';?></span>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<input type="checkbox" name="check_company" id="check_company" value="1" onclick="showcompany_block();" <?= isset($data["check_company"]) ? 'checked' : ''; ?>> <?= $this->lang->line("company_sure");?>
						</div>
					</div>
				</div>
				<div class="row" style="display: none;" id="compblock">
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_name");?></label>
							<input type="text" class="form-control" id="company_name" name="company_name" placeholder="<?= $this->lang->line("company_name");?>" value="<?= isset($data["company_name"]) ? $data["company_name"] : ''; ?>">
							<span style="color: red;" id="company_name_msg"><?= isset($error_data["company_name"]) ? $error_data["company_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_vat_number");?></label>
							<input type="text" class="form-control" id="company_vat_number" name="company_vat_number" placeholder="<?= $this->lang->line("company_vat_number");?>" value="<?= isset($data["company_vat_number"]) ? $data["company_vat_number"] : ''; ?>">
							<span style="color: red;" id="company_vat_number_msg"><?= isset($error_data["company_vat_number"]) ? $error_data["company_vat_number"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_street");?></label>
							<input type="text" class="form-control" id="company_street" name="company_street" placeholder="<?= $this->lang->line("company_street");?>" value="<?= isset($data["company_street"]) ? $data["company_street"] : ''; ?>">
							<span style="color: red;" id="company_street_msg"><?= isset($error_data["company_street"]) ? $error_data["company_street"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_town");?></label>
							<input type="text" class="form-control" id="company_town" name="company_town" placeholder="<?= $this->lang->line("company_town");?>" value="<?= isset($data["company_town"]) ? $data["company_town"] : ''; ?>">
							<span style="color: red;" id="company_street_msg"><?= isset($error_data["company_town"]) ? $error_data["company_town"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_zipcode");?></label>
							<input type="text" class="form-control" id="company_zipcode" name="company_zipcode" placeholder="<?= $this->lang->line("company_zipcode");?>" value="<?= isset($data["company_zipcode"]) ? $data["company_zipcode"] : ''; ?>" onkeypress="return /\d/.test(String.fromCharCode(event.keyCode || event.which))" minlength="5" maxlength="6">
							<span style="color: red;" id="company_zipcode_msg"><?= isset($error_data["company_zipcode"]) ? $error_data["company_zipcode"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_country");?></label>
							<input type="text" class="form-control" id="company_country" name="company_country" placeholder="<?= $this->lang->line("company_country");?>" value="<?= isset($data["company_country"]) ? $data["company_country"] : ''; ?>">
							<span style="color: red;" id="company_country_msg"><?= isset($error_data["company_country"]) ? $error_data["company_country"] : '';  ?></span>
						</div>
					</div>
				</div>
				<div class="text-right">
					<button type="reset"  class="btn btn-secondary mr-2 btn-md"><?= $this->lang->line("Reset");?></button>

					<input type="submit" onclick="this.form.submited=this.value;"  name="submit_advance" id="submit_advance" class="btn btn-primary btn-md" value="<?= $this->lang->line("cash_on_advance")?>">

					<input type="submit" onclick="this.form.submited=this.value;"  name="submit" id="submit" class="btn btn-primary btn-md" value="<?= $this->lang->line("Pay_Register");?>">
				</div>
			</div> 
		</div>
		</form>	
	</section>

	<section class="inner-section pt-0 pb-0">
		<div class="container">
			<div class="about-row">
				<div class="row">
					<div class="col-md-3 col-sm-6">
						<div class="about-box">
							<i class="flaticon-uninterrupted-power-supply"></i>
							<h3>POWER SUPPLY</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="about-box">
							<i class="flaticon-hot"></i>
							<h3>CLIMAT CONTROL</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="about-box">
							<i class="flaticon-server"></i>
							<h3>TOP PROTECTION</h3>
						</div>
					</div>
					<div class="col-md-3 col-sm-6">
						<div class="about-box">
							<i class="flaticon-24-hours"></i>
							<h3>SUPPORT 24/7</h3>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</section>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script type="text/javascript">
			function showcompany_block(){
				if($("#check_company").is(':checked') == true){
					$("#compblock").show();
				}else{
					$("#compblock").hide();
				}	
			}
			showcompany_block();
			function validateform(submitvalue){
						if(submitvalue == "<?= $this->lang->line("cash_on_advance")?>"){
							$("#submitaction").val(1);	
						}else{
							$("#submitaction").val(2);
						}				
						var error_data = ["f_name","l_name","phone","email","password","cpassword","address","city","zipcode","company_name","company_vat_number","company_street","company_town","company_zipcode","company_country"];
						var f_name = $("#f_name").val() == "" ? error_data["f_name"] = "<?php echo $this->lang->line('f_name_blank')?>" : error_data["f_name"] = "";
						var l_name = $("#l_name").val() == "" ? error_data["l_name"] = "<?php echo $this->lang->line('l_name_blank')?>" : error_data["l_name"] = "";
						var phone = $("#phone").val() == "" ? error_data["phone"] = "<?php echo $this->lang->line('phone_blank')?>" : error_data["phone"] = "";
						var email = $("#email").val() == "" ? error_data["email"] = "<?php echo $this->lang->line('email_blank')?>" : error_data["email"] = "";
						var password = $("#password").val() == "" ? error_data["password"] = "<?php echo $this->lang->line('password_blank')?>" : error_data["password"] = "";
						var cpassword = $("#cpassword").val() == "" ? error_data["cpassword"] = "<?php echo $this->lang->line('cpassword_blank')?>" : error_data["cpassword"] = "";
						var address = $("#address").val() == "" ? error_data["address"] = "<?php echo $this->lang->line('address_blank')?>" : error_data["address"] = "";
						var city = $("#city").val() == "" ? error_data["city"] = "<?php echo $this->lang->line('city_blank')?>" : error_data["city"] = "";
						var zipcode = $("#zipcode").val() == "" ? error_data["zipcode"] = "<?php echo $this->lang->line('zipcode_blank')?>" : error_data["zipcode"] = "";
						if(error_data["cpassword"] == "" ){
							var passcheck = $("#password").val() != $("#cpassword").val() ? error_data["cpassword"] = "<?php echo $this->lang->line('password_not_matched')?>" : error_data["cpassword"] = "" ;
						}
						if($("#check_company").is(':checked') == true){
								var company_name = $("#company_name").val() == "" ? error_data["company_name"] = "<?php echo $this->lang->line("company_name_blank")?>" : error_data["company_name"] = "";
								var company_vat_number = $("#company_vat_number").val() == "" ? error_data["company_vat_number"] = "<?php echo $this->lang->line("company_vat_number_blank")?>" : error_data["company_vat_number"] = "";
								var company_street = $("#company_street").val() == "" ? error_data["company_street"] = "<?php echo $this->lang->line("company_street_blank")?>" : error_data["company_street"] = "";
								var company_town = $("#company_town").val() == "" ? error_data["company_town"] = "<?php echo $this->lang->line("company_town_blank")?>" : error_data["company_town"] = "";
								var company_zipcode = $("#company_zipcode").val() == "" ? error_data["company_zipcode"] = "<?php echo $this->lang->line("company_zipcode_blank")?>" : error_data["company_zipcode"] = "";
								var company_country = $("#company_country").val() == "" ? error_data["company_country"] = "<?php echo $this->lang->line("company_country_blank")?>" : error_data["company_country"] = "";	

						}else{
								error_data["company_name"] = "";
								error_data["company_vat_number"] = "";
								error_data["company_street"] = "";
								error_data["company_town"] = "";
								error_data["company_zipcode"] = "";
								error_data["company_country"] = "";
						}	


						var emailval = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/ ;
						if(error_data["email"] == ""){
							if(!emailval.test($("#email").val())){
								error_data["email"] = "<?php echo $this->lang->line('email_not_proper')?>";
							}else{
								error_data["email"] = "";
							}
						}


						var cnt = 0;
						$.each(error_data, function (key, val){
								$("#"+val+"_msg").html(error_data[val]);
								if(error_data[val] == ""){
									cnt++;
								}	
						});
						console.log(error_data.length +"  "+cnt);
						if(error_data.length == cnt){
							return true;
						}else{
							return false;
						}
			}

	</script>
<?php
	$this->load->view("front/footer");
?>