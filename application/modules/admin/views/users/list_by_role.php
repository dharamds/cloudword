<?php 
	$roles_array = array();
	foreach ($roles as $role){
		if($role->role_id == 2){
			$roles_array[$role->role_id] =  $this->lang->line("customer");
		}else{
			$roles_array[$role->role_id] =  $this->lang->line($role->role_name);	
		}
		
	}
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

												<!-- <a href="javascript:" onclick='viewUser(<?php //echo json_encode($usr);?>)'><i class="flaticon-view"></i></a> -->

												<a style="min-width: 40px;" href="<?php echo base_url();?>admin/users/update/<?php echo base64_encode($usr->client_id);?>" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary"><i class="flaticon-pencil"></i></a>
												
												<a style="min-width: 40px;" href="javascript:" onclick='deleteUser(<?php echo $usr->client_id;?>)' data-toggle="tooltip" data-placement="top" title="Delete" class="btn btn-danger"><i class="flaticon-trash"></i></a>
												
												<!-- <a href="<?php //echo base_url();?>admin/users/plan_details/<?php //echo $usr->client_id;?>" target="_blank" data-toggle="tooltip" title="Plan Details" ><i  class="flaticon-user"></i></a> -->

											</td>
										</tr>
										<?php
										$cnt++;
									}	
								?>