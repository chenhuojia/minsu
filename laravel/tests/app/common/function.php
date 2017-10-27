<?php
function abc() {
    return "foo";
}

function ajaxReturn($code=200,$state=1,$msg='',$data='') {
    $httpStatus = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
        
    );
    $msg=($msg !='')?$msg:$httpStatus[$code];
    echo json_encode(array('success'=>$state,'code'=>$code,'httpmsg'=>$httpStatus[$code],'content'=>$msg,'data'=>$data),JSON_UNESCAPED_UNICODE);exit;
    //echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>$data),JSON_UNESCAPED_UNICODE);exit;
}

function rsa_sign($url,$order_from){
        $result=$string=null;
        $return=false;
        if (is_array($data)){
            require_once (app_path().'/libs/rsa/Token.class.php');
            $config=array(
                'rsa_private_key_file'=>app_path().'/libs/rsa/rsa_private_key.pem',
                'rsa_public_key_file'=>app_path().'/libs/rsa/rsa_public_key.pem',
            );
            $token=new \Token($config);
            $data=array_filter($data);
            $data=$token->arg_sort($data);
            $string=$token->getUrlQuery($data);
            if (strlen($string)>100){
                $string=mb_substr($string,0,80);
            }
            $string=$token->set_private_key_sign($string);
            if (empty($string)){
                return false;
            }
        }
        if ($string){
            $data['sign']=$string;            
            $result=json_encode($data,JSON_UNESCAPED_UNICODE);
            $return=http_curl($url,array('data'=>$result));
        }
        return $return;    
}

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function http_curl($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));

    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);

    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}
