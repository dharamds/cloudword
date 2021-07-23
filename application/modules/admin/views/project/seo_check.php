<?php $this->load->view("admin/layout/header_new"); ?>
<?php $this->load->view("admin/layout/sidebar"); ?>
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
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default panel-grid optimization">
      <div class="panel-body no-padding p-0">

                                <h4 align="center"><?= $this->lang->line("seo_analytics")?></h4>
                                <p><?= $this->lang->line("audit_msg")?></p>
                                <div class="table-responsive">
                                  <table class="fold-table">
                                    <thead>
                                      <tr>
                                        <th><?= $this->lang->line("impact")?></th>
                                        <th><?= $this->lang->line("audit")?></th>
                                        <th></th>
                                        <th></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr class="view">
                                        <td class="<?php
                                            if($seo_analytics["meta_status"] == 1){
                                                echo 'low';
                                                if($seo_analytics["meta_data"]["description_status"] == 1){
                                                    $desclen = strlen($seo_analytics["meta_data"]["description_content"]);
                                                      if($desclen >= 50 && $desclen <= 160){
                                                        $meta_stat = 1;
                                                        $meta_description =   $this->lang->line("web_length_msg");
                                                      }else{
                                                          $meta_description = $this->lang->line("Please update Meta Descrption length in between 50 - 160");
                                                          $meta_stat = 0;
                                                      }
                                                }else{
                                                    $meta_description = $this->lang->line("You have not mentioned Meta desciption");
                                                    $meta_stat = 0;
                                                }

                                                if($seo_analytics["meta_data"]["charset_status"] == 1){
                                                    $meta_charset = $this->lang->line("You have set Charset in Meta tag");
                                                    $meta_charstat = 1;
                                                  }else{
                                                    $meta_charset = $this->lang->line("You have not set Charset in Meta tag");
                                                    $meta_charstat = 0;
                                                  }

                                                  if($seo_analytics["meta_data"]["viewport_status"] == 1){
                                                    $meta_viewport = $this->lang->line("You have set Viewport in Meta tag");
                                                    $meta_view = 1;
                                                  }else{
                                                    $meta_viewport = $this->lang->line("You have not set Charset in Meta tag");
                                                    $meta_view = 0;
                                                  }
                                            }else{
                                              echo 'med';
                                              $meta_description = $this->lang->line("You have not mentioned Meta desciption");
                                              $meta_charset = $this->lang->line("You have not set Charset in Meta tag");
                                              $meta_viewport = $this->lang->line("You have not set Viewport in Meta tag");
                                              $meta_stat = 0;
                                              $meta_charstat = 0;
                                              $meta_view = 0;
                                            } 
                                             ?>"><div class="mobilehide"><?= $this->lang->line("Meta Tag")?></div></td>
                                        <td><a href="#"><?= $this->lang->line("Checking Meta tag Status")?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["meta_status"] == 1 ? $this->lang->line('Found') :  $this->lang->line('Not Found')?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><b>Meta</b> <?= $this->lang->line("tags are invisible tags that provide data about your page to search engines and website visitors. In short, they make it easier for search engines to determine what your content is about, and thus are vital for")?> <b>SEO</b></p>
                                            </div>
                                          </div></td>
                                      </tr>
                                      <tr class="view">
                                        <td class="<?= $meta_stat == 1 ? 'low' : 'med' ?>"><div class="mobilehide"><?= $this->lang->line("Meta Description")?></div></td>
                                        <td><a href="#"><?= $meta_description ?></a></td>
                                        <td><div class="mobilehide"><?= $meta_stat == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><?= $this->lang->line("It's best to keep meta descriptions long enough that they're sufficiently descriptive, so we recommend descriptions between 50–160 characters")?></p>
                                            </div>
                                          </div></td>
                                      </tr>
                                      <tr class="view">
                                        <td class="<?php
                                        $titlechkmsg = '';
                                        if($seo_analytics["title_status"] == 1){
                                            $titlelen = strlen($seo_analytics["title_content"]);
                                            if($titlelen >= 50 && $titlelen <= 70){
                                                echo 'low';
                                                $titlechkmsg = $this->lang->line("You have a perfect title length for SEO");
                                              }else{
                                                echo 'med';
                                                $titlechkmsg = $this->lang->line("Your title content must be between 50-70 charecters in length");
                                              }
                                          }else{
                                            echo 'med';
                                            $titlechkmsg = $this->lang->line("You haven't mentioned Title on your Website");
                                          }
                                         $seo_analytics["title_status"] == 1 ? 'low' : 'med' ?>"><div class="mobilehide">Title</div></td>
                                        <td><a href="#"><?= $titlechkmsg ?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["title_status"] == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><?= $this->lang->line("If you keep your titles under 70 characters, our research suggests that you can expect about 90% of your titles to display properly")?></p>
                                            </div>
                                          </div></td>
                                      </tr>

                                      <tr class="view">
                                        <td class="<?php echo $seo_analytics["crawler_status"] == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Check Crawl Ability")?></div></td>
                                        <td><a href="#"><?php echo $seo_analytics["crawler_status"] == 1 ? $this->lang->line('Your website is enabled for Crawling') :$this->lang->line('Your website is disabled for Crawling') ;?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["crawler_status"] == 1 ? 'Enabled' : 'Disabled' ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><?= $this->lang->line("Each search engine identifies itself with a different user-agent. You can set custom instructions for each of these in your robots.txt file. There are hundreds of user-agent")?></p>
                                            </div>
                                          </div></td>
                                      </tr>
                                      <?php
                                          if($seo_analytics["link_status"] == 1){
                                              if($seo_analytics["link_status"]["canonical_status"] == 1){
                                                $canonical_stat = 1;
                                                $canonical_desc = $this->lang->line("You have Canonical URL mentionaed"); 
                                              }else{
                                                $canonical_stat = 0;
                                                $canonical_desc = $this->lang->line("You don't have Canonical URL mentioned"); 
                                              }

                                               if($seo_analytics["link_status"]["favicon_status"] == 1){
                                                $favicon_stat = 1;
                                                $favicon_desc = $this->lang->line("You have set Favicon for Website"); 
                                              }else{
                                                $favicon_stat = 0;
                                                $favicon_desc = $this->lang->line("You don't have Favicon URL mentioned"); 
                                              }

                                               if($seo_analytics["link_status"]["apple_icon_status"] == 1){
                                                 $apple_stat = 0;
                                                 $apple_desc = $this->lang->line("You have set Apple touch icon URL mentioned"); 
                                              }else{
                                                 $apple_stat = 0;
                                                 $apple_desc = $this->lang->line("You don't have Apple Touch Icon mentioned"); 
                                              }
                                          }else{
                                              $canonical_stat = 0;
                                              $canonical_desc = $this->lang->line("You don't have Canonical URL mentioned"); 

                                              $favicon_stat = 0;
                                              $favicon_desc = $this->lang->line("You don't have Favicon URL mentioned");

                                              $apple_stat = 0;
                                              $apple_desc = $this->lang->line("You don't have Apple Touch Icon mentioned"); 


                                          }
                                      ?>




                                      <tr class="view">
                                        <td class="<?php echo $canonical_stat == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Canonical URL")?></div></td>
                                        <td><a href="#"><?php echo $canonical_desc; ?></a></td>
                                        <td><div class="mobilehide"><?= $canonical_stat == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><?= $this->lang->line("A canonical URL is the URL of the page that Google thinks is most representative from a set of duplicate pages on your site. For example, if you have URLs for the same page (for example: example.com? dress=1234 and example.com/dresses/1234 ), Google chooses one as canonical")?></p>
                                            </div>
                                          </div></td>
                                      </tr>

                                       <tr class="view">
                                        <td class="<?php echo $seo_analytics["language_status"] == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Language")?></div></td>
                                        <td><a href="#"><?= "Checking for Language Status"; ?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["language_status"] == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4"><div class="fold-content">
                                            <div class="buttonrow">
                                              <p><?= $this->lang->line('The lang attribute specifies the language of the elements content. Common examples are "en" for English, "es" for Spanish, "fr" for French and so on')?></p>
                                            </div>
                                          </div></td>
                                      </tr>

                                      <tr class="view">
                                        <td class="<?php echo $seo_analytics["alinkcount"] > 0 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Alternate Links")?></div></td>
                                        <td><a href="#"><?= "Checking Status of alternate links"; ?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["alinkcount"] > 0 ?  $seo_analytics["alinkcount"]." ".$this->lang->line("URL links Found") : $this->lang->line('No URL Found'); ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4" style="position: relative;">
                                          <div class="loader2" id="htmlbody_loader" style="display: none;"></div>
                                          <table class="table waterfall-table">
                                        <thead>
                                          <tr>
                                            <th><?= $this->lang->line("url")?></th>
                                            <th><?= $this->lang->line("status")?></th>
                                            <th><?= $this->lang->line("domain_url")?></th>
                                            <th><?= $this->lang->line("size")?></th>
                                            <th><?= $this->lang->line("time")."s"?></th>
                                          </tr>
                                        </thead>
                                        <tbody id="htmlbody">
                                          
                                        </tbody>
                                      </table>
                                    </td>
                                      </tr>


                                      <tr class="view">
                                        <td class="<?php echo $meta_charstat == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Charset details in Meta Tag")?></div></td>
                                        <td><a href="#"><?= $this->lang->line("Checking for Charset Details"); ?></a></td>
                                        <td><div class="mobilehide"><?= $meta_charstat == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("When used by the <b>'meta'</b> element, the charset attribute specifies the character encoding for the HTML document. When used by the '<b>script</b>' element, the charset attribute specifies the character encoding used in an external script file")?> </p>
                                          </div>
                                        </td>
                                      </tr>

                                      <tr class="view">
                                        <td class="<?php echo $seo_analytics["doctype_status"] == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Doctype Tag")?></div></td>
                                        <td><a href="#"><?= $this->lang->line("Checking for !Doctype tag element status"); ?></a></td>
                                        <td><div class="mobilehide"><?= $seo_analytics["doctype_status"] == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("Doctype stands for Document Type Declaration. It informs the web browser about the type and version of HTML used in building the web document. This helps the browser to handle and load it properly. While the HTML syntax for this statement is somewhat simple, you must note each version of HTML has its own rules.")?></p>
                                          </div>
                                        </td>
                                      </tr>


                                      <tr class="view">
                                        <td class="<?php echo $meta_view == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Viewport in Meta tag")?></div></td>
                                        <td><a href="#"><?= "Checking for Viewporrt in Meta tag"; ?></a></td>
                                        <td><div class="mobilehide"><?= $meta_view == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("The viewport is the user's visible area of a web page. It varies with the device - it will be smaller on a mobile phone than on a computer screen. This gives the browser instructions on how to control the page's dimensions and scaling.")?></p>
                                          </div>
                                        </td>
                                      </tr>


                                      <tr class="view">
                                        <td class="<?php echo $favicon_stat == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Icon Check of Website")?></div></td>
                                        <td><a href="#"><?= $favicon_desc; ?></a></td>
                                        <td><div class="mobilehide"><?= $favicon_stat == 1 ? $this->lang->line('Found') : $this->lang->line('Not Found') ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("A favicon is a small file containing the one or more icons which are used to represent the website or a blog")?></p>
                                          </div>
                                        </td>
                                      </tr>
                                     
                

                                      <tr class="view">
                                        <td class="<?php echo $apple_stat == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Apple Touch Icon")?></div></td>
                                        <td><a href="#"><?= $apple_desc ?></a></td>
                                        <td><div class="mobilehide"><?= $apple_stat == 1 ? 'Found' : 'Not Found' ?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("For web page icon on iPhone or iPad, use the Apple Touch Icon or apple-touch-icon. png file. This icon is used when someone adds your web page as a bookmark")?></p>
                                          </div>
                                        </td>
                                      </tr>

                                      <?php
                                        $jsss = $seo_analytics["jslinkcount"];
                                        if($jsss > 8){
                                          $scrptmsg = $this->lang->line("This may affect the load time negatively");
                                          $js_links_stat = 0;
                                        }else{
                                          $scrptmsg = $this->lang->line("Use of JS file within 8 is good for managing loading time");
                                          $js_links_stat = 1;
                                        }
                                      ?>

                                      <tr class="view">
                                        <td class="<?php echo $js_links_stat == 1 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("JS Scripts")?></div></td>
                                        <td><a href="#"><?= $scrptmsg ?></a></td>
                                        <td><div class="mobilehide"><?= $jsss.' JS scripts Found'?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("Make use of JS file at minimum count it is good for managing Website loading time")?></p>
                                               <div class="loader2" id="jsbody_loader" style="display: none;"></div>
                                              <table class="table waterfall-table">
                                                <thead>
                                                  <tr>
                                                    <th><?= $this->lang->line("url")?></th>
                                                    <th><?= $this->lang->line("status")?></th>
                                                    <th><?= $this->lang->line("domain_url")?></th>
                                                    <th><?= $this->lang->line("size")?></th>
                                                    <th><?= $this->lang->line("time")."s"?></th>
                                                  </tr>
                                                </thead>
                                                <tbody id="jsbody">
                                                 
                                                </tbody>
                                              </table>

                                          </div>
                                        </td>
                                      </tr>

                                      <?php
                                        $boldstrong = $seo_analytics["bold_tag_count"] + $seo_analytics["strong_tag_count"];
                                      ?>

                                      <tr class="view">
                                        <td class="<?php echo $boldstrong <= 15 ? 'low' : 'med' ;?>"><div class="mobilehide"><?= $this->lang->line("Bold Tags & Strong Tags")?></div></td>
                                        <td><a href="#"><?= $this->lang->line("The usage of strong and bold tags is perfect. We recommend the use of up to 15 tags for this page.") ?></a></td>
                                        <td><div class="mobilehide"><?= $boldstrong.' '.$this->lang->line('Bold & Strong Tags Found')?></div></td>
                                        <td></td>
                                      </tr>
                                      <tr class="fold">
                                        <td colspan="4">
                                          <div class="fold-content">
                                              <p><?= $this->lang->line("Make use of JS file at minimum count it is good for managing Website loading time")?></p>
                                          </div>
                                        </td>
                                      </tr>



                                    </tbody>
                                  </table>
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
  get_jslinks();
  get_htmllinks();
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
                                    html_blody += '<tr>';
                                    html_blody += '<td>'+data.js_links[i].url+'</td>';
                                    html_blody += '<td>'+data.js_links[i].status_code+'</td>';
                                    html_blody += '<td>'+data.js_links[i].domain+'</td>';
                                    html_blody += '<td>'+formatFileSize(data.js_links[i].size)+'</td>';
                                    html_blody += '<td>'+Math.round(data.js_links[i].total_time,2)+'s</td>';
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
                                 $("#jsbody_loader").hide()
                             }
                        });
  }
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
                                    html_blody += '<tr>';
                                    html_blody += '<td>'+data.altername_links[i].url+'</td>';
                                    html_blody += '<td>'+data.altername_links[i].status_code+'</td>';
                                    html_blody += '<td>'+data.altername_links[i].domain+'</td>';
                                    html_blody += '<td>'+formatFileSize(data.altername_links[i].size)+'</td>';
                                    html_blody += '<td>'+Math.round(data.altername_links[i].total_time,2)+'s</td>';
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
                                $("#htmlbody").html(html_blody);
                                 $("#htmlbody_loader").hide()
                             }
                        });
  }
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