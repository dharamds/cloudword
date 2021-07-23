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
										<td><?= $bkp->added_date ?></td>
										<td>
										<?php	
										$currentFile = "./projects/".$bkp->folder_name."/ftp_server/".$bkp->file_name;
        								$size = filesize($currentFile);
        								echo $this->general->convert_size($size);
										?>
										</td>
										<td><a style="margin:5px;" href="javascript:" class="btn btn-primary waves-effect waves-light" onclick='restorebkp(<?=$bkp->backup_id;?>)'> <i class="flaticon-sand-clock-1"></i> <?= $this->lang->line("restore_bkp")?></a>
										<a style="margin:5px;" href="javascript:" class="btn btn-primary waves-effect waves-light" onclick='deletebkp(<?=$bkp->backup_id;?>)'> <i class="flaticon-trash"></i> <?= $this->lang->line("del_bkp")?></a>

										<a style="margin:5px;" href="<?=base_url()?>client/backup/downloadftp/<?=$bkp->backup_id;?>" class="btn btn-primary waves-effect waves-light"> </i><?= $this->lang->line("download")?> </a>

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
<script type="text/javascript">
	$(document).ready(function() {
    	
    	$('#ftpbackup').DataTable({
	        "language": {
	            "url": "<?php echo $this->lang->line("language_file")?>"
	        }
	     });
	

	});
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

</script>
<?php $this->load->view("client/layout/footer_new");?>
