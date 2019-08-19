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
    public function getProductData($product_id)
    {
        $product_data = DB::selectOne('select * from video_product where id = ? ', [$product_id]);
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
                'create_time'=>time(),
            ]
        );
            // 流程操作顺利则commit
            DB::commit();
            return $userId;
        } catch (ModelNotFoundException $e) {
            // 抛出异常则rollBack
            DB::rollBack();
            return [];
        }
    }

    public function getOrderData()
    {
        $order_data = DB::select('select user_order.*, video_product.* from user_order left JOIN video_product ON user_order.product_id=video_product.id WHERE user_order.user_id = ? ', [$user_id]);
        if(empty($order_data)){
            return [];
        }

        return $order_data;
    }
    
    public function getProductData($user_id)
    {
        $product_data = DB::select('select * from video_product');
        if(empty($product_data)){
            return [];
        }

        return $product_data;
    }

}