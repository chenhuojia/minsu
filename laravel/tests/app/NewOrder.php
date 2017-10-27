<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Dotenv\Validator;
class NewOrder extends Model
{   
     protected $table = 'new_order';
     protected $primaryKey = 'order_id';
     public $timestamps = false;
     public $validator = [
                'goods_price' => 'required',
                'shipping_price' => 'required|numeric',
                'order_price' => 'required|numeric',
                'discount_price' => 'required|numeric',
                'order_amount' => 'required|numeric',
                'team_num' => 'required|numeric',
                'pay_id' => 'required',
                'pay_type' => 'required',
                'buy_id' => 'required|numeric',
                'pay_price' => 'required|numeric',
                'order_code' => 'required',
                'order_from' => 'required',
            ];
     public function create($order){        
         $this->goods_price     =   $order['goods_price'];
         $this->shipping_price  =   $order['shipping_price'];
         $this->order_price     =   $order['order_price'];
         $this->discount_price  =   isset($order['discount_price'])?$order['discount_price']:0;
         $this->order_amount    =   isset($order['order_amount'])?$order['order_amount']:$order['order_price'];
         $this->team_num        =   $order['team_num'];
         $this->pay_id          =   $order['pay_id'];
         $this->pay_type        =   isset($order['pay_type'])?$order['pay_type']:0;
         $this->buy_id          =   isset($order['buy_id'])?$order['buy_id']:0;
         $this->pay_price       =   isset($order['pay_price'])?$order['pay_price']:$order['order_price'];
         $this->user_remark     =   isset($order['user_remark'])?$order['user_remark']:null;
         $this->order_code      =   $order['order_code'];
         $this->user_code       =   $order['user_code'];
         $this->order_from      =   $order['order_from'];
         $this->save();
         return $this->order_id;
     }
     
}
