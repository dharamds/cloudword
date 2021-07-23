<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>




<div class="container-fluid">
   <div class="row mr-0">

      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-12">
                  <h3 class="filter-content-title text-center" style="color:red;"> <?= $this->lang->line("Payment not completed, please try again")?> </h3>
               </div>
               
            </div>
         </div>
      </div>


   
   </div>
</div>



<?php $this->load->view("client/layout/footer_new");?>

