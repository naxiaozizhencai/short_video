<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\UserModel;

class AuthController extends Controller
{
    private $salt = 'short video';

    public function postLogin(Request $request)
    {
        $user_model = new UserModel();
        $user_info = $user_model->where('uuid', '=', $request->input('uuid'))->first();
        if ($user_info) {
            if (!$token = Auth::login($user_info)) {
                $response['code']     = '5000';
                $response['errorMsg'] = '系统错误，无法生成令牌';
            } else {
                $response['data']['user_id']      = strval($user_info->user_id);
                $response['data']['access_token'] = $token;
                $response['data']['expires_in']   = strval(time() + 86400);
            }
        } else {
            $response['code'] = '5002';
            $response['msg']  = '无法响应请求，服务端异常';
        }
        return response()->json($response);
    }
}
