<?php
namespace App\Service;
use App\Repositories\OrderRepositories;
use App\Repositories\UsersRepositories;

class OrderService
{
    protected $orderRepositories;
    protected $usersRepositories;
    public function __construct(OrderRepositories $orderRepositories,UsersRepositories $usersRepositories)
    {
        $this->orderRepositories = $orderRepositories;
        $this->usersRepositories = $usersRepositories;
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
            $url = $_SERVER['HTTP_HOST'].$result;
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

    //更新订单状态
    public function UpdateOrder($value)
    {
//        $uid         = $value['uid'];//支付用户
//        $total_fee   = $value['total_fee'];//实际支付金额（可能会带增加0.01等）
//        $pay_title   = $value['pay_title'];//标题
//        $sign        = $value['sign'];//签名
//        $order_no    = $value['order_no'];//订单号
//        $me_pri      = $value['me_pri'];//订单的金额,参与签名
//        $me_param      = $value['me_param'];//其他参数
        
        $order = $this->orderRepositories->getOrderBySn('2019082834799');
        var_dump($order);exit;
        //更新订单状态
        $status = $this->orderRepositories->updateOrderBySn($order_no);
        //更新用户vip过期时间
//        $amount = $order['free_day']*86400;
        $user = $this->usersRepositories->UpdateVipTime(48,31536000);
        
        return $data = ['code'=>200, 'data'=>$user];
    }


}