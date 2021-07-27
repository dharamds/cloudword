<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>

<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("space_update_requests")." ".$this->lang->line("list")?></h3>
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
									<th>Sr No</th>
									<th style="width:10%"><?= $this->lang->line("ftp_space") ?></th>
									<th style="width:10%"><?= $this->lang->line("db_space") ?></th>
									<?php if($role_type != "client"){ ?>
									<th style="width:10%"><?= $this->lang->line("no_of_customers") ?></th>
									<?php } ?>
									<th style="width:10%"><?= $this->lang->line("status")?></th>
									<th style="width:10%"><?= $this->lang->line("request_date")?></th>
									<th style="width:40%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$cnt = 1;

									$status_des = [
										0 => $this->lang->line("pending"),
										1 => $this->lang->line("approve"),
										2 => $this->lang->line("unapprove"),
									];

									foreach ($requestlist as $request){				
										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?= empty($request->ftp_size) ? '-' : $this->general->formatBytes($request->ftp_size) ?></td>
											<td><?= empty($request->db_size) ? '-' : $this->general->formatBytes($request->db_size) ?></td>
											<?php if($role_type != "client"){ ?>
											<td><?= empty($request->user_count) ? '-' : $request->user_count ?></td>
											<?php } ?>
											<td><?= $status_des[$request->status] ?></td>
											<td><?= displayDate($request->request_date) ?></td>
											<td>

												<?php if ( !in_array($request->status, [1,2]) ): ?>
													<a style="min-width: 40px;" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:" onclick="deleteRequest(<?= $request->request_id ?>)"><i class="flaticon-trash"></i></a>
												<?php endif ?>

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
    	$('#plantable').DataTable( {
		    "language": {
		        "url": "<?php echo $this->lang->line("language_file")?>"
		    }
		 });

	});

	function deleteRequest(request_id){
		var chk = "<?php echo $this->lang->line("are_u_sure")?>";
		swal(chk, {
			  buttons: {
			    cancel: "<?php echo $this->lang->line("No")?>",
			    catch: {
			      text: "<?php echo $this->lang->line("Yes")?>",
			      value: "catch",
			    },
			  },
			})
			.then((value) => {
			  switch (value) {
			    case "defeat":
			      return false;
			      break;
			    case "catch":
			    $.ajax({
			           url:"<?php echo base_url();?>client/updateRequest/delete/"+request_id,
			           type:"get",
			           beforeSend:function(){
			           		$("#cover-spin").show()
			           },
			            dataType: 'json',
			            success:function(data){
			            	$("#cover-spin").hide();
			            	swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
                                                location.reload(true);
                                            });


							
			            }
			        });
			      break;
			 
			    default:
			     return false;
			  }
			});


	}
					

</script>
<?php $this->load->view("client/layout/footer_new");?>
