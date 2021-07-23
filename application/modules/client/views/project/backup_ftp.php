

<?php $this->load->view("client/layout/header_new"); ?>
<?php $this->load->view("client/layout/sidebar"); ?>

<div class="container-fluid">
<div class="row mr-0">
<nav aria-label="breadcrumb">
   <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="<?= base_url('client/project')?>"><?=$this->lang->line("projects") ?></a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('client/project/manage_backup/'.base64_encode($ftp_server_details->project_id))?>"><?=$this->lang->line("backups") ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?=$this->lang->line("ftp_backup") ?></li>
   </ol>
</nav>
<div id="cover-spin"></div>
<div class="row">
   <div class="col-md-12">
      <div class="filter-container flex-row">
         <div class="flex-col-6">
            <h3 class="filter-content-title"><?=$this->encryption->decrypt($ftp_server_details->caption) ?></h3>
         </div>
         <div class="flex-col-6 text-right">
            <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/client/projects/create"> New Project </a> --> 
         </div>
      </div>
   </div>
</div>
<div>
   <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=$this->lang->line("ftp_backup") ?></a></li>
   </ul>
   <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="profile">
         <div data-widget-role="role1">
            <div class="row">
               <div class="col-md-12">
                  <div class="panel panel-default panel-grid" style="visibility: visible;">
                     <div class="panel-body no-padding p-0">
                        <div id="memListTable_wrapper" class="form-inline no-footer">
                           <div class="row">
                              <div class="col-lg-12">
                                 <h3><?=$this->lang->line("list_of_bkps") ?></h3>
                              </div>
                              <div class="col-sm-5 searchdiv">
                                 <!-- <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                    <input type="search" class="form-control" placeholder="" aria-controls="memListTable">
                                     <i class="fa fa-calendar"></i>
                                    </div> -->
                              </div>
                              <div class="col-sm-7 ftpbtn">
                                 <!--  <div class="form-group" id="schedule_datebox" >
                                    <input type="text" name="schedule_date" id="schedule_date" value="" class="hasDatepicker form-control">
                                    <span id="schedule_date_msg"></span>
                                    </div> -->
                                 <a class="btn btn-primary" href="<?=base_url("client/backup/restore_process/".$ftp_server_details->ftp_id)?>" ><?=$this->lang->line("restore_process") ?></a>
                                 <a class="btn btn-primary" href="javascript:" onclick="backup_now(<?= $ftp_server_details->ftp_id ?>)"><?=$this->lang->line("backup_now") ?></a>
                                 <!--    <a class="btn btn-primary" href="javascript:" onclick="schedule_now(<?= $ftp_server_details->ftp_id ?>)"><?php //$this->lang->line("schedule_bkp") ?></a> --> 
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
            <h4 class="modal-title"><?= $this->lang->line("live_status_ftp_process")?></h4>
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
            <div class="alert alert-info" role="alert" id="zippmsg" style="display: none;">
              <?= $this->lang->line("backup_success_taken_and_zip")?>
            </div>
            <!--<div class="row">
               <div class="col-md-12">
                  <table id="livestatutable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
                     <thead>
                        <th>#</th>
                        <th><?= $this->lang->line("file_folder_name")?></th>
                        <th><?= $this->lang->line("status")?></th>
                     </thead>
                     <tbody id="livestatutbody">
                     </tbody>
                  </table>
               </div>
            </div> -->
            <div class="row">
               <div class="col-md-12" id="error_logfilecontent">
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="restore-Modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-lg" role="document" style="width: 500px;">
<div class="modal-content">
   <div class="modal-header">
      <h4 class="modal-title"><?= $this->lang->line("restore_bkp") ?></h4>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
   </div>
   <div class="modal-body">
      <div class="row">
         <div class="col-sm-12">
            <div class="card">
               <div class="card-block" style="overflow-x:hidden;overflow-y:auto;">
                  <p style="color: orange"><?= $this->lang->line("do_not_close_browser") ?></p>
                  <div id="processingWindow" style="display:none;">
                     <h1 id="processHeading"><?= $this->lang->line("Processing") ?>..</h1>
                     <div class="progress" style="height:20px;">
                        <div class="progress-bar progress-bar-striped active" id="probar" role="progressbar"
                           aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height:20px;">
                        </div>
                     </div>
                     <h3 id="showingDoneCount"></h3>
                  </div>
                  <div id="showingProcessMsg">
                     <div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-12">
                  <table id="livestatustable" class="table table-bordered table-striped table-hover" cellspacing="0" style="display:none;">
                     <thead>
                        <th>#</th>
                        <th><?= $this->lang->line("file_folder_name")?></th>
                        <th><?= $this->lang->line("status")?></th>
                     </thead>
                     <tbody id="livestatustbody">
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
   $(document).ready(function() {
     // $('[data-toggle="tooltip"]').tooltip({container: 'body'})
     //    $('#backuplist').DataTable( {
     //         "language": {
     //             "url": "<?php //echo $this->lang->line("language_file")?>"
     //         },
     // });
        $('[data-toggle="tooltip"]').tooltip({container: 'body'})
          var dttt =  $('#backuplist').DataTable({
                "language": {
                    "url": "<?php echo $this->lang->line("language_file")?>"
                },
              'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                   'url':'<?=base_url()?>client/project/getftp_ajax',
                   'data': function(data){
                      var ftp_id = parseInt("<?php echo $ftp_id ?>");
                      data.ftp_id = ftp_id;
                     }
                },
                'columns': [
                   { data: 'sr_no' },
                   { data: 'created' },
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
         case "defeat":
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
   function schedule_now(ftp_id){
       var schedule_date = $("#schedule_date").val();
       if(schedule_date !=""){
           var type = "schedule";
           var chk = "<?php echo $this->lang->line("ftp_bkp_schedule_sure")?>";
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
                            url:"<?php echo base_url();?>client/project/putftpcron",
                            type:"post",
                            beforeSend:function(){
                               $("#cover-spin").show()
                            },
                             data: {ftp_id:ftp_id,schedule_date:schedule_date,type:type},
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
   
       }else{
         $("#schedule_date_msg").html("<?= $this->lang->line("choose_schedule_date")?>");
       }
   }
   function viewlogs(backup_id){
    if($("#bkpmodal_id").val() == ""){
        $("#bkpmodal_id").val(backup_id);
    } 
    


                     $.ajax({
                            url:"<?php echo base_url();?>client/project/backuplogs",
                            type:"post",
                            beforeSend:function(){
                               $("#cover-spin").show()
                            },
                             data: {backup_id:backup_id},
                             dataType: 'json',
                                     success:function(res){
                                         if(res.status == "success" || res.status == 'processing'){
                                           var tbodyhtml = '';
                                             var cnt = res.remote_total_size;
                                             var per = res.total_size;
                                                       /* $.each(res.data, function(key, value ){
                                                                 if(value.status == "success" && res.data != null){
                                                                   var clr = 'style="color:green"';
                                                                   var cll = 'class= "completed"';
                                                                   per++;
                                                                 }else{
                                                                   var clr = 'style="color:orange"';
                                                                   var cll = 'class= "processing"';
                                                                 }
                                                                 cnt++;
                                                                   tbodyhtml += '<tr  >';
                                                                   tbodyhtml += '<td>'+cnt+'</td>';
                                                                   tbodyhtml += '<td>'+value.filename+'</td>';
                                                                   tbodyhtml += '<td '+cll+' '+clr+'>'+value.status+'</td>';
                                                                   tbodyhtml += '</tr>';   
                                                         });
														 */
														 if(cnt > 0){
															 var perc = Math.round((per/cnt) * 100);
														 }else{
															 var perc = 0;
														 }
														  if(perc > 100){
															 perc = 100;
														 }
                                                        //var perc = Math.round((per/cnt) * 100);
                                                            if(perc == 100 && res.status == "processing"){
                                                              $("#successmsggg").hide();
                                                              $("#zippmsg").show();

                                                            }else if(perc == 100){
                                                                $("#successmsggg").show();
                                                                $("#zippmsg").hide();
                                                             }else{
                                                                $("#successmsggg").hide();
                                                             }
                                                             $("#livestatuspercent").html(perc+"%");
                                                             $("#livestatuspercent").attr("aria-valuenow",perc);
                                                             $("#livestatuspercent").css("width",perc+"%");
                                                             $("#livestatutbody").html(tbodyhtml);
                                                             $("#livestatusmodal-Modal").modal("show");
                                                          }else{
                                                                  tbodyhtml = "";
                                                                   tbodyhtml += '<tr  >';
                                                                   tbodyhtml += '<td colspan="3">Backup Not started</td>';
                                                                   tbodyhtml += '</tr>';
                                                            $("#livestatuspercent").html("0%");
                                                            $("#livestatuspercent").attr("aria-valuenow",0);
                                                            $("#livestatuspercent").css("width","0%");
                                                            $("#livestatutbody").html(tbodyhtml);
                                                            $("#livestatusmodal-Modal").modal("show");

                                                          }
                                                              $("#cover-spin").hide()     
                                                          }
                                                      });
     
   }
   function restorebkptest(bkp_id){
           var type = "schedule";
           var chk = "<?php echo $this->lang->line("Are you sure to start restore now")?>";
             swal(chk,{
                 buttons: {
                   cancel: "<?php echo $this->lang->line("No")?>",
                   catch: {
                     text: "<?php echo $this->lang->line("Yes")?>",
                     value: "catch",
                   },
                 },
               }).then((value) => {
                 switch (value) {
                   case "defeat":
                     return false;
                     break;
                   case "catch":
                       $.ajax({
                            url:"<?php echo base_url();?>client/project/putftprestorecron",
                            type:"post",
                            beforeSend:function(){
                               $("#cover-spin").show()
                            },
                             data: {bkp_id:bkp_id},
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
   
   
   
</script>

<?php $this->load->view("client/layout/footer_new"); ?>

