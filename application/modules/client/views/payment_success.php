<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">
      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-12">
                  <h3 class="filter-content-title text-center" style="color:green;"> <?= $this->lang->line("Payment has been completed, your subscription is renewed")?> </h3>
                 
                  <p class="text-center"><?= $this->lang->line("please_relogin") ?> <a href="<?= base_url('client/login') ?>"><?= $this->lang->line("click_here") ?></a> </p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php $this->load->view("client/layout/footer_new");?>

