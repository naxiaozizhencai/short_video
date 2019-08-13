<?php
namespace App\Service;
class HotspotService
{

   public function __construct()
   {
   }

   public function InvitationRankData()
   {
       return $data = [
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

    public function SupportRankData()
    {
        return $data = [
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

   public function UploadRankData()
   {

       return  $data = [
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





}