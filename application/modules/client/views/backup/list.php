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

</style>

<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">                            

				<div class="flex-col-sm-6">
					<h3><?= $this->lang->line("ftp_bkp_list")?></h3>
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
						<table id="ftpbackup" class="table table-bordered table-striped table-hover" cellspacing="0">                              
							<thead>
								<tr>
									<th><?= $this->lang->line("sr_no")?></th>
									<th><?= $this->lang->line("project_name")?></th>
									<th><?= $this->lang->line("file_name")?></th>
									<th><?= $this->lang->line("downloading_status")?></th>
									<th><?= $this->lang->line("status")?></th>
									<th><?= $this->lang->line("created_date")?></th>
									<th><?= $this->lang->line("file_size")?></th>
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
										<td><?php
										if($bkp->status == "success" ){
											echo "100%";
										}else if($bkp->total_files_folders > 0){
											$perc = round(($bkp->completed_files_folders/$bkp->total_files_folders) * 100);
											echo $perc."%";
										}else{
											echo "100%";
										}  
										?></td>
										<td><?= $bkp->status ?></td>
										<td><?= $bkp->added_date ?></td>
										<td>
										<?php	
										$currentFile = "./projects/".$bkp->folder_name."/ftp_server/".$bkp->file_name;
        								$size = filesize($currentFile);
        								echo $this->general->convert_size($size);
										?>
										</td>
										<td><?php if($bkp->status == "success"){

												?>
											<a style="margin:5px;min-width: 40px;" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("restore_bkp")?>' href="javascript:" class="btn btn-primary waves-effect waves-light" onclick='restorebkptest(<?=$bkp->backup_id;?>)'><i class="flaticon-backup-2"></i></a>
											
											<a style="margin:5px;min-width: 40px;" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("del_bkp")?>' href="javascript:" class="btn btn-primary waves-effect waves-light" onclick='deletebkp(<?=$bkp->backup_id;?>)'><i class="flaticon-delete-2"></i></a>

											<a style="margin:5px;min-width: 40px;" data-toggle="tooltip" data-placement="top" title='<?= $this->lang->line("download")?>' href="<?=base_url()?>admin/backup/downloadftp/<?=$bkp->backup_id;?>" class="btn btn-primary waves-effect waves-light"> <?= $this->lang->line("download")?> </a>
											<?php
											}else{
												?>
												<a style="margin:5px;min-width: 40px;" href="javascript:"  data-toggle="tooltip" data-placement="top" title='Check live status' class="btn btn-primary waves-effect waves-light" onclick='checklivestatus(<?php echo $bkp->foldersdata; ?> )'><i class="flaticon-speedometer-1"></i></a>
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
					<!-- <div class="panel-footer"></div> -->
				</div>
			</div>
		</div>
	</div>

	</div>
</div>

<div class="modal fade livestatusmodal" id="livestatusmodal-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document" style="width: 500px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Live Status of FTP Backup Process</h4>
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="progress" style="height:20px">
				    <div class="progress-bar" id="livestatuspercent" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;height:20px">
				      0%
				    </div>
				</div>
				<div class="alert alert-success" role="alert" id="successmsggg" style="display: none;">
  					<?= $this->lang->line("backup_success_taken") ?>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table id="livestatutable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
							<thead>
								<th>#</th>
								<th><?= $this->lang->line("file_folder_name")?></th>
								<th><?= $this->lang->line("status")?></th>
							</thead>
							<tbody id="livestatutbody">
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12" id="error_logfilecontent">

					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="restore-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document" style="width: 500px;">
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
							<div class="card-block" style="overflow-x:hidden;overflow-y:auto;">
								<p style="color: orange">Please do not close browser till Backup process get done</p>
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


				<div class="row">
					<div class="col-md-12">
						<table id="livestatustable" class="table table-bordered table-striped table-hover" cellspacing="0" style="display:none;">
							<thead>
								<th>#</th>
								<th><?= $this->lang->line("file_folder_name")?></th>
								<th><?= $this->lang->line("status")?></th>
							</thead>
							<tbody id="livestatustbody">
								
							</tbody>
						</table>
					</div>
				</div>
			


			</div>

		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
    	$('[data-toggle="tooltip"]').tooltip({container: 'body'})
    	$('#ftpbackup').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });
	

	});
	function restorebkptest(bkp_id){

		$("#showingDoneCount").html("");
		$("#processingWindow").hide();
		$('#livestatustbody').html("");
        $('#livestatustable').hide();


		$('#probar').css("width","0%").html("");

		$("#showingProcessMsg").html('<div class="form-group row"><div class="col-sm-12"><div> <?= $this->lang->line("Are you sure to start restore now") ?> </div><br><button type="button" onclick="startbkprestore('+bkp_id+');" class="btn btn-primary m-b-0"><?= $this->lang->line("restore_bkp") ?></button><button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button></div></div>');
		$("#restore-Modal").modal({backdrop: 'static', keyboard: false});

		
	}



