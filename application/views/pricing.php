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
						<h1><?= $this->lang->line("Pricing");?></h1>
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
				 <div class="col-md-12">
					 <div class="content-box">
						<div class="page-title">
							<h1><?= $this->lang->line("Pricing");?><span> <?= $this->lang->line("best_price");?></span></h1>
						</div>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. </p>
					</div>
				 </div>
			 </div>
			<div class="services-row pricing-row">
				<div class="row">
					<?php
						foreach($plans as $p){
							if(!empty($p->modules))
								$gemdl = $this->db->query("select * from modules where module_id IN(".$p->modules.")")->result();
							else
								$gemdl = array();
								?>
							<div class="col-md-4">
								<div class="service-box price-box">
									<h1><?php echo $p->name;?></h1>
									<div class="price-title">
										<h3><?php echo $p->price;?> <?= $currency ?></h3>
										<p></p>
									</div>
									<i class="flaticon-hot-air-balloon"></i>
									<ul>
										<li> <i class="flaticon-checked"></i> <?= $p->description ?> </li>
										<?php
										if(count($gemdl) > 0){


										foreach($gemdl as $mf) {
											?>
											<li> <i class="flaticon-checked"></i> <?= $mf->module_name ?> </li>
											<?php
											# code...
										}
										}
										?>										
									</ul>
									<a href="<?= base_url();?>pricing/subscribe/<?= base64_encode($p->id) ?>" target="_blank" class="btn btn-outline-primary btn-md "><?= $this->lang->line("buy_now");?> </a>
								</div>
							</div>
					<?php
						}
					?>
					
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