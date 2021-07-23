  	<?php
		$this->load->view("front/header");
	?>
	<style>
      
      i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
    </style>
  	<!-- Banner section -->
  	
  	<section style="text-align: center;margin: 30px 0;" >
  		<div class="card" style=" text-align: center;padding: 40px;background: #EBF0F5;"> 
	      <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
	        <i class="checkmark">✓</i>
	      </div>
	        <h1><?=$this->lang->line("success")?></h1> 
	        <p style="color: green"><?=$this->lang->line("Advance_success_msg")?><br/> <?=$this->lang->line("for_login")?><a href="<?= base_url('client/login')?>"><?=$this->lang->line("click_here")?></a></p>
	      </div>
  	</section>
	<!-- Banner section end -->
	
	<?php
$this->load->view("front/footer");
?>