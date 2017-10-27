<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Model\Postage;
class PostageList extends Controller{
    
    protected static $a=0;
    public function bindedList(){
        $where=[
            ['type','ewe',],
        ];
        $postage=new Postage();
        $data=Postage::list($where);
        $a=0;
       dd($data);
    }

    public function bin(){
        $a=['a'=>['a1'=>'aaaa','a2'=>'aaaaaaaaaa'],'b'=>['b1','b2'],'c'];       
        $res=self::getParam(['a','a1','c3'],$a);
        dd($res);
    }
    
    public static function getParam($arr1=[],$arr=[]){ 
        if (is_array($arr) && is_array($arr1)){    
           $count=count($arr1);
           if ($count > 1){
               $ret=isset($arr[$arr1[0]])?$arr[$arr1[0]]:$arr;
               array_splice($arr1,0,1);
               if (is_array($ret) && count($arr1) > 1 ){
                 return self::getParam($arr1,$ret);
               }else{
                   return $ret;
               } 
           }else {
               return $arr; 
           }
        }else{
            return $arr;
        } 
    }
    
    
}