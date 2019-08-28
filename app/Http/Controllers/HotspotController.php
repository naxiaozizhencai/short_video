<?php

namespace App\Http\Controllers;

use App\Service\HotspotService;
use App\Service\VideoService;
use Illuminate\Http\Request;

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
         $this->videoService = $videoService;
    }

    public function HotIndex(Request $request)
    {
        return [
            'code'=>200,
            'data'=>[
                'today_hot'=>[

                ],
                'rank_hot'=>[

                ],
                'label_hot'=>[

                ],

            ],
        ];
    }

    /**
     * 今日热点
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function VideoDayRank(Request $request)
    {
        $data = $this->hotspotService->VideoDayRank($request);
        return response()->json($data);
    }

    public function VideoRank(Request $request)
    {
        $data = $this->hotspotService->VideoRankData($request);
        return response()->json($data);
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

    public function SupportRank()
    {
        $data =$this->hotspotService->SupportRankData();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function VideoLabelList(Request $request)
    {
        $data = $this->videoService->VideoLabelData($request);
        return response()->json($data);
    }

}
