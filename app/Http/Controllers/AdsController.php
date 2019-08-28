<?php

namespace App\Http\Controllers;

use App\Service\AdsService;
use App\Service\OrderService;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

class AdsController extends Controller
{
    protected $adsService;
    public function __construct(AdsService $adsService)
    {
        $this->adsService = $adsService;
    }


    public function AdsList(Request $request)
    {
        $data = $this->adsService->GetAdsList($request);
        return response()->json($data);
    }
}
