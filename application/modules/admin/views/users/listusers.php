<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>

<?php 
	$roles_array = array();
	foreach ($roles as $role){
		$roles_array[$role->role_id] = $role->role_name;
	}
?>

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
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("user_list");?></h3>
				</div>
				<div class="flex-col-6 text-right">
					<a class="btn btn-primary" href="<?php echo base_url();?>admin/users/create">
						<?= $this->lang->line("new_user");?>
					</a> 
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<!-- <div class="panel-heading panel-button">
						<div class="flex-row">
							<div class="flex-col-auto">
								
							</div>
						</div>
					</div> -->
					<div class="panel-body no-padding p-0">
						<!-- Custom Filter -->
						
						<br>

						<table id="usrtable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                  
							<thead>
								<tr>
									<th><?= $this->lang->line("sr_no");?></th>
									<th style="width:10%"><?= $this->lang->line("name");?></th>
									<th style="width:10%"><?= $this->lang->line("email");?></th>
									<th style="width:5%"><?= $this->lang->line("Phone");?></th>
									<th style="width:10%"><?= $this->lang->line("City");?></th>
									<th style="width:10%"><?= $this->lang->line("user_roles");?></th>
									<th style="width:50%"><?= $this->lang->line("action");?>  </th> 
								</tr>
							</thead>
						</table>
					</div>
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>

	</div>
</div>
<div class="modal fade" id="plan_details-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Plan Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="plandata">


				
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip({container: 'body'})
    	$('#usrtable').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        },
	        "processing": true,
            "serverSide": true,
            "ajax":{
		     "url": "<?php echo base_url('admin/users/getlist') ?>",
		     "dataType": "json",
		     "type": "POST",
		     "data":{  '<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }
		                   },
	    "columns": [
		          { "data": "sr_no" },
		          { "data": "name" },
		          { "data": "email" },
		          { "data": "phone" },
		          { "data": "city" },
		          { "data": "user_roles" },
		          { "data": "action" },
		       ]	
	     });

	});
	function viewUser(data){

		swal(data.password, {
			title: "<?= $this->lang->line("info") ?>",
			type: "info",
			buttons: true,
			timer: 3000
		})
	}


	function assignreseller(user_id){
		var chk = "<?php echo $this->lang->line("role_confirm")?>";

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
		           url:"<?php echo base_url();?>admin/users/assign_role/"+user_id,
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
							buttons: true,
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
	function deleteUser(client_id){

		var chk = "<?php echo $this->lang->line("del_user_conf")?>";

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
		           url:"<?php echo base_url();?>admin/users/delete/"+client_id,
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
							buttons: true,
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
	function plan_details(client_id){
		$.ajax({
	       url:"<?php echo base_url();?>admin/users/plan_details/",
	       type:"post",
	       beforeSend:function(){
	       		$("#cover-spin").show()
	       },
	        data:{client_id:client_id},
	                success:function(data){
	                    
	                    	$("#plandata").html(data);
	                        $("#plan_details-Modal").modal("show");
	                    $("#cover-spin").hide()     
	                 }
	            });
   	 
	}


	$("#filterRole").change(function() {
		var selectedVal = $(this).val();
		$.ajax({
			type:"post",
			url: "<?php echo base_url();?>admin/users/",
			data: {
				selected:selectedVal
			},
			beforeSend:function(){
	       		$("#cover-spin").show()
	       },
			success: function(data) { 
				//console.log(selectedVal); 
			  	//location.reload();
			  	$("#cover-spin").hide() 
			  	$("body").html(data);
			}
		});
	});
</script>
<?php $this->load->view("admin/layout/footer_new");?>
