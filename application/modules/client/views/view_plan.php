<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>

<style type="text/css">
   .subscriptionbox{
      background: #fff; border-radius: 0px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 0px 0px 25px; margin: 0 20px; text-align: center;
      border-bottom: 5px solid #3a6cab;
      }
   .subscriptionbox h1{ 
      text-align: center;  margin: 0; padding: 10px 20px; background: #3a6cab; color: #fff; font-size: 32px;
   }
   .subscriptionbox .price-title h3{
      text-align: center;  color: #3a6cab; font-size: 30px; font-weight: bold; margin: 0; padding: 10px 10px 0;
   }
   .subscriptionbox i.flaticon-hot-air-balloon{
      font-size: 30px;
   }
   .subscriptionbox ul{ 
      margin: 10px 30px; padding: 0px 0px; list-style: none; min-height: 135px;
   }
   .subscriptionbox ul li{ 
      margin: 5px 0;
   }
   .subscriptionbox ul li i{ 
      color: #3a6cab; margin-right: 5px;
   }
   .subscriptionbox a.btn.btn-md{
      padding: 10px 25px; font-size: 16px; height: 44px; border: 1px solid #3a6cab; border-radius: 6px; color:#3a6cab; margin: 15px auto 0;
   }
   .subscriptionbox a.btn.btn-md:hover{
      background: #3a6cab; color:#fff; 
   }
</style>


<div class="container-fluid">
   <div class="row mr-0">

      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               
               <div class="flex-col-md-6">
                  <h3 class="filter-content-title"> <?= $this->lang->line("Get a new plan")?> </h3>
               </div>
               <div class="flex-col-md-6 text-right">
                  <!-- <a class="btn btn-primary" href="<?php //echo base_url();//?>admin/projects/create">
                     New Project
                     </a> --> 
               </div>

            </div>
         </div>
      </div>


      <div class="row mt-5">
         <div class="col-sm-12">

               <?php
              
               if(!empty($plans)){
                  foreach($plans as $p){
                     if(!empty($p->modules))
                        $gemdl = $this->db->query("select * from modules where module_id IN(".$p->modules.")")->result();
                     else
                        $gemdl = array();
                        ?>
                     <div class="col-md-4">
                        <div class="service-box price-box subscriptionbox">
                           <h1><?php echo $p->name;?></h1>
                           <div class="price-title">
                              <h3><?= $currency ?> <?php echo $p->price;?></h3>
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
                              }
                              }
                              ?>                            
                           </ul>
                           <a href="<?= base_url();?>client/dashboard/renew_subscription/<?= base64_encode($p->id) ?>" target="_blank" class="btn btn-outline-primary btn-md "><?= $this->lang->line("buy_now");?> </a>
                        </div>
                     </div>
               
               <?php
                  }//end foreach
               }else{   
               ?>

                   <div class="filter-container flex-row">
                     <div class="flex-col-md-12">
                        <h3 class="filter-content-title"> <?= $this->lang->line("No plan found, Please check later")?> </h3>
                     </div>
                  </div>

               <?php } ?>
         </div>
      </div>

   
   </div>
</div>


<?php $this->load->view("client/layout/footer_new");?>

