<?php $this->load->view("admin/layout/header_new"); ?>
<?php $this->load->view("admin/layout/sidebar"); 

//$web_content = @file_get_contents($url);
?>
<style type="text/css">
  .loader2 {
  border: 5px solid #f3f3f3;
  border-radius: 50%;
  border-top: 5px solid #3a6cab;
  width: 40px;
  height: 40px;
  top: 31%;
  left: 50%;
  position: absolute;
  -webkit-animation: spin 1s linear infinite; / Safari /
  animation: spin 1s linear infinite;
}

/ Safari /
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<link rel="stylesheet" href="<?php echo base_url();?>public/public/assets/css/optimization.css">

<div class="container-fluid">
            <div class="row mr-0">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <!--     <li class="breadcrumb-item"><a href="#">Projects</a></li> -->
                  <li class="breadcrumb-item active" aria-current="page"><?= $this->lang->line("Check Website Performance")?> </li>
                </ol>
              </nav>
              <div class="row" id="tstst">
                <div class="col-md-12">
                  <div class="panel panel-default panel-grid optimization">
                    <div class="panel-body no-padding p-0">
                      <div class="row">
                        <div class="col-lg-4">
                          <div class="mainimage"> 
                          <img src="<?= base_url()?>public/public/front/img/csw.jpg" style="width: 100%">
