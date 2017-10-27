<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Model\Postage;
class Postage extends Controller{
       
    /***
     * ewe等待打包
     * ***/
    public function ewe(){
        $web_id=request()->input('web',1);
        $condition = [
            ['order_team_goods.is_deliver','N']
        ];
        if ($web_id){
            $web=DB::table('web_site')->where('id',$web_id)->first();           
            if (preg_match('/.*www.(.*)/', $web->domain,$arr)){
                $domain =$arr[1];
            }elseif(preg_match('/.{7,8}(.*)/',$web->domain,$arr)){
                $domain =$arr[1];
            }
           if ($domain){
               $condition[]=['new.order_from', 'like' ,'%'.$domain.'%'];             
           }
        }
        $teams=DB::table('order_team_goods')
                ->leftJoin('storage_goods_define AS s','order_team_goods.goods_id','=','s.id')
                ->leftJoin('order_address AS n','order_team_goods.order_id','=','n.order_id')
                ->leftJoin('order_team AS t','order_team_goods.order_id','=','t.order_id')
                ->leftJoin('new_order AS new','order_team_goods.order_id','=','new.order_id')
                ->where($condition)
                ->select('order_team_goods.*','order_team_goods.id as oid','n.*','s.title','t.quantity as num','new.order_from')
                ->groupBy('order_team_goods.id')
                ->orderBy('order_team_goods.id','desc')
                ->paginate(10);
        foreach($teams as $v) {
            $v->ttqty = $v->quantity*$v->num;
            $t =(!empty($v->order_id)?$v->order_id:0).'-'.(!empty($v->goods_id)?$v->goods_id:0).'-'.(!empty($v->quantity)?$v->quantity:1).'-'.(!empty($v->title)?$v->title:'false').'-'.(!empty($v->user_code)?$v->user_code:'none').'-'.$v->id;  //订单ID--商品id--商品数量--商品标题---user_code--order_team_goods中的id
            $v->sender =$v->sender_name.$v->sender_phone.$v->sender_country.$v->sender_province.$v->sender_city.$v->sender_area.$v->sender_address; //发件人
            $v->consignee =$v->consignee_name.$v->consignee_phone.$v->consignee_country.$v->consignee_province.$v->consignee_city.$v->consignee_area.$v->consignee_address;//收件人
            $v->comm= base64_encode($v->sender.$v->consignee);
            $v->ut= $t;
        }
        $webdata=DB::select('select * from web_site where state = 1');
        return view('admin.ewe-postage-list',['teams'=>$teams,'webdata'=>$webdata,'web_id'=>$web_id]);
    }
    
    /***
     * 其他等待打包
     * ***/
    public function other(){
        $web_id=request()->input('web',1);
        $condition = [
            ['order_team_goods.is_deliver','N']
        ];
        if ($web_id){
            $web=DB::table('web_site')->where('id',$web_id)->first();
            if (preg_match('/.*www.(.*)/', $web->domain,$arr)){
                $domain =$arr[1];
            }elseif(preg_match('/.{7,8}(.*)/',$web->domain,$arr)){
                $domain =$arr[1];
            }
            if ($domain){
                $condition[]=['new.order_from', 'like' ,'%'.$domain.'%'];
            }
        }
        $teams=DB::table('order_team_goods')
            ->leftJoin('storage_goods_define AS s','order_team_goods.goods_id','=','s.id')
            ->leftJoin('order_address AS n','order_team_goods.order_id','=','n.order_id')
            ->leftJoin('order_team AS t','order_team_goods.order_id','=','t.order_id')
            ->leftJoin('new_order AS new','order_team_goods.order_id','=','new.order_id')
            ->where($condition)
            ->select('order_team_goods.*','order_team_goods.id as oid','n.*','s.title','t.quantity as num')
            ->groupBy('order_team_goods.id')
            ->orderBy('order_team_goods.id','desc')
            ->paginate(10);        
        foreach($teams as $v) {
            $v->ttqty = $v->quantity*$v->num;
            $t =(!empty($v->order_id)?$v->order_id:0).'-'.(!empty($v->goods_id)?$v->goods_id:0).'-'.(!empty($v->quantity)?$v->quantity:1).'-'.(!empty($v->title)?$v->title:'false').'-'.(!empty($v->user_code)?$v->user_code:'none').'-'.$v->id;  //订单ID--商品id--商品数量--商品标题---user_code--order_team_goods中的id
            $v->sender =$v->sender_name.$v->sender_phone.$v->sender_country.$v->sender_province.$v->sender_city.$v->sender_area.$v->sender_address; //发件人
            $v->consignee =$v->consignee_name.$v->consignee_phone.$v->consignee_country.$v->consignee_province.$v->consignee_city.$v->consignee_area.$v->consignee_address;//收件人
            $v->comm= base64_encode($v->sender.$v->consignee);
            $v->ut= $t;
        }
        $sql='select * from courier_company where state =1';
        $data=DB::select($sql);
        $webdata=DB::select('select * from web_site where state = 1');
        return view('admin.other-postage-list',['teams'=>$teams,'data'=>$data,'webdata'=>$webdata,'web_id'=>$web_id]);
    }
    
