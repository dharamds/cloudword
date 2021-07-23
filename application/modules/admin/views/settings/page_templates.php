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
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?=$this->lang->line("page_templates")?></h3>
				</div>
				<div class="flex-col-md-6 text-right">
					
				</div>
			</div>
		</div>        
	</div>
	<div data-widget-role="role1">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default panel-grid">
					<div class="panel-body no-padding p-0">
						<div class="table-responsive">
						<table id="memListTable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
							<thead>
								<tr>
									<th>
										<label class="checkbox-tel"><input type="checkbox" class="select_all"></label>
									</th>
									<th><?=$this->lang->line("name")?></th>
									<th><?=$this->lang->line("page_code")?></th>
									<th><?=$this->lang->line("action")?></th> 
								</tr>
							</thead>
							<tbody>
								<?php
								if(count($page_templates) > 0){
									$cnt = 1;
									foreach ($page_templates as $set){				
										?>
										<tr>
											<td><?= $cnt ?></td>
											<td><?= $set->title?></td>
											<td><?= $set->page_code ?></td>
											<td>
												<a style="min-width: 40px;" data-toggle="tooltip" data-placement="top" title="Edit" class="btn btn-primary" href="<?= base_url('admin/settings/page_template_edit/').$set->page_id ?>"><i class="flaticon-edit"></i> </a>
											</td>
										</tr>
										<?php
										$cnt++;
									}
								}else{
									?>
										<tr>
											<td align="center" colspan="4"><?=$this->lang->line("no_records_found")?></td>
										</tr>

									<?php
								}	
								?>
							</tbody>
						</table>
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
