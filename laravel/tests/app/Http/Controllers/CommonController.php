<?php
namespace App\Http\Controllers;
use App\Events\RequestApi;
use Illuminate\Support\Facades\Event;
use Illuminate\Http\Request;
class CommonController extends Controller{
    
    public function __construct(Request $request){
        $data=$_POST;
        if (empty($data)){
            ajaxReturn(406,0,'请求内容不能为空');
        }
        Event::fire(new RequestApi($data));
    }
    
}