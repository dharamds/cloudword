<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Database Error </title>
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
				<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">
						<h4>An uncaught Exception was encountered</h4>
						<p>Type: <?php echo get_class($exception); ?></p>
						<p>Message: <?php echo $message; ?></p>
						<p>Filename: <?php echo $exception->getFile(); ?></p>
						<p>Line Number: <?php echo $exception->getLine(); ?></p>
						<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
							<p>Backtrace:</p>
							<?php foreach ($exception->getTrace() as $error): ?>
								<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
									<p style="margin-left:10px">
									File: <?php echo $error['file']; ?><br />
									Line: <?php echo $error['line']; ?><br />
									Function: <?php echo $error['function']; ?>
									</p>
								<?php endif ?>
							<?php endforeach ?>
						<?php endif ?>
						</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?= config_item('base_url'); ?>public/public/assets/js/jquery-1.10.2.min.js"></script> 
	<script src="<?= config_item('base_url'); ?>public/public/assets/js/popper.min.js"></script> 
	<script src="<?= config_item('base_url'); ?>public/public/assets/js/bootstrap.min.js"></script>
</body>
</html>