    /***
     * 新增其他打包包裹   
     * ***/
    public function addOther(){       
        $company_id=request()->input('company_id',0);
        $express_id=request()->input('express_id',0);
        $data=request()->input('postage_data');
        if (empty($data) || empty($company_id) || empty($express_id)){
            return redirect('other');
        }
        $courier_company=DB::table('courier_company')->where('id',$company_id)->first();
        if ($courier_company){
            $realdata=self::dataLogice($data,1);
            if (empty($realdata)){
                return redirect('other');
            }
            $realdata['type']='package';
            $realdata['express_id']      =   $company_id;
            $realdata['express_name']    =   $courier_company->name;
            $realdata['express_url']     =   $courier_company->domain;
            $realdata['boxNo']           =   'APS'.$_SERVER['REQUEST_TIME'].self::getRandomString(2);
            $realdata['ewe_order_id']    =   $express_id;
            $realdata['printurl']        =   $courier_company->domain;
            $realdata['IsEconomic']      =   null;
            self::addPostage($realdata);
        }
        
        return redirect('other');
    }
    
    /***
     * 新增Ewe打包包裹
     * ***/
    public function addEwe(){
        $type=request()->input('type',1);
        $data=request()->input('postage_data');        
        if (empty($data) || empty($type)){
            return redirect('ewe');
        }
        $realdata=self::dataLogice($data,$type);
        if (empty($realdata)){
            return redirect('ewe');
        }
        $result=self::eweData($realdata['address'],$realdata['send_goods'],$type);
        if ($result){
            $realdata['type']='EWE';
            $realdata['express_id']      =   0;
            $realdata['express_name']    =   'EWE';
            $realdata['express_url']     =   'https://id.ewe.com.au/oms/contacts/upload';
            $realdata['boxNo']           =   $result->Payload->BOXNO;
            $realdata['ewe_order_id']    =   $result->Payload->ORDERNO;
            $realdata['printurl']        =   $result->Payload->PrintURL;
            $realdata['IsEconomic']      =   ($type==1)?'false':'true';
            self::addPostage($realdata);
        }
        return redirect('ewe');        
    }
    
    
    /***
     * 打包模板
     * @return number[]|string[]|string[][]|string[][][]|number[][][]|NULL[][][]
     * ***/
    public function temp(){
        return [
            "address" => [
                "sender" => [
                    "Name" => "MR_chen",
                    "Phone" => "13413885166",
                    "Street" => "林和西路167号",
                    "City" => "广州市",
                    "State" => "广东省",
                    "Suburb" => "天河区",
                    "Country" => "中国大陆",
                    "Company" => "false",
                    "Postcode" => "526641",
                ],
                "receiver" => [
                    "Name" => "李斌",
                    "Phone" => "1371388511",
                    "Street" => "林和西路167号",
                    "City" => "广州市",
                    "State" => "广东省",
                    "Suburb" => "天河区",
                    "Country" => "中国大陆",
                    "Company" => "",
                    "Postcode" => "301980",
                ],
            ],
            "goods_num" => 2,
            "goods_id" =>[
                0 => "36",
                1 => "36",
            ],
            "order_id" =>[
                 0 =>[
                    "order_id"=> 4,
                    "user_code"=> "8ui",
                    "admin_remark"=> null,
                    "order_code"=>"Y5RqmGVpmGQ=",
                    "order_from"=>"aud",
                     "team_goods" => "9"
                    ],                
                1 =>[
                    "order_id"=> 4,
                    "user_code"=> "8ui",
                    "admin_remark"=> null,
                    "order_code"=>"Y5RqmGVpmGQ=",
                    "order_from"=>"aud",
                    "team_goods" => "8"
                ],
            ],
            "user_id" =>[
                0 => "8",
                1 => "8",
            ],
            "goods_name" =>[
                0 => "Coconut Detox 椰子水 750毫升",
                1 => "Coconut Detox 椰子水 750毫升",
            ],
            "goods" =>[
                0 => [
                    "id" => "4",
                    "title" => "Coconut Detox 椰子水 750毫升",
                ],
                1 =>[
                    "id" => "4",
                    "title" => "Coconut Detox 椰子水 750毫升",
                ],
            ],
            "send_goods" =>[
                0 =>[
                    "ItemName" => "Coconut Detox 椰子水 750毫升",
                    "Quantity" => 1,
                    "Barcode" => "36",
                ],
                1 =>[
                    "ItemName" => "Coconut Detox 椰子水 750毫升",
                    "Quantity" => 1,
                    "Barcode" => "36",
                ],
            ],
            "type" => "EWE",
            "express_id" => 0,
            "express_name" => "EWE",
            "express_url" => "https://id.ewe.com.au/oms/contacts/upload",
            "boxNo" => "APS1504233375CM",
            "ewe_order_id" => "EWED001184574794",
            "printurl" => "http://newomstest.ewe.com.au/eweApi/api/order/restPrintOrder?orderNo=TQL41wFDi5RJmgbib924BA%3D%3D",
            "IsEconomic" => "false",
        ];
    }
    
