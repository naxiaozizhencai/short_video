<?php

namespace App\Http\Controllers;

use App\Service\UsersService;
use App\Service\VideoService;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    /**
     * @var UsersService
     */
    protected $usersService;
    protected $videoService;
    public function __construct(UsersService $usersService, VideoService $videoService)
    {
        $this->usersService = $usersService;
        $this->videoService = $videoService;
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
