<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    //充值产品列表
    public function ProductList()
    {
        
        $data = $this->orderService->GetProductList();
        return response()->json($data);
    }

    /**
     * 提交订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function CreateOrder(Request $request)
    {
        $user_id = Auth::user()->id;
        $data = $this->orderService->SaveOrder($user_id,$request);
        return response()->json($data);

    }
    /**
     * 充值明细
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function PayDetails(Request $request)
    {
        $user_id = Auth::user()->id;
        $data = $this->orderService->GetOrderList($user_id);
        return response()->json($data);

    }

    public function Notify(){

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
