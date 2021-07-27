<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>

<div class="container-fluid">
	<div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?= $this->lang->line("customer_space_update_requests")." ".$this->lang->line("list")?></h3>
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<table id="plantable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">                              
							<thead>
								<tr>
									<th>Sr No</th>
									<th ><?= $this->lang->line("client_name") ?></th>
									<th style="width:10%"><?= $this->lang->line("ftp_space") ?></th>
									<th style="width:10%"><?= $this->lang->line("db_space") ?></th>
									<th style="width:10%"><?= $this->lang->line("no_of_customers") ?></th>
									<th style="width:10%"><?= $this->lang->line("status")?></th>
									<th style="width:10%"><?= $this->lang->line("request_date")?></th>
									<th style="width:40%"><?= $this->lang->line("action")?>  </th> 
								</tr>
							</thead>
							<tbody>
								<?php
									$cnt = 1;

									//STATUS AS DEFINED IN DB
									$status_des = [
										0 => $this->lang->line("pending"),
										1 => $this->lang->line("approve"),
										2 => $this->lang->line("unapprove"),
									];

									foreach ($requestlist as $request){

										$client = $this->db->select(['fname', 'lname'])->where('client_id', $request->client_id )->get('client')->row();

										?>
										<tr>
											<td><?=$cnt?></td>
											<td><?= $client->fname." ".$client->lname ?></td>
											
											<td><?= empty($request->ftp_size) ? '-' : $request->ftp_size ." ".($request->ftp_unit ? ucwords( $request->ftp_unit)  :"B")?></td>
											<td><?= empty($request->db_size) ? '-' : $request->db_size ." ". ($request->db_unit ? ucwords($request->db_unit) :"B") ?></td>
											<td><?= empty($request->user_count) ? '-' : $request->user_count ?></td>
											<td><?= $status_des[$request->status] ?></td>
											<td><?= displayDate($request->request_date) ?></td>
											<td>

												<?php if ($request->status != 1): ?>
													
													<a href="javascript:" class="btn btn-primary" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Edit" onclick='updateStatus(<?php echo json_encode($request); ?>)'><i class="flaticon-pencil-1"></i></a>

													<a style="min-width: 40px;" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" href="javascript:" onclick="deleteRequest(<?= $request->request_id ?>)"><i class="flaticon-trash"></i></a>
													
												<?php endif ?>

												
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


<div class="modal fade" id="req-Modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= $this->lang->line("update_status")?></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
						<div class="col-sm-12">
							<div class="card">								
								<div class="card-block">
									<form id="reqform" method="post">
										<input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
										<input type="hidden" name="req_id" id="req_id">
										<input type="hidden" name="client_id" id="client_id">
										<p class="alert-danger p-2 text-center" id="space_error_msg"></p>
										<div class="form-group row">
			                                 <label class="col-sm-3 col-form-label"><?= $this->lang->line("no_of_customers")?></label>
			                                 <div class="col-sm-6">
			                                    <input type="number" name="no_of_users" value="" id="no_of_users" class="form-control">
			                                    <span style="color: red;" class="errmsg"></span>
			                                 </div>
			                              </div>

			                              <div class="form-group row sizeDiv">
			                                 <label class="col-sm-3 col-form-label"><?= $this->lang->line("ftp_space")?></label>
			                                 <div class="col-sm-6">
			                                    <input type="number" name="ftp_space" value="" id="ftp_space" class="form-control">
			                                    <span style="color: red;" class="errmsg"></span>
			                                 </div>
			                                 <div class="col-sm-3">
			                                    <select id="ftp_space_unit" name="ftp_space_unit" class="form-control sizeConvert">
			                                       <option value="b" selected="">Bytes</option>
			                                       <option value="kb" >KB</option>
			                                       <option value="mb">MB</option>
			                                       <option value="gb">GB</option>
			                                       <option value="tb">TB</option>
			                                    </select>
			                                    <input type="hidden" class="size_unit" name="size_unit_ftp" value="b">
			                                 </div>
			                              </div>

			                              <div class="form-group row sizeDiv">
			                                 <label class="col-sm-3 col-form-label"><?= $this->lang->line("db_space")?></label>
			                                 <div class="col-sm-6">
			                                    <input type="number" name="db_space" value="" id="db_space" class="form-control">
			                                    <span style="color: red;" class="errmsg"></span>
			                                 </div>
			                                 <div class="col-sm-3">
			                                    <select id="db_space_unit" name="db_space_unit" class="form-control sizeConvert">
			                                       <option value="b" selected="">Bytes</option>
			                                       <option value="kb">KB</option>
			                                       <option value="mb">MB</option>
			                                       <option value="gb">GB</option>
			                                       <option value="tb">TB</option>
			                                    </select>
			                                    <input type="hidden" class="size_unit" name="size_unit_db" value="b">
			                                 </div>
			                              </div>

										<div class="form-group row">
											<label class="col-sm-3 col-form-label"><?= $this->lang->line("status")?></label>
											<div class="col-sm-9">
												<select class="form-control" name="request_status" id="request_status">
													<?php foreach ($status_des as $key => $value): ?>
														<option value="<?= $key ?>"><?= $value ?></option>
													<?php endforeach ?>
												</select>
												<span style="color: red;" class="req_name_msg errmsg"></span>
											</div>
										</div>

										<div class="form-group row">
											<div class="input-group">
												<div class="col-sm-6" id="reqerrormsg" style="color: red;"></div>
											<div class="col-sm-6 text-right">
												<button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit")?></button>
												<button type="button" class="btn btn-default waves-effect " data-dismiss="modal"><?= $this->lang->line("close")?></button>
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
	$('#plantable').DataTable( {
	    "language": {
	        "url": "<?php echo $this->lang->line("language_file")?>"
	    }
	 });

	 $('#space_error_msg').hide();
	$('#reqform').submit(function (e) {
		e.preventDefault();

		var formdata = new FormData(this)

		$.ajax({
           url:"<?php echo base_url();?>client/updateRequest/update",
           type:"POST",
           data : formdata,
           processData : false,
           contentType : false,
           beforeSend:function(){
           		$("#cover-spin").show()
           },
	        dataType: 'json',
	        success:function(data){
				console.log(data["status"]);
	        	$("#cover-spin").hide();
				if(data["status"] == "failed"){
					$('#space_error_msg').text(data["msg"]);
					$('#space_error_msg').show();
				}else{
					$('#space_error_msg').hide();
					swal(data.msg, {
						title: "<?= $this->lang->line("great") ?>",
						type: "success",
						timer: 3000
					}).then(() => {
						location.reload();
					})
				}
	        }
        });

	})


    	$('.sizeConvert').change(function (e) {

    		var unit = $(this).val()
    		var size_input = $(this).closest('.sizeDiv').find('input[type="number"]')
    		var size_unit_el = $(this).parent().find('.size_unit')
    		var size_unit = size_unit_el.val()

    		console.log(size_unit)
    		var size_in_bytes = size_input.val()

    		$.ajax({
	           url:"<?php echo base_url();?>client/updateRequest/getFormat",
	           type:"POST",
	           data : {
	           		unit,
	           		size_in_bytes,
	           		size_unit
	           },
	           beforeSend:function(){
	           		//$("#cover-spin").show()
	           		$(size_input).attr('readonly','readonly')
	           },
		        dataType: 'json',
		        success:function(res){
		        	//$("#cover-spin").hide();
		        	$(size_input).removeAttr('readonly','readonly')
		        	$(size_input).val(res.data)
		        	$(size_unit_el).val(res.unit)
		        	//alert(res.msg);
					//location.reload(true);
		        }
	        });

    	})

	});

function updateStatus(data){

	$("#req_id").val(data.request_id);
	$("#client_id").val(data.client_id);
	$("#request_status option[value="+data.status+"]").attr('selected','selected');
	$("#db_space_unit option[value="+data.db_unit+"]").attr('selected','selected');
	$("#ftp_space_unit option[value="+data.ftp_unit+"]").attr('selected','selected');


	$("#ftp_space").val(data.db_size);
	$("#db_space").val(data.ftp_size);
	$("#no_of_users").val(data.user_count);
	
	$("#req-Modal").modal("show");
}

function deleteRequest(request_id){

	var chk = "<?php echo $this->lang->line("are_u_sure")?>";

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
	           url:"<?php echo base_url();?>client/updateRequest/delete/"+request_id,
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
<?php $this->load->view("client/layout/footer_new");?>
