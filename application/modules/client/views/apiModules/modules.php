<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
<div class="container-fluid">
   <div class="row mr-0">

   <div class="row">                    
      <div class="col-md-12">
         <div class="filter-container flex-row">
            <div class="flex-col-md-6">
               <h3 class="filter-content-title"> <?= $this->lang->line("modules_list")?></h3>
            </div>
            <div class="flex-col-md-6 text-right">
            </div>
         </div>
      </div>        
   </div>

   <div class="boxbg" style="padding: 15px 25px 25px;">
      <div class="row">
         <?php 
            foreach($modulesdata as $mdl) {
               ?>
            
         <div class="col-md-4">

            <div class="apimodule-list">
               <div class="list-body">
                  <div class="tile-icon">
                     <img src="<?= base_url()?>uploads/api_modules/<?=$mdl->logo?>">
                  </div>
                  <div class="tile-heading">
                     <span><?=$mdl->module_name?></span>
                  </div>
                  <div class="tile-subheading">
                     <span id="today_orders" class="tilecount">
                         Shopware
                     </span>
                  </div>
               </div>
               <div class="list-footer text-right">
                  <span class="">
                  <a href="<?= base_url();?>client/apiModules/<?=$mdl->url_path_to_add_credentials?>" class="btn btn-primary" target="_blank"> 
                  <?= $this->lang->line("add_cred")?>
                  </a>
                  </span>
               </div>
            </div>
            <!-- <div class="info-tile tile-ocean-green__ dactive-user">
               <div class="tile-footer">
               </div>
               <div class="wave"></div>
            </div> -->
         </div>
         <?php
            }
         ?>
      </div>  
   </div>

    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
      var sproject_id = "";
      $.ajax({
               url:"<?php echo base_url();?>client/shopware/getoverviewdata",
               type:"post",
               data: {sproject_id:sproject_id},
               success:function(data){
                  var obj = JSON.parse(data);
                  $("#today_orders").html(obj.data.total_orders);
                  $("#today_revenue").html(obj.data.total_revenue);
               }
      });
});

</script>

<?php $this->load->view("client/layout/footer_new");?>