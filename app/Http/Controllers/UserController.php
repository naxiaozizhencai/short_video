<?php

namespace App\Http\Controllers;

use App\Service\UsersService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class UserController extends BaseController
{
    protected $usersService;
    public function __construct(UsersService $usersService)
    {

        $this->usersService = $usersService;
    }

    public function Index()
    {

    }


    public function UserDetail(Request $request)
    {

        $data = [];
        $data['code'] = 200;
        $data['data'] = [
            'user_info'=>[
                'user_id'=>'',
                'uuid'=>'',
                'user_name'=>'',
                'sign'=>'',
                'sex'=>'',
                'age'=>'',
                'city'=>'',
                'viewed_num'=>'',
                'total_view_num'=>'',
                'coin_num'=>'',
                'fans_num'=>'',
                'follow_num'=>'',
                'support_num'=>'',
            ],
            'video_list'=>[
                'id'=>'',
                'user_id'=>'',
                'video_image'=>'',
                'favorite_number'=>'',
            ],


        ];

        return response()->json($data);

    }

    public function LookUserDetail(Request $request)
    {

        $data = [];
        $data['code'] = 200;
        $data['data'] = [
            'user_info'=>[
                'user_id'=>'',
                'uuid'=>'',
                'user_name'=>'',
                'sign'=>'',
                'sex'=>'',
                'city'=>'',
                'fans_num'=>'',
                'follow_num'=>'',
                'support_num'=>'',
                'is_follow'=>'',
            ],
            'video_list'=>[
                'id'=>'',
                'user_id'=>'',
                'video_image'=>'',
                'favorite_number'=>'',
            ],


        ];

        return response()->json($data);

    }


    public function UserVideoList(Request $request)
    {

    }


    public function UserFavoriteList(Request $request)
    {

    }

    /**
     * 粉丝列表
     */
    public function FansList()
    {

    }

    /**
     * 關注列表
     */
    public function FollowList()
    {

    }

    /**
     * 关注
     */
    public function DoFollow()
    {
        $data = $this->usersService->DoFollow();
        return response()->json($data);
    }

    /**
     * 取消关注
     */
    public function CancelFollow()
    {

    }


    /**
     * 被贊列表
     */
    public function SupportList()
    {

    }

    /**
     * 被贊列表
     */
    public function CancelSupport()
    {

    }


    /**
     * 填写推广码
     */
    public function AddPopularizeNum()
    {
        $data = $this->usersService->AddPopularNum();
        return response()->json($data);
    }



}