    /***
     * 添加新的包裹
     * @param unknown $data
     * ***/
    public function addPostage($data){       
        $postage=new Postage();
        //$a=json_decode(json_encode($data['order_id']),true);
        $insertId=$postage->create($data);
        dd($insertId);
        if($insertId){
           $d=self::sendPost($data['send_goods'],$data['order_id'],$data['user_id'],$insertId,$postage->unique_code);
           dd($d);
        }
       dd(1);
    }
    
    /***
     * 向订单域名发送包裹
     * @param unknown $send_goods
     * @param unknown $order_id
     * @param unknown $user_id
     * @param unknown $insertId
     * @param unknown $unique_code
     * @return unknown[]|NULL[]
     * ***/
    public static function sendPost($send_goods,$order_id,$user_id,$insertId,$unique_code){
        $sendTo=array();
        $id=null;
        $sql = 'UPDATE order_team_goods SET is_deliver = CASE id ';
        foreach ($send_goods as $k=> $v) {
            $third_order=DB::table('order_team_goods')->select('id','third_order_id')->where('id',$order_id[$k]['team_goods'])->first();
            $orderx[]=array('order_id'=>$third_order->third_order_id,'goods_id'=>$v['Barcode'],'goods_num'=>$v['Quantity']);
            $sql .= sprintf("WHEN %d THEN %s ", $third_order->id, "'Y'");
            $this_goods=DB::table('stock')->where('id',$v['Barcode'])->first();
            if ($this_goods->is_storage == 'false'){
                $stock_sql='update stock set real_stock = real_stock-'.$v['Quantity'].',frozen = frozen-'.$v['Quantity'].' where id ='.$this_goods->id;
            }else{
                if ($this_goods->frozen > $v['Quantity']){
                    if ($this_goods->real_stock > $v['Quantity']){
                        $stock_sql='update stock set real_stock = real_stock-'.$v['Quantity'].',frozen = frozen-'.$v['Quantity'].' where id ='.$this_goods->id;
                    }elseif($this_goods->real_stock == $v['Quantity']){
                        $stock_sql='update stock set real_stock = 0,frozen = frozen-'.$v['Quantity'].' where id ='.$this_goods->id;
                    }elseif($this_goods->real_stock < $v['Quantity']){
                        //$stock_sql='update stock set real_stock = 0,frozen = frozen-'.$id[2].' where id ='.$id[0];
                    }
                }
                
            }
           DB::update($stock_sql);
            if(isset($sendTo[$order_id[$k]['order_from']])){
                $sendTo[$order_id[$k]['order_from']]['info'][]=array(
                    'order_id'=>$order_id[$k]['order_code'],
                    'goods_id'=>$v['Barcode'],
                );
                $sendTo[$order_id[$k]['order_from']]['orders'][]=array('order_id'=>$third_order->third_order_id,'goods_id'=>$v['Barcode'],'goods_num'=>$v['Quantity'],'goods_name'=>$v['ItemName']);
            }else{
                $sendTo[$order_id[$k]['order_from']]['info'][]=array(
                    'order_id'=>$order_id[$k]['order_code'],
                    'goods_id'=>$v['Barcode'],
                );
                $sendTo[$order_id[$k]['order_from']]['orders'][]=array('order_id'=>$third_order->third_order_id,'goods_id'=>$v['Barcode'],'goods_num'=>$v['Quantity'],'goods_name'=>$v['ItemName']);
            }
            $id .=$third_order->id.','; 
        }      
       $sql .= 'END WHERE id IN ('.mb_substr($id,0,-1).')';
       DB::update($sql);
        if (!$ds=DB::select('select * from order_team_goods where is_deliver= ? and order_id = ?',['N',$order_id[0]['order_id']])){
            DB::update('update new_order set order_state=1 where order_id='.$order_id[0]['order_id']);
        }
        if (!empty($sendTo)){
            foreach ($sendTo as $k =>$v){
                $crpos=DB::table('postage')->where('id',$insertId)->first();
                $data=array(
                    'postage'=>json_decode(json_encode($crpos),true),
                    'order'=>$v['info'],
                    'unique_code'=>$unique_code,
                    'user_id'=>$user_id[0],
                    'order_info'=>$v['orders'],
                );               
                //$as=rsa_sign($k.'/api/postageApi.php', $data);
            }
        }
        return $sendTo;
    }
    
