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
		<form action="<?php echo base_url('pricing/buy/'.$plan_id);?>" method="post" enctype="form-data/multipart">
			<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
		 <div class="container">
			 <div class="row">
				 <div class="col-md-12">
					 <div class="content-box">
						<div class="page-title">
							<h1>Project list <span> List of all projects</span></h1>
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
						</div>
					</div>

					
					<input type="hidden" id="plan_price" value="<?=$plandata["price"]?>">
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("plan_type");?></label>
							<select id="plan_type" name="plan_type" class="form-control" onChange="changeplantype();">
								<option value="monthly">Monthly</option>
								<option value="annually">Annually</option>
							</select>
							<span style="color: red;"><?= isset($error_data["plan_type"]) ? $error_data["plan_type"] : '';  ?></span>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("price");?></label>
							<input type="text" class="form-control" readonly="true" id="price" name="price" placeholder="<?= $this->lang->line("price");?>"  value="<?= isset($plandata["price"]) ? $plandata["price"] : '';  ?>">
							<span style="color: red;"><?= isset($error_data["price"]) ? $error_data["price"] : '';  ?></span>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("First_Name");?></label>
							<input type="text" class="form-control" id="f_name" name="f_name" placeholder="<?= $this->lang->line("First_Name");?>"  value="<?= isset($data["f_name"]) ? $data["f_name"] : '';?>">
							<span style="color: red;"><?= isset($error_data["f_name"]) ? $error_data["f_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Last_Name");?></label>
							<input type="text" class="form-control" id="l_name" name="l_name" placeholder="<?= $this->lang->line("Last_Name");?>" value="<?= isset($data["l_name"]) ? $data["l_name"] : '';?>">
							<span style="color: red;"><?= isset($error_data["l_name"]) ? $error_data["l_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Phone");?></label>
							<input type="text" class="form-control" id="phone" name="phone" placeholder="<?= $this->lang->line("Phone");?>"  value="<?= isset($data["phone"]) ? $data["phone"] : '';?>">
							<span style="color: red;"><?= isset($error_data["phone"]) ? $error_data["phone"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Email");?></label>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?= $this->lang->line("Email");?>"  value="<?= isset($data["email"]) ? $data["email"] : '';?>">
							<span style="color: red;"><?= isset($error_data["email"]) ? $error_data["email"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Password");?></label>
							<input type="password" class="form-control" id="password" name="password" placeholder="<?= $this->lang->line("Password");?>" value="<?=isset($data["password"])?$data["password"]:'';?>">
							<span style="color: red;"><?= isset($error_data["password"]) ? $error_data["password"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Date_of_Birth");?></label>
							<input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="<?= $this->lang->line("Date_of_Birth");?>" value="<?= isset($data["birthdate"]) ? $data["birthdate"] : '';?>">
							<span style="color: red;"><?= isset($error_data["birthdate"]) ? $error_data["birthdate"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Address");?></label>
							<textarea class="form-control" id="address" name="address" placeholder="<?= $this->lang->line("Address");?>"><?= isset($data["address"]) ? $data["address"] : '';?></textarea>
							<span style="color: red;"><?= isset($error_data["address"]) ? $error_data["address"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("landmark");?></label>
							<textarea class="form-control" id="landmark" name="landmark" placeholder="<?= $this->lang->line("landmark");?>"><?= isset($data["landmark"]) ? $data["landmark"] : '';?></textarea>
							<span style="color: red;"><?= isset($error_data["landmark"]) ? $error_data["landmark"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("City");?></label>
							<input type="text" class="form-control" id="city" name="city" placeholder="<?= $this->lang->line("City");?>" value="<?= isset($data["city"]) ? $data["city"] : '';?>">
							<span style="color: red;"><?= isset($error_data["city"]) ? $error_data["city"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("Zip_Code");?></label>
							<input type="text" class="form-control" id="zipcode" name="zipcode" placeholder="<?= $this->lang->line("Zip_Code");?>" minlength="6" maxlength="6" value="<?= isset($data["zipcode"]) ? $data["zipcode"] : '';?>">
							<span style="color: red;"><?= isset($error_data["zipcode"]) ? $error_data["zipcode"] : '';?></span>
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
							<span style="color: red;"><?= isset($error_data["company_name"]) ? $error_data["company_name"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_vat_number");?></label>
							<input type="text" class="form-control" id="company_vat_number" name="company_vat_number" placeholder="<?= $this->lang->line("company_vat_number");?>" value="<?= isset($data["company_vat_number"]) ? $data["company_vat_number"] : ''; ?>">
							<span style="color: red;"><?= isset($error_data["company_vat_number"]) ? $error_data["company_vat_number"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_street");?></label>
							<input type="text" class="form-control" id="company_street" name="company_street" placeholder="<?= $this->lang->line("company_street");?>" value="<?= isset($data["company_street"]) ? $data["company_street"] : ''; ?>">
							<span style="color: red;"><?= isset($error_data["company_street"]) ? $error_data["company_street"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_town");?></label>
							<input type="text" class="form-control" id="company_town" name="company_town" placeholder="<?= $this->lang->line("company_town");?>" value="<?= isset($data["company_town"]) ? $data["company_town"] : ''; ?>">
							<span style="color: red;"><?= isset($error_data["company_town"]) ? $error_data["company_town"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_zipcode");?></label>
							<input type="text" class="form-control" id="company_zipcode" name="company_zipcode" placeholder="<?= $this->lang->line("company_zipcode");?>" value="<?= isset($data["company_zipcode"]) ? $data["company_zipcode"] : ''; ?>">
							<span style="color: red;"><?= isset($error_data["company_zipcode"]) ? $error_data["company_zipcode"] : '';  ?></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label><?= $this->lang->line("company_country");?></label>
							<input type="text" class="form-control" id="company_country" name="company_country" placeholder="<?= $this->lang->line("company_country");?>" value="<?= isset($data["company_country"]) ? $data["company_country"] : ''; ?>">
							<span style="color: red;"><?= isset($error_data["company_country"]) ? $error_data["company_country"] : '';  ?></span>
						</div>
					</div>
				</div>
				<div class="text-right">
					<button type="reset"  class="btn btn-secondary mr-2 btn-md"><?= $this->lang->line("Reset");?></button>
					<input type="submit"  name="submit" id="submit" class="btn btn-primary btn-md" value="<?= $this->lang->line("Pay_Register");?>"> 
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

			function changeplantype(){
				 	var value = document.getElementById('plan_type').value;
					var price = document.getElementById('plan_price').value;
					if(value == 'annually'){document.getElementById('price').value =price*12;}		
					if(value == 'monthly'){document.getElementById('price').value =price;}
			}

			//init function
			changeplantype();
			showcompany_block();


	</script>
<?php
	$this->load->view("front/footer");
?>