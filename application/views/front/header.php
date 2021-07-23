<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?php echo base_url();?>public/public/front/css/style-cloud.css">
    <link rel="stylesheet" href="<?php echo base_url();?>public/public/front/css/flaticon/flaticon.css">

    <link rel="icon" href="<?php echo base_url();?>public/public/assets/img/favicon.ico" type="image/ico" sizes="16x16">

    <title><?= $this->lang->line("Cloud Service World")?></title>
  </head>
  <body>
  	<!-- Header start  -->
  	<nav class="navbar navbar-expand-lg navbar-light bg-light">
  		<div class="container">
	  <a class="navbar-brand" href="<?php echo base_url();?>"><img src="<?php echo base_url();?>public/public/front/img/frontend-logo.png"></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>
	  <div class="collapse navbar-collapse" id="navbarSupportedContent">
	    <ul class="navbar-nav ml-auto">
	      <li class="nav-item active">
	        <a class="nav-link" href="<?= base_url() ?>"><?= $this->lang->line("Home")?></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="<?= base_url() ?>about"><?= $this->lang->line("About Us")?></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="<?= base_url() ?>contact"><?= $this->lang->line("Contact Us")?></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="<?= base_url() ?>pricing"><?= $this->lang->line("Pricing")?></a>
	      </li>
	     
	      <li class="nav-item">
	        <a class="nav-link login" href="<?php echo base_url();?>client/login"><?= $this->lang->line("login")?></a>
	      </li>
	    </ul>	    
	  </div>
	</div>
	</nav>
  	<!-- Header end  -->
