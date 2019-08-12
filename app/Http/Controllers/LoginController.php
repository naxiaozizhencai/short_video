<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        exit;

        $result = app('db')->selectone("SELECT * FROM users WHERE uuid=?", array($uuid));
        //初始化用戶
        if(empty($result)){

        }




        $data = [
            'code'=>200,
            'data'=>[
                'video_data'=>[
                    'video_user_avatar'=>'',
                    'video_user_id'=>'',
                    'video_user_name'=>'',
                    'video_title'=>'',
                    'video_image'=>'',
                    'video_url'=>'',
                    'video_label'=>'',
                    'favorite_number'=>'',
                    'reply_number'=>'',
                ],
            ],
        ];


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
