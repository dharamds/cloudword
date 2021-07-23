<?php $this->load->view("admin/layout/header_new");?>
<?php $this->load->view("admin/layout/sidebar");?>

<div class="container-fluid">
    <div class="row mr-0">

	<div id="cover-spin"></div>
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-6">
					<h3 class="filter-content-title"><?= $this->lang->line("view_plan")?></h3>
                </div>
                <div class="flex-col-6 text-right">
					<a class="btn btn-primary" href="javascript:" onclick="goBack()">
						<?= $this->lang->line("back")?>
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
						<div class="row">
							<div class="col-sm-12">
								<div class="card">
									<div class="card-block">
                                        <table class="table">
                                        	<?php
                                        	if($plandata->icon !=""){
                                        		?>
                                        		<tr>
                                                	<td><strong><?= $this->lang->line("icon")?></strong> </td>
                                                	<td><img src="<?=base_url('uploads/plan/'.$plandata->icon)?>" width="30" height="30"/></td>
                                            	</tr>
                                        		<?php
                                        	}
                                        	?>
                                            <tr>
                                                <td><strong><?= $this->lang->line("plan_name")?></strong></td>
                                                <td><?= $plandata->name; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?= $this->lang->line("description")?></strong></td>
                                                <td><?= $plandata->description; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?=$this->lang->line("price")?></strong></td>
                                                <td><?=$currency." "?><?= $plandata->price; ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?=$this->lang->line("ftp_space")?></strong></td>
                                                <td><?= $this->general->formatBytes($plandata->ftp_space_bytes)?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?=$this->lang->line("db_space")?></strong></td>
                                                <td><?= $this->general->formatBytes($plandata->db_space_bytes)?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?=$this->lang->line("expiry_days")?></strong></td>
                                                <td><?= $plandata->expiry_days?></td>
                                            </tr>
                                        </table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="panel-footer"></div>
				</div>
			</div>
		</div>
	</div>

    </div>
</div>
<?php $this->load->view("admin/layout/footer_new");?>
<script>
function goBack() {
	window.history.back();
}
</script>
