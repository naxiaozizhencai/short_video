<?php

namespace App\Http\Controllers;

class PopularizeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function Share()
    {
        $data = [
            'code'=>200,
            'data'=>[
                'video_image'=>'',
                'share_title'=>'',
                'share_url'=>'',
                'qr_code_url'=>'',

            ],
        ];
    }

    /**
     * 分享记录
     */
    public function ShareList()
    {

    }
}
