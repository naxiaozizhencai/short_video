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
//        $this->middleware('jwt.auth', ['except' => ['login']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Login(Request $request)
    {
        $uuid = $request->input("uuid");
        $data = $this->UsersService->Login($uuid);
        return response()->json($data);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function PhoneLoginOrRegister(Request $request)
    {
        $data = $this->UsersService->PhoneLoginOrRegister($request);
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function PhoneLogin(Request $request)
    {
        $data = $this->UsersService->PhoneLogin($request);
        return response()->json($data);
    }

    public function SendCode(Request $request)
    {
        $data = $this->UsersService->SendCode($request);
        return response()->json($data);
    }

    public function Logout()
    {
        $data = $this->UsersService->Logout();
        return response()->json($data);
    }

    public function ForgetPassword(Request $request)
    {
        $data = $this->UsersService->ForgetPassword($request);
        return response()->json($data);
    }



}
