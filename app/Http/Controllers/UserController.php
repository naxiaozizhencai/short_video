<?php

namespace App\Http\Controllers;

use App\Service\UsersService;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UserController extends BaseController
{
    protected $usersService;
    public function __construct(UsersService $usersService)
    {

        $this->usersService = $usersService;
    }

    /**
     * 查看用户信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function UserInfo(Request $request)
    {
        $data = $this->usersService->UserInfo($request);
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
        $data = $this->usersService->FansList();
        return response()->json($data);
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
    public function AddPopularizeNum(Request $request)
    {
        $data = $this->usersService->AddPopularNum($request);
        return response()->json($data);
    }

    /**
     * 分享自己
     */
    public function UserShare(Request $request)
    {
        $data = $this->usersService->UserShareData($request);
        return response()->json($data);

    }

    /**
     * 分享记录
     */
    public function ShareList()
    {
        $data = $this->usersService->ShareList();
        return response()->json($data);
    }




}
