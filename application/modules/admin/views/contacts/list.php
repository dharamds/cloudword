<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>

<style type="text/css">
	#cover-spin {
    position:fixed;
    width:100%;
    left:0;right:0;top:0;bottom:0;
    background-color: rgba(255,255,255,0.7);
    z-index:9999;
    display:none;
}

@-webkit-keyframes spin {
	from {-webkit-transform:rotate(0deg);}
	to {-webkit-transform:rotate(360deg);}
}

@keyframes spin {
	from {transform:rotate(0deg);}
	to {transform:rotate(360deg);}
}

#cover-spin::after {
    content:'';
    display:block;
    position:absolute;
    left:48%;top:40%;
    width:40px;height:40px;
    border-style:solid;
    border-color:black;
    border-top-color:transparent;
    border-width: 4px;
    border-radius:50%;
    -webkit-animation: spin .8s linear infinite;
    animation: spin .8s linear infinite;
}

</style>
<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("contact_enquiries");?></h3>
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
		
					<div class="panel-body no-padding p-0">
						

						<table id="contacttable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<th><?= $this->lang->line("sr_no");?></th>
									<th style="width:10%"><?= $this->lang->line("name");?></th>
									<th style="width:10%"><?= $this->lang->line("email");?></th>
									<th style="width:5%"><?= $this->lang->line("Phone");?></th>
									<th style="width:10%"><?= $this->lang->line("message");?></th>
									<th style="width:10%"><?= $this->lang->line("date");?></th>
									<th style="width:50%"><?= $this->lang->line("action");?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$cnt = 1;
									foreach ($contactlist as $singleCon){				
										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?= $singleCon->name ?></td>
											<td><?= $singleCon->email?></td>
											<td><?= $singleCon->phone?></td>
											<td><?= strlen($singleCon->message) > 100 ? substr($singleCon->message, 0, 100) : $singleCon->message ?></td>
											<td style="width: 100px;"><?= displayDate($singleCon->added_date) ?></td>
											<td style="width: 110px;">

												<a style="min-width: 40px;" href="<?php echo base_url();?>admin/contacts/reply/<?php echo base64_encode($singleCon->contact_id);?>" data-toggle="tooltip" data-placement="top" title="Reply" class="btn btn-primary"><i class="flaticon-mail"></i></a>
												
												<a style="min-width: 40px;" href="javascript:" onclick='deleteContact(<?php echo $singleCon->contact_id;?>)' data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-danger"><i class="flaticon-trash"></i></a>
												
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
    	$('#contacttable').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });

	});

	function viewContact(data){
		//alert(data.password)
	}
	
	function deleteContact(contact_id){
		var chk = "<?php echo $this->lang->line('confirm_del_enq');?>";

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
		           url:"<?php echo base_url();?>admin/contacts/delete/"+contact_id,
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
