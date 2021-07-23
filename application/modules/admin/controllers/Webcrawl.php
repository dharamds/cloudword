<?php
class Webcrawl extends MX_Controller {
    function __construct() {
       parent::__construct();
        $this->start = "";
        $this->already_crawled = array();
        $this->crawling_data = array();
    }
    public function checksystemresponse($url) {
        $header_size = array();
        if ($url != '') {
            //$url = 'https://www.google.com';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $response = curl_exec($ch);
            $header_size = curl_getinfo($ch);
        }
        return $header_size;
    }
public function get_details($url){
    error_reporting(1);
    $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
    $context = stream_context_create($options);
    $doc = new DOMDocument();
    @$doc->loadHTML(@file_get_contents($url, false, $context));
    $title = $doc->getElementsByTagName("title");
    $title = $title->item(0)->nodeValue;
    $description = "";
    $keywords = "";
    $metas = $doc->getElementsByTagName("meta");
    for ($i = 0; $i < $metas->length; $i++) {
        $meta = $metas->item($i);
        if (strtolower($meta->getAttribute("name")) == "description")
            $description = $meta->getAttribute("content");
        if (strtolower($meta->getAttribute("name")) == "keywords")
            $keywords = $meta->getAttribute("content");
    }
    $imagesdata = $this->get_attributess($url,"images");
    $scriptlinks = $this->get_attributess($url,"scripts");
    $csslinks = $this->get_attributess($url,"csss");
    $systemresponsedata = $this->checksystemresponse($url);
    return array("title" => str_replace("\n", "", $title), "description" => str_replace("\n", "", $description),"keywords"=> str_replace("\n", "", $keywords),"url" => $url,"imagedata" => $imagesdata,"jsscripts" => $scriptlinks,"csslinks" =>$csslinks,"systemresponsedata"=>$systemresponsedata);

}
public function follow_links($url,$urldata=array()) {
    $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
    $context = stream_context_create($options);
    $doc = new DOMDocument();
    @$doc->loadHTML(@file_get_contents($url, false, $context));
    $linklist = $doc->getElementsByTagName("a");
    $imagelist = $doc->getElementsByTagName("img");
    foreach ($linklist as $link) {
        $l =  $link->getAttribute("href");
        if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
        } else if (substr($l, 0, 2) == "//") {
            $l = parse_url($url)["scheme"].":".$l;
        } else if (substr($l, 0, 2) == "./") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
        } else if (substr($l, 0, 1) == "#") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
        } else if (substr($l, 0, 3) == "../") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
        } else if (substr($l, 0, 11) == "javascript:") {
            continue;
        } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
        }
        if(!in_array($l, $this->already_crawled)){
                array_push($this->already_crawled, $l);
                $datatt = $this->get_details($l);
                array_push($this->crawling_data, $datatt);
                $this->follow_links($l);
        }
    }
     return $this->crawling_data;
}
public function get_attributess($url,$type){
    $options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));
    $context = stream_context_create($options);
    $doc = new DOMDocument();
    @$doc->loadHTML(@file_get_contents($url, false, $context)); 
    if($type == "images"){
        $tag = "img";
        $attr = "src";
    }else if($type == "scripts"){
        $tag = "script";
        $attr = "src";
    }else if($type == "csss"){
        $tag = "link";
        $attr = "href";
    }
    $linklist = $doc->getElementsByTagName($tag);
    $imagearray = array();
    foreach($linklist as $link) {
        $l =  $link->getAttribute($attr);
        if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
        } else if (substr($l, 0, 2) == "//") {
            $l = parse_url($url)["scheme"].":".$l;
        } else if (substr($l, 0, 2) == "./") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
        } else if (substr($l, 0, 1) == "#") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
        } else if (substr($l, 0, 3) == "../") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
        } else if (substr($l, 0, 11) == "javascript:") {
            continue;
        } else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
            $l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
        }
    array_push($imagearray,$l);
    }
    return $imagearray;
}
    public function check(){
    	echo phpinfo();

       // $aa = $this->follow_links("http://devdemo.pro/cloud_world/");
       // echo "<pre>";
       // print_r($aa);die();
    }

    public function  checkPageSpeed($url){    
  $ch = curl_init();    
  $timeout = 60;    
  curl_setopt($ch, CURLOPT_URL, $url);    
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);  
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);    
  $result = curl_exec($ch);    
  curl_close($ch);    
 return $result;    
}  
public function testopti(){
		$myKEY = "your_key";  
		$url = "http://kingsquote.com";  
		$url_req = 'https://www.googleapis.com/pagespeedonline/v1/runPagespeed?url='.$url.'&screenshot=true&key='.$myKEY;  
		$results = checkPageSpeed($url_req);  
		echo '<pre>';  
		print_r(json_decode($results,true));   
		echo '</pre>'; 
}

public function testlog(){


$myfile = fopen("newfile.txt", "a") or die("Unable to open file!");
$txt = "John Doe\n";
fwrite($myfile, $txt);


echo file_get_contents("newfile.txt");//
die();

$txt = "Jane Doe\n";
fwrite($myfile, $txt);



fclose($myfile);

 
}

}
