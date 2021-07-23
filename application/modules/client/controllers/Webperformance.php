<?php

ini_set('maximum_input_vars',100000);
ini_set('max_execution_time', -1);
//ini_set('memory_limit', '5000G');
ini_set('display_errors', 'Off');
class Webperformance extends MX_Controller {
    function __construct() {
        parent::__construct();
        
        if($this->session->userdata("user_id") == "" ) {
            redirect(base_url() . "client/login");
        }

    }
    public function check($project_id = NULL){
        $project_id = base64_decode($project_id);
    	$user_id = $this->session->userdata("user_id");
        $prodata = $this->db->query("select p.* from project p where p.project_id = ".$project_id." ")->row();
        if(!empty($prodata)){
            $url = $this->encryption->decrypt($prodata->url);
            $data["performance"] = $this->fetch_per($url);
            $data["browser_info"] = $_SERVER['HTTP_USER_AGENT'];
            $data["project_data"] = $prodata;
            $data["url"] = $url;
            $data["page"] = "projects";
            
            $this->load->view("client/project/webperformance", $data);
        }else{
            redirect(base_url('client/project'));
        }
    }
    public function fetch_per($url){
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
        $metas = $doc->getElementsByTagName("meta");
        $title = $doc->getElementsByTagName("title");
        $link = $doc->getElementsByTagName("link");
        $html = $doc->getElementsByTagName("html");
        $script = $doc->getElementsByTagName("script");
        $a = $doc->getElementsByTagName("a");
        $b = $doc->getElementsByTagName("b");
        $strong = $doc->getElementsByTagName("strong");
        $img = $doc->getElementsByTagName("img");
        $seoanalytics = array();
        $seoanalytics["bold_tag_count"] = $b->length;
        $seoanalytics["strong_tag_count"] =$strong->length;
        $seoanalytics["jslinkcount"] =$script->length;
        $seoanalytics["alinkcount"] =$a->length;
        $doccont = @file_get_contents($url);
        $dcheck     = stripos($doccont,"!DOCTYPE");
        $getrobottxt = @file_get_contents($url."/Robots.txt");
        $totalpagerequest = 0;
        $totalpagesize = 0;
        if($link->length > 0){
            $seoanalytics["link_status"] = 1;
            $link_array = array("canonical_status" => 0,"canonical_url" => "","favicon_status" => 0,"favicon_url" => "","apple_icon_status" => 0,"apple_icon_url" => ""); 
            $link_srcs = array();
            for($l = 0; $l < $link->length; $l++) {
                $ll = $link->item($l);
                $uri = $ll->getAttribute("href");
                if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                } else if (substr($uri, 0, 2) == "//") {
                    $uri = parse_url($url)["scheme"].":".$uri;
                } else if (substr($uri, 0, 2) == "./") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                } else if (substr($uri, 0, 1) == "#") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                } else if (substr($uri, 0, 3) == "../") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                } else if (substr($uri, 0, 11) == "javascript:") {
                    continue;
                } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                }
                if(strtolower($ll->getAttribute("rel")) == "canonical"){
                    $link_array["canonical_status"] = 1;
                    $link_array["canonical_url"] = $uri;
                }
                $pos = explode(" ",strtolower($ll->getAttribute("rel")));
                if(in_array("icon", $pos)){
                    $link_array["favicon_status"] = 1;
                    $link_array["favicon_url"] = $uri;
                }

                if(in_array("apple-touch-icon", $pos)){
                    $link_array["apple_icon_status"] = 1;
                    $link_array["apple_icon_url"] = $uri;
                }

                //$hdddata = $this->checklinkresponse($uri);
                $sourceUrl = parse_url($uri);
                    $sourceUrl = $sourceUrl['host'];
                  //  $totalpagesize += $hdddata["size_download"];
                    $link_srcs[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);

            }
            if(count($link_array) == 0){
                    $link_array["canonical_status"] = 0;
                    $link_array["canonical_url"] = "";
            }

            $totalpagerequest += count($link_array); 
             $seoanalytics["link_data"] = $link_array;
             $seoanalytics["link_src"] = $link_srcs;
        }else{
            $seoanalytics["link_status"] = 0;
            $seoanalytics["link_data"] = array();
            $seoanalytics["link_src"] = array();
        }
        if(!empty($getrobottxt)){
            $chkrobo = strpos($getrobottxt, "crawler");
            $seoanalytics["robot_file_status"] = 1;
            if($chkrobo !== false){
            $seoanalytics["crawler_status"] = 1;
            }else{
                $seoanalytics["crawler_status"] = 0;
            }
        }else{
             $seoanalytics["robot_file_status"] = 0;
             $seoanalytics["crawler_status"] = 0;
        } 
        if($dcheck !== false){
            $seoanalytics["doctype_status"] = 1;
        }else{
            $seoanalytics["doctype_status"] = 0;
        }
        $html = $html->item(0);
        if(!empty($html)){
            $htmllang = $html->getAttribute("lang");
        }else{
            $htmllang = "";
        }
        $domain_data = $this->checksystemresponse($url);    
        $seoanalytics["domain_status"] = $domain_data["http_code"] == 200 ? 1 : 0;
        $seoanalytics["domain_data"] = $domain_data;
        $totalpagerequest = 0;
        $totalpagesize    = $domain_data['size_download'];
        //media list
       //language
        $seoanalytics["language_status"] = !empty($htmllang) ? 1 : 0 ;
        $seoanalytics["language_content"]= $htmllang;
        //meta tags
        if($metas->length > 0){
            $seoanalytics["meta_status"] = 1;
            $metaarray = array("description_status" => 0,"description_content" => "","description_length" => 0,"charset_status" => 0,"charset_content" => "","viewport_status" => 0,"viewport_content" => "");
            for($i = 0; $i < $metas->length; $i++) {
                $meta = $metas->item($i);
                if(strtolower($meta->getAttribute("name")) == "description"){
                    $description = $meta->getAttribute("content");
                    $metaarray["description_status"] = 1;
                    $metaarray["description_content"] = $description;
                    $metaarray["description_length"] = strlen($description);
                }
                if(strtolower($meta->getAttribute("charset")) != ""){
                    $keywords = $meta->getAttribute("charset");
                    $metaarray["charset_status"] = 1;
                    $metaarray["charset_content"] =  $keywords;
                }
                if (strtolower($meta->getAttribute("name")) == "viewport"){
                    $keywords = $meta->getAttribute("content");
                    $metaarray["viewport_status"] = 1;
                    $metaarray["viewport_content"] =  $keywords;
                }
            }
            $seoanalytics["meta_data"] = $metaarray;
        }else{
            $seoanalytics["meta_status"] = 0;
        }

        if($title->length > 0){
            $seoanalytics["title_status"] = 1;
            $seoanalytics["title_content"] = $title->item(0)->nodeValue;
            $seoanalytics["title_length"] = strlen($seoanalytics["title_content"]);
        }else{
            $seoanalytics["title_status"] = 0;
            $seoanalytics["title_content"] = "";
            $seoanalytics["title_length"] = 0;
        }    
         $seoanalytics["total_page_request"] = $totalpagerequest;
         $seoanalytics["total_page_size"] = $totalpagesize;

     return $seoanalytics;
    }

    public function checkdomain(){
        $d1="cloudserviceworld.com";
        $r1=checkdnsrr($d1, "MX"); 
        if($r1){
            echo "yes";
        }else{
            echo "no";
        }
    }
     public function checksystemresponse($url){
                                    $header_size = array();
                                    if ($url != '') {
                                        $url_data = parse_url($url);
                                        if(!isset($url_data["scheme"])){
                                            $url = 'http://'.$url;
                                        }
                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                                        curl_setopt($ch, CURLOPT_HEADER, 1);
                                        $response = curl_exec($ch);
                                        $header_size = curl_getinfo($ch);
                                        if($header_size["http_code"] == 0){
                                            $url_data = parse_url($url);
                                            if($url_data["scheme"] === "http://"){
                                                $ss = "https://".$url_data["host"];
                                                return $this->checksystemresponse($ss);
                                            }else if(strpos($url_data["host"], 'www.') !== false){
                                                
                                               $ss = "https://".str_replace("www.", "", $url_data["host"]);
                                               $url_data = parse_url($ss); 
                                               return $this->checksystemresponse($ss);
                                            }
                                        }
                                    }
                                    return $header_size;
                                }
                                public function checklinkresponse($url){
                                    $header_size = array();
                                        $ch = curl_init($url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                                        curl_setopt($ch, CURLOPT_HEADER, 1);
                                        $response = curl_exec($ch);
                                        $header_size = curl_getinfo($ch);
                                        return $header_size;
                                }
    public function seo_check($project_id = NULL){
        $project_id = base64_decode($project_id);
        $user_id = $this->session->userdata("user_id");
        $prodata = $this->db->query("select p.* from project p where p.project_id = ".$project_id." ")->row();
        if(!empty($prodata)){
            $url = $this->encryption->decrypt($prodata->url);
            $data["seo_analytics"] = $this->fetch_per($url);
            $data["browser_info"] = $_SERVER['HTTP_USER_AGENT'];
            $data["project_data"] = $prodata;
            $data["url"] = $url;
            $data["page"] = "projects";
            // echo "<pre>";
            // echo print_r($data);die();
            $this->load->view("client/project/seo_check", $data);
        }else{
            redirect(base_url('client/project'));
        }
    }

    public function testcapture(){
        $client = Client::getInstance();
        // $width  = 800;
        // $height = 600;
        // $top    = 0;
        // $left   = 0;
        // $filename = "filegf.png";
        // $client->getEngine()->setPath(FCPATH.'uploads');
        // $request = $client->getMessageFactory()->createCaptureRequest('https://www.google.com/', 'GET');

        // $request->setOutputFile(FCPATH.'uploads/'.$filename);

        // $request->setViewportSize($width, $height);
        // $request->setCaptureDimensions($width, $height, $top, $left);
        // $response = $client->getMessageFactory()->createResponse();
       
        // $client->send($request, $response);

        $request = $client->getMessageFactory()->createRequest('http://jonnyw.me', 'GET');

    /** 
     * @see JonnyW\PhantomJs\Http\Response 
     **/
    $response = $client->getMessageFactory()->createResponse();
    $client->getEngine()->setPath(FCPATH.'uploads');
    // Send the request
    $client->send($request, $response);
    
    if($response->getStatus() === 200) {

        // Dump the requested page content
        echo $response->getContent();
    }
        print_r($request);
    }


    public function fetch_seo_data(){
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
        $link = $doc->getElementsByTagName("link");
        $script = $doc->getElementsByTagName("script");
        $a   = $doc->getElementsByTagName("a");
        $img = $doc->getElementsByTagName("img");        
    }

    public function fetch_images(){
         
        //scripts
        

        // Anchor tag
        

         //links
        if($link->length > 0){
            $seoanalytics["link_status"] = 1;
            $link_array = array("canonical_status" => 0,"canonical_url" => "","favicon_status" => 0,"favicon_url" => "","apple_icon_status" => 0,"apple_icon_url" => ""); 
            $link_srcs = array();
            for($l = 0; $l < $link->length; $l++) {
                $ll = $link->item($l);
                $uri = $ll->getAttribute("href");

                if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                } else if (substr($uri, 0, 2) == "//") {
                    $uri = parse_url($url)["scheme"].":".$uri;
                } else if (substr($uri, 0, 2) == "./") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                } else if (substr($uri, 0, 1) == "#") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                } else if (substr($uri, 0, 3) == "../") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                } else if (substr($uri, 0, 11) == "javascript:") {
                    continue;
                } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                }
                if(strtolower($ll->getAttribute("rel")) == "canonical"){
                    $link_array["canonical_status"] = 1;
                    $link_array["canonical_url"] = $uri;
                }
                $pos = explode(" ",strtolower($ll->getAttribute("rel")));
                if(in_array("icon", $pos)){
                    $link_array["favicon_status"] = 1;
                    $link_array["favicon_url"] = $uri;
                }

                if(in_array("apple-touch-icon", $pos)){
                    $link_array["apple_icon_status"] = 1;
                    $link_array["apple_icon_url"] = $uri;
                }

               // $hdddata = $this->checklinkresponse($uri);
                $sourceUrl = parse_url($uri);
                    $sourceUrl = $sourceUrl['host'];
                    //$totalpagesize += $hdddata["size_download"];
                    $link_srcs[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);

            }
            if(count($link_array) == 0){
                    $link_array["canonical_status"] = 0;
                    $link_array["canonical_url"] = "";
            }

            $totalpagerequest += count($link_array); 
             $seoanalytics["link_data"] = $link_array;
             $seoanalytics["link_src"] = $link_srcs;
        }else{
            $seoanalytics["link_status"] = 0;
            $seoanalytics["link_data"] = array();
            $seoanalytics["link_src"] = array();
        }
    }


    public function get_urls(){
        ini_set('maximum_input_vars',100000);
        ini_set('max_execution_time', -1);
        $url = $this->input->post("url");
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
        $a = $doc->getElementsByTagName("a");
        $seoanalytics = array();
        $totalpagesize = 0;
        $totalpagerequest = 0;
        if($a->length > 0){
            $altername_links = array();
            for($an = 0; $an < $a->length; $an++) {
                $alink = $a->item($an);
                $uri = $alink->getAttribute("href");
                if($uri != ''){

                if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                } else if (substr($uri, 0, 2) == "//") {
                    $uri = parse_url($url)["scheme"].":".$uri;
                } else if (substr($uri, 0, 2) == "./") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                } else if (substr($uri, 0, 1) == "#") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                } else if (substr($uri, 0, 3) == "../") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                } else if (substr($uri, 0, 11) == "javascript:") {
                    continue;
                } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                }
                    //$hdddata = $this->checklinkresponse($uri);
                    $sourceUrl = parse_url($uri);
                    $sourceUrl = $sourceUrl['host'];
                   // $totalpagesize += $hdddata["size_download"];
                    $altername_links[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);
                    $totalpagerequest++;
                
                }
            }
            
              $seoanalytics["totalpagerequest"] = $totalpagerequest; 
              $seoanalytics["totalpagesize"] = $totalpagesize; 
              $seoanalytics["altername_links"]=  $altername_links;
        }else{
            $seoanalytics["altername_links"]= array();
            $seoanalytics["totalpagerequest"] = 0; 
            $seoanalytics["totalpagesize"] = 0; 
        }
        $seoanalytics["totalpagesize"]= $totalpagesize;
        echo json_encode($seoanalytics);
    }

    public function get_images(){
        ini_set('maximum_input_vars',100000);
		ini_set('max_execution_time', -1);
        $url = $this->input->post("url");
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
        $img = $doc->getElementsByTagName("img");
        $seoanalytics = array();
        $totalpagesize = 0;
        $totalpagerequest = 0;
        if($img->length > 0){
            $img_array = array();
            $img_count = 0;
            for($im = 0; $im < $img->length; $im++) {
                $imglink = $img->item($im);
               $uri = $imglink->getAttribute("src");
               if($uri!= ""){
                    if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                    } else if (substr($uri, 0, 2) == "//") {
                        $uri = parse_url($url)["scheme"].":".$uri;
                    } else if (substr($uri, 0, 2) == "./") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                    } else if (substr($uri, 0, 1) == "#") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                    } else if (substr($uri, 0, 3) == "../") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                    } else if (substr($uri, 0, 11) == "javascript:") {
                        continue;
                    } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                    }
                    if(!in_array($uri,$img_array)){
                   // $hdddata = $this->checklinkresponse($uri);
                    $img_count++;
                    $sourceUrl = parse_url($uri);
                    $sourceUrl = $sourceUrl['host'];
                   // $totalpagesize += $hdddata["size_download"];
                    $img_array[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);
                    $totalpagerequest++;
                }
                } 
            }
           
            $seoanalytics["media_list"] = $img_array;
            $seoanalytics["media_list_count"] = $img_count; 
            $seoanalytics["totalpagerequest"] = $totalpagerequest; 
        }else{
           $seoanalytics["media_list"] = array();
           $seoanalytics["media_list_count"] = 0; 
           $seoanalytics["totalpagerequest"] = 0; 
        }
        $seoanalytics["totalpagesize"]= $totalpagesize;
        echo json_encode($seoanalytics);

    }
    public function get_jss(){
        ini_set('maximum_input_vars',100000);
		ini_set('max_execution_time', -1);
        $url = $this->input->post("url");
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
         $script = $doc->getElementsByTagName("script");
        $seoanalytics = array();
        $totalpagesize = 0;
        $totalpagerequest = 0;
        if($script->length > 0){
            $js_links = array();
            $js_link_cnt = 0;
            for($js = 0; $js < $script->length; $js++) {
                $jlink = $script->item($js);
                $uri = $jlink->getAttribute("src");
                if($uri!= ""){
                    if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                    } else if (substr($uri, 0, 2) == "//") {
                        $uri = parse_url($url)["scheme"].":".$uri;
                    } else if (substr($uri, 0, 2) == "./") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                    } else if (substr($uri, 0, 1) == "#") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                    } else if (substr($uri, 0, 3) == "../") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                    } else if (substr($uri, 0, 11) == "javascript:") {
                        continue;
                    } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                        $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                    }
                    if(!in_array($uri,$js_links)){
	                   // $hdddata = $this->checklinkresponse($uri);
	                    $js_link_cnt++;
	                    $sourceUrl = parse_url($uri);
	                    $sourceUrl = $sourceUrl['host'];
	                  //  $totalpagesize += $hdddata["size_download"];
	                    $js_links[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);

	                    $totalpagerequest++;
                	}
                }  
            } 
           
            $seoanalytics["js_links"]= $js_links;
            $seoanalytics["js_script_counts"]= $js_link_cnt;
            $seoanalytics["totalpagerequest"] = $totalpagerequest;
             $seoanalytics["totalpagesize"] = $totalpagesize;
        }else{
            $seoanalytics["js_links"]= array();
             $seoanalytics["js_script_counts"]= 0;
             $seoanalytics["totalpagesize"] = $totalpagesize;
             $seoanalytics["totalpagerequest"] = $totalpagerequest;
        }
        echo json_encode($seoanalytics);
    }
    public function get_css(){
        ini_set('maximum_input_vars',100000);
        ini_set('max_execution_time', -1);
        $url = $this->input->post("url");
        $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
        $context = stream_context_create($options);
        $doc = new DOMDocument();
        @$doc->loadHTML(@file_get_contents($url, false, $context));
        $link = $doc->getElementsByTagName("link");
        $seoanalytics = array();
        $totalpagesize = 0;
        $totalpagerequest = 0;
        if($link->length > 0){
            $seoanalytics["link_status"] = 1;
            $link_array = array("canonical_status" => 0,"canonical_url" => "","favicon_status" => 0,"favicon_url" => "","apple_icon_status" => 0,"apple_icon_url" => ""); 
            $link_srcs = array();
            for($l = 0; $l < $link->length; $l++) {
                $ll = $link->item($l);
                $uri = $ll->getAttribute("href");
                if (substr($uri, 0, 1) == "/" && substr($uri, 0, 2) != "//") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].$uri;
                } else if (substr($uri, 0, 2) == "//") {
                    $uri = parse_url($url)["scheme"].":".$uri;
                } else if (substr($uri, 0, 2) == "./") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($uri, 1);
                } else if (substr($uri, 0, 1) == "#") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$uri;
                } else if (substr($uri, 0, 3) == "../") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                } else if (substr($uri, 0, 11) == "javascript:") {
                    continue;
                } else if (substr($uri, 0, 5) != "https" && substr($uri, 0, 4) != "http") {
                    $uri = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$uri;
                }
                
	              //  $hdddata = $this->checklinkresponse($uri);
	                $sourceUrl = parse_url($uri);
	                    $sourceUrl = $sourceUrl['host'];
	                 //   $totalpagesize += $hdddata["size_download"];
	                    $link_srcs[] = array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"],"domain" => $sourceUrl);
	                    $totalpagerequest++;
                
            }
             
            $seoanalytics["link_data"] = $link_array;
            $seoanalytics["link_src"] = $link_srcs;
            $seoanalytics["totalpagerequest"] = $totalpagerequest;
            $seoanalytics["totalpagesize"] = $totalpagesize;
        }else{
            $seoanalytics["link_status"] = 0;
            $seoanalytics["link_data"] = array();
            $seoanalytics["link_src"] = array();
            $seoanalytics["totalpagerequest"] = $totalpagerequest;
            $seoanalytics["totalpagesize"] = $totalpagesize;
        }
        echo json_encode($seoanalytics);
    }

    function uri_responce_status(){
        $uri = $this->input->post("url");
        if($uri){
         $hdddata = $this->checklinkresponse($uri);
         echo json_encode(array("url" => $uri,"size" => $hdddata["size_download"],"status_code" => $hdddata["http_code"],"total_time" => $hdddata["total_time"]));
        }
         exit;
    }
}