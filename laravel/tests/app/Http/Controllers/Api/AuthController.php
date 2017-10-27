<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class AuthController extends Controller{
    
    public function index(){
        $request=new Request();
        $ip=$request->getClientIp();
        $ret=filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        dd($ret);
    }
}