    /***
     * 发送ewe打包数据
     * @param unknown $address
     * @param unknown $send_goods
     * @param unknown $type
     * @return mixed|boolean
     * ***/
    public static function eweData($address,$send_goods,$type){
        $config=self::eweconfig();
        $realdata['USERNAME'] 		= $config['USERNAME'];																			// 这是正式的形成ewe的数组格式
        $realdata['APIPASSWORD'] 	= $config['APIPASSWORD'];
        $realdata['BoxNo'] 			= $config['ewe_box_prefix'].$_SERVER['REQUEST_TIME'].self::getRandomString(2);		// 加起来，不能超过15个字符。
        $realdata['TotalPackage'] 	= $config['TotalPackage'];																						// 我们发给ewe的包裹，每次就是一个。
        if($type==1){
            $realdata['IsEconomic'] 	= 'true';
        }else{
            $realdata['IsEconomic'] 	= 'false';
        }
        $realdata['Items'] 			= $send_goods;
        $realdata['Sender'] 		= $address['sender'];
         
        $realdata['Receiver'] 		= $address['receiver'];
        $realdata=self::jsonChinese($realdata);  
        $json_data = self::postData($config['url'], $realdata);
        $result = json_decode($json_data);	//快递单生成成功
        if ($result->Status == 0){
            return $result;
        }
        return false;
    }
    /***
     * posts数据处理
     * @param unknown $data
     * @param unknown $type
     * @return NULL[][]|string[][]
     * ***/
    public static function dataLogice($data,$type){
        $puints = array_filter(explode("||",strval($data))); //拆成每个库存商品
        $user_id=$orders_id=$goods_id=$send_goods=$goods_name=$good=array();
        $goods_num=null;
        foreach($puints as $k => $v){ //循环每个库存商品，得到其中的各个参数，我们要用这些参数组成给ewe api的 json 数据。            
            $goods=array_filter(explode("-",strval($v)));
            $send_goods[]=array(
                'ItemName'=>isset($goods[3])?$goods[3]:'',
                'Quantity'=>1,
                'Barcode'=>$goods[1],
            );
            $goods_num += $goods[2];
            $order=DB::table('new_order')->select('order_id','user_code','admin_remark','order_code','order_from')->where('order_id',$goods[0])->first();
            $order->team_goods =isset($goods[5])?$goods[5]:0;
            array_push($orders_id,json_decode(json_encode($order),true));
            array_push($goods_id,$goods[1]);
            array_push($goods_name,isset($goods[3])?$goods[3]:'');
            array_push($user_id,isset($goods[4])?$goods[4]:0);
            $good[]=array(
                'id'=>$goods[0],
                'title'=>isset($goods[3])?$goods[3]:'',
            );
            //$goods[0]订单ID $goods[1]商品id$goods[2]商品数量$goods[3]商品标题$goods[4]order_team_goods中的id
           // print_r($goods);
        }
        $ret['address']=self::order_address($goods[0]);
        $ret['goods_num']=$goods_num;
        $ret['goods_id']=$goods_id;
        $ret['order_id']=$orders_id;
        $ret['user_id'] = $user_id;
        $ret['goods_name']=$goods_name;
        $ret['goods']=$good;
        $ret['send_goods']=$send_goods;        
        return $ret;;
          
    }
    
