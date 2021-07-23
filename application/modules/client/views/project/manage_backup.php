<?php $this->load->view("client/layout/header_new"); ?>
<?php $this->load->view("client/layout/sidebar"); ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/public/assets/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" href="<?php echo base_url();?>public/public/assets/css/bootstrap.min.css">

<script type="text/javascript" src="<?php echo base_url();?>public/public/assets/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>public/public/assets/js/bootstrap-datetimepicker.min.js"></script>
<style type="text/css">
    .switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}

.modal{
  z-index: 1200;
}
.modal-backdrop.in {
    filter: alpha(opacity=50);
    opacity: .2;
    z-index: 0;
}

      .schedule img{position: relative; margin: -5px 5px 0 15px;}

      .schedule .radio label::before{display:none;}
      .schedule .radio input[type="radio"]{
        opacity:1;
      }

      .schedule .radio {
        margin-top:15px;
        margin-bottom:15px;
        display: inline-block;
        width: 100%;
      }
      .schedule h3 {
        color:#3a6cab;
        margin-bottom:15px
      }
      .schedule_calendar{
        margin:0;
        padding:0;
        list-style-type:none;
        display:inline-block;
        border:1px solid #ccc
      }
      .schedule_calendar li{
        margin:0;
        padding:10px 10px;
        list-style-type:none;
        display:inline-block;
        width:auto;
        border-right:1px solid #ccc;
        float: left;
        background:#eee
      }
      .schedule_calendar li:last-child{
        border-right:0
      }
      .schedule_calendar li.active{
        color:#fff;
        background:#15aae5
      }


      .radio-label {
  width: 100%;
  text-align: center;
}

.radio-pillbox {
  border:1px solid #ccc;
  background:#eee
}

.radio-pillbox radiogroup {
  height: 100%;
  width: 100%;
  display: flex;
}

.radio-pillbox radiogroup div {
  width: 100%;
  position: relative;
}

.radio-pillbox radiogroup div input {
  -webkit-appearance: inherit;
  width: 100%;
  height: 100%;
  transition: background 300ms ease-out;
  margin: 0;
  outline: 0;
  border-left: 1px solid rgba(0, 0, 0, 0.05);
  position: absolute;
}

.radio-pillbox radiogroup .first {
  border-left: none;
}


.radio-pillbox radiogroup div label {
  /*position: absolute;*/
    top: 0;
    text-align: center;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    margin-bottom:0;
    font-weight:normal;
    padding:10px
}

.radio-pillbox input:focus { outline: 0; }
.radio-pillbox input[type="radio"]:checked + label {
  color: #fff; background:#3a6cab
}





    
  </style>
