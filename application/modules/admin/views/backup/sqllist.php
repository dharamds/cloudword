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

				<div class="flex-col-sm-6">
					<h3><?= $this->lang->line("db_bkp_list")?></h3>
				</div>
				<div class="flex-col-sm-4">
					
				</div>
			</div>
		</div>        
	</div>

	 <?php if( $this->session->flashdata('notallowtoaccess') == true){ ?>
         <div class="row">                    
            <div class="col-md-12">
               <div class="filter-container flex-row">
                  <div class="flex-col-md-12">
                     <h3 class="filter-content-title" style="color:red;"> <?= $this->lang->line("cant_access")?> </h3>
                  </div>
                 
               </div>
            </div>        
         </div>
      <?php } ?>


	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-heading panel-button">
						<div class="flex-row">
							<form method="post" action="<?php echo base_url('users/download_csv')?>" id="export_user_form">
								<input type="hidden" name="check_users" id="check_users">
								<div class="flex-col">
								                           
								</div>
							</form>
							<div class="flex-col-auto">
								<!-- <div class="panel-ctrls" id="manage_user_control"></div> -->
							</div>
						</div>
					</div>
					<div class="panel-body no-padding p-0">
						<table id="sqlbackup" class="table table-bordered table-striped table-hover" cellspacing="0">                              
							<thead>
								<tr>
									<th><?= $this->lang->line("sr_no")?></th>
									<th><?= $this->lang->line("project_name")?></th>
									<th><?= $this->lang->line("file_name")?></th>
									<th><?= $this->lang->line("created_date")?></th>
									<th><?= $this->lang->line("action")?></th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$cnt = 1;
								foreach($backups as $bkp) {
									?>
									<tr>
										<td><?= $cnt ?></td>
										<td><?= $bkp->project_name ?></td>
										<td><?= $bkp->file_name ?></td>
										<td><?= $bkp->added_date ?></td>
										<td>

										<a style="margin:5px;min-width: 40px;" href="javascript:" class="btn btn-primary waves-effect waves-light" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("restore_bkp")?>' onclick='restorebkp(<?=$bkp->backup_id;?>)'><i class="flaticon-backup-2"></i><!-- <?= $this->lang->line("restore_bkp")?> --></a>
										
										<a style="margin:5px;min-width: 40px;" href="javascript:" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("del_bkp")?>' class="btn btn-primary waves-effect waves-light" onclick='deletebkp(<?=$bkp->backup_id;?>)'><i class="flaticon-delete-2"></i><!-- <?= $this->lang->line("del_bkp")?> --></a>	

										<a style="margin:5px;min-width: 40px;"  data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("download")?>' href="<?=base_url()?>admin/backup/downloadsql/<?=$bkp->backup_id;?>" class="btn btn-primary waves-effect waves-light"> <?= $this->lang->line("download")?> </a>

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




<div class="modal fade" id="mysqlrestore-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("restore_bkp") ?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-block" style="height:500px;overflow-x:hidden;overflow-y:auto;">
									<div id="processingWindow" style="display:none;">
										<h1 id="processHeading"><?= $this->lang->line("Processing") ?>..</h1>
										
										<div class="progress" style="height:20px;">
										  <div class="progress-bar progress-bar-striped active" id="probar" role="progressbar"
										  aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height:20px;">
										  </div>
										</div>

										<h3 id="showingDoneCount"></h3>


									</div>	

									<div id="showingProcessMsg">
									<div>	
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
	

	




<script type="text/javascript">
	$(document).ready(function() {
    	$('[data-toggle="tooltip"]').tooltip({container: 'body'})
		$('#sqlbackup').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });
	});


	function restorebkp(bkp_id){

		$("#showingDoneCount").html("");
		$("#processingWindow").hide();
		$('#probar').css("width","0%").html("");

		$("#showingProcessMsg").html('<div class="form-group row"><div class="col-sm-12"><div> <?= $this->lang->line("Are you sure to start restore now") ?> </div><br><button type="button" onclick="startbkprestore('+bkp_id+');" class="btn btn-primary m-b-0"><?= $this->lang->line("restore_bkp") ?></button><button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button></div></div>');
		$("#mysqlrestore-Modal").modal({backdrop: 'static', keyboard: false});

		
	}




