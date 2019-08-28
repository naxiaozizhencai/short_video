<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

class NotifyController
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    
    public function Notify(){
    var_dump($_POST);exit;
        //加载fastpay支付插件

        if (!function_exists('get_openid')) {
        require $_SERVER['DOCUMENT_ROOT'].'/fastpay/Fast_Cofig.php';
        }

        $sign=$_POST['sign_notify'];//获取签名2.07版,2.07以下请使用$sign=$_POST['sign'];
        $check_sign=notify_sign($_POST);
        if($sign!=$check_sign){
          exit("签名失效");
        //签名计算请查看怎么计算签名,或者下载我们的SDK查看
        }

        //更新数据库
        $data = $this->orderService->UpdateOrder($_POST);
        return response()->json($data);
    }
}
