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
.btn + .btn{ margin-left: 3px; padding: 5px 6px;}

	

</style>
<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">

				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("api_details") ?></h3>
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
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<th style="width:5%">#</th>
									<th style="width:90%"><?= $this->lang->line("api_key")?>  </th>
									<th style="width:5%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$srcnt = 0;
									foreach ($userapi as $val){				
										?>
										<tr>
											<td><?= ++$srcnt; ?></td>
											<td><?= $val->userapikey?></td>
											<td>
												<a href="javascript:" data-toggle="tooltip" data-placement="top" title='Edit' class="btn btn-primary" style="min-width: 40px;" onclick='updateproj(<?php echo json_encode($val);?>)'> <i class="flaticon-pencil-1"></i></a>
											</td>
										</tr>
										<?php
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




<div class="modal fade" id="api-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("update_api_key")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="apiform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="client_id" id="client_id">
										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("api_key")?><span class="text-danger">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="api_key" value="" id="api_key" placeholder="<?= $this->lang->line("api_key")?>" class="form-control">
												<span style="color: red;" class="api_key_msg"></span>
											</div>
										</div>

										<div class="form-group row">
											<div class="col-sm-9 col-sm-offset-3">
												<button type="button" id="genratekey" class="btn btn-sm btn-default"><?= $this->lang->line("generate_api_key");?></button>
											</div>
										</div>


										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="errormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close")?></button>
												<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit")?></button>
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
</div>


<script type="text/javascript">
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip({container: 'body'})
    $('#memListTable').DataTable( {
       "language": {
            "url": "<?php echo $this->lang->line("language_file")?>"
        }
    });

});
	
	function updateproj(data){
		$("#client_id").val(data.client_id);
		$("#api_key").val(data.userapikey);
		$("#api-Modal").modal("show");
	}

	function makeid(length) {
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for ( var i = 0; i < length; i++ ) {
		  result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

			
	$("#genratekey").on('click', function(e){
        e.preventDefault();
        //call
        var akey = makeid(60);
        if(akey != ''){
        	$("#api_key").val(akey);
        }
     });    

	$("#apiform").on('submit', function(e){
        e.preventDefault();
        var api_key = $("#api_key").val();
        if(api_key != ""){
        	$("#errormsg").html("");
    	$.ajax({
           url:"<?php echo base_url();?>client/userapi/update",
           type:"post",
           beforeSend:function(){
           		$("#cover-spin").show()
           },
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
                    success:function(data){
                        if(data.status == "success"){
                           
                            swal(data.msg, {
						title: "<?= $this->lang->line("great") ?>",
						type: "success",
						buttons: true,
						timer: 3000
					}).then(() => {
						$("#errormsg").html("");
                            location.reload();
					})
                            
                        }else{
                        	$("#errormsg").html(data.msg);
                        						swal(data.msg, {
			                                      title: "<?= $this->lang->line("oops")?>",
			                                      type: "error",
			                                      buttons: true,
			                                      timer: 3000
			                                    });
                        }
                        $("#cover-spin").hide()     
                     }
                });
    	}else{
    		$("#errormsg").html("<?php echo $this->lang->line("api_key_blank")?>");
    	}
   	 });


					
</script>
<!-- .container-fluid -->
<?php $this->load->view("client/layout/footer_new");?>
