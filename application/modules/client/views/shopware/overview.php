<?php $this->load->view("client/layout/header_new"); ?>
<?php $this->load->view("client/layout/sidebar"); ?>
<div class="container-fluid">
<div class="row mr-0">
   <div class="row">                    
      <div class="col-md-12">
         <div class="filter-container flex-row">
            <div class="flex-col-md-6">
               <h3 class="filter-content-title"> <?=ucfirst($this->encryption->decrypt($sdata->project_name)) ?> <?=$this->lang->line("project_overview") ?></h3>
            </div>
            <div class="flex-col-md-6 text-right">
            </div>
         </div>
      </div>        
   </div>
   <form id="all_records_form">
   <input type="hidden" name="project_id" id="project_id" value="<?php echo $sdata->sproject_id ?>">
   <input type="hidden" name="filter_status" id="filter_status" value="today">
   <hr>
   <p id="error_fetch_data" style="color: red"></p>
                        <div class="row">
                              <div class="col-md-3">
                                 <select class="form-control" id="status_wise_filter" name="status_wise_filter" onclick="chk(this.value)">
                                    <option value="today"><?= $this->lang->line("Today")?></option>
                                    <option value="all_records"><?= $this->lang->line("All Records")?></option>
                                    <option value="choose_date"><?= $this->lang->line("Choose Date range")?></option>
                                 </select>
                              </div>
                              <div id="date_range" style="display: none;">
                                 <label for="from_date" class="col-md-1 control-label text-right">From<span class="text-danger">*</span></label>
                                 <div class="col-md-2">
                                    <input type="date" class="form-control" name="from_date" id="from_date">
                                    <span style="color: red;" id="from_date_msg"></span>
                                 </div>
                                 <label for="to_date" class="col-md-1 control-label text-right">To<span class="text-danger">*</span></label>
                                 <div class="col-md-2">
                                    <input type="date" class="form-control" name="to_date" id="to_date">
                                    <span style="color: red;" id="to_date_msg"></span>
                                 </div>
                                 </div>
                                 <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" name="to_date" id="to_date" onclick="fetch_records()"> <?= $this->lang->line("Fetch Records")?> </button>
                                 </div>
                        </div>
<hr>
<div style="background: #fff; padding: 15px;">
   <div class="row">
      <div class="col-md-3 col-md-offset-2">
         <div class="overviewbox todayorder">
         <div class="tile-icon"> 
            <img src="<?=base_url() ?>public/public/assets/img/todayorder.png"> 
         </div>
         <div class="tile-footer">
               <span> <a href="<?=base_url(); ?>admin/users"> <span id="orders"> <?=$this->lang->line("today_orders") ?></span> </a> </span>
            </div>
            <div class="tile-body">
               <span id="today_orders"> 0 </span>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="overviewbox todayreview">
         <div class="tile-icon"> <img src="<?=base_url() ?>public/public/assets/img/todayreview.png"> </div>
            <div class="tile-footer">
               <span> <a href="<?=base_url(); ?>client/projects"> <span id="revenues"> <?=$this->lang->line("today_revenue") ?></span>  </a>  </span>
            </div>
            <div class="tile-body">
               <span id="today_revenue"> 0.00 </span>
            </div>            
         </div>
      </div>
      <div class="col-md-3">
         <div class="overviewbox todayreview">
         <div class="tile-icon"> <img src="<?=base_url() ?>public/public/assets/img/todayreview.png"> </div>
            <div class="tile-footer">
               <span> <a href="<?=base_url(); ?>client/projects" > <span id="customers"> <?= $this->lang->line("Today's Joined Customer")?></span>  </a>  </span>
            </div>
            <div class="tile-body">
               <span id="today_revenue"> 0.00 </span>
            </div>            
         </div>
      </div>
   </div> 
   </div> 

    </div>
</div>

