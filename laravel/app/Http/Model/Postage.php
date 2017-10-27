<?php

namespace App\Http\Model;
use Illuminate\Database\Eloquent\Model;
class Postage extends Model
{   
     protected $table = 'postage';
     protected $primaryKey = 'id';
     public $timestamps = false;
     public function create($order){ 
         $order_id=null;         
         $this->type            =   $order['type'];
         $this->express_id      =   $order['express_id'];
         $this->express_name    =   $order['express_name'];
         $this->express_url     =   $order['express_url'];
         
         $this->package_time    =   isset($order['package_time'])?$order['package_time']:$_SERVER['REQUEST_TIME'];
         $this->BoxNo           =   $order['boxNo'];
         $this->ewe_order_id    =   $order['ewe_order_id'];
         $this->IsEconomic      =   isset($order['IsEconomic'])?$order['IsEconomic']:null;
         $this->fin_count       =   $order['goods_num'];
         $this->printurl        =   isset($order['printurl'])?$order['printurl']:$order['express_url'];
         foreach ($order['order_id'] as $v){
             $order_id .=$v['order_id'].'-';
             $this->from_url    =$v['order_from'];
         }  
         $this->order_id        =   mb_substr($order_id,0,-1);
         $this->user_id         =   $order['user_id'][0];
         $this->goods_define_id         =   implode('-', $order['goods_id']);
         $this->goods_define_title      =   implode(';', $order['goods_name']);;
         $this->goods_num               =   $order['goods_num'];
         $this->unique_code             =   md5(time().'YLT'.mt_rand());
         //$this->to_web             =   $order['to_url'];
         $this->save();
         return $this->id;
     }
     
     public static function list($where){
        $data=(new Postage())->where($where)->paginate(15);
        if ($data){
            foreach ($data as $v){
                $v->address=(new OrderAddress())->where('order_id',(explode('-', $v->order_id)[0]))->first();
            }
        }
        return $data;
     }
}
