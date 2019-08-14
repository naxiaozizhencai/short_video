<?php

namespace App\Http\Controllers;

use App\Service\HotspotService;

class HotspotController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $hotspotService;
    public function __construct(HotspotService $hotspotService)
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

    public function SupportRank()
    {
        $data =$this->hotspotService->SupportRankData();
        return response()->json($data);

    }


}