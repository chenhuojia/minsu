<?php

namespace App\Listeners;

use App\Events\RequestApi;
use Cache;
use Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class RequestApiListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RequestApi  $event
     * @return void
     */
    public function handle(RequestApi $event)
    {   $post = $event->post;
        if(!self::rsaDesign($post)){
            ajaxReturn(401,0,'请求身份验证不通过');
        }        
        /* Cache::put($key,$post,60*24*7);
        Log::info('保存文章到缓存成功！'); */
    }
    
    private function rsaDesign($data){
        $return=false;        
        if (is_array($data)){            
            if (isset($data['sign'])){
                require_once (app_path().'/libs/rsa/Token.class.php');
                $config=array(
                    'rsa_private_key_file'=>app_path().'/libs/rsa/rsa_private_key.pem',
                    'rsa_public_key_file'=>app_path().'/libs/rsa/rsa_public_key.pem',
                );
                $sign=$data['sign'];
                unset($data['sign']);
                $token=new \Token($config);
                $unsign=$token->get_public_key_sign($sign);
                $data=$token->arg_sort($data);
                $string=$token->getUrlQuery($data);
                if (strlen($string)>100){
                    $string=mb_substr($string,0,80);
                }
                if ($unsign==$string) $return = true;
            }
        }
        return $return;
    }
    
    private function rsaSign($url,$data){
        $string=null;
        $result=false;
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
        $data['sign']=$string;
        if ($string){
            $data['sign']=$string;        
            $result=json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        return $data;
    }
}
