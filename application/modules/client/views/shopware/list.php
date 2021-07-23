<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>
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
.btn + .btn{ margin-left: 3px; padding: 5px 6px; font-size: 16px;}

</style>
<div class="container-fluid">
<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("shop_proj_list") ?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
					<a class="btn btn-primary" href="<?php echo base_url();?>client/shopware/create">
						<?= $this->lang->line("new_shop_project") ?>
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
						<p id="errmsg" style="color: red;"></p>
						<p id="sucmsg" style="color: green;"></p>
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
							<thead>
								<tr>
									<th>
										#
									</th>
									<th ><?= $this->lang->line("project_name") ?></th>
									<th ><?= $this->lang->line("shopware_proj_url") ?></th>
									<th ><?= $this->lang->line("created_date") ?> </th>
									<th ><?= $this->lang->line("action")?> </th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($slist) > 0){
									$cnt = 1;
									foreach($slist as $sl){				
										?>
										<tr>
											<td><?= $cnt++; ?></td>
											<td><?= $this->encryption->decrypt($sl->project_name); ?></td>
											<td><?= $this->encryption->decrypt($sl->url); ?></td>
											<td><?= displayDate($sl->added_date) ?></td></td>
											<td>
												<a class="btn btn-info" style="min-width: 40px;" href="<?php echo base_url();?>client/shopware/overview/<?= $sl->sproject_id ;?>" target="_blank"> <i class="flaticon-view"></i> </a>
												<a class="btn btn-primary" style="min-width: 40px;" href="<?php echo base_url();?>client/shopware/edit/<?= $sl->sproject_id ;?>"> <i class="flaticon-pencil-1"></i> </a>

												<a class="btn btn-danger" style="min-width: 40px;" href="javascript:" onclick='deletesproject(<?= $sl->sproject_id ;?>)'> <i class="flaticon-trash"></i> </a>
											</td>
										</tr>
										<?php
									}
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
    	$('#memListTable').DataTable( {
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });
	});


	function deletesproject(sproject_id){
		var chk = confirm("<?= $this->lang->line("delete_confirm_proj_msg");?>");
			if(chk){
				$.ajax({
				           url:"<?php echo base_url();?>client/shopware/delete",
				           type:"post",
				           beforeSend:function(){
				           		$("#cover-spin").show()
				           },
				            data: {sproject_id:sproject_id},
				            dataType: 'json',
				            success:function(data)
				            		{
				            			if(data.status == "success"){
				            				$("#errmsg").html('');
				            				$("#sucmsg").html(data.msg);
				            				location.reload();
				            			}else{
				            				$("#sucmsg").html('');
				            				$("#errmsg").html(data.msg);
				            			}
				                        $("#cover-spin").hide()     
				                     }
				                });
			}
}
</script>
<?php $this->load->view("client/layout/footer_new");?>
