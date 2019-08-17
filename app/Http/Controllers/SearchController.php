<?php

namespace App\Http\Controllers;

use App\Service\UsersService;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * @var UsersService
     */
    protected $usersService;
    public function __construct(UsersService $usersService)
    {
        $this->usersService = $usersService;
    }

    /**
     * 视频搜索
     */
    public function SearchVideo()
    {

    }


    /**
     * 搜索用户
     */
    public function SearchUsers(Request $request)
    {
       $data = $this->usersService->UsersList($request);
        return response()->json($data);
    }

}
