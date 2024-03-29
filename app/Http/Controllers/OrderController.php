<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function CheckVersion(){
        //ip是否来自共享互联网
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
//ip是否来自代理
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
//ip是否来自远程地址
        else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
//        $res = DB::table('ip_popular')->where('ip','=',$ip_address)->find();
        $res = DB::selectOne('select * from ip_popular  WHERE ip = ? ', [$ip_address]);
        $data['ip'] = $ip_address;
        if(empty($res)){
            DB::table("ip_popular")->insertGetId($data);
            $list["code"]=0;
            $list["msg"]="最新版本";
            $list["data"]='';
            return response()->json($list);
        }
        $ios_id = $res->ios_id;
        $android_id = $res->android_id;

//        $ios_version = DB::table('version')->where('type','=',2)->orderBy('id','desc')->get();
        $ios_version = DB::selectOne('select * from version  WHERE type = ? ', [2]);
        $android_version = DB::selectOne('select * from version  WHERE type = ? ', [1]);;
//        $android_version = DB::table('version')->where('type','=',1)->orderBy('id','desc')->get();

        if(!empty($ios_version) && !empty($android_version)){
            if($ios_id == $ios_version->id || $android_id == $android_version->id){
                $list["code"]=0;
                $list["msg"]="最新版本";
                $list["data"]='';
                return response()->json($list);
            }else{
                $list["code"]=200;
                $list["msg"]="新版本已经发布！请前去更新";
                $list["data"]='';
                return response()->json($list);
            }
        }
    }
}
