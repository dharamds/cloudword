<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>
<div class="container-fluid">
	<div class="row mr-0">
	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("plan_subscription_details")?></h3>
				</div>
				<div class="flex-col-6 text-right"> 
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<table id="plantable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                   
							<thead>
								<tr>
									<th><?= $this->lang->line("Sr_No")?></th>
									<th style="width:10%"><?= $this->lang->line("name")?></th>
									<th style="width:10%"><?= $this->lang->line("email")?></th>
									<th style="width:10%"><?= $this->lang->line("plan_name")?></th>
									<th style="width:10%"><?= $this->lang->line("Start date")?></th>
									<th style="width:10%"><?= $this->lang->line("expiry_date")?></th>
									<th style="width:10%"><?= $this->lang->line("price")?></th>
									<th style="width:10%"><?= $this->lang->line("payment_status")?></th>
									<th style="width:10%"><?= $this->lang->line("status")?></th>
									<th style="width:40%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$cnt = 1;
									foreach ($subscription_plan_details as $plan){
										$plandata = json_decode($plan->plandata);				
										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?=$plan->fname." ".$plan->lname?></td>
											<td><?=$plan->email ?></td>
											<td><?= $plandata->name;?></td>
											<td><?= displayDate($plan->start_date,false)?></td>
											<td><?= $plan->cash_advance_flag == 1 ?  displayDate($plan->cash_advance_expiry_date,false)   : displayDate($plan->expiry_date,false)?></td>
											<td><?= $plandata->price." ".$currency?></td>
											<td><?php 
													if( ($plan->payment_status == "success" ) && !($plan->cash_advance_flag == 1)){
														echo  $this->lang->line("paid")	;	
													}else{
														echo $this->lang->line("not_paid");
													}
												?>												
											</td> 
											<td><?= $this->lang->line($plan->status) ?></td>
											<td>
												<?php if($plan->cash_advance_flag == 1){
													?>
													 
												<a href="javascript:" class="btn btn-primary" onclick="extend_warrenty(<?= $plan->sub_id ?>)"><?= $this->lang->line("Extend_Warrenty")?></a>
												<?php
														}
												?>

												<?php if($plan->payment_status == "pending"){
													?>													 
												<a href="javascript:" class="btn btn-primary" onclick="payment_status(<?= $plan->sub_id ?>)"><?= $this->lang->line("status_activate")?></a>
												<?php
														}
												?>
											</td>
										</tr>
										<?php
										$cnt++;
									}	
								?>
							</tbody>
						</table>
					</div>
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
    	$('[data-toggle="tooltip"]').tooltip({container: 'body'})
    	$('#plantable').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });

	});
	function extend_warrenty(sub_id){
		$.ajax({
		           url:"<?php echo base_url();?>admin/plan/extend_warrenty/",
		           type:"post",
		           data:{sub_id:sub_id},
		           beforeSend:function(){
		           		$("#cover-spin").show()
		           },
		            dataType: 'json',
		            success:function(data){
		            	if(data.status == "success"){
		            	swal(data.msg, {
							title: "<?= $this->lang->line("great")?>",
							type: "success",
							buttons: true,
							timer: 3000
						}).then(() => {
							location.reload();
						});
					}else{
						swal(data.msg, {
							title: "<?= $this->lang->line("oops") ?>",
							type: "success",
							buttons: true,
							timer: 3000
						});
						$("#cover-spin").hide();
					}
		            }
		        });
	}

	function payment_status(sub_id){
		$.ajax({
		           url:"<?php echo base_url();?>admin/plan/payment_status/",
		           type:"post",
		           data:{sub_id:sub_id},
		           beforeSend:function(){
		           		$("#cover-spin").show()
		           },
		            dataType: 'json',
		            success:function(data){
		            	if(data.status == "success"){
		            	swal(data.msg, {
							title: "<?= $this->lang->line("great")?>",
							type: "success",
							buttons: true,
							timer: 3000
						}).then(() => {
							location.reload();
						});
					}else{
						swal(data.msg, {
							title: "<?= $this->lang->line("oops") ?>",
							type: "success",
							buttons: true,
							timer: 3000
						});
						$("#cover-spin").hide();
					}
		            }
		        });
	}
</script>
<?php $this->load->view("admin/layout/footer_new");?>