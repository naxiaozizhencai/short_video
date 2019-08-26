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

    public function FansRank()
    {

    }

}