<div class="row">
   <div class="col-md-12">
      <table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
                     <thead>
                        <tr>
                           <th>
                              #
                           </th>
                           <th ><?= $this->lang->line("Order Number")?></th>
                           <th ><?= $this->lang->line("Customer Name")?></th>
                           <th ><?= $this->lang->line("Customer Email")?></th>
                           <th ><?= $this->lang->line("Shipping Cost")?></th>   
                           <th ><?= $this->lang->line("Invoice Value")?></th>
                           <th ><?= $this->lang->line("Order Date")?></th>
                           <th ><?= $this->lang->line("Order Status")?></th>
                        </tr>
                     </thead>
                     <tbody id="order_fetch_data">
                        <tr>
                           <td colspan="8" align="center"><?= $this->lang->line("no_records_found")?></td>
                        </tr>
                     </tbody>
                  </table>
   </div>
</div>



</form>
<script type="text/javascript">
$(document).ready(function(){
      fetch_records()
});

function fetch_records(){
               var status_wise_filter = $("#status_wise_filter").val();
               var chest = 0;
               if(status_wise_filter == "choose_date"){
                  var from_date = $("#from_date").val(); 
                  var to_date = $("#to_date").val(); 
                  if(from_date == ""){
                        $("#from_date_msg").html("<?= $this->lang->line("Please enter from date")?> ");
                        chest = 1;
                  }else{
                         $("#from_date_msg").html("");
                         chest = 0;
                  }
                  if(to_date == ""){
                        $("#to_date_msg").html("<?= $this->lang->line("Please enter To date")?> ");
                        chest = 1;
                  }else{
                        $("#to_date_msg").html("");
                        chest = 0;
                  }
                  $("#orders").html("<?php echo $this->lang->line("orders") ?>");
                  $("#revenues").html("<?php echo $this->lang->line("revenue") ?>");
                  $("#customers").html("<?php echo $this->lang->line("customers") ?>");
               }else if(status_wise_filter == "all_records"){
                  $("#orders").html("<?php echo $this->lang->line("orders") ?>");
                  $("#revenues").html("<?php echo $this->lang->line("revenue") ?>");
                  $("#customers").html("<?php echo $this->lang->line("customers") ?>");
               }else if(status_wise_filter == "today"){
                  $("#orders").html("<?php echo $this->lang->line("today_orders")?>");
                  $("#revenues").html("<?php echo $this->lang->line("today_revenue")?>");
                  $("#customers").html("<?php echo $this->lang->line("Today's Joined Customer")?>");
               }
               if(chest == 0){
                  $.ajax({
                       url:"<?php echo base_url(); ?>client/shopware/fetch_record",
                       type:"post",
                        data: $("#all_records_form").serialize(),
                        dataType: 'json',
                        success:function(data){
                                    if(data.status == "success"){
                                       var tbodyhtml = "";
                                       if(data.order_data != undefined){


                                          if(data.order_data.orders_data.length > 0){
                                             var j=0;
                                             $("#today_orders").html(data.order_data.total)
                                             $("#today_revenue").html(data.order_data.total_revenue)
                                          for(var i= 0;i<data.order_data.orders_data.length;i++){
                                              j = i +1;
                                             tbodyhtml += "<tr>";
                                             tbodyhtml += "<td>"+j+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].order_number+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].customer_name+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].customer_email+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].shipping_cost+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].price+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].order_date+"</td>";
                                             tbodyhtml += "<td>"+data.order_data.orders_data[i].order_status+"</td>";
                                             tbodyhtml += "</tr>";
                                          }
                                       }else{
                                             tbodyhtml += "<tr>";
                                             tbodyhtml += "<td align='center' colspan='8'><?php echo $this->lang->line("no_records_found")?></td>";
                                             tbodyhtml += "</tr>";
                                       }
                                    }else  {
                                             $("#today_orders").html(0)
                                             $("#today_revenue").html(0.00)
                                             tbodyhtml += "<tr>";
                                             tbodyhtml += "<td align='center' colspan='8'><?php echo $this->lang->line("no_records_found")?></td>";
                                             tbodyhtml += "</tr>"; 
                                    }
                                       $("#order_fetch_data").html(tbodyhtml);
                                    
                                    }else{
                                       $("#error_fetch_data").html(data.msg);
                                    }     
                                 }
                            });
               }
}

function chk(select_value){
      if(select_value == "choose_date"){
         $("#date_range").show();
      }else{
         $("#date_range").hide();
      }
}

</script>

<?php $this->load->view("client/layout/footer_new"); ?>