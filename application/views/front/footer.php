<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="footer-info">
						<div class="footer-logo"><img src="<?php echo base_url();?>public/public/front/img/footer-logo.png"></div>
					</div>
				</div>
				<div class="col-md-3">
					<h1>Links</h1>
					<ul>
						<li> <a href="<?php echo base_url();?>"><?= $this->lang->line("Home")?></a></li>
						<li> <a href="<?php echo base_url();?>faq"><?= $this->lang->line("FAQ")?></a></li>
						<li> <a href="<?php echo base_url();?>contact"><?= $this->lang->line("Contact us")?></a></li>
						<li> <a href="<?php echo base_url();?>technical_support"><?= $this->lang->line("Technical support")?></a></li>
					</ul>
				</div>
				<div class="col-md-3">
					<h1><?= $this->lang->line("Cloud Service World")?></h1>
					<ul>
						<li> <a href="javascript:void(0);"><?= $this->lang->line("Cloud backup")?></a></li>
						<li> <a href="javascript:void(0);"><?= $this->lang->line("Cloud restore")?></a></li>
						<li> <a href="javascript:void(0);"><?= $this->lang->line("Git versioning tools")?></a></li>
						<li> <a href="javascript:void(0);"><?= $this->lang->line("Versioning control of backup")?></a></li>
					</ul>
				</div>
			</div>
			<div class="copyright">
				<?= $this->lang->line("cpright")?>
			</div>
		</div>
	</footer>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" ></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
  </body>
</html>