<input type="hidden" name="page_req" id="page_req" value="0">
<input type="hidden" name="page_sz" id="page_sz" value="0">

                           </div>
                        </div>
                        <div class="col-lg-8">
                          <h1><?= $this->lang->line("Performance Report for:")?></h1>
                          <h2><?=$url?></h2>
                          <?= strtolower($performance["domain_data"]["scheme"]) == "https" ? "<p style='color:green'>Website is secured </p>" : "<p style='color:red'>Website is not secured </p" ?>
                          <div class="report-info">
                            <div class="report-info-item">
                              <label><?= $this->lang->line("Website Title:")?></label>
                              <div class="report-details-value"> <?= $performance["title_status"] == 1 ? $performance["title_content"] : "No title for this website" ; ?> </div>
                            </div>
                            <div class="report-info-item">
                              <label><?= $this->lang->line("Report generated:")?></label>
                              <div class="report-details-value"> <?= date("d-m-Y H:i:s") ?> </div>
                            </div>
                            <div class="report-info-item">
                              <label><?= $this->lang->line("Using:")?></label>
                              <div class="report-details-value"><?=$browser_info?> </div>
                            </div>
                            <div class="report-info-item">
                              <label><?= $this->lang->line("Total Page Request:")?></label>
                              <div class="report-details-value" id="total_page_request"><?=$performance["total_page_request"]?> </div>
                            </div>
                            <div class="report-info-item">
                              <label><?= $this->lang->line("Total Page Size:")?></label>
                              <div class="report-details-value" id="total_page_size"><?= $this->general->convert_size($performance["total_page_size"])?> </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <h4><?=$url?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i> </h4>
                      <div class="row">
                        <div class="col-lg-6">
                          <div class="box clear">
                            <div class="report-score">
                              <h5><?= $this->lang->line("Connect_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["connect_time"],2)."s"?></span>
                            </div>

                            <div class="report-score">
                              <h5><?= $this->lang->line("Name_Lookup_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["namelookup_time"],2)."s"?></span> </div>
                            <div class="report-score">
                              <h5><?= $this->lang->line("Pre_Transfer_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["pretransfer_time"],2)."s"?></span> </div>
                          </div>
                        </div>
                        <div class="col-lg-6">
                          <div class="box clear">
                            <div class="report-score">
                              <h5><?= $this->lang->line("Redirect_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["redirect_time"],2)."s"?></span> </div>
                            <div class="report-score">
                              <h5><?= $this->lang->line("Start_Transfer_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["starttransfer_time"],2)."s"?></span> </div>
                            <div class="report-score">
                              <h5><?= $this->lang->line("Total_Time")?> <i class="fa fa-question help-tooltip" data-toggle="tooltip" data-placement="top" title="Tooltip here"></i></h5>
                              <span class="report-score-value green"><?= round($performance["domain_data"]["total_time"],2)."s"?></span> </div>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <h5><?= $this->lang->line("All Links")?></h5>
                      <div class="row">
                        <div class="col-lg-12">
                          <div class="report-tabs">
                          <ul class="nav nav-tabs" role="tablist">
                                    
                                    <li role="presentation" class="active" onclick="get_htmllinks();"><a href="#html"   aria-controls="profile" role="tab" data-toggle="tab"><?= $this->lang->line("Alternate Links")?></a></li>
                                    <li role="presentation" onclick="get_csslinks()"><a href="#css"  aria-controls="messages" role="tab" data-toggle="tab"><?= $this->lang->line("CSS")?></a></li>
                                    <li role="presentation" onclick="get_jslinks()"><a href="#js"  aria-controls="settings" role="tab" data-toggle="tab"><?= $this->lang->line("JS")?></a></li>
                                     <li role="presentation" onclick="get_jslinks()"><a href="#media" onclick="get_imagelinks()" aria-controls="settings" role="tab" data-toggle="tab"><?= $this->lang->line("Media List")?></a></li>
                                  </ul>
                                  <!-- Tab panes -->
                                  <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="html" style="position: relative;">
                                      <div class="loader2" id="htmlbody_loader" style="display: none;"></div>
                                      <table class="table waterfall-table" >
                                        <thead>
                                          <tr>
                                            <th><?= $this->lang->line("url")?></th>
                                            <th><?= $this->lang->line("status")?></th>
                                            <th><?= $this->lang->line("domain_url")?></th>
                                            <th><?= $this->lang->line("size")?></th>
                                            <th><?= $this->lang->line("response_time")?></th>
                                          </tr>
                                        </thead>
                                        <tbody id="htmlbody">
                                          
                                        </tbody>
                                      </table>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="css" style="position: relative;">
                                      <div class="loader2" id="cssbody_loader" style="display: none;"></div>
                                      <table class="table waterfall-table">
                                        <thead>
                                          <tr>
                                            <th><?= $this->lang->line("url")?></th>
                                            <th><?= $this->lang->line("status")?></th>
                                            <th><?= $this->lang->line("domain_url")?></th>
                                            <th><?= $this->lang->line("size")?></th>
                                            <th><?= $this->lang->line("response_time")?></th>
                                          </tr>
                                        </thead>
                                        <tbody id="cssbody">
                                          
                                        </tbody>
                                      </table>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="js" style="position: relative;">
                                        <div class="loader2" id="jsbody_loader" style="display: none;"></div>
                                      <table class="table waterfall-table">
                                        <thead>
                                          <tr>
                                            <th><?= $this->lang->line("url")?></th>
                                            <th><?= $this->lang->line("status")?></th>
                                            <th><?= $this->lang->line("domain_url")?></th>
                                            <th><?= $this->lang->line("size")?></th>
                                            <th><?= $this->lang->line("response_time")?></th>
                                          </tr>
                                        </thead>
                                        <tbody id="jsbody">
                                          
                                        </tbody>
                                      </table>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="media" style="position: relative;">
                                        <div class="loader2" id="mediabody_loader" style="display: none;"></div>
                                      <table class="table waterfall-table">
                                        <thead>
                                          <tr>
                                            <th><?= $this->lang->line("url")?></th>
                                            <th><?= $this->lang->line("status")?></th>
                                            <th><?= $this->lang->line("domain_url")?></th>
                                            <th><?= $this->lang->line("size")?></th>
                                            <th><?= $this->lang->line("response_time")?></th>
                                          </tr>
                                        </thead>
                                        <tbody id="mediabody">
                                          
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                          </div>
                        </div>
                      </div>
                      <hr>  
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <script>
  $(function(){
  $(".fold-table tr.view").on("click", function(){
    $(this).toggleClass("open").next(".fold").toggleClass("open");
  });
});
</script> 
<script type="text/javascript">


  function get_htmllinks(){
            var url = "<?php echo $url;?>";
            $.ajax({
                   url:"<?php echo base_url(); ?>admin/webperformance/get_urls",
                   type:"post",
                   beforeSend:function(){
                      $("#htmlbody_loader").show()
                   },
                    data: {url:url},
                    dataType: 'json',
                    success:function(data){
                              var html_blody = '';
                              var page_size = parseInt($("#page_sz").val());
                              var page_req = parseInt($("#page_req").val());
                              var dtlen = data.altername_links.length;
                              if(dtlen > 0){
                                var cnt = 0;
                                
                                for(var i = 0;i<dtlen;i++){
                                  page_size = page_size + data.altername_links[i].size;
                                    html_blody += '<tr class="url_fetched" id="'+i+'" data-url="'+data.altername_links[i].url+'">';
                                    html_blody += '<td>'+data.altername_links[i].url+'</td>';
                                    html_blody += '<td id="alt_status_'+i+'">...</td>';
                                    html_blody += '<td>'+data.altername_links[i].domain+'</td>';
                                    html_blody += '<td id="alt_size_'+i+'">...</td>';
                                    html_blody += '<td id="alt_time_'+i+'">...</td>';  
                                    html_blody += '</tr>';
                                    cnt++;
                                }
                                var ss = page_req + cnt;
                                $("#page_req").val(ss);
                                $("#page_sz").val(page_size);
                                $("#total_page_size").val(formatFileSize(page_size));
                                $("#total_page_request").html(ss);



                              }else{
                                     html_blody += '<tr>';
                                     html_blody += '<td colspan="5"><?php echo $this->lang->line("no_record_found") ?></td>';
                                     html_blody += '</tr>';
                              }  
                                $("#htmlbody").html(html_blody);
                                $("#htmlbody_loader").hide();

                                $( "tr.url_fetched" ).each(function( index ) {

                      get_css_data($( this ).attr('id'), $( this ).attr('data-url'), 'alt_');    

                      console.log( index + ": " + $( this ).attr('id') );
                      });

                             }
                        });
  }
  function get_imagelinks(){
            var url = "<?php echo $url;?>";
            $.ajax({
                   url:"<?php echo base_url(); ?>admin/webperformance/get_images",
                   type:"post",
                   beforeSend:function(){
                      $("#mediabody_loader").show()
                   },
                    data: {url:url},
                    dataType: 'json',
                    success:function(data){
                                var html_blody = '';
                                var page_size = parseInt($("#page_sz").val());
                                var page_req = parseInt($("#page_req").val());
                                 var dtlen = data.media_list.length;
                              if(dtlen > 0){
                                var cnt = 0;
                                for(var i = 0;i<dtlen;i++){
                                  page_size = page_size + data.media_list[i].size;
                                    html_blody += '<tr class="image_fetched" id="'+i+'" data-url="'+data.media_list[i].url+'">';
                                    html_blody += '<td>'+data.media_list[i].url+'</td>';
                                    html_blody += '<td id="img_status_'+i+'">...</td>';
                                    html_blody += '<td>'+data.media_list[i].domain+'</td>';
                                    html_blody += '<td id="img_size_'+i+'">...</td>';
                                    html_blody += '<td id="img_time_'+i+'">...</td>';  
                                    html_blody += '</tr>';
                                    cnt++;
                                }
                                var ss = page_req + cnt;
                                $("#page_req").val(ss);
                                $("#page_sz").val(page_size);
                                $("#total_page_size").html(formatFileSize(page_size));
                                $("#total_page_request").html(ss)
                                }else{
                                     html_blody += '<tr>';
                                     html_blody += '<td colspan="5"><?php echo $this->lang->line("no_record_found") ?></td>';
                                     html_blody += '</tr>';
                                }  
                                $("#mediabody").html(html_blody);
                                $("#mediabody_loader").hide();

                                $( "tr.image_fetched" ).each(function( index ) {
                                  get_css_data($( this ).attr('id'), $( this ).attr('data-url'), 'img_');    
                            });

                             }
                        });
  }
  function get_jslinks(){
            var url = "<?php echo $url;?>";
            $.ajax({          
                   url:"<?php echo base_url(); ?>admin/webperformance/get_jss",
                   type:"post",
                   beforeSend:function(){
                      $("#jsbody_loader").show()
                   },
                    data: {url:url},
                    dataType: 'json',
                    success:function(data){
                                var html_blody = '';
                                var page_size = parseInt($("#page_sz").val());
                                var page_req = parseInt($("#page_req").val());
                                var dtlen = data.js_links.length;
                                if(dtlen > 0){
                                  var cnt = 0;
                                for(var i = 0;i<dtlen;i++){
                                    page_size = page_size + data.js_links[i].size;

                                    html_blody += '<tr class="js_fetched" id="'+i+'" data-url="'+data.js_links[i].url+'">';
                                    html_blody += '<td>'+data.js_links[i].url+'</td>';
                                    html_blody += '<td id="js_status_'+i+'">...</td>';
                                    html_blody += '<td>'+data.js_links[i].domain+'</td>';
                                    html_blody += '<td id="js_size_'+i+'">...</td>';
                                    html_blody += '<td id="js_time_'+i+'">...</td>';  
                                    html_blody += '</tr>';
                                    cnt++;
                                }
                                var ss = page_req + cnt;
                                $("#page_req").val(ss);
                                $("#page_sz").val(page_size);
                                $("#total_page_size").val(formatFileSize(page_size));
                                $("#total_page_request").html(ss)
                                }else{
                                     html_blody += '<tr>';
                                     html_blody += '<td colspan="5"><?php echo $this->lang->line("no_record_found") ?></td>';
                                     html_blody += '</tr>';
                                }  
                                $("#jsbody").html(html_blody);
                                $("#jsbody_loader").hide();

                                $( "tr.js_fetched" ).each(function( index ) {
                                  get_css_data($( this ).attr('id'), $( this ).attr('data-url'), 'js_');    
                            });
                             }
                        });
  }
  function get_csslinks(){
            var url = "<?php echo $url;?>";
            $.ajax({
                   url:"<?php echo base_url(); ?>admin/webperformance/get_css",
                   type:"post",
                   beforeSend:function(){
                      $("#cssbody_loader").show()
                   },
                    data: {url:url},
                    dataType: 'json',
                    success:function(data){
                                var html_blody = '';
                                var page_size = parseInt($("#page_sz").val());
                                var page_req = parseInt($("#page_req").val());
                                var dtlen = data.link_src.length;
                                if(dtlen > 0){
                                  var cnt = 0;
                                for(var i = 0;i<dtlen;i++){
                                  page_size = page_size + data.link_src[i].size;
                                    html_blody += '<tr class="css_fetched" id="'+i+'" data-url="'+data.link_src[i].url+'">';
                                    html_blody += '<td>'+data.link_src[i].url+'</td>';
                                    html_blody += '<td id="css_status_'+i+'">...</td>';
                                    html_blody += '<td>'+data.link_src[i].domain+'</td>';
                                    html_blody += '<td id="css_size_'+i+'">...</td>';
                                    html_blody += '<td id="css_time_'+i+'">...</td>';
                                    html_blody += '</tr>';
                                    cnt++;
                                }
                                var ss = page_req + cnt;
                                $("#page_req").val(ss);
                                $("#page_sz").val(page_size);
                                $("#total_page_size").val(formatFileSize(page_size));
                                $("#total_page_request").html(ss)
                                }else{
                                     html_blody += '<tr>';
                                     html_blody += '<td colspan="5"><?php echo $this->lang->line("no_record_found") ?></td>';
                                     html_blody += '</tr>';
                                }  
                                $("#cssbody").html(html_blody); 
                                $("#cssbody_loader").hide();

                                $( "tr.css_fetched" ).each(function( index ) {
                                  get_css_data($( this ).attr('id'), $( this ).attr('data-url'), 'css_');    
                            });

                             }
                        });
  }
  
  
function get_css_data(url_id, url, prefix= 'alt_'){

        $.ajax({
                      url:"<?php echo base_url(); ?>admin/webperformance/uri_responce_status",
                      type:"post",
                      beforeSend:function(){
                          //$("#cssbody_loader").show()
                            $('td#'+prefix+'st_'+url_id).html('...');
                            $('td#'+prefix+'size_'+url_id).html('...');
                            $('td#'+prefix+'time_'+url_id).html('...');
                      },
                        data: {url:url},
                        dataType: 'json',
                        success:function(data){
                          var time = data.total_time;
                            $('td#'+prefix+'status_'+url_id).html(data.status_code);
                            $('td#'+prefix+'size_'+url_id).html(formatFileSize(data.size));
                            $('td#'+prefix+'time_'+url_id).html(time.toFixed(2)+'s');
                          }
                  });
}

  $(document).ready(function(){
      get_htmllinks();


  });
 
  
function formatFileSize(bytes,decimalPoint) {
   if(bytes == 0) return '0 Bytes';
   var k = 1000,
       dm = decimalPoint || 2,
       sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
       i = Math.floor(Math.log(bytes) / Math.log(k));
   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}
</script>
<?php $this->load->view("admin/layout/footer_new"); ?>