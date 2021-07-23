
<?php
$this->load->view("front/header");
?>
  	 <!-- Banner section -->
  	<section class="banner">
  		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<div class="banner-text">
						<h1>Cloud Backup Service</h1>
						<p>Backups in der Cloud sind einfach, sicher und kosteneffizient. Anstelle eigener Hardware buchen Sie bei uns den Service und wir kümmern uns um die Sicherungen. Wir kümmern uns um das wichtigste, den Schutz und die Sicherung Ihrer Daten. </p>
						<p>Wir sind für Sie da. Sie richten ein und wir kümmern uns um alles Weitere.</p>
						<a href="<?php echo base_url('pricing');?>" type="button" class="btn btn-primary btn-md">Get Started</a>
					</div>  
				</div>
				<div class="col-md-6">
				<div class="banner-image">
					<img src="<?php base_url();?>public/public/front/img/cloud-backup.png" alt="">
				</div>
				</div>
			</div>
   		</div>
  	</section>
	  <!-- Banner section end -->
	  

	<section class="middle-section">
		<div class="container">
			<div class="middle-items">
				<h1>Was darf es sein?</h1>
				<div class="row">
					<div class="col-md-4">
						<div class="item-box">
							<img src="<?php base_url();?>public/public/front/img/sql-backup.png" alt="">
							<h1>SQL Backup</h1>
							<p>Datenbanken speichern alle Informationen von Programmen. Gehen diese verloren, sind auch alle Daten verloren und Programme können nicht mehr ausgeführt werden. Um dies zu verhindern, empfiehlt sich ein zuverlässiges SQL Backup. Dieses wird automatisiert in vom Nutzer bestimmten Intervallen durchgeführt. Alle Backups können aus dem System auch lokal gesichert werden. </p>
						</div>						
					</div>
					<div class="col-md-4">
						<div class="item-box">
							<img src="<?php base_url();?>public/public/front/img/fpt-backup.png" alt="">
							<h1>FTP Backup</h1>
							<p>Die FTP Daten stellen neben der SQL-Datenbank das Herz Ihres Online Systems dar. Hierbei werden FTP Daten des Öfteren durch Hackerangriffe beschädigt oder verändert. Wir sichern Ihre FTP Daten nach Ihren bestimmten Intervallen und schützen diese vor Veränderungen. Ihr Backup steht Ihnen jederzeit zur Verfügung und kann auch lokal gesichert werden. Alle Daten sind versioniert und können auch einzeln wiederhergestellt werden.</p>
						</div>						
					</div>
					<div class="col-md-4">
						<div class="item-box">
							<img src="<?php base_url();?>public/public/front/img/version-control.png" alt="">
							<h1>Versionierung</h1>
							<p>Manchmal braucht meine eine frühere Version einer Datei. Die Besten Beispiele sind eine ungewollte Veränderung der Dateien oder ein schadhafter Eingriff durch Hacker. Genau hier hilft die Versionierung. Sie können gezielt auf eine funktionsfähige Version wiederherstellen und somit Ihr System schnell wieder zurück auf den gewünschten Stand bringen.</p>
						</div>						
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="item-box mb-0">
							<img src="<?php base_url();?>public/public/front/img/sql-restore.png" alt="">
							<h1>SQL-Wiederherstellung</h1>
							<p>Ihr Server ist abgestürzt? Die Datenbank beschädigt? Wir stellen die Datenbanken anhand der aktuellen Sicherung in kurzer Zeit wieder her! Hierbei wird der Restore Prozess direkt auf Ihre beschädigte Datenbank oder auch auf ein neues System in Echtzeit durchgeführt.</p>
						</div>						
					</div>
					<div class="col-md-4">
						<div class="item-box mb-0">
							<img src="<?php base_url();?>public/public/front/img/ftp-restore.png" alt="">
							<h1>FTP Wiederherstellung</h1>
							<p>Sollten Daten verloren gegangen, böswillig verändert worden sein oder eine Beschädigung vorliegen, können wir die Daten in Echtzeit wiederherstellen. Dies aus verschiedenen Versionen. Ihr System ist somit schnell wieder verfügbar. </p>
						</div>						
					</div>
					<div class="col-md-4">
						<div class="item-box mb-0">
							<img src="<?php base_url();?>public/public/front/img/api-dashboard.png" alt="">
							<h1>Schnittstellen-Dashboard</h1>
							<p>Mit unserem Dashboard auf einen Blick sehen, ob noch alles richtig läuft. Hier werden alle wichtigen Informationen zu Ihren Systemen angezeigt. Behalten Sie einen Überblick über alle eingesetzten APIs und damit verbundene Kosten. Es werden keine Überraschungen mehr über zu viele Aufrufe erfolgen.</p>
						</div>						
					</div>
				</div>
			</div>

			<div class="features">
				<h1>Features</h1>
				<div class="features-row">
					<div class="row">
						<div class="col-md-6">
							<div class="feature-box">
								<div class="feature-image">
									<img src="<?php base_url();?>public/public/front/img/feature_autobackup.png" alt="">
								</div>
								Datenbank automatisiertes Backup, Einrichtungen und vergessen
							</div>
						</div>
						<div class="col-md-6">
							<div class="feature-box">
								<div class="feature-image">
									<img src="<?php base_url();?>public/public/front/img/feature_aes.png" alt="">
								</div>
								FTP automatisiertes Backup, Einrichtungen und vergessen
							</div>
						</div>
						<div class="col-md-6">
							<div class="feature-box mb-3">
								<div class="feature-image">
									<img src="<?php base_url();?>public/public/front/img/feature_localbackup.png" alt="">
								</div>
								Sicherung aller Ihrer Online Inhalte 
							</div>
						</div>
						<div class="col-md-6">
							<div class="feature-box mb-3">
								<div class="feature-image">
									<img src="<?php base_url();?>public/public/front/img/featrue_defaultfile.png" alt="">
								</div>
								Versionierung aller Backups zur Zielgenauen Sicherung und Wiederherstellung einzelner Versionen Ihres Systems
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="features">
				<h1>How it works? </h1>
				<div class="how-it-works">
					 <div class="row">
						 <div class="col-md-4">
							 <div class="work-box">
								<div class="work-image">
									<img src="<?php base_url();?>public/public/front/img/step1_login.png" alt="">
								</div>
								<h1>1. Login </h1>
								<p>Loggen Sie sich einfach in Ihr System ein und haben Sie direkten Zugriff zu allen Funktionen und Möglichkeiten</p>
							 </div>
						 </div>
						 <div class="col-md-4">
							 <div class="work-box">
								<div class="work-image">
									<img src="<?php base_url();?>public/public/front/img/step2_installation.png" alt="">
								</div>
								<h1>2. Installation</h1>
								<p>Keine Installation notwendig. Sie können sofort loslegen und brauchen lediglich die Zugangsdaten zu Ihrer Webseite </p>
							 </div>
						 </div>
						 <div class="col-md-4">
							 <div class="work-box last-work">
								<div class="work-image">
									<img src="<?php base_url();?>public/public/front/img/step3_backup.png" alt="">
								</div>
								<h1> 3. Backup </h1>
								<p>Nach der Eingabe Ihrer Daten können Sie sofort mit dem Backup beginnen und sind geschützt</p>
							 </div>
						 </div>
					 </div>
				</div>
			</div>
		</div>	
			
	</section>
<?php
$this->load->view("front/footer");
?>