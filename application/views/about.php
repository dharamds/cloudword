
<?php
$this->load->view("front/header");
?>
  	<!-- Banner section -->
  	<section class="banner inner-banner">
  		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-text">
						<h1><?= $this->lang->line("about_us")?></h1>
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
		 <div class="container">
			 <div class="row">
				 <div class="col-md-6">
					 <div class="content-box">
						<div class="page-title">
							<h1><?= $this->lang->line("who_we_are")?> <span><?= $this->lang->line("in_few_words")?></span></h1>
						</div>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
						<p> It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </p>
						<p> Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.  </p>
					</div>
				 </div>
				 <div class="col-md-6">
					 <div class="image-box">
						<img class="img-fluid" src="<?= base_url();?>public/public/front/img/about-image.jpg">
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
			<div class="features">
				<h1><?= $this->lang->line("how_it_works")?></h1>
				<div class="how-it-works">
					 <div class="row">
						 <div class="col-md-4">
							 <div class="work-box">
								<div class="work-image">
									<img src="<?= base_url();?>public/public/front/img/step1_login.png" alt="">
								</div>
								<h1>1. Login </h1>
								<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's since the 1500s, when an unknown pri</p>
							 </div>
						 </div>
						 <div class="col-md-4">
							 <div class="work-box">
								<div class="work-image">
									<img src="<?= base_url();?>public/public/front/img/step2_installation.png" alt="">
								</div>
								<h1>2. Installation</h1>
								<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's since the 1500s, when an unknown pri</p>
							 </div>
						 </div>
						 <div class="col-md-4">
							 <div class="work-box last-work">
								<div class="work-image">
									<img src="<?= base_url();?>public/public/front/img/step3_backup.png" alt="">
								</div>
								<h1> 3. Backup </h1>
								<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's since the 1500s, when an unknown pri</p>
							 </div>
						 </div>
					 </div>
				</div>
			</div>
		</div>	
	</section>
<?php
$this->load->view("front/footer");
?>