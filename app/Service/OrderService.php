<?php
namespace App\Service;
use App\Repositories\OrderRepositories;

class OrderService
{
    protected $orderRepositories;
    public function __construct(OrderRepositories $orderRepositories)
    {
        $this->orderRepositories = $orderRepositories;
    }

    /**
     *获取产品信息
     * @return array
     */
    public function GetProduct($product_id)
    {
        $result = $this->orderRepositories->getProductData($product_id);

        if(empty($result)){
            return $data = ['code'=>-1, 'errMsg'=>'数据不存在'];
        }
        return $result;
    }

     /**
     *保存订单
     * @return array
     */
    public function SaveOrder($user_id,$request)
    {
        $data =  ['code'=>-1, 'errMsg'=>'订单创建失败'];
        $order_type = 1;//vip充值
        $product_id = $request['product_id'];
        $product = $this->GetProduct($request['product_id']);
        $order_price = $product->product_price;
        $pay_type = $request->input('pay_type');
        $result = $this->orderRepositories->InsertUserOrder($user_id,$order_type,$product_id,$order_price,$pay_type);

        if(!empty($result)){
            $url = $result;
            // $url = $_SERVER['HTTP_HOST'].$result;
            return $data =['code'=>200, 'msg'=>'订单创建成功','url'=>$url];
        }
        return $data;
    }

    public function GetOrderList($user_id)
    {
        $result = $this->orderRepositories->getOrderData($user_id);

        if(empty($result)){
            return $data = ['code'=>-1, 'errMsg'=>'订单数据不存在'];
        }
        return $result;
    }


    public function GetProductList()
    {
        $result = $this->orderRepositories->getProductList();

        if(empty($result)){
            return $data = ['code'=>-1, 'errMsg'=>'数据不存在'];
        }
        return $data = ['code'=>200, 'data'=>$result];
    }



}