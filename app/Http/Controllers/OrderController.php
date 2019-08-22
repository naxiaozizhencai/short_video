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
}
