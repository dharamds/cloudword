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
					<h3><?php echo $this->lang->line("ftp_backup")?></h3>
					<p id="bulkmsg" style="color: red; margin: 0 0 0;"></p>
				</div>
				<div class="flex-col-sm-6 text-right" >
					<a href="javascript:" onclick="bulkbackup()" style="float: right;" class="btn btn-primary"><?php echo $this->lang->line("bulk_backup")?></a>
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					
					<div class="panel-body no-padding p-0">
						<input type="hidden" name="rootfolder" id="rootfolder" value="<?php echo base64_encode($root_folder); ?>">
						<input type="hidden" name="project_id" id="project_id" value="<?php echo base64_encode($projectss->project_id); ?>">
						<input type="hidden" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
						<div style="display: none;" class="alert alert-success" role="alert" id="successbulk" >
						</div>
						<div class="alert alert-danger" style="display: none;" role="alert" id="failedbulk">
						</div>

						<table id="memListTable" class="table table-bordered table-striped table-hover" cellspacing="0">                              
							<thead>
								<tr>
									<th>
										<label class="checkbox-tel"><input type="checkbox" name="selectall" id="selectall" class="form-control"></label>
									</th>
									<th><?php echo $this->lang->line("sr_no")?></th>
									<th><?php echo $this->lang->line("file_folder_name")?></th>
									<th><?php echo $this->lang->line("type")?></th>
									<th><?php echo $this->lang->line("action")?></th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($list) > 0){
									$cnt = 1;
									foreach($list as $key => $val){
										if($key > 1){
										?>
										<tr>
											<td><input type="checkbox" name="cheftpids[]" id="chk_<?=$key?>" value="<?=$key?>"></td>
											<td><?= $cnt ?></td>
											<td><?= $val ?></td>
											<td><?php 
											if(strpos($val,".") !== false){
												$as = explode(".",$val)[1];
												$fcheck = 0;
												echo $as." ".$this->lang->line("file");  
											}else{
												$fcheck = 1;
												echo $this->lang->line("folder");	
											}
											?></td>
											<td>
												<?php if($fcheck == 1){
													if($root_folder == "/"){


														$rrr = base64_encode($projectss->project_id)."/".base64_encode($root_folder.$val."/");
													}else{
														$rrr = base64_encode($projectss->project_id)."/".base64_encode($root_folder."/".$val."/");
													}
													?>
													<?= $root_folder?>
													<a href="<?php echo base_url(); ?>client/projects/listftp/<?php echo $rrr;?>" class="btn btn-primary btn-round waves-effect waves-light"><?=$this->lang->line("view_directory_data")?></a>
													<?php
												}
												?>
											</td>
										</tr>
										<?php
										$cnt++;
										}
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



	$("#selectall").click(function(){
		var checkBox = document.getElementById("selectall");
		if(checkBox.checked == true){
			$('input[name="cheftpids[]"]').each(function() {
				this.checked = true;
			});
		} else {
			$('input[name="cheftpids[]"]').each(function() {
				this.checked = false;
			});
		}
	});
	function bulkbackup(){
		var rootfolder = $("#rootfolder").val();
		var project_id = $("#project_id").val();
		var folderids = new Array();
		$("input[name='cheftpids[]']:checked").each(function() {
			folderids.push($(this).val());
		});
		var x = folderids.toString();
		var csrfName = $('.txt_csrfname').attr('name');
		var csrfHash = $('.txt_csrfname').val();
		console.log(csrfName+"  "+csrfHash);
		if(folderids.length > 0){
			$("#bulkmsg").html("")
		$.ajax({
			url:"<?php echo base_url();?>admin/projects/bulkftp",
			method:"post",
			dataType: 'json',
			beforeSend: function(){
						$("#cover-spin").show();
			},
			data:{rootfolder:rootfolder,folderid:x,project_id:project_id,'<?php echo $this->security->get_csrf_token_name(); ?>':'<?php echo $this->security->get_csrf_hash(); ?>'},
			success:function(data){
				if(data.status == "success"){
					alert(data.msg);
					window.location.replace("<?php echo base_url();?>admin/projects")
				}else{
					alert(data.msg);
				}
				$("#cover-spin").hide();
			}
		});
	}else{
		$("#bulkmsg").html("<?=$this->lang->line("atlease_file_folder")?>");
	}
	}
</script>
<?php $this->load->view("admin/layout/footer_new");?>