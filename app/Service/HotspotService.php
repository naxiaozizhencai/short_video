<?php
namespace App\Service;
use App\Repositories\LabelConfigRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRankRepositories;
use App\Repositories\VideoRepositories;

class HotspotService
{

    protected $usersRepositories;
    protected $videoRepositories;
    protected $videoRankRepositories;
    protected $labelConfigRepositories;
   public function __construct(UsersRepositories $usersRepositories, VideoRepositories $videoRepositories,
                               VideoRankRepositories $videoRankRepositories, LabelConfigRepositories $labelConfigRepositories)
   {
       $this->usersRepositories = $usersRepositories;
       $this->videoRepositories = $videoRepositories;
       $this->videoRankRepositories = $videoRankRepositories;
       $this->labelConfigRepositories = $labelConfigRepositories;
   }

   public function HotIndex($request)
   {
       $label_config = $this->labelConfigRepositories->GetAllLabelConfig();
       $label_hots = [['name'=>'原创','image'=>'', 'favorite_num'=>100]];
       $rank_hots = [['name'=>'官方推荐','image'=>'', ]];
       $popular_hots = [['name'=>'邀请大神', 'image'=>'','user_info'=>['user_name'=>'111', 'avatar'=>'']]];
       $today_hots = [['name'=>'top1', 'image'=>'']];
       $data = [
           'code'=>200,
           'data'=>[
               'today_hot'=>$today_hots,
               'popular_hot'=>$popular_hots,
               'rank_hot'=>$rank_hots,
               'label_hot'=>$label_hots,
           ],
       ];
   }
    /**
     * 今日热点
     * @param $request
     * @return array
     */
   public function VideoDayRank($request)
   {
        $condtion['rank_type'] = VideoRankRepositories::DAY_RANK_TYPE;
        $condtion['rank_group'] = date('Ymd');
        $rank_data = $this->videoRankRepositories->GetVideoRankList($condtion);

        if(empty($rank_data['data'])){
            return ['code'=>200, 'data'=>[]];
        }

       $data = ['code'=>200];

        foreach($rank_data['data'] as $key=>$value){

            $video_data['video_id'] = $value->video_id;
            $video_data['video_user_avatar'] = $value->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $value->vip_level;
            $video_data['video_username'] = $value->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $data['data']['video_data'][] = $video_data;

        }
        unset($rank_data['data']);
        $data['data']['page'] = $rank_data;
        return $data;
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
        $upload_data = $this->usersRepositories->GetSupportRankData();

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
            $temp_data['support_num'] = $value->support_num;
            $rank_list[] = $temp_data;
        }

        return ['code'=>200, 'data'=>['rank_list'=>$rank_list]];
    }



}