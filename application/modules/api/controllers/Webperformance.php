<?php
class Webperformance extends MX_Controller {
    function __construct() {
        parent::__construct();
    }
    public function index(){
        $url = "https://datalogysoftware.com/";
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
        $seoanalytics["strong_tag_count"] = $strong->length;
        $doccont = @file_get_contents($url);
        $dcheck = strpos($doccont,"!DOCTYPE");
        if($dcheck !== false){
            $seoanalytics["doctype_status"] = 1;
        }else{
            $seoanalytics["doctype_status"] = 0;
        }
        $html = $html->item(0);
        $htmllang = $html->getAttribute("lang");
        $domain_data = $this->checksystemresponse($url);    
        $seoanalytics["domain_status"] = $domain_data["http_code"] == 200 ? 1 : 0;
        $seoanalytics["domain_data"] = $domain_data;
        //media list
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
                    $img_count++;
                    $img_array[] = $uri;
                } 
            }
            $seoanalytics["media_list"] = $img_array;
            $seoanalytics["media_list_count"] = $img_count; 
        }else{
           $seoanalytics["media_list"] = array();
           $seoanalytics["media_list_count"] = 0; 
        }

        //scripts
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
                    $js_link_cnt++;
                    $js_links[] = $uri;
                }
                
            } 
            $seoanalytics["js_links"]= $js_links;
            $seoanalytics["js_script_counts"]= $js_link_cnt;
        }else{
            $seoanalytics["js_links"]= array();
             $seoanalytics["js_script_counts"]= 0;
        }

        // Anchor tag
        if($a->length > 0){
            $altername_links = array();
            for($an = 0; $an < $a->length; $an++) {
                $alink = $a->item($an);
                $uri = $alink->getAttribute("href");
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
                $altername_links[] = $uri;
            } 
              $seoanalytics["altername_links"]= $altername_links;
        }else{
            $seoanalytics["altername_links"]= array();

        }
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
                if (strtolower($meta->getAttribute("charset")) != ""){
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





             $link_srcs[] = $uri;

            }
            if(count($link_array) == 0){
                    $link_array["canonical_status"] = 0;
                    $link_array["canonical_url"] = "";
            }
             $seoanalytics["link_data"] = $link_array;
             $seoanalytics["link_src"] = $link_srcs;
        }else{
            $seoanalytics["link_status"] = 0;
            $seoanalytics["link_data"] = array();
            $seoanalytics["link_src"] = array();
        }


        echo "<pre>";
        print_r($seoanalytics);die();
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
}