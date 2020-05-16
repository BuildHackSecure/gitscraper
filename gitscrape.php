<?php

class HttpResult {

    private $data;
    private $status;

    public function __construct($data, $status) {
        $this->data = $data;
        $this->status = $status;
    }

    public function getData(){
        return $this->data;
    }

    public function getJSON() {
        return json_decode($this->data, true);
    }

    public function getHttpStatus() {
        return $this->status;
    }


}

class Http {

    private $ch;
    private $headers = array();

    public function __construct($url){
        $this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function header($header){
        $this->headers[] = $header;
        return $this;
    }

    public function send(){
        if( $this->headers ){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 1);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        $response = curl_exec($this->ch);
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        return new HttpResult( $body, curl_getinfo($this->ch, CURLINFO_HTTP_CODE) );
    }
}

function writeIt($file,$arr){
    foreach( $arr as $a ){
        if( strlen($a) > 0 ) {
            file_put_contents('raw/'.$file, $a . "\n", FILE_APPEND);
        }
    }
}

function getparams($url){
    $contents = file_get_contents($url);
    preg_match_all("/_SERVER\\[[\"'](.*?)[\"']\\]/s",$contents,$headers);
    writeIt('header_vars.txt',$headers[1]);
    preg_match_all("/_GET\\[[\"'](.*?)[\"']\\]/s",$contents,$gets);
    writeIt('get_vars.txt',$gets[1]);
    preg_match_all("/_POST\\[[\"'](.*?)[\"']\\]/s",$contents,$posts);
    writeIt('post_vars.txt',$posts[1]);
    preg_match_all("/ function (.*?)\(/s",$contents,$methods);
    writeIt('method_vars.txt',$methods[1]);
    preg_match_all("/Route::get\\([\"'](.*?)[\"']/si",$contents,$lara_get);
    writeIt('laravel_get_url.txt',$lara_get[1]);
    preg_match_all("/Route::post\\([\"'](.*?)[\"']/si",$contents,$lara_post);
    writeIt('laravel_post_url.txt',$lara_post[1]);
    preg_match_all("/Route::post\\([\"'](.*?)[\"']/si",$contents,$lara_put);
    writeIt('laravel_put_url.txt',$lara_put[1]);
    preg_match_all("/Route::post\\([\"'](.*?)[\"']/si",$contents,$lara_delete);
    writeIt('laravel_delete_url.txt',$lara_delete[1]);
}
$since = file_get_contents('progress/since_id.txt');
$file_num = file_get_contents('progress/file_count.txt');
$repo_num = file_get_contents('progress/repo_count.txt');

if( count($argv) < 3 ) {
    die("Expecting GitHub Username as first argument and GitHub Personal Token as second argument"."\n");
}

$apikey = base64_encode($argv[1].':'.$argv[2]);

for($i=0;$i<10000;$i++){
    echo "Viewing Since: ".$since."\n";	
    file_put_contents('progress/since_id.txt',$since);
    $http = new Http('https://api.github.com/repositories?since='.$since);
    $res = $http->header('User-Agent: MyBrowser')->header('Authorization: Basic '.$apikey)->send();
    $json = $res->getJSON();
    if( $res->getHttpStatus() == 403 ){
        echo "Used all my credits, waiting for 5 mins\n";
        sleep(300);
    }
    if( count($json) > 10 ){
        foreach( $json as $item ){
        $since = $item["id"];
            $repo = $item["full_name"];
            $base_url = $item["url"];
            $http2 = new Http($item["url"].'/branches');
            $json2 = $http2->header('User-Agent: MyBrowser')->header('Authorization: Basic '.$apikey)->send()->getJSON();
            $sha = false;
            foreach( $json2 as $branch ){
                if( $branch["name"] == 'master' ){
                    $sha = $branch["commit"]["sha"];
                }
            }
            if( $sha ){
                $http3 = new Http($item["url"].'/git/trees/'.$sha.'?recursive=true');
                $json3 = $http3->header('User-Agent: MyBrowser')->header('Authorization: Basic '.$apikey)->send()->getJSON();
                $has_php = false;
                foreach( $json3["tree"] as $file ){
                    if( substr($file["path"],-4,4 ) == '.php' ){
                $has_php = true;
                $file_num = $file_num + 1;
                echo "Examaning File: ".$file_num."\n";
                file_put_contents('progress/file_count.txt',$file_num);
                        $file_url = 'https://raw.githubusercontent.com/'.$repo.'/master/'.$file["path"];
                        $file_sp = explode('/',$file["path"]);
                        foreach( $file_sp as $k=>$file_item ){
                            if( strlen($file_item) > 0 ) {
                                if ($k == (count($file_sp) - 1)) {
                                    file_put_contents('raw/files.txt', substr($file_item, 0, (strlen($file_item) - 4)) . "\n", FILE_APPEND);
                                } else {
                                    file_put_contents('raw/folders.txt', $file_item . "\n", FILE_APPEND);
                                }
                            }
                        }
                        getparams($file_url);
                    }
                }
            if( $has_php ){
            $repo_num = $repo_num + 1;
            echo "PHP Repo: ".$repo_num."\n";
                file_put_contents('progress/repo_count.txt',$repo_num);
                }
            }
        echo "Completed Repo: ".$repo."\n";
        }
    }	
}
