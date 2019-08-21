<?php

namespace App\Http\Controllers;

use App\Service\VideoService;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    protected $videoService;
    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    /**
     * 返回視頻列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ViewVideo(Request $request)
    {
        $data = $this->videoService->ViewVideo($request);
        return response()->json($data);
    }

    public function PlayVideo(Request $request)
    {
        $data = $this->videoService->PlayVideo($request);
        return response()->json($data);
    }
    /**
     * 观看关注视频
     * @return \Illuminate\Http\JsonResponse
     */
    public function FollowViewVideo()
    {
        $data = $this->videoService->FollowViewVideo();
        return response()->json($data);
    }

    /**
     * 點擊喜歡
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function DoFavorite(Request $request)
    {

        $data = $this->videoService->DoFavorite($request);
        return response()->json($data);
    }


    public function CancelFavorite(Request $request)
    {
        $data = $this->videoService->CancelFavorite($request);
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

        $data = $this->videoService->AddDiscuss($request);
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
     * 取消喜欢这条评论
     * @return \Illuminate\Http\JsonResponse
     */
    public function CancelFavorDiscuss()
    {
        $data = $this->videoService->CancelFavorDiscuss();
        return response()->json($data);
    }


    /**
     * 上传视频
     */
    public function UploadVideo()
    {
        $data = $this->videoService->UploadVideo();
        return response()->json($data);
    }

    public function Upload()
    {
        $data = $this->videoService->Upload();
        return response()->json($data);
    }

    /**
     * 分享这条视频
     */
    public function ShareVideo()
    {

    }

}
