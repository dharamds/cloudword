<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>

<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("plan_list")?></h3>
				</div>
				<div class="flex-col-6 text-right">
					<a class="btn btn-primary" href="<?php echo base_url();?>admin/plan/add">
						<?= $this->lang->line("add_new_plan")?>
					</a> 
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
									<th style="width:10%"><?= $this->lang->line("description")?></th>
									<th style="width:10%"><?= $this->lang->line("ftp_space")?></th>
									<th style="width:10%"><?= $this->lang->line("db_space")?></th>
									<th style="width:10%"><?= $this->lang->line("expiry_days")?></th>
									<th style="width:40%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$cnt = 1;
									foreach ($planlist as $plan){				
										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?php echo $plan->name;?></td>
											<td><?= $plan->description?></td>
											<td><?= $this->general->formatBytes($plan->ftp_space_bytes)?></td>
											<td><?= $this->general->formatBytes($plan->db_space_bytes)?></td>
											<td><?= $plan->expiry_days?></td>
											<td><a style="min-width: 40px;" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="View" href="<?php echo base_url();?>admin/plan/add/<?= $plan->id; ?>?type=view" ><i class="flaticon-view"></i></a>
												<a style="min-width: 40px;" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Edit"  href="<?php echo base_url();?>admin/plan/add/<?= $plan->id; ?>" ><i class="flaticon-edit"></i></a>
												<a style="min-width: 40px;" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:" onclick="deleteplan(<?= $plan->id ?>)"><i class="flaticon-trash"></i></a>
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

	function deleteplan(plan_id){
		var chk = "<?php echo $this->lang->line("sure_delete_plan")?>";

		swal(chk, {
			buttons: {
				cancel: "<?php echo $this->lang->line("No")?>",
				catch: {
					text: "<?php echo $this->lang->line("Yes")?>",
					value: "catch",
				},
			},
		})
		.then((confirmValue) => {
			if(confirmValue == 'catch'){
				$.ajax({
		           url:"<?php echo base_url();?>admin/plan/delete/"+plan_id,
		           type:"get",
		           beforeSend:function(){
		           		$("#cover-spin").show()
		           },
		            dataType: 'json',
		            success:function(data){
		            	$("#cover-spin").hide();
		            	swal(data.msg, {
							title: "<?= $this->lang->line("great") ?>",
							type: "success",
							timer: 3000
						}).then(() => {
							location.reload();
						})
		            }
		        });
			}
			else{
				return false
			}
		});

	}
					

</script>
<?php $this->load->view("admin/layout/footer_new");?>
