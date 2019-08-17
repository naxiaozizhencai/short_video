<?php

namespace App\Http\Controllers;

use App\Service\HotspotService;
use App\Service\VideoService;

class HotspotController extends Controller
{
    /**
     * @var HotspotService
     */
    protected $hotspotService;
    protected $videoService;
    public function __construct(HotspotService $hotspotService, VideoService $videoService)
    {
         $this->hotspotService = $hotspotService;
    }

    /**
     * 邀請排行榜
     */
    public function InvitationRank()
    {
        $data = $this->hotspotService->InvitationRankData();
        return response()->json($data);

    }

    /**
     * 上传大神拍上榜
     */
    public function UploadRank()
    {
        $data =$this->hotspotService->UploadRankData();
        return response()->json($data);

    }

    /**
     * 最多爱心
     * @return \Illuminate\Http\JsonResponse
     */
    public function SupportRank()
    {
        $data =$this->hotspotService->SupportRankData();
        return response()->json($data);

    }

    /**
     * 最新上传
     */
    public function NewUploadRank()
    {
        $data = $this->hotspotService->NewUploadRankData();
        return response()->json($data);
    }

    /**
     * 最多播放
     */
    public function PlayVideoRank()
    {
        $data = $this->hotspotService->PlayVideoRankData();
        return response()->json($data);
    }

    /**
     * 最多评论
     */
    public function DiscussVideoRank()
    {
        $data = $this->hotspotService->DiscussVideoRankData();
        return response()->json($data);
    }

    /**
     * 官方推荐
     */
    public function RecommendVideoRank()
    {
        $data = $this->hotspotService->RecommendVideoRankData();
        return response()->json($data);
    }


}
