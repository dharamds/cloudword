  <?php $this->load->view("admin/layout/header_new"); ?>
  <?php $this->load->view("admin/layout/sidebar"); ?>
  <div class="container-fluid">
    <div class="row mr-0">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=base_url('admin/project') ?>"><?=$this->lang->line("projects") ?></a></li>
          <li class="breadcrumb-item"><a href="<?=base_url('admin/project/manage_backup/' . base64_encode($backup_data->project_id)) ?>"><?=$this->lang->line("backups") ?></a></li>
          <li class="breadcrumb-item active" aria-current="page"><?=$this->lang->line("restore_to_remote_server")?></li>
        </ol>
      </nav>
      <div id="cover-spin"></div>
      <div class="row">
        <div class="col-md-12">
          <div class="filter-container flex-row">
            <div class="flex-col-6">
              <h3 class="filter-content-title"><?=$this->encryption->decrypt($backup_data->caption) ?></h3>
            </div>
            <div class="flex-col-6 text-right"> <!-- <a class="btn btn-primary" href="http://devdemo.pro/cloud_world/admin/projects/create"> New Project </a> --> </div>
          </div>
        </div>
      </div>
      <div>        
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><?=$this->lang->line("restore_to_remote_server")?></a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="profile">
            <div data-widget-role="role1">
              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default panel-grid" style="visibility: visible;"> 
                    <div class="panel-body no-padding p-0">
                      <div id="memListTable_wrapper" class="form-inline no-footer">
                        <div style="margin-top:30px">
                          <form class="addftp" id="addftpform">
                          <div class="row">
                              <div class="col-sm-6">
                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title"><?=$this->lang->line("add_remote_server_ftp")?></h5>
                                    <input type="hidden" name="project_id" id="project_id" value="<?=$backup_data->project_id?>">
                                    <input type="hidden" name="backup_id" id="backup_id" value="<?=$backup_data->backup_id?>">
                                    <div class="row">
                                        <label for="f_caption" class="col-lg-2 col-form-label"><?=$this->lang->line("caption") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-10">
                                          <input type="text" class="form-control" id="f_caption" name="caption" placeholder="<?=$this->lang->line("caption") ?>" required>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <label for="f_protocol_type" class="col-lg-2 col-form-label"><?=$this->lang->line("protocol_type") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-6">
                                          <select class="form-control" id="f_protocol_type" name="protocol_type" onchange="addport(this.value)">
                                            <option value=""><?=$this->lang->line("select_protocol") ?></option>
                                            <option value="ftp">FTP</option>
                                            <option value="sftp">SFTP</option>
                                          </select>
                                        </div>
                                        <label for="f_port_no" class="col-lg-2 col-form-label"><?=$this->lang->line("port_no") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-2">
                                          <input type="text" class="form-control" name="port_no" id="f_port_no" placeholder="<?=$this->lang->line("port_no") ?>" required>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <label for="f_hostname" class="col-lg-2 col-form-label"><?=$this->lang->line("hostname") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-10">
                                          <input type="text" class="form-control" name="hostname" id="f_hostname" placeholder="<?=$this->lang->line("hostname") ?>" required>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <label for="f_username" class="col-lg-2 col-form-label"><?=$this->lang->line("username") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-10">
                                          <input type="text" class="form-control" name="username" id="f_username" placeholder="<?=$this->lang->line("username") ?>" required>
                                        </div>
                                      </div>

                                      <div class="row">
                                        <label for="f_password" class="col-lg-2 col-form-label"><?=$this->lang->line("password") ?><span class="text-danger">*</span></label>
                                        <div class="col-lg-10">
                                          <input type="password" class="form-control" name="password" id="f_password" placeholder="******" required>
                                        </div>
                                      </div>
                                      <div class="row">
                                        <div class="col-lg-12 text-right">
                                          <button type="button" class="btn btn-primary" id="fetch_ftp_data" onclick="fetchftp_data()"><?=$this->lang->line("fetch_ftp_data") ?></button>
                                        </div>
                                      </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title"> <?=$this->lang->line("folder_data")?></h5>
                                    <span id="folder_error" style="color: red"></span>
                                    <table class="table table-bordered table-striped table-hover datatable dataTable no-footer">
                                      <thead>
                                        <th>
                                        <?=$this->lang->line("choose_any_one_folder")?>
                                        </th>
                                        <th>
                                        <?=$this->lang->line("folder_name")?>
                                        </th>
                                      </thead>
                                      <tbody id="ftp_remote_server_data">
                                        
                                      </tbody>
                                    </table>
                                  </div>
                                </div>
                              </div>
                          </div>
                        
                                <div class="row">
                            <div class="col-lg-9 text-right">
                              <a href="<?=base_url('admin/project/manage_backup/'.$backup_data->project_id) ?>" class="btn btn-secondary" style="display: none;"><?=$this->lang->line("cancel") ?></a>
                              <button type="submit" id="restore_to_remote_ftp"  class="btn btn-primary" style="display: none;"><?=$this->lang->line("restore_to_remote_ftp") ?></button>
                            </div>
                          
                          </div>  
                          </form>
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
  <script type="text/javascript">
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip({container: 'body'})
         $('#backuplist').DataTable( {
              "language": {
                  "url": "<?php echo $this->lang->line("language_file") ?>"
              },
      });
   });
    function fetchftp_data(){
                $.ajax({
                   url:"<?php echo base_url();?>admin/project/fetch_remote_server_data",
                   type:"post",
                   beforeSend:function(){
                      $("#cover-spin").show()
                   },
                    data:$("#addftpform").serialize(),
                    dataType: 'json',
                    success:function(data){
                      if(data.status == "success"){
                          var tbodyhtml = "";
                          for(var i = 0;i<data.folderdata.length;i++) {
                                 tbodyhtml += '<tr>';
                                 tbodyhtml += '<td><input type="radio" name="remote_path" value="'+data.folderdata[i].folder_name+'"></td>';
                                 tbodyhtml += '<td>'+data.folderdata[i].folder_name+'</td>';

                                 tbodyhtml += '</tr>';     
                              }
                              $("#f_caption").attr("readonly",true);
                              $("#f_protocol_type").attr("readonly",true);
                              $("#f_port_no").attr("readonly",true);
                              $("#f_hostname").attr("readonly",true);
                              $("#f_username").attr("readonly",true);
                              $("#f_password").attr("readonly",true);
                              $("#fetch_ftp_data").attr("disabled",true);
                              $("#ftp_remote_server_data").html(tbodyhtml); 
                              $("#restore_to_remote_ftp").show()
                              $("#restore_to_remote_ftp_cancel").show()
                                  
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

  $("#addftpform").on('submit', function(e){
        e.preventDefault();
        var check = true;
        $("input:radio").each(function(){
            var name = $(this).attr("name");
            if($("input:radio[name="+name+"]:checked").length == 0){
                check = false;
            }
        });
        if(check){
          $("#folder_error").html("");
                  $.ajax({
                       url:"<?php echo base_url();?>admin/project/putftprestorecron_server",
                       type:"post",
                       beforeSend:function(){
                          $("#cover-spin").show()
                       },
                        data: new FormData(this),
                        dataType: 'json',
                        contentType: false,
                        cache: false,
                        processData:false,
                                success:function(data){
                                    if(data.status == "success"){
                                        swal(data.msg, {
                            title: "<?= $this->lang->line("great") ?>",
                            type: "success",
                            timer: 3000
                          }).then(() => {
                            location.reload();
                          })
                                    }else{
                                      $("#passvererrormsg").html(data.msg);
                                      swal(data.msg, {
                            title: "<?= $this->lang->line("oops") ?>",
                            type: "error",
                            timer: 3000
                          })
                                    }
                                    $("#cover-spin").hide()     
                                 }
                            });
                  }else{
                      $("#folder_error").html("<?= $this->lang->line("please_select_folder")?>");
                  }
              });

  function addport(vv){
    if(vv !=""){
      if(vv == "ftp"){
        $("#f_port_no").val(21);
      }else if(vv == "sftp"){
        $("#f_port_no").val(22);
      }
    }
  }

  </script>
   
  <?php $this->load->view("admin/layout/footer_new"); ?>
