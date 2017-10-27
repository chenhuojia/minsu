<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class OrderAddress extends Model
{
     protected $table = 'order_address';
     protected $primaryKey = 'order_id';
     public $timestamps = false;
     public $validator1 = [
         'consignee_name' => 'required',
         'consignee_phone' => 'required|numeric',
         'consignee_country' => 'required',
         'consignee_province' => 'required',
         'consignee_city' => 'required',
         'consignee_area' => 'required',
         'consignee_address' => 'required',
     ];
     public $validator2 = [
         'sender_name' => 'required',
         'sender_phone' => 'required|numeric',
         'sender_country' => 'required',
         'sender_province' => 'required',
         'sender_city' => 'required',
         'sender_area' => 'required',
         'sender_address' => 'required',         
     ];
     public function create($order_id,$order_from,$address){
             $this->order_id            =   $order_id;
             $this->sender_phone        =   isset($address['sender_phone'])?$address['sender_phone']:null;
             $this->sender_name         =   isset($address['sender_name'])?$address['sender_name']:null;
             $this->sender_country      =   isset($address['sender_country'])?$address['sender_country']:null;
             $this->sender_province     =   isset($address['sender_province'])?$address['sender_province']:null;
             $this->sender_city         =   isset($address['sender_city'])?$address['sender_city']:null;
             $this->sender_area         =   isset($address['sender_area'])?$address['sender_area']:null;
             $this->sender_zipcode      =   isset($address['sender_zipcode'])?$address['sender_zipcode']:null;
             $this->sender_address      =   isset($address['sender_address'])?$address['sender_address']:null;
             $this->sender_company      =   isset($address['sender_company'])?$address['sender_company']:null;
             $this->consignee_phone     =   $address['consignee_phone'];
             $this->consignee_name      =   $address['consignee_name'];
             $this->consignee_country   =   $address['consignee_country'];
             $this->consignee_province  =   $address['consignee_province'];
             $this->consignee_city      =   isset($address['consignee_city'])?$address['consignee_city']:null;
             $this->consignee_area      =   isset($address['consignee_area'])?$address['consignee_area']:null;
             $this->consignee_zipcode   =   isset($address['consignee_zipcode'])?$address['consignee_zipcode']:null;
             $this->consignee_address   =   $address['consignee_address'];
             $this->consignee_company   =   isset($address['consignee_company'])?$address['consignee_company']:null;
         if($order_from != 'https://www.buyercamp.com.au'){
             $this->sender_phone    =   13413885166;
             $this->sender_name     =   'MR_chen';
             $this->sender_country  =   '中国大陆';
             $this->sender_province =    '广东省';
             $this->sender_city     =   '广州市';
             $this->sender_area     =   '天河区';
             $this->sender_zipcode  =   '526641';
             $this->sender_address  =   '林和西路167号';
             $this->sender_company  =   'false';
         }
         return $this->save();
     }
}
