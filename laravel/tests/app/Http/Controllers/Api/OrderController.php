<?php
namespace App\Http\Controllers\Api;
use App\Jobs\CreateOrder;
use Illuminate\Queue\Jobs\Job;
use App\Http\Controllers\CommonController;
class OrderController extends commonController{
    
    public function info(){
        //$post=array('id'=>1,'i'=>2);
       // return require_once (app_path().'/libs/rsa/Token.class.php');
       
        //ajaxReturn();
        //return 
       // return DB::select('select * from user');
    }   
    
    
    public function addOrder(){  
        $podcast=$_POST;
        $job=(new CreateOrder($podcast))->onQueue('order'); 
        echo $this->dispatch($job);
    }
    
    
    protected function teamp(){
      return  $data=Array
        (
            'order' => Array
            (
                'order_code' => 'Y5RqmGVpmGQ=',
                'user_code' => '8ui',
                'goods_price' => 0.03,
                'shipping_price' => 0.00,
                'order_price' => 0.03,
                'discount_price' => 0,
                'order_amount' =>0,
                'team_num' => 3,
                'pay_id' => 'go-20131072-3-ifbd',
                'pay_type' => 9,
                'buy_id' => 86,
                'pay_price' =>0,
                'user_remark' =>0,
                'order_address' => Array
                (
                    'order_id' => 20131072,
                    'sender_phone' => 15976816006,
                    'sender_name' => '联系人',
                    'sender_country' => '位置信息',
                    'sender_province' => '',
                    'sender_city' =>'' ,
                    'sender_area' => '',
                    'sender_zipcode' =>'',
                    'sender_address' => '',
                    'sender_company' => '',
                    'consignee_phone' => '1371388511',
                    'consignee_name' => '李斌',
                    'consignee_country' => '中国大陆',
                    'consignee_province' => '中国大陆',
                    'consignee_city' => '北京',
                    'consignee_area' =>'beij',
                    'consignee_address' => '中国大陆 天津市 北京,北城街道新花园小区230号1102室 高城区',
                    'consignee_zipcode' => '301980',
                    'consignee_company' => '',
                    ),
        
                'order_from' => 'au',
                ),
        
            'order_team' => Array
            (
                Array
                (
                    'title' =>' Swisse 叶绿素口服液 薄荷味 500ml',
                    'quantity' => 1,
                    'price' => 0.01,
                    'goods_id' => '17:1',
                    'order_id'=>1,
                    ),
        
                Array
                (
                    'title' => 'Fatblaster Coconut Detox 瘦身排毒椰子水 750毫升',
                    'quantity' => 2,
                    'price' => 0.01,
                    'goods_id' => '36:1',
                    'order_id'=>1,
                    )
        
                )
            );
    }
    
    
    public function sendsms(){     
       header('Content-Type: text/plain; charset=utf-8');
       include_once app_path(). '/libs/SendSms/api_demo/SmsDemo.php';
       $demo = new \SmsDemo(
           'LTAIQyqofKxMA7Gk',
           '90fshzpFTohLLRIo1iTL0uemoBj0wr'
           );   
       $mobile='13622742951';
       //$tempcode='SMS_86625167';
       $tempcode='SMS_86585160';
       //$tempcode='SMS_86595200';
       $week =date("N",time());
       $weekarray=array('一','二','三','四','五','六','日');
       $name='蔡家裕';
       /* for($i=0;$i<6;$i++){
           $asc=mt_rand(65,90);
          $name .=chr($asc);
       } */
       $old=strtotime('2011-08-30 20:00:00');       
       $now=strtotime('now');
       $bg=floor(($now-$old)/(3600*24));
       $today=time();
       $msg=Array('name'=>$name,'week'=>$weekarray[($week-1)],'number'=>mt_rand(1,99),'today'=>$bg);
       //$msg=array('number'=>mt_rand());
       $response = $demo->sendSms("独行侠",$tempcode,$mobile,$msg,"123"); 
       
       /* if ($response->Code == 'OK'){
           $response = $demo->queryDetails("13622742951","20170821",10,1);
       } */
       //$response = $demo->queryDetails("13622742951","20170821",10,1);
       print_r($response); 
    }
}