<?php
namespace App\Service;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRepositories;

class HotspotService
{

    protected $usersRepositories;
    protected $videoRepositories;
   public function __construct(UsersRepositories $usersRepositories, VideoRepositories $videoRepositories)
   {
       $this->usersRepositories = $usersRepositories;
       $this->videoRepositories = $videoRepositories;
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



    public function SupportRankData()
    {

        $result = $this->videoRepositories->GetVideoSupportRankData();

        if(empty($result['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        $data = ['code'=>200];
        foreach($result['data'] as $key=>$value){
            $temp_data = [];
            $temp_data['rank'] = $key;
            $temp_data['video_id'] = $value;
            $temp_data['user_id'] = $value->user_id;
            $temp_data['video_id'] = $value->id;
            $temp_data['support_num'] = $value->favorite_num;
            $temp_data['video_title'] = $value->video_title;
            $temp_data['video_image'] = $value->video_image;
            $data['data']['list'][] = $temp_data;
        }
        return $data;
    }






}