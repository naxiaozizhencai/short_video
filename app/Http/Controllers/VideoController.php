<?php

namespace App\Http\Controllers;

class VideoController extends Controller
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

    public function VideoDetail(Request $request)
    {


        $data = [
            'code'=>200,
            'data'=>[
                'video_data'=>[
                    'video_user_avatar'=>'',
                    'video_user_id'=>'',
                    'video_user_name'=>'',
                    'video_title'=>'',
                    'video_image'=>'',
                    'video_url'=>'',
                    'video_label'=>'',
                    'favorite_number'=>'',
                    'reply_number'=>'',
                ],
            ],
        ];

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
