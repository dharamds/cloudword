<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>

<?php 
$roles_array = array('a','admin','user','reseller');

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
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("user_list");?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
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
							<tbody>
								<?php
									$cnt = 1;
									foreach ($userlist as $usr){				
										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?php echo $usr->fname." ".$usr->lname;?></td>
											<td><?= $usr->email?></td>
											<td><?= $usr->phone?></td>
											<td><?= $usr->city?></td>
											<td><?= $roles_array[$usr->role_id]?></td>
											<td>

												<!-- <a href="javascript:" onclick='viewUser(<?php echo json_encode($usr);?>)'><i class="flaticon-view"></i></a> -->

												<a href="<?php echo base_url();?>admin/users/update/<?php echo base64_encode($usr->client_id);?>"><i class="flaticon-pencil"></i></a>
												
												<a href="javascript:" onclick='deleteUser(<?php echo $usr->client_id;?>)'><i class="flaticon-trash"></i></a>
												
												<!-- <a href="<?php echo base_url();?>admin/users/plan_details/<?php echo $usr->client_id;?>" target="_blank" data-toggle="tooltip" title="Plan Details" ><i  class="flaticon-user"></i></a> -->

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
<!-- <div class="modal fade" id="editUser-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update User</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">
								<div class="card-block">
									<form id="userEditForm" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="euser_id" id="euser_id">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">First Name </label>
											<div class="col-sm-4">
												<input type="text" class="form-control" name="f_name" id="f_name" placeholder="Enter First Name">
												<span style="color: red;" class="f_name_msg"></span>
											</div>
											<label class="col-sm-2 col-form-label">Last Name </label>
											<div class="col-sm-4">
												<input type="text" class="form-control" name="l_name" id="l_name" placeholder="Enter Last Name">
												<span style="color: red;" class="l_name_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Phone</label>
											<div class="col-sm-4">
												<input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Phone">
												<span style="color: red;" class="phone_msg"></span>
											</div>
											<label class="col-sm-2 col-form-label">Email </label>
											<div class="col-sm-4">
												<input type="text" class="form-control" name="email" id="email" placeholder="Enter Email">
												<span style="color: red;" class="email_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Address</label>
											<div class="col-sm-4">
												<textarea id="address"  name="address" class="form-control"></textarea>
												<span style="color: red;" class="address_msg"></span>
											</div>
											<label class="col-sm-2 col-form-label">Landmark </label>
											<div class="col-sm-4">
												<textarea id="landmark"  name="landmark" class="form-control"></textarea>
												<span style="color: red;" class="landmark_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">City</label>
											<div class="col-sm-4">
												<input type="text" id="city"  name="city" class="form-control">
												<span style="color: red;" class="city_msg"></span>
											</div>
											<label class="col-sm-2 col-form-label">Zip code </label>
											<div class="col-sm-4">
												<input type="text" id="zipcode"  name="zipcode" class="form-control">
												<span style="color: red;" class="zipcode_msg"></span>
											</div>
										</div>
										<div class="form-group row">
											<div class="input-group">
											<div class="col-sm-6" id="usererrormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="submit" class="btn btn-primary m-b-0">Submit</button>
												<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
											</div>
										</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div> -->
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
		
    	$('#usrtable').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
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


</script>
<?php $this->load->view("admin/layout/footer_new");?>
