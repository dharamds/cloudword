<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error </title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="<?= config_item('base_url'); ?>public/public/assets/css/iofrm-style.css">
	<link rel="stylesheet" type="text/css" href="<?= config_item('base_url'); ?>public/public/assets/css/iofrm-theme9.css">
	<link rel="stylesheet" type="text/css" href="<?= config_item('base_url'); ?>public/public/assets/fonts/flaticon/flaticon.css">
	<link rel="shortcut icon" href="<?= config_item('base_url'); ?>public/public/assets/img/favcon.png"/>
	<style>
		.inputStyle{
			color:#000 !important ;  
		}
	</style>

</head>
<body style="background-image:url('<?= config_item('base_url'); ?>public/public/front/img/banner-bg.png');">
	<div class="form-body" style="background-image:url('<?= config_item('base_url'); ?>public/public/front/img/banner-bg.png');">
		<div class="row ">
			<div class="col-lg-6 col-md-6 offset-3 offset-md-3">
				<div class="website-logo-inside text-center">
					<a href="javascript:void(0)">
						<div class="logo"> 
							<h1>Cloud Service World</h1>
							<!-- <img class="logo-size" src="<?php // base_url(); ?><?= config_item('base_url'); ?>public/public/assets/img/logo_admin.png" alt=""> -->
						</div> 
					</a>
				</div>
				<div class="form-holder">
					<div class="form-content">                  
						<div class="form-items">
							<div class="form-title-holder">
								<h1><?php echo $heading; ?></h1>
								
							</div>
							<div class="page-links">
								<?php echo $message; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?= config_item('base_url'); ?>public/public/assets/js/jquery-1.10.2.min.js"></script> 
	<script src="<?= config_item('base_url'); ?>public/public/assets/js/popper.min.js"></script> 
	<script src="<?= config_item('base_url'); ?>public/public/assets/js/bootstrap.min.js"></script>
</body>
</html>