function startbkprestore(bkp_id){


	//alert('clicked');
	
	$('#showingProcessMsg').html('<p><?=$this->lang->line("Starting Restore Process");?></p>');
	$('#showingProcessMsg').append('<p><?=$this->lang->line("Extracting zip file");?>Extracting zip file</p>');
	$('#showingProcessMsg').append('<p><?=$this->lang->line("For large files it might take more time to restore");?>For large files it might take more time to restore</p>');


	$.ajax({
		url:"<?php echo base_url();?>admin/backup/startftprestore",
		method:"post",
		data:{bkp_id:bkp_id},
		dataType:'json',
		success:function(data){
			
			//alert(data);
			//return false;

			if(data.status == 'failed'){
				$('#showingProcessMsg').prepend('<p style="color:red;">'+data.msg+'</p>');
				$('#showingProcessMsg').prepend('<p><?=$this->lang->line("Please retry");?> </p>');
			}

			if(data.status == 'success'){

				//return false;
				//alert(data.path);
				//var filepath = data.path;
				var processing = "Processing";
				var successmsg = "Success";


				var tbodyhtml = '';	
            	var cnt = 1;
            	$.each(data.allFiles, function(key, value ){
            		var val = value.split("/");
            		var temp = val[val.length-1];
						if(temp =="." || temp ==".." || temp =="/.." || temp =="/."){
            			tbodyhtml += "";
            		}else{
            			tbodyhtml += '<tr style="background:orange" id="status_id_'+key+'">';
            			tbodyhtml += '<td>'+cnt+'</td>';
            			tbodyhtml += '<td>'+value+'</td>';
            			tbodyhtml += '<td id="st_'+key+'">'+processing+'</td>';
            			tbodyhtml += '</tr>';
            			cnt++;
            		}
				});


            	$('#livestatustbody').html(tbodyhtml);
            	$('#livestatustable').show();

            	//return false;
				if(data.allFilesCnt > 0){

					$('#processingWindow').show();
				    
				    $.each(data.allFiles, function (fileKey, fileQuery) {
				    	var tKey = fileKey+1;	

				    
				    	setTimeout(function(){ 
				    		
				    	
					    	$.ajax({
								url:"<?php echo base_url();?>admin/backup/restorefilesonebyone",
								method:"post",
								data:{fileQuery:fileQuery,tKey:tKey,bkp_id:bkp_id},
								dataType:'json',
								async: false,
								success:function(res){
									
									if(res.status == 'failed'){
										$("#status_id_"+fileKey).css("background","red");
								        $("#st_"+fileKey).html("failed");
									}

									if(res.status == 'success'){

										$("#status_id_"+fileKey).css("background","green");
								        $("#st_"+fileKey).html("success");

								        

										var per = ((tKey/data.allFilesCnt)*100).toFixed(0);
										$('#probar').css("width",""+per+"%");
										$('#probar').html(""+per+"% <?=$this->lang->line("Completed");?>");

										if(tKey == data.allFilesCnt){	
											
											$("#showingDoneCount").prepend('<div class="form-group row"><div class="col-sm-12"><div> <button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close") ?></button> </div></div>');


											$.ajax({
												url:"<?php echo base_url();?>admin/backup/deletetempfolder",
												method:"post",
												data:{bkp_id:bkp_id},
												dataType:'json',
												async: false,
												success:function(res){
													
												}
											});


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




	function restorebkp(bkp_id){
		$chk = confirm("<?= $this->lang->line("restore_bkp_cnfirm")?>");
		if($chk){
			$.ajax({
				url:"<?php echo base_url();?>client/backup/restore",
				method:"post",
				 beforeSend:function(){
				           		$("#cover-spin").show()
				           },
				data:{bkp_id:bkp_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(data){
					var obj = JSON.parse(data);
					alert(obj.msg);
					$("#cover-spin").hide();
					if(obj.status == "success"){

						location.reload();
					}
					
				}
			});

		}

	}
	function deletebkp(bkp_id){
		$chk = confirm("<?= $this->lang->line("delete_bkp_cnfirm")?>");
		if($chk){
			$.ajax({
				url:"<?php echo base_url();?>client/backup/delete",
				method:"post",
				 beforeSend:function(){
				           		$("#cover-spin").show()
				           },
				data:{bkp_id:bkp_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
				success:function(data){
					var obj = JSON.parse(data);
					alert(obj.msg);
					$("#cover-spin").hide();
					if(obj.status == "success"){
						location.reload();
					}
					
				}
			});

		}
	}
	function checklivestatus(data){
		var tbodyhtml = '';
		var cnt = 1;
		var per = 0;
						        $.each(data, function(key, value ){
						        		if(value.status == "success"){
						        			var clr = 'style="background:green"';
						        			per++;
						        		}else{
						        			var clr = 'style="background:orange"';
						        		}

				            			tbodyhtml += '<tr '+clr+' >';
				            			tbodyhtml += '<td>'+cnt+'</td>';
				            			tbodyhtml += '<td>'+value.filename+'</td>';
				            			tbodyhtml += '<td>'+value.status+'</td>';
				            			tbodyhtml += '</tr>';
				            			cnt++;
								});
								

						        var perc = Math.round((per/cnt) * 100);
						        if(perc == 100){
						        	$("#successmsggg").show();
						        }
						        $("#livestatuspercent").html(perc+"%");
								$("#livestatuspercent").attr("aria-valuenow",perc);
								$("#livestatuspercent").css("width",perc+"%");
								$("#livestatutbody").html(tbodyhtml);
								$("#livestatusmodal-Modal").modal("show");
	}	


</script>
<?php $this->load->view("client/layout/footer_new");?>
