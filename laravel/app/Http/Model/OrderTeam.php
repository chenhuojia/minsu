<?php

namespace App\Http\Model;
use Illuminate\Database\Eloquent\Model;
use Dotenv\Validator;
class OrderTeam extends Model
{
     protected $table = 'order_team';
     protected $primaryKey = 'id';
     public $timestamps = false;
     public $validator = [
         'order_id' => 'required|numeric',
         'title' => 'required',
         'quantity' => 'required|numeric',
         'price' => 'required|numeric',
         'goods_id' => 'required',
     ];

     
     public function create($order_id,$title,$quantity,$price,$goods_id){
         $this->order_id = $order_id;
         $this->title    = $title;
         $this->quantity = $quantity;
         $this->price    = $price;
         $this->goods_id = $goods_id;
         return $this->save();
     }
   
}
