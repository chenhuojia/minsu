<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Model\NewOrder;
class Index extends Controller{
    
    public function index(){
        return view('admin.index');
    }
    
    public function welcome(){
        return view('admin.welcome');
    }
    
    public function ges(){
       $order=new NewOrder();
       $data=$order->with('order_address','order_goods')->get();
       print_r($data);
       
    }
    
    
    public function order(){
        $order_state=intval(request()->get('order_state',0));
        $consignee_name=strval(request()->get('consignee_name',0));
        $sender_name=strval(request()->get('sender_name',0));
        $order_from=strval(request()->get('order_from',0));
        $where=null;
        if ($order_state){
            if ($order_state==1){
                $order_state=0;
            }elseif ($order_state==2){
                $order_state=1;
            }
            $where .=' o.order_state = '.$order_state;
        }
        if($consignee_name){
            if ($where){
                $where .=' and a.consignee_name = '."'{$consignee_name}'";
            }else
                $where .=' a.consignee_name = '."'{$consignee_name}'";
        }
        if ($sender_name){
            if ($where){
                $where .=' and a.sender_name = '."'{$sender_name}'";
            }else{
                $where .=' a.sender_name = '."'{$sender_name}'";
            }
        
        }
        if ($order_from){
            if ($where){
                $where .=' and o.order_from = '."'{$order_from}'";
            }else
                $where .=' o.order_from = '."'{$order_from}'";
        }                
        if ($where){
            $sql='select o.*,a.* from new_order o left join order_address a on o.order_id= a.order_id where '.$where.' order by o.order_id desc';           
        }else{
            $count=DB::select('select count(order_id) as cut from new_order ');
        }
        //list($pagesize, $offset, $pagestring) = pagestring($count['cut'], 20);
       
       $orders=DB::table('new_order') 
              ->leftJoin('order_address','new_order.order_id','=','order_address.order_id')
             // ->leftJonin('order_address','new_order.order_id','=','order_address.order_id')
              ->select('new_order.*','order_address.*')
              ->orderBy('new_order.order_id', 'desc')
              ->paginate(10);       
       if ($orders){           
            foreach ($orders as $v){
                $v->team=DB::select('select * from order_team where order_id='.$v->order_id);
            }
        }
        //dd($orders);
        return view('admin.article-list',['orders'=>$orders]);
    }
    
    public function view($id){
        $order = DB::select('select o.*,a.* from new_order o left join order_address a on o.order_id= a.order_id where o.order_id='.$id);
        if ($order){
            $team=DB::select('select * from order_team where order_id='.$id);
            $title=null;
            foreach ($team as $v){
                $title .=$v->title.'&nbsp;&nbsp;&nbsp;';
            }
            $order[0]->order_team=$title;
            $order[0]->team=$team;
        }
        return view('admin.article-add',['order'=>$order[0]]);
    }

}