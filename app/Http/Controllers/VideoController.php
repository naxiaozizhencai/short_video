<?php

namespace App\Http\Controllers;

use App\Repositories\FavoriteRepositories;
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

    public function DoFavorite(Request $request)
    {

        $video_id = $request->input('video_id');
        $data = $this->videoService->DoFavorite(1, 2);
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function DiscussList(Request $request)
    {
        $video_id = $request->get('video_id', 1);

        $data = $this->videoService->getDiscussList($video_id);
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function AddDiscuss(Request $request)
    {
        $video_id = $request->input('video_id', 0);//视频id
        $content = $request->input('content', '');
        $data = $this->videoService->AddDiscuss($video_id, $content);
        return response()->json($data);
    }

    /**
     * 举报评论
     */
    public function ReportDiscuss()
    {
        $data = $this->videoService->ReportDiscuss();
        return response()->json($data);
    }

    /**
     * 喜欢这条评论
     */
    public function FavorDiscuss()
    {
        $data = $this->videoService->DoFavorDiscuss();
        return response()->json($data);
    }

    /**
     * 喜欢这条评论
     */
    public function CancelFavorDiscuss()
    {
        $data = $this->videoService->CancelFavorDiscuss();
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
       $data = $this->videoService->UploadVideo();
        return response()->json($data);
    }
}