<div class="container-fluid">
   <div class="row mr-0">
      <nav aria-label="breadcrumb">
         <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('client/project')?>"><?=$this->lang->line("projects") ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $this->lang->line("backups") ?></li>
         </ol>
      </nav>
      <div id="cover-spin"></div>
      <div class="row">
         <div class="col-md-12">
            <div class="filter-container flex-row">
               <div class="flex-col-md-6">
                  <h3 class="filter-content-title"><?= $this->encryption->decrypt($project_data->project_name) ?></h3>
               </div>
               <div class="flex-col-md-6 text-right">
                  <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/client/projects/create"> New Project </a> --> 
               </div>
            </div>
         </div>
      </div>
      <div>
         <!-- Nav tabs -->
         <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><?= $this->lang->line("ftp_backup")?></a></li>
            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?= $this->lang->line("db_backup")?></a></li>
         </ul>
         <!-- Tab panes -->
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
               <div data-widget-role="role1">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="panel panel-default panel-grid" style="visibility: visible;">
                           <div class="panel-body no-padding p-0">
                              <div id="memListTable_wrapper" class=" form-inline no-footer">
                                 <div class="row">
                                    <div class="col-lg-12">
                                       <h3><?= $this->lang->line("ftp_list")?></h3>
                                    </div>
                                    <div class="col-xs-6">
                                       <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                        <input type="hidden" name="ftp_id_daily" id="ftp_id_daily">  
                                       </div>
                                    </div>
                                    <div class="col-xs-6 text-right ftpbtn"> <a class="btn btn-primary" href="<?= base_url('client/project/add_ftp/'.$project_data->project_id) ?>" ><?= $this->lang->line("add_newftp")?></a> </div>
                                 </div>
                                 
                                    <table id="ftpservertable" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info">
                                       <thead>
                                          <tr role="row">
                                             <th style="width: 16px;" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
                                             <th style="width: 300px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Name  : activate to sort column ascending"><?= $this->lang->line("name")?> </th>
                                             <th style="width: 71px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?= $this->lang->line("status")?> </th>
                                             <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?= $this->lang->line("created")?> </th>
                                             <th style="width: 40px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Action  : activate to sort column ascending"><?= $this->lang->line("action")?> </th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          
                                          	<?php
                                          	if (count($ftp_servers) > 0) {
                                          		$cnt = 1;
                                          		foreach($ftp_servers as $ftps) {
                                          	?>
                                             <tr role="row" class="odd">
                                          	<td class="sorting_1"><?= $cnt ?></td>
                                             <td style="width:300px"><?= $this->encryption->decrypt($ftps->caption)?> </td>
                                             <td><span class="badge badge-success"><?= $this->lang->line($ftps->status)?></span></td>
                                             <td><?= displayDate($ftps->added_date)?></td>
                                            
                                             <td>
                                                <a href="<?= base_url('client/project/backup_ftp/'.$ftps->ftp_id) ?>" class="btn btn btn-primary"><?= $this->lang->line("manage")?></a>
                                                
                                                <a href="<?= base_url('client/project/edit_ftp/'.base64_encode($ftps->ftp_id)) ?>" class="btn btn-primary" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Edit"><i class="flaticon-pencil-1"></i></a>
                                                <a href="javascript:" class="btn btn-danger" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Delete" onclick="deleteftp(<?= $ftps->ftp_id?>)"><i class="flaticon-trash"></i></a>
                                                <a class="btn btn-primary" href="javascript:" onclick="backup_now(<?= $ftps->ftp_id?>)"><?=$this->lang->line("backup_now") ?></a>

                                                <label class="switch">
                                                    <input type="hidden" name="dttime_<?= $ftps->ftp_id?>" id="dttime_<?= $ftps->ftp_id?>" value="<?= $ftps->scheduling_time?>">
                                                    <input type="checkbox" <?= $ftps->scheduling_flag ? "checked":"" ?> id="ftp_status_<?= $ftps->ftp_id?>"  onclick='action_on_daily_ftp(<?= json_encode(array("ftp_id" =>$ftps->ftp_id,"scheduling_type" => $ftps->scheduling_type, "scheduling_day" => $ftps->scheduling_day , "scheduling_time" => $ftps->scheduling_time))?>)'>
                                                    <span class="slider round"></span>
                                                </label>
                                                <span id="tt_up_<?= $ftps->ftp_id?>" style="display: none;"><?= $ftps->scheduling_flag ? $ftps->scheduling_time : ""; ?></span>
                                                <strong >
                                                  <?php
                                                    if($ftps->scheduling_flag){
                                                        ?>
                                                        <?= $this->lang->line($ftps->scheduling_type)?><?= $ftps->scheduling_type != "daily" ? " (".$this->lang->line(ucfirst($ftps->scheduling_day)).") " : "  " ?> <?= $ftps->scheduling_time ?>
                                                        <?php
                                                    }
                                                  ?>
                                                 </strong>
                                             </td>
                                              </tr>
                                          	<?php
                                          	$cnt++;
                                          		}
                                          	}
                                          	?>
                                         
                                       </tbody>
                                    </table>
                                 
                                 
                              </div>
                           </div>
                           <!-- <div class="panel-footer"></div> -->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
               <div data-widget-role="role1">
                  <div class="row">
                     <div class="col-md-12">
                        <div class="panel panel-default panel-grid" style="visibility: visible;">
                           <div class="panel-body no-padding p-0">
                              <div id="memListTable_wrapper" class=" form-inline no-footer">
                                 <div class="row">
                                    <div class="col-lg-12">
                                       <h3><?= $this->lang->line("db_list")?></h3>
                                    </div>
                                    <div class="col-lg-6">
                                       <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                          <input type="search" class="form-control" placeholder="" aria-controls="memListTable">
                                          <i class="fa fa-calendar"></i>
                                       </div>
                                    </div>
                                    <div class="col-lg-6 text-right ftpbtn"> <a class="btn btn-primary" href="<?= base_url('client/project/add_db/'.$project_data->project_id) ?>" ><?= $this->lang->line("add_new_db")?></a> </div>
                                 </div>
                                 <input type="hidden" name="mysql_id_daily" id="mysql_id_daily">
                                    <table id="dbservertable" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info">
                                       <thead>
                                          <tr role="row">
                                             <th style="width: 16px;" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
                                             <th style="width: 200px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Name  : activate to sort column ascending"><?= $this->lang->line("name")?> </th>
                                             <th style="width: 71px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?= $this->lang->line("status")?> </th>
                                             <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?= $this->lang->line("created")?> </th>
                                             <th style="width: 469px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Action  : activate to sort column ascending"><?= $this->lang->line("action")?> </th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php
                                             if (count($db_servers) > 0) {
                                                $cnt = 1;
                                                foreach($db_servers as $dbs) {
                                             ?>
                                             <tr role="row" class="odd">
                                             <td class="sorting_1"><?= $cnt ?></td>
                                             <td style=""><?= $this->encryption->decrypt($dbs->caption)?> </td>
                                             <td><span class="badge badge-success"><?= $this->lang->line($dbs->status)?></span></td>
                                             <td><?= displayDate($dbs->added_date)?></td>
                                             
                                             <td>
                                                <a href="<?= base_url('client/project/backup_db/'.$dbs->mysql_id) ?>" class="btn btn btn-primary"><?= $this->lang->line("manage")?></a>
                                                <a href="<?= base_url('client/project/edit_db/'.base64_encode($dbs->mysql_id)) ?>" class="btn btn-primary" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Edit"><i class="flaticon-pencil-1"></i></a>
                                              
                                                <a href="javascript:" class="btn btn-danger" style="min-width:40px" data-toggle="tooltip" data-placement="top" title="Delete" onclick="deletedb(<?= $dbs->mysql_id?>)"><i class="flaticon-trash"></i></a>
                                                <a class="btn btn-primary" href="javascript:" onclick="backup_now_db(<?= $dbs->mysql_id?>)"><?=$this->lang->line("backup_now") ?></a> 

                                                <label class="switch">
                                                  <input type="hidden" name="dttimedb_<?= $dbs->mysql_id?>" id="dttimedb_<?= $dbs->mysql_id?>">
                                                  <input type="checkbox" <?= $dbs->scheduling_flag ? "checked":"" ?> id="db_status_<?= $dbs->mysql_id?>"  
                                                  onclick='action_on_daily_db(<?= json_encode(array("mysql_id" =>$dbs->mysql_id,"scheduling_type" => $dbs->scheduling_type, "scheduling_day" => $dbs->scheduling_day , "scheduling_time" => $dbs->scheduling_time))?>)' >
                                                  <span class="slider round"></span>
                                                  </label>
                                                  

                                                  <span id="tt_upd_<?= $dbs->mysql_id?>" style="display: none;"><?= $dbs->scheduling_flag ? $dbs->scheduling_time : ""; ?></span>
                                                <strong >
                                                  <?php
                                                    if($dbs->scheduling_flag){
                                                        ?>
                                                        <?= $this->lang->line($dbs->scheduling_type)?><?= $dbs->scheduling_type != "daily" ? " (".$this->lang->line(ucfirst($dbs->scheduling_day)).") " : "  " ?> <?= $dbs->scheduling_time ?>
                                                        <?php
                                                    }
                                                  ?>
                                                 </strong>
                                             </td>
                                              </tr>
                                             <?php
                                             $cnt++;
                                                }
                                             }
                                             ?>
                                       </tbody>
                                    </table>
                                 
                              </div>
                           </div>
                           <!-- <div class="panel-footer"></div> -->
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<div class="modal fade" id="schedulebackup-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cancelftp_daily()"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><?= $this->lang->line("Set Time for Daily Backup")?></h4>
                              </div>
                              <div class="modal-body">
                                <form id="ftp_scheduling_form" method="post" onsubmit="update_daily_schedule();return false;">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                <input type="hidden" name="ftp_id_schedule" id="ftp_id_schedule">
                                <input type="hidden" name="cntstatus" id="cntstatus">
                                <p id="scheduling_type_error" class="ftpsch_error" style="color:red;"></p>
                                <div class="schedule">
                                  <div class="row">
                                    <div class="col-lg-4">
                                      <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type" id="scheduling_type_daily" value="daily" class="ftpschedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/daily.png">
                                      <?= $this->lang->line("daily")?>
                                      
                                    </label>

                                  </div>
                                  <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type" id="scheduling_type_weekly" value="weekly" class="ftpschedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/weekly.png">
                                       <?= $this->lang->line("weekly")?>
                                    </label>
                                  </div>
                                  <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type" id="scheduling_type_monthly" value="monthly" class="ftpschedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/monthly.png">
                                      <?= $this->lang->line("monthly")?>
                                      
                                    </label>
                                  </div>
                                  
                                    </div>
                                    <div class="col-lg-8">
                                      <p id="scheduling_day_error" class="ftpsch_error" style="color:red;"></p>
                                      <div class="schedule_option">
                                        <h3 id="ftp_sch_tp"></h3>
                                        <div class="radio-pillbox">
                                          <radiogroup>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="mon" value="mon" class="first ftp_sch_day">
                                              <label for="js" class="radio-label"><?=$this->lang->line("Mon")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="tue" value="tue" class="ftp_sch_day">
                                              <label for="tricky"><?=$this->lang->line("Tue")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="wed" value="wed" class="ftp_sch_day">
                                              <label for="css"><?=$this->lang->line("Wed")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="thu" value="thu" class="ftp_sch_day">
                                              <label for="angularjs"><?=$this->lang->line("Thu")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="fri" value="fri" class="ftp_sch_day">
                                              <label for="jquery"><?=$this->lang->line("Fri")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="sat" value="sat" class="ftp_sch_day">
                                              <label for="jquery"><?=$this->lang->line("Sat")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day" id="sun" value="sun" class="last ftp_sch_day">
                                              <label for="rn"><?=$this->lang->line("Sun")?></label>
                                              </input>
                                            </div>
                                          </radiogroup>
                                        </div>
                                        <div class="form-inline" style="margin-top:15px">
                                        <div class="form-group">
                                          <label for="exampleInputName2"><?=$this->lang->line("Start at")?>:</label>

                                          <div class="input-group date" id="datetimepicker3">
                                             <input type="text" class="form-control" name="scheduletime" id="scheduletime" placeholder="<?= $this->lang->line("select time") ?>">
                                             <span class="input-group-addon">
                                             <span class="glyphicon glyphicon-time"></span>
                                             </span>
                                              
                                          </div>
                                          <p id="scheduletime_error" class="ftpsch_error" style="color:red;"></p>
                                        </div>

                                        <div class="form-group" style="margin-top:15px">
                                          <div class="input-group">
                                            <div class="" id="projerrormsg" style="color: red;"></div>
                                          <div class="text-right">
                                            <button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit")?></button>
                                            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal" onclick="cancelftp_daily()"><?= $this->lang->line("close")?></button>
                                          </div>
                                          </div>
                                        </div>
                                        
                                      </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="modal fade" id="schedulebackupdb-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="canceldb_daily()"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel"><?= $this->lang->line("Set Time for Daily Backup")?></h4>
                              </div>
                              <div class="modal-body">
                                <form id="db_scheduling_form" method="post" onsubmit="update_daily_schedule_db();return false;">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                <input type="hidden" name="db_id_schedule" id="db_id_schedule">
                                <input type="hidden" name="cntstatusdb" id="cntstatusdb">
                                <p id="scheduling_typedb_error" class="dbsch_error" style="color:red;"></p>
                                <div class="schedule">
                                  <div class="row">
                                    <div class="col-lg-4">
                                      <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type_db" id="scheduling_type_daily_db" value="daily" class ="db_schedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/daily.png">
                                      <?= $this->lang->line("daily")?>
                                    </label>

                                  </div>
                                  <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type_db" id="scheduling_type_weekly_db" value="weekly"  class ="db_schedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/weekly.png">
                                      <?= $this->lang->line("weekly")?>
                                    </label>
                                  </div>
                                  <div class="radio">
                                    <label>
                                      <input type="radio" name="scheduling_type_db" id="scheduling_type_monthly_db" value="monthly" class ="db_schedule_type">
                                      <img src="<?=base_url()?>public/public/assets/img/monthly.png">
                                      <?= $this->lang->line("monthly")?>
                                    </label>
                                  </div>
                                  
                                    </div>
                                    <div class="col-lg-8">
                                      <p id="scheduling_daydb_error" class="dbsch_error" style="color:red;"></p>
                                      <div class="schedule_option">
                                        <h3 id="db_sch_tp"></h3>
                                        <div class="radio-pillbox">
                                          <radiogroup>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="mon_db" value="mon" class="first db_sch_day">
                                              <label for="js" class="radio-label"><?=$this->lang->line("Mon")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="tue_db" value="tue" class="db_sch_day">
                                              <label for="tricky"><?=$this->lang->line("Tue")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="wed_db" value="wed" class="db_sch_day">
                                              <label for="css"><?=$this->lang->line("Wed")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="thu_db" value="thu" class="db_sch_day">
                                              <label for="angularjs"><?=$this->lang->line("Thu")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="fri_db" value="fri" class="db_sch_day">
                                              <label for="jquery"><?=$this->lang->line("Fri")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="sat_db" value="sat" class="db_sch_day">
                                              <label for="jquery"><?=$this->lang->line("Sat")?></label>
                                              </input>
                                            </div>
                                            <div>
                                              <input type="radio" name="scheduling_day_db" id="sun_db" value="sun" class="last db_sch_day">
                                              <label for="rn"><?=$this->lang->line("Sun")?></label>
                                              </input>
                                            </div>
                                          </radiogroup>
                                        </div>
                                        <div class="form-inline" style="margin-top:15px">
                                        <div class="form-group">
                                          <label for="exampleInputName2"><?=$this->lang->line("Start at")?>:</label>

                                          <div class="input-group date" id="datetimepicker4">
                                             <input type="text" class="form-control" name="scheduletime" id="scheduletime_db" placeholder="<?= $this->lang->line("select time") ?>">
                                             <span class="input-group-addon">
                                             <span class="glyphicon glyphicon-time"></span>
                                             </span>
                                              
                                          </div>
                                          <p id="scheduletimedb_error" class="dbsch_error" style="color:red;"></p>
                                        </div>
                                        <div class="form-group" style="margin-top:15px">
                                          <div class="input-group">
                                            <div class="" id="projerrormsg" style="color: red;"></div>
                                          <div class="text-right">
                                            <button type="submit" class="btn btn-primary m-b-0"><?= $this->lang->line("submit")?></button>
                                            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal" onclick="canceldb_daily()"><?= $this->lang->line("close")?></button>
                                          </div>
                                          </div>
                                        </div>
                                        
                                      </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </form>
                              </div>
                            </div>
                          </div>
                        </div>



