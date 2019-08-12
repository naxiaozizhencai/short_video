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
     * 用户登录
     * @param Request $request
     */
    public function Login(Request $request)
    {
        $uuid = $request->input("uuid");
        $data = $this->UsersService->Login($uuid);
        return response()->json($data);

    }

    /**
     * 用户登录
     * @param Request $request
     */
    public function PhoneLogin(Request $request)
    {

    }

    //
}
