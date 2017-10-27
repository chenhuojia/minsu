<?php

class Token{
    
    const TOKEN_STR='access_token';
    private  $error = '';     // 错误信息
    protected static $user_id='';
    private $config =   array(
        'rsa_private_key_file'       =>  '',    // 私钥文件
        'rsa_public_key_file'        =>  '',    // 公钥文件
        'rsa_private_key_text'       =>  '',    // 私钥文本
        'rsa_public_key_text'        =>  '',    // 公钥文本
        'sign'                       =>  '',    // 生成的签名       
    );
    
    public function __get($name){
        if(isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
    
    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }
    
    public function __isset($name){
        return isset($this->config[$name]);
    }
    
    /**
     * 架构函数
     * @access public
     * @param array $config  上传参数
     */
    public function __construct($config=array()) {
        if(is_array($config)) {
            $this->config   =   array_merge($this->config,$config);
        }
    }
    
    /*
     * 解出token中的user_id
     * @param string $token
     * @return boolean|intval
     * ***/
    public static function get_token($token){
        if (empty($token)){
            $this->error='token不存在';
            return false;
        }
        $token=base64_decode($token);
        if ($token){
           $token=explode('/', $token);
           $time=$_SERVER['REQUEST_TIME'];
           if($time < $token[2]+(30*60*60*24) ){
               self::$user_id=$token[1];
               return $token[1];
           }else{
               $this->error='token已过期！';
               return false;
           } 
        }
        $this->error='token有误！';
        return false;
    }
    
    
    /*
     * 使用user_id生成token 
     * @param intval $user_id
     * @return string
     * ***/
    public static function set_token($user_id){
        $str='';
        for($i=0;$i<5;$i++){
            $asc=mt_rand(0,255);
            $str .=bin2hex(chr($asc));
        }
        $str=md5($str.self::TOKEN_STR);
        $time=$_SERVER['REQUEST_TIME'];
        $str =$str.'/'.$user_id.'/'.$time;
        return base64_encode($str);
    }
    
    /*
     * RSA私钥加密》》生成签名
     * @param string $data
     * @return boolean|string
     * ***/
    public  function set_private_key_sign($data){
        if (!$pi_key=self::check_private_key()){
            return $this->error='私钥不存在';
            return false;
        }  
        if ($pi_keys =  openssl_pkey_get_private($pi_key)){
            $encrypted='';
            openssl_private_encrypt($data,$encrypted,$pi_keys);           
            return base64_encode($encrypted);
        }else{
           
           return $this->error='私钥错误';
            return false;
        } 
    }
    
    /*
     * rsa解密公钥生成的签名
     * @param string $sign
     * @return boolean|string
     * ***/
    public static function get_private_key_sign($sign){
        if (!$pi_key=self::check_private_key()){
            $this->error='私钥不存在';
            return false;
        }
        if ($pi_key =  openssl_pkey_get_private($pi_key)){
            $decrypted='';
            openssl_private_decrypt(base64_decode($sign),$decrypted,$pi_key);
            return $decrypted;
        }else{
            $this->error='私钥错误';
            return false;
        }
    }
      
    /*
     * rsa公钥加密 》》生成的签名
     * @param string $data
     * @return boolean|string
     * ***/
    public static function set_public_key_sign($data){
        if (!$pu_key=self::check_public_key()){
            $this->error='公钥不存在';
            return false;
        }
        if ($pu_key =  openssl_pkey_get_private($pu_key)){
            $encrypted = "";
            openssl_public_encrypt($data,$encrypted,$pu_key);
            return base64_encode($encrypted);
        }else{
            $this->error='公钥错误';
            return false;
        }
    }
    
    /*
     * rsa公钥解密私钥生成的签名
     * @param string $sign
     * @return boolean|string
     * ***/
    public function get_public_key_sign($sign){
        if (!$pu_key=self::check_public_key()){
            $this->error='公钥不存在';
            return false;
        }
        $pu_key = openssl_pkey_get_public($pu_key);
        $decrypted='';
        openssl_public_decrypt(base64_decode($sign),$decrypted,$pu_key);
        return $decrypted;
    }
    

    /*
     * 检验RSA私钥是否有效
     * @return string
     * ***/
    private function check_private_key(){
        $private_key='';
        if ($this->rsa_private_key_file && empty($this->rsa_private_key_text)){
            $private_key = file_get_contents($this->rsa_private_key_file);
        }elseif ($this->rsa_private_key_text && empty($this->rsa_private_key_file)){
            $private_key=$this->rsa_private_key_text;
        }elseif ($this->rsa_private_key_text && $this->rsa_private_key_file){
            if ($d =file_get_contents($this->rsa_private_key_file)){
                $private_key=$d;
            }else{
                $private_key=$this->rsa_private_key_text;
            }
        }
        return $private_key;
    }
    
    /*
     * 检验RSA公钥是否有效
     * @return string
     * ***/
    private function check_public_key(){
        $public_key='';
        if ($this->rsa_public_key_file && empty($this->rsa_public_key_text)){
            $public_key = file_get_contents($this->rsa_public_key_file);
        }elseif ($this->rsa_public_key_text && empty($this->rsa_public_key_file)){
            $public_key =$this->rsa_public_key_text;
        }elseif ($this->rsa_public_key_text && $this->rsa_public_key_file){
            if ($d =file_get_contents($this->rsa_public_key_file)){
                $public_key=$d;
            }else{
                $public_key=$this->rsa_public_key_text;
            }
        }
        return $public_key;
    }
    
    
    /*
     * 拆分URL参数成数组
     * @param string $query
     * @return array
     * ***/
    public static function convertUrlQuery($query){
        if (!is_string($query)) return $query;
        //$query=parse_url($query);
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }
    
    /*
     * 数组变URL参数
     * @param array $query
     * @return string
     * ***/
    public static function getUrlQuery($array_query){
        if (!is_array($array_query)) return $array_query;
        $tmp = array();
        foreach($array_query as $k=>$param)
        {
            $tmp[] = $k.'='.$param;
        }
        $params = implode('&',$tmp);
        return $params;
    }
   
   /*
    * 排序
    * @param  array $array
    * @return array
    * ***/
   public static function arg_sort($array) {
        ksort($array);
        reset($array);
        return $array;
    }
    
    
}

