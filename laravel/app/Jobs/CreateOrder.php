<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use App\Http\Model\NewOrder;
use App\OrderAddress;
class CreateOrder implements ShouldQueue
{
    use  InteractsWithQueue, Queueable, SerializesModels;
    protected $order;
    protected $orderAddress;
    protected $orderFrom;
    protected $userCode;
    protected $team;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($podcast)
    {
        $this->order=$podcast['order'];
        $this->orderAddress=$podcast['order']['order_address'];
        $this->orderFrom=$podcast['order']['order_from'];
        $this->orderCode=$podcast['order']['user_code'];
        $this->team=$podcast['order_team'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        $order_id=self::addOrder($this->order);
        $b=self::addOrderAddress($order_id, $this->orderFrom, $this->orderAddress);
        $c=self::addOrderTeam($order_id,$this->userCode,$this->team);
        if ($order_id && $b && $c){
            DB::commit();
            self::pushData($order_from,$this->team);
            return true;
        }
        DB::rollback();
        return false;
    }

    private function pushData($order_from,$data){
        $web=DB::select('select * from web_site where state=1 and domain != :domain',['domain' => $order_from]);
        foreach ($web as $v){
            rsa_sign($v->domain.'/manage/storage/stock.php',$data);
        }
        if (!$a=DB::select('select * from web_site where domain = :domain',['domain' => $order_from])){
            DB::insert('insert into web_sit (name, domain) values (?, ?)', [chin, $order_from]);
        }
    }
    
    private  function addOrderTeam($order_id,$user_code,$data){
        $goodsql='insert into `order_team_goods` (`id`, `order_id`,`user_code`,`goods_id`, `quantity`,`third_order_id`)  values ';
        $order_sql='insert into order_team values ';
        foreach ($data as $k =>$v){
            $order_idx=isset($v['order_id'])?$v['order_id']:0;
            $team_goods=explode(',', $v['goods_id']);
            foreach ($team_goods as $kk => $vv){
                $goods=explode(':',$vv);
                $total=($v['quantity']*$goods[1]);
                $sql='update stock set logic_stock=logic_stock-'.$total.',frozen=frozen+'.$total.' where id ='.$goods[0];
                DB::statement($sql);
                if ($total>1){
                    for($i=0;$i<$total;$i++){
                        $goodsql .='( null,'.$order_id.','."'{$user_code}'".','.$goods[0].',1,'.$order_idx.'),';
                    }
                }else{
                    $goodsql .='( null,'.$order_id.','."'{$user_code}'".','.$goods[0].','.$total.','.$order_idx.'),';
                }
            }
            $order_sql .= '(null,'.$order_id.','."'{$v['title']}'".','.$v['quantity'].','.$v['price'].','."'{$v['goods_id']}'".'),';
        }
        $order_sql=substr($order_sql,0,-1);
        $goodsql=substr($goodsql,0,-1);
        if (DB::statement($order_sql) && DB::statement($goodsql)){
            return true;
        }
        return false;
    }
    
    private  function addOrder($order){
        $orderobj=new NewOrder();
        $validator=validator($order,$orderobj->validator);
        if ($validator->fails()){
            $errors=$validator->errors();
            $str='';
            foreach ($errors->all() as $message){
                $str .=$message.'&nbsp;&nbsp;';
            }
            ajaxReturn(412,0,$str);
        }
        return $orderobj->create($order);
    }
    
    
    private  function addOrderAddress($order_id,$order_from,$address){
        $orderobj=new OrderAddress();
        $validator=validator($address,$orderobj->validator1);
        if ($validator->fails()){
            $errors=$validator->errors();
            $str='';
            foreach ($errors->all() as $message){
                $str .=$message.'&nbsp;&nbsp;';
            }
            ajaxReturn(412,0,$str);
        }
        if ($order_from=='au'){
            $validator=validator($address,$orderobj->validator2);
            if ($validator->fails()){
                $errors=$validator->errors();
                $str='';
                foreach ($errors->all() as $message){
                    $str .=$message.'&nbsp;&nbsp;';
                }
                ajaxReturn(412,0,$str);
            }
        }
        return $orderobj->create($order_id,$order_from,$address);
    }
    
   
}
