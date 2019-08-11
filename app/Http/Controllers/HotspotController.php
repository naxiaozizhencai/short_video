<?php

namespace App\Http\Controllers;

class HotspotController extends Controller
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

    /**
     * 邀請排行榜
     */
    public function InvitationRank()
    {
        $data = [
            'code'=>200,
            'data'=>[
                'list'=>[[
                    'rank'=>'',
                    'user_id'=>'',
                    'invitation_num'=>'',
                    'user_name'=>'',
                ]],

            ],
        ];
    }

    /**
     * 上传大神拍上榜
     */
    public function UploadRank()
    {
        $data = [
            'code'=>200,
            'data'=>[
                'list'=>[[
                    'rank'=>'',
                    'user_id'=>'',
                    'upload_num'=>'',
                    'user_name'=>'',
                ]],

            ],
        ];
    }

    public function SupportRank()
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


}
