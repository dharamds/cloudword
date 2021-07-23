  <?php $this->load->view("admin/layout/header_new"); ?>
  <?php $this->load->view("admin/layout/sidebar"); ?>
  <div class="container-fluid">
    <div class="row mr-0">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project')?>"><?=$this->lang->line("projects") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project/manage_backup/'.$project_data->project_id)?>"><?=$this->lang->line("backups") ?></a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/project/backup_db/'.$project_data->db_id)?>"><?=$this->lang->line("db_backup") ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?=$this->lang->line("restore_bkp") ?></li>
        </ol>
      </nav>
      <div id="cover-spin"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="filter-container flex-row">
            <div class="flex-col-md-6">
              <h3 class="filter-content-title"><?=$this->encryption->decrypt($project_data->caption) ?></h3>
            </div>
            <div class="flex-col-md-6 text-right"> <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/admin/projects/create"> New Project </a> --> </div>
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
                      <div id="memListTable_wrapper" class="dataTables_wrapper form-inline no-footer">
                        <div class="row">
                            <div class="col-lg-12"><h3><?=$this->lang->line("list_of_dbs") ?></h3></div>
                            <div class="col-lg-6">
                              <div id="memListTable_filter" class="dataTables_filter pull-left" style="margin-bottom:20px">
                                  <input type="search" class="form-control" placeholder="" aria-controls="memListTable">
                                   <i class="fa fa-calendar"></i>
                              </div>
                            </div>
                            <div class="col-lg-6 text-right ftpbtn">
                              <a class="btn btn-primary" href="<?=base_url("admin/backup/restore_process_db/".$backup_id)?>" ><?=$this->lang->line("restore_process") ?></a>
                            </div>
                          </div>
                        <div class="table-responsive">
                          <table id="backuplist" class="table table-bordered table-striped table-hover datatable dataTable no-footer" cellspacing="0" role="grid" aria-describedby="memListTable_info">
                            <thead>
                              <tr role="row">
                               <th style="width: 16px;" class="sorting_asc" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="#: activate to sort column ascending">#</th>
                               <th style="width: 200px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Name  : activate to sort column ascending"><?=$this->lang->line("db_name") ?> </th>
                               <th style="width: 71px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("status") ?> </th>
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("size") ?> </th>
                               <th style="width: 53px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Status : activate to sort column ascending"><?=$this->lang->line("file_name") ?>  </th>
                               <th style="width: 469px;" class="sorting" tabindex="0" aria-controls="memListTable" rowspan="1" colspan="1" aria-label="Action  : activate to sort column ascending"><?=$this->lang->line("action") ?> </th>
                             </tr>
                           </thead>
                         </thead>
                         <tbody>
                            <?php
                            $alldata = json_decode($project_data->foldersdata);
                            if(count($alldata) > 0) {
                              $cnt = 1;
                              foreach ($alldata as $dbs) {
                                ?>
                                <tr role="row" class="odd"> 
                                <td class="sorting_1"><?=$cnt
                                ?></td>
                                <td style="width:200px"><?=$dbs->db_name ?></td>
                                <td><span class="badge badge-<?= $dbs->status == 'processing' ? 'warning' : 'success';?>"><?=$this->lang->line($dbs->status) ?></span></td>
                                <td><?=$this->general->convert_size($dbs->size)?></td>
                                <td><?=$dbs->file_name.".zip"?></td>
                                <td>
                                  <?php
                                    $ss = json_encode(array("backup_id" => $project_data->backup_id,"db_name" => $dbs->db_name,"file_name" => $dbs->file_name.".zip"));
                                  ?>
								  <a href="<?=base_url("admin/backup/downloaddb/".$project_data->backup_id.'/'.base64_encode($dbs->db_name));?>" class="btn btn btn-primary"><?=$this->lang->line('download') ?></a>
                                  <a href="javascript:" class="btn btn btn-info" onclick='restoredb(<?= $ss ?>)'><?=$this->lang->line('restore') ?></a>
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mysqlrestore-Modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
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
              <div class="card-block" style="height:500px;overflow-x:hidden;overflow-y:auto;">
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
      </div>

    </div>
  </div>
</div>
  <script type="text/javascript">
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip({container: 'body'})
         $('#backuplist').DataTable( {
              "language": {
                  "url": "<?php echo $this->lang->line("language_file")?>"
              },
      });
   });
    
    function restoredb(dt){
            var chk = "Are you sure you want to restore Database";
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
                             url:"<?php echo base_url();?>admin/project/putdbrestorecron",
                             type:"post",
                             beforeSend:function(){
                                $("#cover-spin").show()
                             },
                              data: {backup_id:dt.backup_id,db_name:dt.db_name},
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
  <?php $this->load->view("admin/layout/footer_new"); ?>