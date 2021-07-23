<?php
$this->load->view("front/header");
?>
<section class="banner inner-banner">
  		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-text">
						<h1><?=$page_data->title;?></h1>
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
  	<section class="inner-section">
		 <div class="container">
			 <div class="row">
				 <div class="col-md-12">
					 <div class="content-box">
						<div class="page-title">
							<h1><?=$page_data->title;?></h1>
						</div>
						<?php
						echo $page_data->html_template;
						?>
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

<?php
$this->load->view("front/footer");
?>