function startbkprestore(bkp_id){


	//alert('clicked');
	
	$('#showingProcessMsg').html('<p><?=$this->lang->line("Starting Restore Process");?></p>');
	$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Collecting database information. Please wait");?></p>');
	$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Creating database connection");?></p>');
	$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Collection table information");?></p>');
	$('#showingProcessMsg').prepend('<p><?=$this->lang->line("For large database it might take more time to restore");?></p>');


	$.ajax({
		url:"<?php echo base_url();?>admin/backup/startrestore",
		method:"post",
		data:{bkp_id:bkp_id},
		dataType:'json',
		success:function(data){
			
			//alert(data);
			//return false;

			if(data.status == 'failed'){
				$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Above error occur while connecting to database");?> </p>');
				$('#showingProcessMsg').prepend('<p style="color:red;">'+data.msg+'</p>');
				$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Please retry");?> </p>');
			}

			if(data.status == 'success'){

				//return false;
				//alert(data.path);
				var filepath = data.path;

				$('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Database connection succeesfull");?></p>');
				$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Counting number of queries to restore, Please wait sometime");?></p>');
				$('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Total no of queries to restore is");?> '+data.querycount+'</p>');

				if(data.querycount > 0){

					$('#processingWindow').show();
				    $('#showingProcessMsg').prepend('<p style="color:green;"> <?=$this->lang->line("Processing database queries one by one, it might be take some time");?> </p>');

				    $.each(data.tabledata, function (tableKey, tableQuery) {
				    	var tKey = tableKey+1;	

				    
				    	setTimeout(function(){ 
				    		
				    		$('#showingProcessMsg').prepend('<p style="color:green;"><?=$this->lang->line("Processing of query");?> '+tKey+' <?=$this->lang->line("started");?></p>');

					    	$.ajax({
								url:"<?php echo base_url();?>admin/backup/restoretableonebyone",
								method:"post",
								data:{tableQuery:tableQuery,filepath:filepath,tKey:tKey,bkp_id:bkp_id},
								dataType:'json',
								async: false,
								success:function(res){
									
									if(res.status == 'failed'){
										$('#showingProcessMsg').prepend('<p style="color:red;">'+res.msg+'</p>');
									}

									if(res.status == 'success'){


										$('#showingProcessMsg').prepend('<p style="color:green;">'+res.msg+'</p>');

										var per = ((tKey/data.querycount)*100).toFixed(0);
										$('#probar').css("width",""+per+"%");
										$('#probar').html(""+per+"% <?=$this->lang->line("Completed");?>");

										if(tKey == data.querycount){	
											$('#showingDoneCount').html(tKey+ ' <?=$this->lang->line("of");?> ' +data.querycount+ ' <?=$this->lang->line("queries done");?>.');
											$('#showingDoneCount').prepend('<p style="color:green;"><?=$this->lang->line("Database restore has been completed successfully");?></p>');

											$("#showingDoneCount").prepend('<div class="form-group row"><div class="col-sm-12"><div> <button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button> </div></div>');

										}else{
											$('#showingDoneCount').html(tKey+ ' <?=$this->lang->line("of");?> ' +data.querycount+ '  <?=$this->lang->line("queries done");?>.');
										}


										
									}
									
									
								}
							});


				    	 }, 1000);

				        
				    		

				    });






				}




			}




			
		}
	});





}










	function restorebkpold(bkp_id){
		$chk = confirm("<?= $this->lang->line("restore_bkp_cnfirm")?>");

		// if($chk){
		// 	$.ajax({
		// 		url:"<?php echo base_url();?>admin/backup/sqlrestore",
		// 		method:"post",
		// 		 beforeSend:function(){
		// 		           		$("#cover-spin").show()
		// 		           },
		// 		data:{bkp_id:bkp_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
		// 		success:function(data){
		// 			var obj = JSON.parse(data);
		// 			alert(obj.msg);
		// 			$("#cover-spin").hide()
		// 			if(obj.status == "success"){
		// 				location.reload();
		// 			}
		// 		}
		// 	});

		// }

	}

	function deletebkp(bkp_id){
		$chk = confirm("<?= $this->lang->line("delete_bkp_cnfirm")?>");

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
					url:"<?php echo base_url();?>admin/backup/deletesql",
					method:"post",
					 beforeSend:function(){
					           		$("#cover-spin").show()
					           },
					data:{bkp_id:bkp_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
					success:function(data){
						var obj = JSON.parse(data);
						$("#cover-spin").hide();

						if(obj.status == "success"){
							swal(obj.msg, {
								title: "<?= $this->lang->line("great") ?>",
								type: "success",
								buttons: true,
								timer: 3000
							}).then(() => {
								location.reload();
							})
						}
						else{
							swal(obj.msg, {
								title: "<?= $this->lang->line("oops") ?>",
								type: "error",
								buttons: true,
								timer: 3000
							})
						}
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
