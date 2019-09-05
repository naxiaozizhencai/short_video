<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Service\UsersService;
class LoginController extends BaseController
{

    private $UsersService;
    public function __construct(UsersService $UsersService)
    {
        $this->UsersService = $UsersService;
    }

    /**
     * 刷新token
     */
    public function RefreshToken()
    {
        $data = $this->UsersService->DoRefreshToken();
        return response()->json($data);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Login(Request $request)
    {
        $data = $this->UsersService->Login($request);
        return response()->json($data);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Register(Request $request)
    {
        $data = $this->UsersService->PhoneRegister($request);
        return response()->json($data);
    }

    /**
     * 手机登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function PhoneLogin(Request $request)
    {
        $data = $this->UsersService->PhoneLogin($request);
        return response()->json($data);
    }

    /**
     * 发送验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function SendCode(Request $request)
    {
        $data = $this->UsersService->SendCode($request);
        return response()->json($data);
    }

    /**
     * 注销
     * @return \Illuminate\Http\JsonResponse
     */
    public function Logout()
    {
        $data = $this->UsersService->Logout();
        return response()->json($data);
    }

    /**
     * 忘记密码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ForgetPassword(Request $request)
    {
        $data = $this->UsersService->ForgetPassword($request);
        return response()->json($data);
    }



}
