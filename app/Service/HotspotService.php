<?php
namespace App\Service;
use App\Repositories\UsersRepositories;

class HotspotService
{

    protected $usersRepositories;
   public function __construct(UsersRepositories $usersRepositories)
   {
       $this->usersRepositories = $usersRepositories;
   }

   public function InvitationRankData()
   {

       $invitation_data = $this->usersRepositories->GetInvitationRankData();
       if(empty($invitation_data)){
            return ['code'=>200, 'data'=>[]];
       }

       $rank_list = [];
       foreach($invitation_data as $key=>$value){
            $temp_data['rank'] = $key + 1;
            $temp_data['user_id'] = $value->id;
            $temp_data['vip_level'] = $value->vip_level;
            $temp_data['avatar'] = $value->avatar;
            $temp_data['user_name'] = $value->username;
            $temp_data['invitation_num'] = $value->invitation_num;
            $rank_list[] = $temp_data;
       }

       return ['code'=>200, 'data'=>['rank_list'=>$rank_list]];

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

       $upload_data = $this->usersRepositories->GetUploadRankData();

       if(empty($upload_data)){
           return ['code'=>200, 'data'=>[]];
       }

       $rank_list = [];
       foreach($upload_data as $key=>$value){
           $temp_data['rank'] = $key + 1;
           $temp_data['user_id'] = $value->id;
           $temp_data['vip_level'] = $value->vip_level;
           $temp_data['avatar'] = $value->avatar;
           $temp_data['user_name'] = $value->username;
           $temp_data['upload_num'] = $value->upload_num;
           $rank_list[] = $temp_data;
       }

       return ['code'=>200, 'data'=>['rank_list'=>$rank_list]];
   }





}