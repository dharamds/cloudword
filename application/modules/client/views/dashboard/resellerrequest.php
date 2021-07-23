<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">
      <style type="text/css">
      .circle1 {
           width: 400px;
           height: 400px;
           line-height: 400px;
           border-radius: 50%;
           font-size: 25px;
           color: #fff;
           text-align: center;
           background: <?= $color ?>
         }
   </style>
   <div class="row" align="center">
      <div class="col-md-12">
         <div class="circle1"><?= $status ?></div>
      </div>
   </div>
   <h1 align="center" style="color: <?= $color ?>"><b><?= $msg ?></b></h1>
     <div  align="center">
     <a href="<?=base_url()?>client/dashboard" class="btn btn-primary"><?= $this->lang->line("backtodashboard")?></a>
     </div>


   </div>
</div>



<?php $this->load->view("client/layout/footer_new");?>