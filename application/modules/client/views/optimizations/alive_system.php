<?php $this->load->view("client/layout/header_new");?>
<?php $this->load->view("client/layout/sidebar");?>


<div class="container-fluid">
	<div class="row mr-0">
	<div class="row">                    
		<div class="col-md-12">
			<div class="filter-container flex-row">
				<div class="flex-col-md-6">
					<h3 class="filter-content-title"><?=$this->lang->line("Website alive system")?></h3>
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
						<h2><?=$this->encryption->decrypt($project_details->project_name)?></h2>
						<div class="row">
							<div class="col-md-6">
								<strong><?=$this->encryption->decrypt($project_details->url)?> : </strong>
							</div>
							<div class="col-md-6">
								<button class="btn btn-<?= $headerdata["http_code"] == 200 ?'success':'danger'?>" ><?= $headerdata["http_code"] == 200 ?$this->lang->line("active"):$this->lang->line("deactive")?></button>
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
<?php $this->load->view("client/layout/footer_new");?>




