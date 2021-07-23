<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>




<div class="container-fluid">
   <div class="row mr-0">

      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-12">
                  <h3 class="filter-content-title">  <?= $this->lang->line("Selected Plan Details")?> </h3>
               </div>
               
            </div>
         </div>
      </div>


       <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-12">
                  
                  
                  <form id="createform" method="post" action="<?php echo base_url();?>client/dashboard/buyplan" enctype="multipart/form-data">
                          
                           <fieldset title="Step 1">
                              
                              <input type="hidden" id="plan_id" name="plan_id" value="<?=$plandata->id?>">
                              <div class="form-group row">
                                 <label for="fieldname" class="col-md-2 control-label"> <?= $this->lang->line("Plan Name")?> : </label>
                                 <div class="col-md-7">
                                    <?php echo $plandata->name; ?>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label for="fieldname" class="col-md-2 control-label"> <?= $this->lang->line("Plan Description")?> : </label>
                                 <div class="col-md-7">
                                    <?php echo $plandata->description; ?>
                                 </div>
                              </div>

                              <div class="form-group row">
                                 <label for="fieldname" class="col-md-2 control-label"> <?= $this->lang->line("price")?> : </label>
                                 <div class="col-md-7" id="pricebox">
                                    <?php echo $plandata->price.' '.$currency;?>
                                 </div>
                                 <input type="hidden" class="form-control" readonly="true" id="price" name="price" placeholder="<?= $this->lang->line("price");?>"  value="<?= isset($plandata->price) ? $plandata->price : '';  ?>">
                                 <input type="hidden" id="plan_price" value="<?=$plandata->price?>">
                              </div>

                              <div class="form-group row">
                                 <label for="fieldname" class="col-md-2 control-label"> <?= $this->lang->line("Plan Duration")?> : </label>
                                 <div class="col-md-7">
                                    <?php echo $plandata->time_period.' '.$plandata->period; ?>
                                 </div>
                              </div>

                               <div class="form-group row">
                                 <label for="fieldname" class="col-md-2 control-label"> <?= $this->lang->line("Will Expire on")?> : </label>
                                 <div class="col-md-7">
                                    <?php echo $expiry_date; ?>
                                 </div>
                              </div>

                              <!-- <div class="form-group row">
                                 <label for="fieldemail" class="col-md-2 control-label"> <?= $this->lang->line("Plan Type")?> : </label>
                                 <div class="col-md-3">
                                    <select id="plan_type" name="plan_type" class="form-control"  onChange="changeplantype();">
                                       <option value="monthly"><?= $this->lang->line("monthly")?></option>
                                       <option value="annually"><?= $this->lang->line("annually")?></option>
                                    </select>   
                                 </div>
                              </div> -->
                              
                           </fieldset>
                           <div class="row">
                              <div class="col-sm-6">
                                 <input type="submit" class="finish btn-primary btn" value="<?= $this->lang->line("Proceed to payment")?>" />
                              </div>
                           </div>
                        </form>





               </div>
               
            </div>
         </div>
      </div>


      

   
   </div>
</div>



<script type="text/javascript">
         
         // function changeplantype(){

         //       //alert('dfgdfg');

         //       var value = document.getElementById('plan_type').value;
         //       var price = document.getElementById('plan_price').value;
         //       if(value == 'annually'){
         //          document.getElementById('price').value = price*12;
         //          document.getElementById('pricebox').innerHTML  = price*12;
         //       }

         //       if(value == 'monthly'){
         //          document.getElementById('price').value =price;
         //          document.getElementById('pricebox').innerHTML  = price;
         //       }
         // }

         //init function
         //changeplantype();


   </script>




<?php $this->load->view("client/layout/footer_new");?>

