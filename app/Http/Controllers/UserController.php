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


    public function UserInfo(Request $request)
    {
        $data = $this->usersService->UserInfo();
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

    /**
     * 作品列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function UserVideoList(Request $request)
    {
        $data = $this->usersService->UserVideoList($request);
        return response()->json($data);
    }


    /**
     * 用户喜欢的列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function UserFavoriteList(Request $request)
    {
        $data = $this->usersService->UserFavoriteList($request);
        return response()->json($data);
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
        $data = $this->usersService->FollowList();
        return response()->json($data);
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
        $data = $this->usersService->CancelFollow();
        return response()->json($data);
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
     * 更新用户信息
     */
    public function UpdateUsersInfo(Request $request)
    {
        $data = $this->usersService->UpdateUsersInfo($request);
        return response()->json($data);
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
