<?php
$this->load->view("front/header");
?>

  	<!-- Banner section -->
  	<section class="banner inner-banner">
  		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-text">
						<h1><?=$this->lang->line("contact_us");?></h1>
						<p>Bei Fragen oder Problemstellungen nutzen Sie bitte unser Kontaktformular. Wir werden uns schnellstmöglich bei Ihnen melden</p>
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
	<section class="inner-section contact-bg">
		 <div class="container">
			 <div class="row">
				 <div class="col-md-12">
					 <div class="content-box">
						<div class="page-title">
							<h1><?=$this->lang->line("we_are_in");?> <span><?php // $this->lang->line("touch_24_7");?></span></h1>
						</div>
					</div>
				 </div>
			 </div>
	 
			 <div class="contact-row">
				 <div class="row">
					<div class="col-md-4">
						<div class="address-box">
							<span>Germany</span>
							<h3>Stadtwerke Hilden</h3>
							<ul>
								<li>
									<i class="flaticon-placeholder"></i>
									Stadtwerke Hilden GmbH Am Feuerwehrhaus 1 40724 Hilden
								</li>
								<li>
									<i class="flaticon-phone-call"></i>
									02103 795-555
								</li>
								<li>
									<i class="flaticon-email"></i>
									support@ssn-computer.de
								</li>
							</ul>
						</div>
					</div>
				 </div>
			 </div>
			 <div class="contact-form">
				<div class="row">
					<div class="col-md-6">
						<div class="image-box">
						   <img class="img-fluid" src="<?= base_url();?>public/public/front/img/contact-img.jpg">
						</div>
				   </div>
					<div class="col-md-6">
						<div class="content-box">
						   <div class="page-title">
							   <h1><?=$this->lang->line("contact_us");?></h1>
						   </div>
						   
						   <form id="contactform" onsubmit="return validatecnt();" action="<?= base_url()?>contact/save" method="post">
						   	<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
						   <div class="form-box">
								<!-- <div class="form-group">
									<input type="text" class="form-control" id="name" name="name" placeholder="<?php //$this->lang->line("y_name");?>">
									<span style="color: red;" id="namemsg"></span>
								</div> -->
								<div class="form-group">
									<input type="email" value="<?php echo (isset($email)) ? $email :'';?>" class="form-control" id="email" name="email" placeholder="<?=$this->lang->line("y_email");?>">
									<span style="color: red;" id="emailmsg"></span>
								</div>
								<!-- <div class="form-group">
									<input type="text" class="form-control" id="phone" name="phone" placeholder="<?php //$this->lang->line("Phone");?>">
									<span style="color: red;" id="phonemsg"></span>
								</div> -->
								<div class="form-group">
									<textarea class="form-control" placeholder="<?=$this->lang->line("y_message");?>" id="message" name="message"><?php echo (isset($message)) ? $message :'';?></textarea>
									<span style="color: red;" id="messagemsg"></span>
								</div>
								
								
								<div class="form-group">
								<div class="row">
									
									<div class="col-md-6"><input type="text" class="form-control" id="img_id" name="captcha" placeholder="<?=$this->lang->line("captcha_img");?>">
									<span style="color: red;" id="img_msg"></span>
									</div>
									<div class="col-md-6" style="align:center;"><?= (isset($image)) ? $image : ''; ?></div>
									</div>
								</div>
								
								<div class="form-group">
									<input type="checkbox" name="i_agree" id="i_agree" >  <a href="<?= base_url()?>data_protection" target="_blank">Accept Data protection & imprint</a>
									<span style="color: red;" id="i_agreemsg" ></span>
								</div>

								<button type="submit" id="contactsub" class="btn btn-primary btn-md"><?=$this->lang->line("send_message");?></button>
								<div class="form-group">
								<p <?php echo $msgtype == "success" ? "style='color:green'" : "style='color:red'"; ?>> <?= (isset($msg)) ? $msg : ''; ?></p>
								</div>
						   </div>
						   </form>
					   </div>
					</div>
				</div>
			 </div>
		</div>	
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
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script type="text/javascript">
		function validatecnt(){
         //var name = $("#name").val();
         var email = $("#email").val();
         var img_id = $("#img_id").val();
         var message = $("#message").val();

         // if(name == ""){
         // 	$("#namemsg").html("<?=$this->lang->line("name_blank");?>");
         // 	return false;
         // }else 
         if(email == ""){
         	$("#emailmsg").html("<?=$this->lang->line("email_blank");?>");
         	return false;
         }else if(message == ""){
         	$("#messagemsg").html("<?=$this->lang->line("message_blank");?>");
         	$("#emailmsg").html("")
         	return false;
         }else if(img_id == ""){
         	$("#img_msg").html("<?=$this->lang->line("captcha_blank");?>");
			$("#messagemsg").html("")
         	return false;
         }else if($("#i_agree").prop('checked') == false){
         		$("#i_agreemsg").html("Please check Data protection & Imprint");
    			$("#messagemsg").html("");
         		$("#emailmsg").html("");
				$("#img_msg").html("")
         		return false;
		 }else{
         	$("#messagemsg").html("");
         	$("#i_agreemsg").html("");
         	$("#emailmsg").html("");
         	$("#img_msg").html("");
         	return true;
         }
		}
	</script>
	<?php
$this->load->view("front/footer");
?>

	