<script type="text/javascript">
  $(document).ready(function() {
             $('#datetimepicker3').datetimepicker({
                 format: 'HH:mm'
             });
             $('#datetimepicker4').datetimepicker({
                 format: 'HH:mm'
             });
         });
</script>

<script type="text/javascript">
   $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip({container: 'body'})
         $('#ftpservertable').DataTable( {
              "language": {
                  "url": "<?php echo $this->lang->line("language_file")?>"
              },
      });
         $('#dbservertable').DataTable( {
              "language": {
                  "url": "<?php echo $this->lang->line("language_file")?>"
              },
      });
         var typess = {"daily" : "<?= $this->lang->line("daily")?>","weekly" : "<?= $this->lang->line("weekly")?>","monthly" : "<?= $this->lang->line("monthly")?>","upon_event" :"<?= $this->lang->line("monthly")?>"} ;

        $(".ftpschedule_type").click(function(){
              $("#ftp_sch_tp").html(typess[this.value])
              if(this.value == "daily"){
                  $(".ftp_sch_day").attr("disabled",true);
              }else{
                  $(".ftp_sch_day").attr("disabled",false);
              }
        });
        $(".db_schedule_type").click(function(){
              $("#db_sch_tp").html(typess[this.value])
              if(this.value == "daily"){
                  $(".db_sch_day").attr("disabled",true);
              }else{
                  $(".db_sch_day").attr("disabled",false);
              }
        })
        
   });




   function action_on_daily_ftp(data){
      
     $("#ftp_id_daily").val(data.ftp_id);
     $("#ftp_id_schedule").val(data.ftp_id);
        if($('#ftp_status_'+data.ftp_id).is(':checked')){
          var txt = "enabled"; 
              $("#schedulebackup-modal").modal("show");
              $("#cntstatus").val(1)
        }else{
              var txt = "disabled";
              $("#schedulebackup-modal").modal("hide");
              $("#cntstatus").val(0)
              update_daily_schedule();   
        }  

      if(data.scheduling_type != ""){


      $("input[name=scheduling_type][value=" + data.scheduling_type + "]").prop('checked', true);
      }
      if(data.scheduling_type == "daily"){
        
        $(".ftp_sch_day").attr("disabled",true);

      }else if(data.scheduling_type == "weekly" || data.scheduling_type == "monthly"){
        $(".ftp_sch_day").attr("disabled",false);
        if(data.scheduling_day != ""){
          $("input[name=scheduling_day][value=" + data.scheduling_day + "]").prop('checked', true);
        } 
      }  
      $("#scheduletime").val(data.scheduling_time);
   }
   function cancelftp_daily(){
    var ftp_id = $("#ftp_id_daily").val();
                        if($('#ftp_status_'+ftp_id).is(':checked')){
                              $('#ftp_status_'+ftp_id).removeAttr("checked");
                        }else{
                              $('#ftp_status_'+ftp_id).attr("checked",true);
                        }
   }
   function canceldb_daily(){
    var mysql_id = $("#mysql_id_daily").val();
                        if($('#db_status_'+mysql_id).is(':checked')){
                              $('#db_status_'+mysql_id).removeAttr("checked");
                        }else{
                              $('#db_status_'+mysql_id).attr("checked",true);
                        }
   }

   function update_daily_schedule(){
    var ftp_id = $("#ftp_id_daily").val();
    if($('#ftp_status_'+ftp_id).is(':checked')){
          var txt = "<?php echo $this->lang->line("enabled")?> "; 
          var cntstatus = 1;
    }else{
          var txt =  "<?php echo $this->lang->line("disabled")?> ";
          var cntstatus = 0;
    }
    var dttime = $("#scheduletime").val();
    var chk = "<?php echo $this->lang->line("Are you sure you want to")?>  "+txt+" <?php echo $this->lang->line("backup")?>";
              swal(chk, {
                  buttons: {
                    cancel: "<?php echo $this->lang->line("No")?>",
                    catch: {
                      text: "<?php echo $this->lang->line("Yes")?>",
                      value: "catch",
                    },
                  },
                })
                .then((value) => {
                  switch (value) {
                    case "cancel":
                    
                      if($('#ftp_status_'+ftp_id).is(':checked')){
                              $('#ftp_status_'+ftp_id).removeAttr("checked");
                        }else{
                              $('#ftp_status_'+ftp_id).attr("checked",true);
                        }
                        //return false;
                      break;
                    case "catch":
                        $.ajax({
                             url:"<?php echo base_url();?>client/project/update_daily_status",
                             type:"post",
                             beforeSend:function(){
                                $("#cover-spin").show()
                             },
                              data:$("#ftp_scheduling_form").serialize(),
                              dataType: 'json',
                                      success:function(data){
                                          if(data.status == "success"){
                                              swal(data.msg, {
                                                  title: "<?= $this->lang->line("great")?>",
                                                  type: "success",
                                                  timer: 3000
                                                });10
                                               txt == 'enabled' ? $("#tt_up_"+ftp_id).html(dttime) :  $("#tt_up_"+ftp_id).html("");
                                              $("#schedulebackup-modal").modal("hide");
                                              location.reload();
                                          }else if(data.error_data != undefined){
                                           
                                              $(".ftpsch_error").html("");
                                              $.each(data.error_data, function (key, val) {
                                                $("#"+key+"_error").html(val);
                                            });
                                          }else{
                                             swal(data.msg, {
                                                  title: "<?= $this->lang->line("oops")?>",
                                                  type: "error",
                                                  timer: 3000
                                                });
                                          }
                                          $("#cover-spin").hide()     
                                       }
                                  });
                      break;
                    default:

                      if($('#ftp_status_'+ftp_id).is(':checked')){
                              $('#ftp_status_'+ftp_id).prop('checked', false);
                        }else{
                              $('#ftp_status_'+ftp_id).prop('checked', true);
                        }
                        $("#schedulebackup-modal").modal("hide");
                     return false;
                  }
                });
                
}
function action_on_daily_db(data){
     $("#mysql_id_daily").val(data.mysql_id);
     $("#db_id_schedule").val(data.mysql_id);
     
        if($('#db_status_'+data.mysql_id).is(':checked')){
          var txt = "enabled"; 
              $("#schedulebackupdb-modal").modal("show");
              $("#cntstatusdb").val(1)

        }else{
              var txt = "disabled";
               $("#cntstatusdb").val(0)
              $("#schedulebackupdb-modal").modal("hide");
              update_daily_schedule_db();
              
        }  

         if(data.scheduling_type !=""){
          $("input[name=scheduling_type_db][value=" + data.scheduling_type + "]").prop('checked', true);
        }
      if(data.scheduling_type == "daily"){
        $(".db_sch_day").attr("disabled",true);
      }else if(data.scheduling_type == "weekly" || data.scheduling_type == "monthly"){
        $(".db_sch_day").attr("disabled",false);
        if(data.scheduling_day !=""){
          $("input[name=scheduling_day_db][value=" + data.scheduling_day + "]").prop('checked', true);
      }
      }  
      $("#scheduletime_db").val(data.scheduling_time);
   }



