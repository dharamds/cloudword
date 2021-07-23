  <?php $this->load->view("admin/layout/header_new"); ?>
  <?php $this->load->view("admin/layout/sidebar"); ?>
  <div class="container-fluid">
    <div class="row mr-0">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project')?>"><?=$this->lang->line("projects") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project/manage_backup/'.base64_encode($backup_data->project_id))?>"><?=$this->lang->line("backups") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project/backup_db/'.$backup_data->db_id)?>"><?=$this->encryption->decrypt($server_data->caption) ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?=$this->lang->line("restore_process") ?></li>
        </ol>
      </nav>
      <div id="cover-spin"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="filter-container flex-row">
            <div class="flex-col-6">
              <h3 class="filter-content-title"><?=$this->encryption->decrypt($server_data->caption) ?></h3>
            </div>
            <div class="flex-col-6 text-right"> <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/admin/projects/create"> New Project </a> --> </div>
          </div>
        </div>
      </div>
      <div><ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=$this->lang->line("DB Restore") ?></a></li>
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
                            <div class="col-lg-12"><h3><?=$this->lang->line("restore_process_list") ?></h3></div>
                            <div class="col-sm-5 searchdiv">
                              <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                  <input type="search" class="form-control" placeholder="" aria-controls="memListTable">
                                   <i class="fa fa-calendar"></i>
                              </div>
                            </div>
                            <div class="col-sm-7 ftpbtn">
                            </div>
                          </div>
                        
                          <table id="backuplist" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info">
                            <thead>
                              <tr role="row">
                               <th style="width: 16px;" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("last_restore_time") ?> </th>
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("db_name") ?> </th>
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
        <h4 class="modal-title"><?= $this->lang->line("live_status_db_restore_process")?></h4>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closestmodal()">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="ftprestore_id" id="ftprestore_id" value="">
        <div class="progress" style="height:20px">
            <div class="progress-bar" id="livestatuspercent" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%;height:20px">
              0%
            </div>
        </div>
        <div class="alert alert-success" role="alert" id="successmsggg" style="display: none;">
            <?= $this->lang->line("restore_process_success_msg") ?>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table id="livestatutable" class="table table-bordered table-striped table-hover datatable" cellspacing="0">
              <thead>
                <th>#</th>
                <th><?= $this->lang->line("table_name")?></th>
                <th><?= $this->lang->line("created")?></th>
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
    $(document).ready(function(){
    	  $('[data-toggle="tooltip"]').tooltip({container: 'body'})
          var dttt =  $('#backuplist').DataTable({
                "language": {
                    "url": "<?php echo $this->lang->line("language_file")?>"
                },
              'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                   'url':'<?=base_url()?>admin/backup/restore_process_db_ajax',
                   'data': function(data){
                      var backup_id = parseInt("<?php echo $backup_id ?>");
                      data.backup_id = backup_id;
                     }
                },
                'columns': [
                   { data: 'sr_no' },
                   { data: 'created' },
                   { data:'db_name'},
                   { data: 'status' },                  
                   { data: 'action' },
                ]
             });

          setInterval( function (){
            var rows= $('#backuplist tbody tr td span.badge-warning').length;
            if(rows > 0){
              dttt.draw();
            }
            var restore_id = $("#ftprestore_id").val();
            if(restore_id != ""){
                if(!$('#successmsggg').is(':visible')){
                    viewlogs(restore_id);
                }
            }
          }, 10000 );
   });
  function closestmodal(){
      $("#ftprestore_id").val("");
   }
  function viewlogs(restore_id){

    if($("#ftprestore_id").val() == ""){
        $("#ftprestore_id").val(restore_id);
    } 
                     $.ajax({
                            url:"<?php echo base_url();?>admin/backup/restorelogsdb",
                            type:"post",
                            beforeSend:function(){
                               $("#cover-spin").show()
                            },
                             data: {restore_id:restore_id},
                             dataType: 'json',
                                     success:function(res){
                                         if(res.status == "success" || res.status == "processing"){
                                            var tbodyhtml = '';
                                            var tabcnt = res.totaltable;
											var cnt = 0;
                                            var per = res.complete_table;
											var perc = parseInt(Math.round((per/tabcnt) * 100));
											
											if(res.data){
												$.each(res.data, function(key, value ){
                                                                if(value.status == "success"){
                                                                  var clr = 'style="color:green"';
                                                                  //per++;
                                                                }else{
                                                                  var clr = 'style="color:orange"';
                                                                }
                                                                cnt++;
                                                                  tbodyhtml += '<tr  >';
                                                                  tbodyhtml += '<td>'+cnt+'</td>';
                                                                  tbodyhtml += '<td>'+value.table_name+'</td>';
                                                                  tbodyhtml += '<td>'+value.date+'</td>';
                                                                  tbodyhtml += '<td '+clr+'>'+value.status+'</td>';
                                                                  tbodyhtml += '</tr>';
                                                                  
                                                        });
											}else{
												var tbodyhtml = "";
                                                                  tbodyhtml += '<tr  >';
                                                                  tbodyhtml += '<td colspan="4" align="center">'+res.msg+'</td>';
                                                                  tbodyhtml += '</tr>';
                                                              $("#livestatuspercent").html("0%");
                                                              $("#livestatuspercent").attr("aria-valuenow",0);
                                                              $("#livestatuspercent").css("width",0+"%");
                                                              $("#livestatutbody").html(tbodyhtml);
                                                              $("#livestatusmodal-Modal").modal("show");
											}
                                                            
                                                        
                                                          
                                                            
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
                                                          }else{
                                                            var tbodyhtml = "";
                                                                  tbodyhtml += '<tr  >';
                                                                  tbodyhtml += '<td colspan="4" align="center">'+res.msg+'</td>';
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


  </script> 
  <?php $this->load->view("admin/layout/footer_new"); ?>