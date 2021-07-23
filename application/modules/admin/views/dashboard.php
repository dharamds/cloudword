<?php $this->load->view("admin/layout/header");?>

<div class="pcoded-content">

	<div class="page-header card">
		<div class="row align-items-end">
			<div class="col-lg-8">
				<div class="page-header-title">
					<i class="feather icon-home bg-c-blue"></i>
					<div class="d-inline">
						<h5>Dashboard CRM</h5>
						<span>lorem ipsum dolor sit amet, consectetur adipisicing elit</span>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="page-header-breadcrumb">
					<ul class=" breadcrumb breadcrumb-title">
						<li class="breadcrumb-item">
							<a href="index.html"><i class="feather icon-home"></i></a>
						</li>
						<li class="breadcrumb-item"><a href="#!">Dashboard CRM</a> </li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="pcoded-inner-content">
		<div class="main-body">
			<div class="page-wrapper">
				<div class="page-body">

					<div class="row">

						<div class="col-xl-3 col-md-6">
							<div class="card prod-p-card card-red">
								<div class="card-body">
									<div class="row align-items-center m-b-30">
										<div class="col">
											<h6 class="m-b-5 text-white">Total Profit</h6>
											<h3 class="m-b-0 f-w-700 text-white">$1,783</h3>
										</div>
										<div class="col-auto">
											<i class="fas fa-money-bill-alt text-c-red f-18"></i>
										</div>
									</div>
									<p class="m-b-0 text-white"><span class="label label-danger m-r-10">+11%</span>From Previous Month</p>
								</div>
							</div>
						</div>
						<div class="col-xl-3 col-md-6">
							<div class="card prod-p-card card-blue">
								<div class="card-body">
									<div class="row align-items-center m-b-30">
										<div class="col">
											<h6 class="m-b-5 text-white">Total Orders</h6>
											<h3 class="m-b-0 f-w-700 text-white">15,830</h3>
										</div>
										<div class="col-auto">
											<i class="fas fa-database text-c-blue f-18"></i>
										</div>
									</div>
									<p class="m-b-0 text-white"><span class="label label-primary m-r-10">+12%</span>From Previous Month</p>
								</div>
							</div>
						</div>
						<div class="col-xl-3 col-md-6">
							<div class="card prod-p-card card-green">
								<div class="card-body">
									<div class="row align-items-center m-b-30">
										<div class="col">
											<h6 class="m-b-5 text-white">Average Price</h6>
											<h3 class="m-b-0 f-w-700 text-white">$6,780</h3>
										</div>
										<div class="col-auto">
											<i class="fas fa-dollar-sign text-c-green f-18"></i>
										</div>
									</div>
									<p class="m-b-0 text-white"><span class="label label-success m-r-10">+52%</span>From Previous Month</p>
								</div>
							</div>
						</div>
						<div class="col-xl-3 col-md-6">
							<div class="card prod-p-card card-yellow">
								<div class="card-body">
									<div class="row align-items-center m-b-30">
										<div class="col">
											<h6 class="m-b-5 text-white">Product Sold</h6>
											<h3 class="m-b-0 f-w-700 text-white">6,784</h3>
										</div>
										<div class="col-auto">
											<i class="fas fa-tags text-c-yellow f-18"></i>
										</div>
									</div>
									<p class="m-b-0 text-white"><span class="label label-warning m-r-10">+52%</span>From Previous Month</p>
								</div>
							</div>
						</div>


						<div class="col-xl-6 col-md-12">
							<div class="card latest-update-card">
								<div class="card-header">
									<h5>What’s New</h5>
									<div class="card-header-right">
										<ul class="list-unstyled card-option"> 
											<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
											<li><i class="feather icon-maximize full-card"></i></li>
											<li><i class="feather icon-minus minimize-card"></i></li>
											<li><i class="feather icon-refresh-cw reload-card"></i></li>
											<li><i class="feather icon-trash close-card"></i></li>
											<li><i class="feather icon-chevron-left open-card-option"></i></li>
										</ul>
									</div>
								</div>
								<div class="card-block">
									<div class="latest-update-box">
										<div class="row p-t-20 p-b-30">
											<div class="col-auto text-right update-meta p-r-0">
												<img src="<?php echo base_url();?>/public/assets/images/avatar-4.jpg" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon">
											</div>
											<div class="col p-l-5">
												<a href="#!"><h6>Your Manager Posted.</h6></a>
												<p class="text-muted m-b-0">Jonny michel</p>
											</div>
										</div>
										<div class="row p-b-30">
											<div class="col-auto text-right update-meta p-r-0">
												<i class="feather icon-briefcase bg-c-red update-icon"></i>
											</div>
											<div class="col p-l-5">
												<a href="#!"><h6>You have 3 pending Task.</h6></a>
												<p class="text-muted m-b-0">Hemilton</p>
											</div>
										</div>
										<div class="row p-b-30">
											<div class="col-auto text-right update-meta p-r-0">
												<i class="feather icon-check f-w-600 bg-c-green update-icon"></i>
											</div>
											<div class="col p-l-5">
												<a href="#!"><h6>New Order Received.</h6></a>
												<p class="text-muted m-b-0">Hemilton</p>
											</div>
										</div>
										<div class="row">
											<div class="col-auto text-right update-meta p-r-0">
												<img src="<?php echo base_url();?>/public/assets/images/avatar-4.jpg" alt="user image" class="img-radius img-40 align-top m-r-15 update-icon">
											</div>
											<div class="col p-l-5">
												<a href="#!"><h6>Your Manager Posted.</h6></a>
												<p class="text-muted m-b-0">Jonny michel</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6 col-md-12">
							<div class="card new-cust-card">
								<div class="card-header">
									<h5>New Customers</h5>
									<div class="card-header-right">
										<ul class="list-unstyled card-option">
											<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
											<li><i class="feather icon-maximize full-card"></i></li>
											<li><i class="feather icon-minus minimize-card"></i></li>
											<li><i class="feather icon-refresh-cw reload-card"></i></li>
											<li><i class="feather icon-trash close-card"></i></li>
											<li><i class="feather icon-chevron-left open-card-option"></i></li>
										</ul>
									</div>
								</div>
								<div class="card-block">
									<div class="align-middle m-b-35">
										<img src="<?php echo base_url();?>/public/assets/images/avatar-2.jpg" alt="user image" class="img-radius img-40 align-top m-r-15">
										<div class="d-inline-block">
											<a href="#!"><h6>Alex Thompson</h6></a>
											<p class="text-muted m-b-0">Cheers!</p>
											<span class="status active"></span>
										</div>
									</div>
									<div class="align-middle m-b-35">
										<img src="<?php echo base_url();?>/public/assets/images/avatar-3.jpg" alt="user image" class="img-radius img-40 align-top m-r-15">
										<div class="d-inline-block">
											<a href="#!"><h6>John Doue</h6></a>
											<p class="text-muted m-b-0">stay hungry stay foolish!</p>
											<span class="status active"></span>
										</div>
									</div>
									<div class="align-middle m-b-35">
										<img src="<?php echo base_url();?>/public/assets/images/avatar-3.jpg" alt="user image" class="img-radius img-40 align-top m-r-15">
										<div class="d-inline-block">
											<a href="#!"><h6>Alex Thompson</h6></a>
											<p class="text-muted m-b-0">Cheers!</p>
											<span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i>30 min ago</span>
										</div>
									</div>
									<div class="align-middle m-b-0">
										<img src="<?php echo base_url();?>/public/assets/images/avatar-2.jpg" alt="user image" class="img-radius img-40 align-top m-r-15">
										<div class="d-inline-block">
											<a href="#!"><h6>Alex Thompson</h6></a>
											<p class="text-muted m-b-0">Cheers!</p>
											<span class="status active"></span>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="col-xl-4 col-md-12">
							<div class="card comp-card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<h6 class="m-b-25">Impressions</h6>
											<h3 class="f-w-700 text-c-blue">1,563</h3>
											<p class="m-b-0">May 23 - June 01 (2017)</p>
										</div>
										<div class="col-auto">
											<i class="fas fa-eye bg-c-blue"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-4 col-md-6">
							<div class="card comp-card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<h6 class="m-b-25">Goal</h6>
											<h3 class="f-w-700 text-c-green">30,564</h3>
											<p class="m-b-0">May 23 - June 01 (2017)</p>
										</div>
										<div class="col-auto">
											<i class="fas fa-bullseye bg-c-green"></i>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-4 col-md-6">
							<div class="card comp-card">
								<div class="card-body">
									<div class="row align-items-center">
										<div class="col">
											<h6 class="m-b-25">Impact</h6>
											<h3 class="f-w-700 text-c-yellow">42.6%</h3>
											<p class="m-b-0">May 23 - June 01 (2017)</p>
										</div>
										<div class="col-auto">
											<i class="fas fa-hand-paper bg-c-yellow"></i>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div class="col-xl-4 col-md-6">
							<div class="card o-hidden">
								<div class="card-header">
									<h5>Total Leads</h5>
								</div>
								<div class="card-block">
									<div class="row">
										<div class="col-4">
											<p class="text-muted m-b-5">Overall</p>
											<h6>68.52%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Monthly</p>
											<h6>28.90%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Day</p>
											<h6>13.50%</h6>
										</div>
									</div>
								</div>
								<div id="sal-income" style="height:100px"></div>
							</div>
						</div>
						<div class="col-xl-4 col-md-6">
							<div class="card o-hidden">
								<div class="card-header">
									<h5>Total Vendors</h5>
								</div>
								<div class="card-block">
									<div class="row">
										<div class="col-4">
											<p class="text-muted m-b-5">Overall</p>
											<h6>68.52%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Monthly</p>
											<h6>28.90%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Day</p>
											<h6>13.50%</h6>
										</div>
									</div>
								</div>
								<div id="rent-income" style="height:100px"></div>
							</div>
						</div>
						<div class="col-xl-4 col-md-12">
							<div class="card o-hidden">
								<div class="card-header">
									<h5>Invoice Generate</h5>
								</div>
								<div class="card-block">
									<div class="row">
										<div class="col-4">
											<p class="text-muted m-b-5">Overall</p>
											<h6>68.52%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Monthly</p>
											<h6>28.90%</h6>
										</div>
										<div class="col-4">
											<p class="text-muted m-b-5">Day</p>
											<h6>13.50%</h6>
										</div>
									</div>
								</div>
								<div id="income-analysis" style="height:100px"></div>
							</div>
						</div>


						<div class="col-xl-8 col-md-12">
							<div class="card latest-update-card">
								<div class="card-header">
									<h5>Latest Activity</h5>
									<div class="card-header-right">
										<ul class="list-unstyled card-option">
											<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
											<li><i class="feather icon-maximize full-card"></i></li>
											<li><i class="feather icon-minus minimize-card"></i></li>
											<li><i class="feather icon-refresh-cw reload-card"></i></li>
											<li><i class="feather icon-trash close-card"></i></li>
											<li><i class="feather icon-chevron-left open-card-option"></i></li>
										</ul>
									</div>
								</div>
								<div class="card-block">
									<div class="latest-update-box">
										<div class="row p-t-20 p-b-30">
											<div class="col-auto text-right update-meta">
												<i class="feather icon-sunrise bg-c-blue update-icon"></i>
											</div>
											<div class="col">
												<h6>John Deo</h6>
												<p class="text-muted m-b-15">The trip was an amazing and a life changing experience!!</p>
												<img src="<?php echo base_url();?>/public/assets/images/mega-menu/01.jpg" alt="" class="img-fluid img-100 m-r-15 m-b-10">
												<img src="<?php echo base_url();?>/public/assets/images/mega-menu/03.jpg" alt="" class="img-fluid img-100 m-r-15 m-b-10">
											</div>
										</div>
										<div class="row p-b-30">
											<div class="col-auto text-right update-meta">
												<i class="feather icon-file-text bg-c-blue update-icon"></i>
											</div>
											<div class="col">
												<h6>Administrator</h6>
												<p class="text-muted m-b-0">Free courses for all our customers at A1 Conference Room - 9:00 am tomorrow!</p>
											</div>
										</div>
										<div class="row p-b-30">
											<div class="col-auto text-right update-meta">
												<i class="feather icon-map-pin bg-c-blue update-icon"></i>
											</div>
											<div class="col">
												<h6>Jeny William</h6>
												<p class="text-muted m-b-15">Happy Hour! Free drinks at <span> <a href="#!" class="text-c-blue">Cafe-Bar all </a> </span>day long!</p>
												<div id="markers-map" style="height:245px;width:100%"></div>
											</div>
										</div>
									</div>
									<div class="text-right">
										<a href="#!" class=" b-b-primary text-primary">View all Activity</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-4 col-md-12">
							<div class="card chat-card">
								<div class="card-header">
									<h5>Chat history</h5>
									<div class="card-header-right">
										<ul class="list-unstyled card-option">
											<li class="first-opt"><i class="feather icon-chevron-left open-card-option"></i></li>
											<li><i class="feather icon-maximize full-card"></i></li>
											<li><i class="feather icon-minus minimize-card"></i></li>
											<li><i class="feather icon-refresh-cw reload-card"></i></li>
											<li><i class="feather icon-trash close-card"></i></li>
											<li><i class="feather icon-chevron-left open-card-option"></i></li>
										</ul>
									</div>
								</div>
								<div class="card-block">
									<div class="row m-b-20 received-chat">
										<div class="col-auto p-r-0">
											<img src="<?php echo base_url();?>/public/assets/images/avatar-2.jpg" alt="user image" class="img-radius img-40">
										</div>
										<div class="col">
											<div class="msg">
												<p class="m-b-0">Nice to meet you!</p>
											</div>
											<p class="text-muted m-b-0"><i class="fa fa-clock-o m-r-10"></i>10:20am</p>
										</div>
									</div>
									<div class="row m-b-20 send-chat">
										<div class="col">
											<div class="msg">
												<p class="m-b-0">Nice to meet you!</p>
											</div>
											<p class="text-muted m-b-0"><i class="fa fa-clock-o m-r-10"></i>10:20am</p>
										</div>
										<div class="col-auto p-l-0">
											<img src="<?php echo base_url();?>/public/assets/images/avatar-3.jpg" alt="user image" class="img-radius img-40">
										</div>
									</div>
									<div class="row m-b-20 received-chat">
										<div class="col-auto p-r-0">
											<img src="<?php echo base_url();?>/public/assets/images/avatar-2.jpg" alt="user image" class="img-radius img-40">
										</div>
										<div class="col">
											<div class="msg">
												<p class="m-b-0">Nice to meet you!</p>
												<img src="<?php echo base_url();?>/public/assets/images/mega-menu/01.jpg" alt="">
												<img src="<?php echo base_url();?>/public/assets/images/mega-menu/03.jpg" alt="">
											</div>
											<p class="text-muted m-b-0"><i class="fa fa-clock-o m-r-10"></i>10:20am</p>
										</div>
									</div>
									<div class="row m-b-20 send-chat">
										<div class="col">
											<div class="msg">
												<p class="m-b-0">Come now to meet you!</p>
											</div>
											<p class="text-muted m-b-0"><i class="fa fa-clock-o m-r-10"></i>10:20am</p>
										</div>
										<div class="col-auto p-l-0">
											<img src="<?php echo base_url();?>/public/assets/images/avatar-3.jpg" alt="user image" class="img-radius img-40">
										</div>
									</div>
									<div class="row m-b-20 received-chat">
										<div class="col-auto p-r-0">
											<img src="<?php echo base_url();?>/public/assets/images/avatar-2.jpg" alt="user image" class="img-radius img-40">
										</div>
										<div class="col">
											<div class="msg">
												<p class="m-b-0">Nice to meet you!</p>
												<img src="<?php echo base_url();?>/public/assets/images/mega-menu/03.jpg" alt="">
											</div>
											<p class="text-muted m-b-0"><i class="fa fa-clock-o m-r-10"></i>10:20am</p>
										</div>
									</div>
									<div class="right-icon-control">
										<div class="input-group input-group-button">
											<input type="text" class="form-control" placeholder="Send message">
											<div class="input-group-append">
												<button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-message-circle"></i></button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view("admin/layout/footer");?>