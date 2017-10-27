<?php
namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class PostageController extends Controller{
    
    private $error='';    
    public function add($company_id){
        $sql='select * from courier_company where id = ?';
        if (!$company=DB::select($sql,[$company_id])){
            $this->error='创建包裹失败。';
            return false;
        }
        dd($company);
    }
    
    public function edit(){
    
    }
    
    public function del(){
    
    }
    
    
    public Static function check($type=1){    
        $posts =trim($_POST['self_postage_data']); //将从上一个页面post过来的checkbox选择的items放到该数组
        $boxno=trim($_POST['self_package']);
        $company_id=trim($_POST['company_id']);       
        if (empty($posts) || empty($boxno)){
            $this->error='创建包裹失败。';
            return false;
        }
        $sql='select * from courier_company where id = ?';
        if (!$company=DB::select($sql,[$company_id])){
             $this->error='创建包裹失败。';
            return false;
        }
        $puints = array_filter(explode("||",strval($posts))); //拆成每个库存商品
        foreach($puints as $key => $value){ //循环每个库存商品，得到其中的各个参数，我们要用这些参数组成给ewe api的 json 数据。
            $puint = array_filter(explode("|",strval(base64_decode($value)))); //得到每个商品中包含的值
            $goods=array_filter(explode("-",strval($puint[0])));
            //$goods[0]订单ID $goods[1]商品id$goods[2]商品数量$goods[3]商品标题$goods[4]order_team_goods中的id
            $goods_namee=DB::GetQueryResult('select * from storage_goods_define where id='.$goods[1]);
            $consignee[]=$puint[2];
            $sender[]=$puint[1];
            $user_ids[]=$puint[3];
            $send_goods=array(
                'ItemName'=>$goods_namee['title'],
                'Quantity'=>$goods[2],
                'Barcode'=>$goods[1],
            );
            $goods_num += $goods[2];
            $goods_id[]=$goods[1].':'.$goods[2].':'.$goods[0].':'.$goods[4]; //商品id---商品数量---订单ID--order_team_goods中的id
            $order_id[]=$goods[0];
            $goods_name .=$goods_namee['title'].';';
            $items[]=$send_goods;
        
        }
    }
    
    public Static function getRandomString($len, $chars=null){
        if (is_null($chars)){
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
}