function update_daily_schedule_db(){
   var mysql_id = $("#mysql_id_daily").val();
    if($('#db_status_'+mysql_id).is(':checked')){
          var txt = "<?php echo $this->lang->line("enabled")?> "; 
          var cntstatus = 1;
    }else{
          var txt = "<?php echo $this->lang->line("disabled")?> ";
          var cntstatus = 0;
    }
    var dttime = $("#scheduletime_db").val();
    var chk = "<?php echo $this->lang->line("Are you sure you want to")?>  "+txt+" <?php echo $this->lang->line("backup")?>";
              swal(chk, {
                  buttons: {
                    cancel: "<?php echo $this->lang->line("No")?>",
                    catch: {
                      text: "<?php echo $this->lang->line("Yes")?>",
                      value: "catch",
                    },
                  },
                })
                .then((value) => {
                  switch (value) {
                    case "defeat":
                    if($('#db_status_'+mysql_id).is(':checked')){
                              $('#db_status_'+mysql_id).removeAttr("checked");
                        }else{
                              $('#db_status_'+mysql_id).attr("checked",true);
                        }
                      return false;
                      break;
                    case "catch":
                        $.ajax({
                             url:"<?php echo base_url();?>client/project/update_daily_status_db",
                             type:"post",
                             beforeSend:function(){
                                $("#cover-spin").show()
                             },
                              data: $("#db_scheduling_form").serialize(),
                              dataType: 'json',
                                      success:function(data){
                                          if(data.status == "success"){
                                              swal(data.msg, {
                                                  title: "<?= $this->lang->line("great")?>",
                                                  type: "success",
                                                  timer: 3000
                                                })
                                              txt == 'enabled' ? $("#tt_upd_"+mysql_id).html(dttime) :  $("#tt_upd_"+mysql_id).html("");
                                               $("#schedulebackupdb-modal").modal("hide");
                                               location.reload();
                                          }else if(data.error_data != undefined){
                                              $(".dbsch_error").html("");
                                              $.each(data.error_data, function (key, val) {
                                                $("#"+key+"db_error").html(val);
                                              });
                                          }else{
                                             swal(data.msg, {
                                                  title: "<?= $this->lang->line("oops")?>",
                                                  type: "error",
                                                  timer: 3000
                                                });
                                          }
                                          $("#cover-spin").hide()     
                                       }
                                  });
                      break;
                    default:
                      if($('#db_status_'+mysql_id).is(':checked')){
                              $('#db_status_'+mysql_id).prop('checked', false);
                        }else{
                              $('#db_status_'+mysql_id).prop("checked",true);
                        }
                        $("#schedulebackup-modal").modal("show");
                     return false;
                  }
                });
}
   function deleteftp(ftp_id){
    var chk = "<?php echo $this->lang->line("ftp_server_bkp_delete_sure")?>";
    swal(chk, {
        buttons: {
          cancel: "<?php echo $this->lang->line("No")?>",
          catch: {
            text: "<?php echo $this->lang->line("Yes")?>",
            value: "catch",
          },
        },
      })
      .then((value) => {
        switch (value) {
          case "cancel":
            return false;
            break;
          case "catch":
              $.ajax({
                   url:"<?php echo base_url();?>client/project/deleteftp",
                   type:"post",
                   beforeSend:function(){
                      $("#cover-spin").show()
                   },
                    data: {ftp_id:ftp_id},
                    dataType: 'json',
                            success:function(data){
                                if(data.status == "success"){
                                  swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
                              location.reload();
                            })
                                }else{
                                  swal(data.msg, {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });

                                }
                                $("#cover-spin").hide()     
                             }
                        });
            break;
       
          default:
           return false;
        }
      });
  
    }

    function deletedb(db_id){
       
    var chk = "<?php echo $this->lang->line("db_server_bkp_delete_sure")?>";
    swal(chk, {
        buttons: {
          cancel: "<?php echo $this->lang->line("No")?>",
          catch: {
            text: "<?php echo $this->lang->line("Yes")?>",
            value: "catch",
          },
        },
      })
      .then((value) => {
        switch (value) {
          case "cancel":
            return false;
            break;
          case "catch":
              $.ajax({
                   url:"<?php echo base_url();?>client/project/deletedb",
                   type:"post",
                   beforeSend:function(){
                      $("#cover-spin").show()
                   },
                    data: {db_id:db_id},
                    dataType: 'json',
                            success:function(data){
                                if(data.status == "success"){
                                   swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
                                            location.reload();
                                        }).then(() => {
                              location.reload();
                            })
                                }else{
                                  swal(data.msg, {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
                                }
                                $("#cover-spin").hide()     
                             }
                        });
            break;
       
          default:
           return false;
        }
      });
  
    }

    function backup_now(ftp_id){
      
   var chk = "<?php echo $this->lang->line("ftp_bkp_sure")?>";
   var type = "current";
   swal(chk, {
       buttons: {
         cancel: "<?php echo $this->lang->line("No")?>",
         catch: {
           text: "<?php echo $this->lang->line("Yes")?>",
           value: "catch",
         },
       },
     })
     .then((value) => {
       switch (value) {
         case "cancel":
           return false;
           break;
         case "catch":
             $.ajax({
                  url:"<?php echo base_url();?>client/project/putftpcron",
                  type:"post",
                  beforeSend:function(){
                     $("#cover-spin").show()
                  },
                   data: {ftp_id:ftp_id,type:type},
                   dataType: 'json',
                           success:function(data){
                               if(data.status == "success"){
                                   
                                   swal(data.msg, {
                                     title: "<?= $this->lang->line("great")?>",
                                     type: "success",
                                     timer: 3000
                                   }).then(() => {
                                      location.reload();
                                     })
                                   
                               }else{
                                 swal(data.msg, {
                                     title: "<?= $this->lang->line("oops")?>",
                                     type: "error",
                                     timer: 3000
                                   });
                               }
                               $("#cover-spin").hide()     
                            }
                       });
           break;
      
         default:
          return false;
       }
     });
   
   }
   function backup_now_db(db_id){
    var chk = "<?php echo $this->lang->line("db_bkp_sure")?>";
      var type = "current";
    swal(chk, {
        buttons: {
          cancel: "<?php echo $this->lang->line("No")?>",
          catch: {
            text: "<?php echo $this->lang->line("Yes")?>",
            value: "catch",
          },
        },
      })
      .then((value) => {
        switch (value) {
          case "cancel":
            return false;
            break;
          case "catch":
              $.ajax({
                   url:"<?php echo base_url();?>client/project/putdbcron",
                   type:"post",
                   beforeSend:function(){
                      $("#cover-spin").show()
                   },
                    data: {db_id:db_id,type:type},
                    dataType: 'json',
                            success:function(data){
                                if(data.status == "success"){
                                    swal(data.msg, {
                                            title: "<?= $this->lang->line("great")?>",
                                            type: "success",
                                            timer: 3000
                                          }).then(() => {
                                          location.reload();
                                    });
                                    
                                }else{
                                  swal(data.msg, {
                                      title: "<?= $this->lang->line("oops")?>",
                                      type: "error",
                                      timer: 3000
                                    });
                                }
                                $("#cover-spin").hide()     
                             }
                        });
            break;
       
          default:
           return false;
        }
      });

   }
</script>
<?php $this->load->view("client/layout/footer_new"); ?>

