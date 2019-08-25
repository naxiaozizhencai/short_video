<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class OrderRepositories
{

    protected $tempDataRepositories;
    public function __construct(TempDataRepositories $tempDataRepositories)
    {
        $this->tempDataRepositories = $tempDataRepositories;
    }

        /**
     *
     * @param $view_max_id
     * @return array
     */
    public function getProductList()
    {
        $product_data = DB::select('select * from video_product');
        if(empty($product_data)){
            return [];
        }

        return $product_data;
    }

    /**
     *
     * @param $view_max_id
     * @return array
     */
    public function getProductData($product_id)
    {
        $product_data = DB::selectOne('select * from video_product where id =?',[$product_id]);
        if(empty($product_data)){
            return [];
        }

        return $product_data;
    }

    public function InsertUserOrder($user_id,$order_type,$product_id,$order_price,$pay_type)
    {
        if(empty($user_id)){
            return [];
        }
        // 开始事务
        DB::beginTransaction();
        try {
            $order_sn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $userId = DB::table("user_order")->insertGetId(
            [
                'user_id'=>$user_id,
                'order_sn'=>$order_sn,
                'order_type'=>$order_type,
                'product_id'=>$product_id,
                'order_price'=>$order_price,
                'pay_type'=>$pay_type,
                'create_time'=>date('Y-m-d H:i:s'),
            ]
        );
            // 流程操作顺利则commit
            DB::commit();


            //加载fastpay支付插件
            if (!function_exists('get_openid')) {
                require $_SERVER['DOCUMENT_ROOT'].'/fastpay/Fast_Cofig.php';
            }

            $paydata=array();
            $paydata['uid']=$user_id;//支付用户id
            $paydata['order_no']=$order_sn;//订单号
            $paydata['total_fee']=$order_price;//金额
            $paydata['param']="";//其他参数
            $paydata['me_back_url']="";//支付成功后跳转
            $paydata['notify_url']=$_SERVER['HTTP_HOST']."/notify";//支付成功后异步回调
            $geturl=fastpay_order($paydata);//获取支付链接

            return $geturl;
        } catch (ModelNotFoundException $e) {
            // 抛出异常则rollBack
            DB::rollBack();
            return [];
        }
    }

    public function getOrderData($user_id)
    {
        $order_data = DB::select('select user_order.*, video_product.* from user_order left JOIN video_product ON user_order.product_id=video_product.id WHERE user_order.user_id = ? ', [$user_id]);
        if(empty($order_data)){
            return [];
        }

        return $order_data;
    }

    public function getOrderBySn($order_sn)
    {
        $order_data = DB::selectOne('select user_order.*, video_product.* from user_order left JOIN video_product ON user_order.product_id=video_product.id WHERE user_order.order_sn = ? ', [$order_sn]);
        if(empty($order_data)){
            return [];
        }

        return $order_data;
    }

    public function updateOrderBySn($order_sn)
    {
         return DB::table('user_order')->where('order_sn', '=', $order_sn)->update(['order_status'=>$1]);
    }

}