    /***
     * ewe配置信息
     * @return string[]|number[]
     * ***/
    public static function eweconfig(){
        return array(
            'USERNAME'=>'API-TEST',
            'APIPASSWORD'=>'DIM875439GYT892130',
            'ewe_box_prefix'=>'APS',
            'TotalPackage'=>1,
            'url'=>'https://newomstest.ewe.com.au/eweApi/ewe/api/createOrder',
        );
       							// ewe包裹前缀
        //$url = 'https://newomstest.ewe.com.au/eweApi/ewe/api/createOrder';  // POST指向的链接
        
    }
    
    /***
     * 处理订单地址
     * @param unknown $order_id
     * @return NULL[][]|string[][]
     * ***/
    public static function order_address($order_id){
        $order_address=DB::table('order_address')->where('order_id',$order_id)->first();
        $city=array(
            '北京市','天津市','上海市','重庆市'
        );
        $sender=array(
            'Name'=>$order_address->sender_name,
            'Phone'=>$order_address->sender_phone,
            'Street'=>$order_address->sender_address,
            'City'=>$order_address->sender_city,
            'State'=>mb_substr($order_address->sender_province,-1)=='省'?$order_address->sender_province:$order_address->sender_province.'省',
            'Suburb'=>$order_address->sender_area,
            'Country'=>$order_address->sender_country,
            'Company'=>$order_address->sender_company,
            'Postcode'=>$order_address->sender_zipcode,
        );
        $consignee=array(
            'Name'=>$order_address->consignee_name,
            'Phone'=>$order_address->consignee_phone,
            'Street'=>$order_address->consignee_address,
            'City'=>mb_substr($order_address->consignee_city,-1)=='市'?$order_address->consignee_city:$order_address->consignee_city.'市',
            'State'=>mb_substr($order_address->consignee_province,-1)=='省'?$order_address->consignee_province:$order_address->consignee_province.'省',
            'Suburb'=>$order_address->consignee_area,
            'Country'=>$order_address->consignee_country,
            'Company'=>$order_address->consignee_company,
            'Postcode'=>$order_address->consignee_zipcode,
        );
        if (in_array($order_address->sender_city, $city)){
            $sender['City']='';
            $sender['State']=$order_address->sender_city;
        }
        if (in_array($order_address->consignee_city, $city)){
            $sender['City']='';
            $consignee['State']=$order_address->consignee_city;
        }
        return array('sender'=>$sender,'receiver'=>$consignee);
    }
    
    /***
     * 获取自定义字符
     * @param unknown $len
     * @param unknown $chars
     * @return string
     * ***/
    public static function getRandomString($len, $chars=null){
        if (is_null($chars)){
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }
    
    /***
     * curl post 方式
     * @param unknown $url
     * @param unknown $jdata
     * @return mixed
     * ***/
    public static function postData($url, $jdata) {						// curl 连接 ewe api
    	$header=array();
    	$header[]="Content-Type:application/javascript";
    	$header[]="Content-Length: " . strlen($jdata);       
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	// 跳过证书检查
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  	// 从证书中检查SSL加密算法是否存在
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, $jdata);
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    	$handles = curl_exec($ch);
    	curl_close($ch);
    	return $handles;
    }
    /**
     * 转换 Json 中文不乱码
     * @param unknown $array
     * @return string
     * ***/
    public static function jsonChinese($array) { 							
            self::arrayRecursive($array, 'urlencode', true);
            $json = json_encode($array);
            return urldecode($json);
    }
    /**
     * 转换 Json 中文不乱码
     * @param unknown $array
     * @return []
     * ***/
    public static function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
        static $recursive_counter = 0;
        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }
            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
        $recursive_counter--;
    }
}