<?php
namespace App\Service;
use App\Repositories\DiscussReportRepositories;
use App\Repositories\DiscussRepositories;
use App\Repositories\FavoriteDiscussRepositories;
use App\Repositories\FavoriteRepositories;
use App\Repositories\LabelConfigRepositories;
use App\Repositories\MessageRepositories;
use App\Repositories\PlayVideoHistoryRepositories;
use App\Repositories\ReplyRepositories;
use App\Repositories\TempDataRepositories;
use App\Repositories\UsersFansRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoLabelRepositories;
use App\Repositories\VideoRankRepositories;
use App\Repositories\VideoRepositories;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class VideoService
{


    protected $videoRepositories;
    protected $tempDataRepositories;
    protected $favoriteRepositories;
    protected $discussRepositories;
    protected $usersRepositories;
    protected $discussReportRepositories;
    protected $messageRepositories;
    protected $playVideoHistoryRepositories;
    protected $favoriteDiscussRepositories;
    protected $fansRepositories;
    protected $videoLabelRepositories;
    protected $labelConfigRepositories;
    protected $usersService;
    protected $videoRankRepositories;


    public function __construct(VideoRepositories $videoRepositories,
                                TempDataRepositories $tempDataRepositories,
                                FavoriteRepositories $favoriteRepositories, DiscussRepositories $discussRepositories,
                                UsersRepositories $usersRepositories, DiscussReportRepositories $discussReportRepositories,
                                MessageRepositories $messageRepositories, PlayVideoHistoryRepositories $playVideoHistoryRepositories,
                                FavoriteDiscussRepositories $favoriteDiscussRepositories,UsersFansRepositories $fansRepositories,
                                VideoLabelRepositories $videoLabelRepositories,LabelConfigRepositories $labelConfigRepositories,
                                UsersService $usersService,VideoRankRepositories $videoRankRepositories
)
    {
        $this->videoRepositories = $videoRepositories;
        $this->tempDataRepositories = $tempDataRepositories;
        $this->favoriteRepositories = $favoriteRepositories;
        $this->discussRepositories = $discussRepositories;
        $this->usersRepositories = $usersRepositories;
        $this->discussReportRepositories = $discussReportRepositories;
        $this->messageRepositories = $messageRepositories;
        $this->playVideoHistoryRepositories = $playVideoHistoryRepositories;
        $this->favoriteDiscussRepositories = $favoriteDiscussRepositories;
        $this->fansRepositories = $fansRepositories;
        $this->videoLabelRepositories = $videoLabelRepositories;
        $this->labelConfigRepositories = $labelConfigRepositories;
        $this->usersService = $usersService;
        $this->videoRankRepositories = $videoRankRepositories;
    }


    /**
     *
     * @param $request
     * @return array
     */
    public function ViewVideoDetail($request)
    {
        $video_id = $request->input('video_id');
        $type = $request->input('type');

        $user_id = Auth::id();

        if(empty($video_id)) {
            return ['code'=>-1, 'msg'=>'参数不能为空！'];
        }

        $condtion_data['video_id'] = $video_id;
        $video_detail_data = $this->videoRepositories->GetVideoData($condtion_data);

        if(empty($video_detail_data['data'])) {
            return ['code'=>-1, 'msg'=>'视频数据为空！'];
        }

        $user_data = $this->usersRepositories->getUserInfoById($user_id);

        if(empty($user_data)) {
            return ['code'=>-1, 'msg'=>'用户数据不存在！'];
        }


        $follows_ids = $this->fansRepositories->GetUsersFollowData($user_id);
        $data = ['code'=>200];
        foreach($video_detail_data['data'] as $key=>$value){

            $video_data['video_id'] = $value->video_id;
            $video_data['video_user_avatar'] = $value->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $value->vip_level;
            $video_data['video_username'] = $value->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->video_favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $video_data['is_follow'] = isset($follows_ids[$value->user_id]) ? 1 : 0;

            $favorite_data = $this->favoriteRepositories->FindFavoriteRow($user_id, $value->video_id);

            $video_data['is_favorite'] = empty($favorite_data) ? 0 : 1;

            $data['data']['video_data'] = $video_data;
        }

        //更新播放到哪里了
        if($type == 1){
            $max_video_id = $this->videoRepositories->GetMaxVideoId();
            if($max_video_id == $video_data['video_id']){
                $rand_num = rand(2,10);
                $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::VIDEO_RECOMMEND_MAX_ID, ['temp_value'=>intval($max_video_id / $rand_num)]);

            }else{
                $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::VIDEO_RECOMMEND_MAX_ID, ['temp_value'=>$video_data['video_id']]);

            }
        }

        //更新播放次数
        $play_video_times = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES);
        $total_video_times = $this->tempDataRepositories->GetValueByKey(TempDataRepositories::TOTAL_VIEWED_TIMES);
        $total_times = empty($total_video_times) ? TempDataRepositories::TOTAL_VIDEO_TIMES  : $total_video_times->temp_value;
        $play_video_time = empty($play_video_times) ? 0  : $play_video_times->temp_value;

        if($user_data->vip_expired_time < time() && $play_video_time < $total_times){

            if(!$result = $this->playVideoHistoryRepositories->ExistHistory($user_id, $video_id)){
                $history_data['user_id'] = $user_id;
                $history_data['video_id'] = $video_id;
                $history_data['add_time'] = date("Y-m-d H:i:s");
                $this->playVideoHistoryRepositories->InsertPlayVideoHistory($history_data);

                if($user_id !=  $data['data']['video_data']['video_user_id']) {
                    $update_temp_data['temp_value'] = (empty($play_video_times)) ? 1 : $play_video_times->temp_value + 1;
                    $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES, $update_temp_data);
                }

            }

            $this->videoRepositories->IncrVideoNum($video_id, 'play_num', 1);
        }

        return $data;
    }


    /**
     * 观看视频
     * @param $request
     * @return array
     */
    public function ViewVideo($request)
    {

        $user_id = Auth::id();


        $page = $request->input('page');
        $type = $request->input('type');
        if($page == 1 && $type == 1){
            $temp_data = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::VIDEO_RECOMMEND_MAX_ID);
            $min_video_id = empty($temp_data) ? 0 : $temp_data->temp_value;
            $request->offsetSet('min_video_id', $min_video_id);
        }


        $result = $this->videoRepositories->GetVideoData($request->toarray());

        if(empty($result['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        foreach($result['data'] as $key=>$value){

            $video_data['video_id'] = $value->video_id;
            $video_data['video_user_avatar'] = $value->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $value->vip_level;
            $video_data['video_username'] = $value->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->video_favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $video_data['is_fifteen'] = $value->is_fifteen;
            $video_data['is_follow'] = 1;
            $data['data']['video_data'][] = $video_data;

        }

        $data['code'] = 200;
        unset($result['data']);
        $data['data']['page'] = $result;

        return $data;
    }

    /**
     * 增加播放次數
     * @param $request
     * @return array
     */
    public function PlayVideo($request)
    {
        $video_id = $request->input('video_id');
        $video_data = $this->videoRepositories->getVideoById($video_id);

        if(empty($video_data)){
            return ['code'=>-1, 'msg'=>'視頻數據不存在'];
        }


        return $this->usersService->UserInfo($request);
    }

    /**
     *随机返回一个
     * @return array
     */
    public function RecommendViewVideo($request)
    {
        $user_id = Auth::id();
        $temp_data = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::VIDEO_RECOMMEND_MAX_ID);
        $search_arr = [];
        $search_arr['is_recommend'] = 1;

        $is_back = $request->input('is_back');

        if(!empty($is_back)) {
            $search_arr['max_video_id'] = empty($temp_data) ? 0 : $temp_data->temp_value;
            $search_arr['is_back'] = 1;
        }else{
            $search_arr['min_video_id'] = empty($temp_data) ? 0 : $temp_data->temp_value;
        }

        $result = $this->videoRepositories->GetRecommendVideoData($search_arr);

        if(empty($result['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        $follows_ids = $this->fansRepositories->GetUsersFollowData($user_id);

        $video_data = [];
        foreach($result['data'] as $key=>$value){
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
            $video_data['is_follow'] = isset($follows_ids[$value->user_id]) ? 1 : 0;
            $data['data']['video_data'] = $video_data;
            break;
        }

        $data['code'] = 200;
        if(!$result = $this->playVideoHistoryRepositories->ExistHistory($user_id, $video_data['video_id'])){

            $play_video_times = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES);
            $update_temp_data['temp_value'] = (empty($play_video_times)) ? 1 : $play_video_times->temp_value + 1;
            $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES, $update_temp_data);
            $history_data['user_id'] = $user_id;
            $history_data['video_id'] = $video_data['video_id'];
            $history_data['add_time'] = date("Y-m-d H:i:s");
            $this->playVideoHistoryRepositories->InsertPlayVideoHistory($history_data);
        }

        $this->videoRepositories->IncrVideoNum($video_data['video_id'], 'play_num', 1);

        $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::VIDEO_RECOMMEND_MAX_ID, ['temp_value'=>$video_data['video_id']]);

        return $data;
    }


    /**
     * 观看关注视频
     * @param $uid
     * @return array
     */
    public function FollowViewVideo($request)
    {
        $user_id = Auth::id();
        $result = $this->videoRepositories->GetFollowVideoData($user_id);

        if(empty($result)){
            return ['code'=>-1, 'msg'=>'关注还未上传视频'];
        }

        $video_data = [];
        foreach($result['data'] as $key=>$value){

            $user_data = $this->usersRepositories->getUserInfoById($value->user_id);
            $video_data['video_id'] = $value->id;
            $video_data['video_user_avatar'] = $user_data->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $user_data->vip_level;
            $video_data['video_username'] = $user_data->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $video_data['is_follow'] = 1;
            $data['data']['video_data'][] = $video_data;
        }

        $data['code'] = 200;
        unset($result['data']);
        $data['data']['page'] = $result;

        return $data;
    }


    /**
     * 点击爱心
     * @param $video_id
     * @param $user_id
     * @return array
     */
    public function DoFavorite($request)
    {

        $video_id = $request->input('video_id');
        $user_id  = Auth::id();

        $video_row = $this->videoRepositories->getVideoById($video_id);

        $user_data = $this->usersRepositories->getUserInfoById($user_id);

        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        $favor_row = $this->favoriteRepositories->FindFavoriteRow($user_id, $video_id);
        if(!empty($favor_row)){
            return $data = ['code'=>-1, 'msg'=>'已经喜欢'];
        }

        $find_data['user_id'] = $user_id;
        $find_data['video_id'] = $video_id;
        $update_data = $find_data;
        $update_data['status'] = 1;
        $update_data['add_time'] = date("Y-m-d H:i:s");
        $this->favoriteRepositories->UpdateFavoriteVideo($find_data, $update_data);
        $this->videoRepositories->IncrVideoNum($video_id, 'favorite_num');

        $msg_data = [];
        $msg_data['message_type'] = MessageRepositories::MESSAGE_TYPE_SUPPORT;
        $msg_data['message'] = '点赞了了你的视频';
        $msg_data['send_id'] = $user_id;
        $msg_data['receive_id'] = $video_row->user_id;
        $msg_data['send_time'] = time();
        $msg_data['message_extend'] = json_encode(['video_id'=>$video_row->id, 'video_image'=>$video_row->video_image]);
        $msg_data['add_time'] = date('Y-m-d H:i:s');

        $this->messageRepositories->InsertMessage($msg_data);
        $data = ['code'=>200, 'msg'=>'喜歡成功'];
        $this->usersRepositories->IncrUsersDetailNum($video_row->user_id, 'favorite_num', 1);

        $video_rank_condition = [];
        $video_rank_condition['rank_video_id'] = $video_id;
        $video_rank_condition['rank_type'] = VideoRankRepositories::DAY_RANK_TYPE;
        $video_rank_condition['rank_group'] = date('Ymd');

        $video_rank_info = $this->videoRankRepositories->GetVideoRankData($video_rank_condition);
        $video_rank_data = [];
        if(!empty($video_rank_info)){
            $video_rank_data['rank_num'] = $video_rank_info->rank_num + 1;
        }else{
            $video_rank_data = $video_rank_condition;
            $video_rank_data['rank_num'] = 1;
            $video_rank_data['add_time'] = date('Y-m-d H:i:s');
        }

        $this->videoRankRepositories->UpdateOrInsert($video_rank_condition, $video_rank_data);
        return $data;
    }

    /**
     * 取消喜欢这条视频
     */
    public function CancelFavorite($request)
    {
        $video_id = $request->input('video_id');
        $user_id  = Auth::id();

        $video_row = $this->videoRepositories->getVideoById($video_id);

        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        $favor_row = $this->favoriteRepositories->FindFavoriteRow($user_id, $video_id);
        if(empty($favor_row)){
            return $data = ['code'=>-1, 'msg'=>'已经取消'];
        }

        $find_data['user_id'] = $user_id;
        $find_data['video_id'] = $video_id;
        $update_data = $find_data;
        $update_data['status'] = 0;
        $update_data['add_time'] = date("Y-m-d H:i:s");
        $this->favoriteRepositories->DeleteFavoriteVideo($user_id, $video_id);
        $this->videoRepositories->DecrVideoNum($video_id, 'favorite_num');
        $this->usersRepositories->DecrUsersDetailNum($video_row->user_id, 'favorite_num', 1);

        $video_rank_condition = [];
        $video_rank_condition['rank_video_id'] = $video_id;
        $video_rank_condition['rank_type'] = VideoRankRepositories::DAY_RANK_TYPE;
        $video_rank_condition['rank_group'] = date('Ymd');

        $video_rank_info = $this->videoRankRepositories->GetVideoRankData($video_rank_condition);
        $video_rank_data = [];
        if(!empty($video_rank_info)){
            $video_rank_data['rank_num'] = ($video_rank_info->rank_num - 1 < 0) ? 0 : $video_rank_info->rank_num - 1;
        }else{
            $video_rank_data = $video_rank_condition;
            $video_rank_data['rank_num'] = 0;
            $video_rank_data['add_time'] = date('Y-m-d H:i:s');
        }

        $this->videoRankRepositories->UpdateOrInsert($video_rank_condition, $video_rank_data);

        $data = ['code'=>200, 'msg'=>'取消成功'];
        return $data;

    }

    /**
     * @param $video_id
     * @param $content
     */
    public function AddDiscuss($request)
    {

        $user_id = Auth::id();
        $video_id = $request->input('video_id', 0);//视频id
        $content = $request->input('content', '');
        $parent_id = $request->input('parent_id', 0);
        $video_row = $this->videoRepositories->getVideoById($video_id);
        $user_data = $this->usersRepositories->getUserInfoById($user_id);

        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        if(empty($content)){
            return $data = ['code'=>-1, 'msg'=>'評論内容不能爲空'];
        }

        $discuss_data['parent_id'] = $parent_id;
        $discuss_data['video_id'] = $video_id;
        $discuss_data['content'] = $content;
        $discuss_data['from_uid'] = $user_id;
        $discuss_data['favorite_number'] = 0;
        $discuss_data['discuss_time'] = time();
        $discuss_data['add_time'] = date("y-m-d H:i:s");

        $this->discussRepositories->InsertDiscuss($discuss_data);

        $msg_data = [];
        $msg_data['send_time'] = time();
        $msg_data['add_time'] = date('Y-m-d H:i:s');
        $msg_data['message_type'] = MessageRepositories::MESSAGE_TYPE_DISCUSS;
        $msg_data['message'] = $content;
        $msg_data['send_id'] = $user_id;
        $msg_data['receive_id'] = $video_row->user_id;
        $msg_data['message_extend'] = json_encode(['video_id'=>$video_row->id, 'video_image'=>$video_row->video_image]);

        $this->messageRepositories->InsertMessage($msg_data);

        if(!empty($parent_id)) {

            $parent_discuss_data = $this->discussRepositories->getDiscussById($parent_id);

            if(empty($parent_discuss_data)){
                return $data = ['code'=>-1, 'msg'=>'操作失败'];
            }

            $msg_data['message_type'] = MessageRepositories::MESSAGE_TYPE_DISCUSS;
            $msg_data['message'] = $content;
            $msg_data['send_id'] = $user_id;
            $msg_data['receive_id'] = $parent_discuss_data->from_uid;
            $this->messageRepositories->InsertMessage($msg_data);

        }

        $this->videoRepositories->IncrVideoNum($video_id, 'reply_num', 1);
        return $data = ['code'=>200, 'msg'=>'评论成功'];
    }

    /**
     * 喜歡這條評論
     */
    public function DoFavorDiscuss()
    {

        $discuss_id = app('request')->input('discuss_id');
        $user_id = Auth::id();
        if(empty($discuss_id)) {
            return $data = ['code'=>-1, 'msg'=>'參數錯誤'];
        }

        $discuss_data = $this->discussRepositories->getDiscussById($discuss_id);

        if(empty($discuss_data)){
            return $data = ['code'=>-1, 'msg'=>'評論不存在'];
        }

        $favorite_discuss = $this->favoriteDiscussRepositories->FindFavoriteDiscussData($user_id, $discuss_id);

        if(!empty($favorite_discuss)){
            return $data = ['code'=>-1, 'msg'=>'已经喜欢这条评论'];
        }

        $favorite_discuss = [];
        $favorite_discuss['video_id'] = $discuss_data->video_id;
        $favorite_discuss['discuss_id'] = $discuss_id;
        $favorite_discuss['user_id'] = $user_id;
        $favorite_discuss['add_time'] = date('Y-m-d H:i:s');
        $this->favoriteDiscussRepositories->InsertVideoDiscussFavorite($favorite_discuss);
        $this->discussRepositories->IncrDiscussfavorById($discuss_id);
        return $data = ['code'=>200, 'msg'=>'操作成功'];
    }

    /**
     * 喜歡這條評論
     */
    public function CancelFavorDiscuss()
    {
        $discuss_id = app('request')->input('discuss_id');
        $user_id = Auth::id();
        if(empty($discuss_id)) {
            return $data = ['code'=>-1, 'msg'=>'參數錯誤'];
        }

        $discuss_data = $this->discussRepositories->getDiscussById($discuss_id);

        if(empty($discuss_data)){
            return $data = ['code'=>-1, 'msg'=>'評論不存在'];
        }

        $favorite_discuss = $this->favoriteDiscussRepositories->FindFavoriteDiscussData($user_id, $discuss_id);

        if(empty($favorite_discuss)){
            return $data = ['code'=>-1, 'msg'=>'已经取消喜欢这条评论'];
        }

        $this->favoriteDiscussRepositories->DeleteFavoriteVideoDiscuss($user_id, $discuss_id);
        $this->discussRepositories->DecrDiscussfavorById($discuss_id);

        return $data = ['code'=>200, 'msg'=>'操作成功'];
    }

    public function ReportDiscuss()
    {
        $discuss_id = app('request')->input('discuss_id');
        if(empty($discuss_id)) {
            return $data = ['code'=>-1, 'msg'=>'參數錯誤'];
        }

        $discuss_data = $this->discussRepositories->getDiscussById($discuss_id);

        if(empty($discuss_data)){
            return $data = ['code'=>-1, 'msg'=>'評論不存在'];
        }

        $data['discuss_id'] = $discuss_id;
        $data['content'] = $discuss_data->content;
        $data['add_time'] = date('Y-m-d H:i:s');
        $this->discussReportRepositories->InsertDiscussReport($data);
        return $data = ['code'=>200, 'msg'=>'操作成功'];

    }

    /**
     * 获取视频评论列表
     * @param $video_id
     * @return array
     */
    public function getDiscussList($video_id)
    {
        $video_row = $this->videoRepositories->getVideoById($video_id);
        $user_id = Auth::id();
        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        $discuss_list = $this->discussRepositories->getDiscussList($video_id);

        $data = ['code'=>200, 'data'=>[]];

        //是否喜欢这条评论

        $favorite_discuss_data = $this->favoriteDiscussRepositories->GetUsersFavoriteDiscussData($user_id, $video_id);

        if(!empty($discuss_list['data'])){
            foreach($discuss_list['data'] as $key=>$value){

                $sub_list = $this->discussRepositories->getSubList($value->video_id, $value->id);
                $user_data = $this->usersRepositories->getUserInfoById($value->from_uid);

                if(empty($user_data)){
                    continue;
                }

                $temp_data = [];
                $temp_data['user_info']['user_id'] = $user_data->id;
                $temp_data['user_info']['username'] = $user_data->username;
                $temp_data['user_info']['vip_level'] = $user_data->vip_level;
                $temp_data['user_info']['avatar'] = $user_data->avatar;
                $temp_data['user_info']['sex'] = $user_data->sex;
                $temp_data['user_info']['city'] = $user_data->city;
                $temp_data['discuss_info']['discuss_id'] = $value->id;
                $temp_data['discuss_info']['discuss_time'] = $value->discuss_time;
                $temp_data['discuss_info']['content'] = $value->content;
                $temp_data['discuss_info']['favorite_number'] = $value->favorite_number;
                $temp_data['discuss_info']['is_favorite'] = isset($favorite_discuss_data[$value->id]) ? 1 : 0;

                $temp_data['reply_info'] = [];

                if(!empty($sub_list)){
                    //最多回复三级
                    $sub_temp_data = [];
                    foreach ($sub_list as $sub_key=>$sub_value){
                        $sub_user_data = $this->usersRepositories->getUserInfoById($value->from_uid);
                        if(empty($sub_user_data)){
                            continue;
                        }

                        $sub_temp_data['user_info']['user_id'] = $sub_user_data->id;
                        $sub_temp_data['user_info']['username'] = $sub_user_data->username;
                        $sub_temp_data['user_info']['vip_level'] = $sub_user_data->vip_level;
                        $sub_temp_data['user_info']['avatar'] = $sub_user_data->avatar;
                        $sub_temp_data['user_info']['sex'] = $sub_user_data->sex;
                        $sub_temp_data['user_info']['city'] = $sub_user_data->city;
                        $sub_temp_data['discuss_info']['discuss_id'] = $sub_value->id;
                        $sub_temp_data['discuss_info']['discuss_time'] = $sub_value->discuss_time;
                        $sub_temp_data['discuss_info']['content'] = $sub_value->content;
                        $sub_temp_data['discuss_info']['favorite_number'] = $sub_value->favorite_number;
                        $sub_temp_data['discuss_info']['is_favorite'] = isset($favorite_discuss_data[$sub_value->id]) ? 1 : 0;
                        $temp_data['reply_info'][] = $sub_temp_data;
                    }
                }
                $data['data']['discuss_list'][] = $temp_data;

            }

            unset($discuss_list['data']);
            $data['page'] = $discuss_list;

        }
        return $data;

    }

    /**
     *上传文件
     * @return array
     */
    public function Upload()
    {
        $file = app('request')->file('file');
        if(empty($file)){
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        if (!$file->isValid()) {
            return ['code'=>-1, 'msg'=>'文件上传失败'];
        }



        $dir = env("UPLOAD_DIR");
        $upload_url = env("UPLOAD_URL");
        $file_name = time().rand(0, 1000).'.'.$file->guessExtension();

        $image_type = ['jpg', 'png', 'jpeg', 'gif'];
        $file_type = $file->getClientOriginalExtension();
        //如果是图片生成缩略图
        $return_file_name = $upload_url . $file_name;

        if(in_array(strtolower($file_type), $image_type)){
            $manager = new ImageManager(array('driver' => 'imagick'));
            $return_file_name = $upload_url . 'cover' . $file_name;

            $manager->make($file)->resize(400, 700)->save($dir. 'cover' . $file_name);
        }

        //获取视频时长
        $time = 0;
        if(!in_array(strtolower($file_type), $image_type)){
            $path = $dir.$file_name;
            $command = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 -i {$path}";
            $time = exec($command);
        }


        $file->move($dir, $file_name);

        return ['code'=>200, 'data'=>['id'=>rand(1,10000),'file_name'=>$return_file_name,'is_fifteen'=>$path]];

    }

    /**
     * 上传视频
     * @return array
     */
    public function UploadVideo()
    {

        $title = app('request')->input('title');
        $video_image = app('request')->input('video_image');
        $video = app('request')->input('video');
        $is_fifteen = app('request')->input('is_fifteen');
        $video_label = app('request')->input('video_label');
        $label_arr = explode('#', $video_label);

        if(empty($video_image) || empty($video_label) || empty($video_label) || empty($video)){
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $video_data = [];
        $user_id = Auth::id();
        $video_data['user_id'] = $user_id;
        $video_data['video_title'] = $title;
        $video_data['video_image'] = $video_image;
        $video_data['video_url'] = $video;
        $video_data['is_fifteen'] = $is_fifteen;
        $video_data['video_label'] = $video_label;
        $video_data['add_time'] = date('Y-m-d H:i:s');
        $video_id = $this->videoRepositories->InsertVideo($video_data);

        if(!empty($label_arr)) {
            foreach($label_arr as $key=>$value){

                if(empty($value)){
                    continue;
                }
                $video_label_data['video_id'] = $video_id;
                $video_label_data['user_id'] = $user_id;
                $label_config = $this->labelConfigRepositories->GetLabelConfigByName(['label_name'=>$value]);
                $label_id = empty($label_config) ? 0 : $label_config->id;
                $video_label_data['label_id'] = $label_id;
                $video_label_data['label_name'] = $value;
                $video_label_data['add_time'] = date("Y-m-d H:i:s");
                $this->videoLabelRepositories->InsertVideoLabel($video_label_data);

            }
        }
        $this->usersRepositories->IncrUsersDetailNum($user_id, 'upload_num');
        return ['code'=>200, 'msg'=>'上传成功'];

    }

    /**
     * 获取标签数据
     * @param $request
     * @return array
     */
    public function VideoLabelData($request)
    {
        $label_name = $request->input('label_name');
        $search_arr = [];
        $search_arr['label_name'] = $label_name;
        $label_data = $this->videoLabelRepositories->GetVideoLabelData($search_arr);

        if(empty($label_data['data'])) {
            return ['code'=>200, 'data'=>[]];
        }
        $data = ['code'=>200];
        foreach($label_data['data'] as $key=>$value){
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
            $video_data['is_follow'] = isset($follows_ids[$value->user_id]) ? 1 : 0;
            $data['data']['video_data'][] = $video_data;
        }
        unset($label_data['data']);
        $data['data']['page'] = $label_data;

        return $data;
    }

    /**
     * @return array
     */
    public function LabelList()
    {
        $config_data = $this->labelConfigRepositories->GetLabelConfigByType(LabelConfigRepositories::LABEL_HOT_TYPE);

        if(empty($config_data)) {
            return ['code'=>200, 'data'=>[]];
        }

        $data = [];
        $data['code'] = 200;
        foreach($config_data as $key=>$value){
            $label['label_id'] = $value->id;
            $label['label_name'] = $value->label_name;
            $label['sort'] = $value->sort;
            $data['data']['labels'][] = $label;
        }

        return $data;
    }


    public function ShareVideo($request)
    {
        $video_id = $request->input('video_id');

        $user_id = Auth::id();
        $user_data = $this->usersRepositories->getUserInfoById($user_id);
        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $video_data = $this->videoRepositories->getVideoById($video_id);
        if(empty($video_data)){
            return ['code'=>-1, 'msg'=>'视频不存在'];
        }

        $data = ['code'=>200, 'data'=>[]];

        $share_info['title'] = '熬过了年少轻狂';
        $share_info['video_image'] = $video_data->video_image;
        $share_info['qrcode'] = env('QRCODE_URL') . $user_data->popular_num . '.png';
        $share_info['download_url'] = env('UPLOAD_APP_URL')  . $user_data->popular_num;
        $share_info['share_url'] =  '网红不雅被流出？精品资源无处寻？一切尽在宅男短视频(十万资源在线看)，请复制链接在浏览器中打开! ' . env('UPLOAD_APP_URL') . $user_data->popular_num;

        $data['data']['share_info'] = $share_info;
        return $data;

    }

}