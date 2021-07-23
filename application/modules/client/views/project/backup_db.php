  <?php $this->load->view("client/layout/header_new"); ?>
  <?php $this->load->view("client/layout/sidebar"); ?>
  
  <div class="container-fluid">
    <div class="row mr-0">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('client/project')?>"><?=$this->lang->line("projects") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('client/project/manage_backup/'.$db_server_details->project_id)?>"><?=$this->lang->line("backups") ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?=$this->lang->line("db_backup") ?></li>
        </ol>
      </nav>
      <div id="cover-spin"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="filter-container flex-row">
            <div class="flex-col-6">
              <h3 class="filter-content-title"><?=$this->encryption->decrypt($db_server_details->caption) ?></h3>
            </div>
            <div class="flex-col-6 text-right"> <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/client/projects/create"> New Project </a> --> </div>
          </div>
        </div>
      </div>
      <div>        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=$this->lang->line("db_backup") ?></a></li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="profile">
            <div data-widget-role="role1">
              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default panel-grid" style="visibility: visible;"> 
                    <div class="panel-body no-padding p-0">
                      <div id="memListTable_wrapper" class=" form-inline no-footer">
                        <div class="row">
                            <div class="col-lg-12"><h3><?=$this->lang->line("list_of_bkps") ?></h3></div>
                            <div class="col-sm-5 searchdiv">
                             <!--  <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                  <input type="search" class="form-control" placeholder="" aria-controls="memListTable">
                                   <i class="fa fa-calendar"></i>
                              </div> -->
                            </div>
                            <div class="col-sm-7 ftpbtn">
                              
                             <!--  <div class="form-group" id="schedule_datebox" >
                                 <input type="text" name="schedule_date" id="schedule_date" value="" class="hasDatepicker form-control">
                              </div> -->
                              <a class="btn btn-primary" href="javascript:" onclick="backup_now(<?= $db_server_details->mysql_id ?>)"><?=$this->lang->line("backup_now") ?></a> 
                              
                              <!-- <a class="btn btn-primary" href="javascript:" onclick="schedule_now(<?= $db_server_details->mysql_id ?>)"><?php // $this->lang->line("schedule_bkp") ?></a>  -->
                            </div>
                          </div>
                        
                          <table id="backuplist" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info">
                            <thead>
                              <tr role="row">
                               <th style="width: 16px;" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
                              
                               
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("created") ?> </th>
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("last_backup_size") ?>  </th>
                               <th style="width: 71px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("status") ?> </th>
                               <th style="width: 469px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Action  : activate to sort column ascending"><?=$this->lang->line("action") ?> </th>
                             </tr>
                           </thead>
                         </thead>
                         
                      </table>
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade livestatusmodal" id="livestatusmodal-Modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document" style="width: 500px;">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?= $this->lang->line("live_status_db_process") ?></h4>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closestmodal()">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="bkpmodal_id" id="bkpmodal_id" value="">
        <div class="progress" style="height:20px">
            <div class="progress-bar" id="livestatuspercent" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;height:20px">
              0%
            </div>
        </div>
        <div class="alert alert-success" role="alert" id="successmsggg" style="display: none;">
            <?= $this->lang->line("backup_success_taken") ?>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table id="livestatutable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
              <thead>
                <th>#</th>
                <th><?= $this->lang->line("db_name")?></th>
                <th><?= $this->lang->line("status")?></th>
              </thead>
              <tbody id="livestatutbody">
                
              </tbody>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12" id="error_logfilecontent">

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
  <script type="text/javascript">
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip({container: 'body'})
          var dttt =  $('#backuplist').DataTable({
                "language": {
                    "url": "<?php echo $this->lang->line("language_file")?>"
                },
              'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                   'url':'<?=base_url()?>client/project/getdb_ajax',
                   'data': function(data){
                      var db_id = parseInt("<?php echo $db_id ?>");
                      data.db_id = db_id;
                     }
                },
                'columns': [
                   { data: 'sr_no' },
                   { data: 'last_backup_time' },
                   { data: 'last_backup_size' },
                   { data: 'status' },
                   { data: 'action' },
                ]
             });
          setInterval(function(){
            var rows= $('#backuplist tbody tr td span.badge-warning').length;
            if(rows > 0){
              dttt.draw();
            }
            var backup_id = $("#bkpmodal_id").val();
            if(backup_id != ""){
              
                if(!$('#successmsggg').is(':visible')){
                    viewlogs(backup_id);
                }
            }

          },15000);


   });
     function closestmodal(){
      $("#bkpmodal_id").val("");
   }
    function backup_now(db_id){
       
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
          case "defeat":
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
    function schedule_now(db_id){
        var schedule_date = $("#schedule_date").val();
        if(schedule_date !=""){
            var type = "schedule";
            var chk = "<?php echo $this->lang->line("db_bkp_schedule_sure")?>";
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
                      return false;
                      break;
                    case "catch":
                        $.ajax({
                             url:"<?php echo base_url();?>client/project/putdbcron",
                             type:"post",
                             beforeSend:function(){
                                $("#cover-spin").show()
                             },
                              data: {db_id:db_id,schedule_date:schedule_date,type:type},
                              dataType: 'json',
                                      success:function(data){
                                          if(data.status == "success"){
                                             swal(data.msg, {
                                                  title: "<?= $this->lang->line("great")?>",
                                                  type: "success",
                                                  timer: 3000
                                                });
                                              location.reload();
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

        }else{
          $("#schedule_date_msg").html("Please choose Schedule Date");
        }
    }
   function viewlogs(backup_id){
    if($("#bkpmodal_id").val() == ""){
        $("#bkpmodal_id").val(backup_id);
    }
                     $.ajax({
                            url:"<?php echo base_url();?>client/project/backupdblogs",
                            type:"post",
                            beforeSend:function(){
                               $("#cover-spin").show()
                            },
                             data: {backup_id:backup_id},
                             dataType: 'json',
                                     success:function(res){
                                         if((res.status == "success" || res.status == "processing") && res.data != null){
                                            var tbodyhtml = '';
                                            var totalCnt = res.total_table;
											var cnt = 0;
                                            var per = res.completed_table;
                                            $.each(res.data, function(key1, value ){
                                                    if(value.status == "success"){
                                                      var clr = 'style="color:green"';
                                                      //per++;
                                                    }else{
                                                      var clr = 'style="color:orange"';
                                                    }
                                                    cnt++;
                                                      tbodyhtml += '<tr '+clr+' >';
                                                      tbodyhtml += '<td>'+cnt+'</td>';
                                                      tbodyhtml += '<td style="font-weight:bold">'+value.db_name+'</td>';
                                                      tbodyhtml += '<td>'+value.status+'</td>';
                                                      tbodyhtml += '</tr>';
													  
													 if(value.tableData){ 
													 var tabcnt = 0;
													 $.each(value.tableData, function(key2, table ){ 
													 
													 if(table.status == "success"){
                                                      var inclr = 'style="color:green"';
                                                      //per++;
                                                    }else{
                                                      var inclr = 'style="color:orange"';
                                                    }
													 
													  tabcnt++;
													  tbodyhtml += '<tr '+inclr+' >';
                                                      tbodyhtml += '<td>'+tabcnt+'</td>';
                                                      tbodyhtml += '<td>'+table.table_name+'</td>';
                                                      tbodyhtml += '<td>'+table.status+'</td>';
                                                      tbodyhtml += '</tr>';
													  
													  });
													 }
                                                      
                                            });
                                            var perc = Math.round((per/totalCnt) * 100);
                                            if(perc == 100){
                                              $("#successmsggg").show();
                                            }else{
                                              $("#successmsggg").hide();
                                            }
                                            $("#livestatuspercent").html(perc+"%");
                                            $("#livestatuspercent").attr("aria-valuenow",perc);
                                            $("#livestatuspercent").css("width",perc+"%");
                                            $("#livestatutbody").html(tbodyhtml);
                                            $("#livestatusmodal-Modal").modal("show");
                                                          }else if(res.status == "failed"){
                                                                    tbodyhtml = "";
                                                                   tbodyhtml += '<tr  >';
                                                                   tbodyhtml += '<td colspan="3">Backup Not started</td>';
                                                                   tbodyhtml += '</tr>';
                                                            $("#livestatuspercent").html("0%");
                                                            $("#livestatuspercent").attr("aria-valuenow",0);
                                                            $("#livestatuspercent").css("width","0%");
                                                            $("#livestatutbody").html(tbodyhtml);
                                                            $("#livestatusmodal-Modal").modal("show");

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
   }
  </script>

  <?php $this->load->view("client/layout/footer_new"); ?>