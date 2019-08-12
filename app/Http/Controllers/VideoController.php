<?php

namespace App\Http\Controllers;

use App\Service\VideoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    protected $videoService;
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * 观看视频
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ViewVideo(Request $request)
    {
        $user_id = Auth::user()->id;
        $data = $this->videoService->RandViewVideo($user_id);
        return response()->json($data);

    }

    /**
     * 搜索列表
     */
    public function SearchList()
    {
        $data = [
            'code'=>200,
            'data'=>[
                'list'=>[[
                    'rank'=>'',
                    'video_id'=>'',
                    'support_num'=>'',
                    'video_title'=>'',
                    'video_image'=>'',
                ]],

            ],
        ];
    }

    /**
     * 上传视频
     */
    public function UploadVideo()
    {

    }
}
