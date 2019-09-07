<?php
namespace App\Service;
use App\Repositories\LabelConfigRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoLabelRepositories;
use App\Repositories\VideoRankRepositories;
use App\Repositories\VideoRepositories;

class HotspotService
{

    protected $usersRepositories;
    protected $videoRepositories;
    protected $videoRankRepositories;
    protected $labelConfigRepositories;
    protected $videoLabelRepositories;
   public function __construct(UsersRepositories $usersRepositories, VideoRepositories $videoRepositories,
                               VideoRankRepositories $videoRankRepositories,
                               LabelConfigRepositories $labelConfigRepositories,
                               VideoLabelRepositories  $videoLabelRepositories
)
   {
       $this->usersRepositories = $usersRepositories;
       $this->videoRepositories = $videoRepositories;
       $this->videoRankRepositories = $videoRankRepositories;
       $this->labelConfigRepositories = $labelConfigRepositories;
       $this->videoLabelRepositories = $videoLabelRepositories;
   }

   public function HotIndex($request)
   {
       $label_config = $this->labelConfigRepositories->GetAllLabelConfig();

       if(empty($label_config)) {
            return ['code'=>200, 'data'=>[]];
       }

        $data = ['code'=>200];

       foreach($label_config as $key=>$value){

           if(!isset(LabelConfigRepositories::$label_type[$key])) {
                continue;
            }

            foreach($value as $_k=>$_v){
                $label_data = [];
                $label_data['image'] = $_v->label_image;
                $searchArr = [];
                switch ($_v->type){

                    case LabelConfigRepositories::TODAY_HOT_TYPE:
                        break;

                    case LabelConfigRepositories::RANK_HOT_TYPE:
                        if($_v->id == 22){
                            $searchArr['is_recommend'] = 1;
                        }elseif($_v->id == 23){
                            $searchArr['add_time'] = 'desc';
                        }elseif($_v->id == 24){
                            $searchArr['play_num'] = 'desc';
                        }elseif($_v->id == 25){
                            $searchArr['reply_num'] = 'desc';
                        }elseif($_v->id == 26){
                            $searchArr['favorite_num'] = 'desc';
                        }
                        $video_data = $this->videoRepositories->GetVideoDataByCondition($searchArr);
                        if(!empty($video_data)){
                            $label_data['image'] = $video_data['data'][0]->video_image;
                        }
                        break;

                    case LabelConfigRepositories::LABEL_HOT_TYPE:
                        $search_arr['label_name'] = $_v->label_name;
                        $label_data = $this->videoLabelRepositories->GetVideoLabelData($search_arr);
                        if(!empty($label_data)){
                            print_r($label_data);
                            exit;
                            $label_data['image'] = $label_data['data'][0]->video_image;
                        }

                        break;

                }

                $label_data['name'] = $_v->label_name;
                $label_data['url'] = $_v->label_url;
                $label_data['type'] = $_v->type;
                $data['data']['label_data'][LabelConfigRepositories::$label_type[$key]][] = $label_data;

            }

       }

       return $data;

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
        $page = $request->input('page', 1);
        if(empty($rank_data['data'])){
            return ['code'=>200, 'data'=>[]];
        }

       $data = ['code'=>200];

        foreach($rank_data['data'] as $key=>$value){

            $video_data['rank_id'] = ($page - 1) * 6 + 1;
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