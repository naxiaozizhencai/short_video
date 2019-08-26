<?php
namespace App\Service;
use App\Repositories\DiscussReportRepositories;
use App\Repositories\DiscussRepositories;
use App\Repositories\FavoriteRepositories;
use App\Repositories\MessageRepositories;
use App\Repositories\PlayVideoHistoryRepositories;
use App\Repositories\ReplyRepositories;
use App\Repositories\TempDataRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRepositories;
use Illuminate\Support\Facades\Auth;

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

    public function __construct(VideoRepositories $videoRepositories,
                                TempDataRepositories $tempDataRepositories,
                                FavoriteRepositories $favoriteRepositories, DiscussRepositories $discussRepositories,
                                UsersRepositories $usersRepositories, DiscussReportRepositories $discussReportRepositories,
                                MessageRepositories $messageRepositories, PlayVideoHistoryRepositories $playVideoHistoryRepositories
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
    }

    /**
     * 观看视频
     * @param $request
     * @return array
     */
    public function ViewVideo($request)
    {
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
            $video_data['favorite_number'] = $value->favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $video_data['is_follow'] = 0;
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
        $user_id = Auth::id();
        $video_data = $this->videoRepositories->getVideoById($video_id);

        if(empty($video_data)){
            return ['code'=>-1, 'msg'=>'視頻數據不存在'];
        }


        if(!$result = $this->playVideoHistoryRepositories->ExistHistory($user_id, $video_id)){

            $play_video_times = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES);
            $update_temp_data['temp_value'] = (empty($play_video_times)) ? 1 : $play_video_times->temp_value + 1;
            $this->tempDataRepositories->UpdateTempValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES, $update_temp_data);
            $history_data['user_id'] = $user_id;
            $history_data['video_id'] = $video_id;
            $history_data['add_time'] = date("Y-m-d H:i:s");
            $this->playVideoHistoryRepositories->InsertPlayVideoHistory($history_data);
        }

        $this->videoRepositories->IncrVideoNum($video_id, 'play_num', 1);
        return ['code'=>200, 'msg'=>'操作成功'];
    }

    /**
     *随机返回一个
     * @return array
     */
    public function RandViewVideo()
    {
        $uid = Auth::id();
        $page = app('request')->input('page', 0);

        $temp_data = $this->tempDataRepositories->GetValue($uid, 'view_max_id');

        if(!empty($temp_data) && empty($page)){
            $page = $temp_data->temp_value;
        }

        $result = $this->videoRepositories->getViewVideoData($uid, $page);

        if(empty($result)) {
            return ['code'=>-1, 'msg'=>'还未上传视频'];
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
        }

        if(!empty($temp_data)){
            if($temp_data->temp_value >= $result['total']){
                $this->tempDataRepositories->ClearValue($uid, 'view_max_id');
            }
        }

        $this->tempDataRepositories->UpdateValue($uid, 'view_max_id');
        $data['code'] = 200;
        $data['data']['video_data'] = $video_data;
        unset($result['data']);
        $data['data']['page'] = $result;

        return $data;
    }


    /**
     * 观看关注视频
     * @param $uid
     * @return array
     */
    public function FollowViewVideo()
    {

        $uid = Auth::id();
        $result = $this->videoRepositories->GetFollowVideoData($uid);

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
     * 格式化视频数据
     * @param $result
     * @return mixed
     */
    public function formatVideoData($result)
    {
        $video_data = [];
        foreach($result['data'] as $key=>$value){
            $video_data['video_id'] = $value->id;
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
        }

        $data['video_data'][] = $video_data;
        unset($result['data']);
        $data['page'] = $result;
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
        $msg_data['message'] = $user_data->username . '点赞了了你的视频';
        $msg_data['send_id'] = $user_id;
        $msg_data['receive_id'] = $video_row->user_id;
        $msg_data['send_time'] = time();
        $msg_data['add_time'] = date('Y-m-d H:i:s');
        $this->messageRepositories->InsertMessage($msg_data);
        $data = ['code'=>200, 'msg'=>'喜歡成功'];
        $this->usersRepositories->IncrUsersDetailNum($video_row->user_id, 'support_num', 1);
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
        $this->usersRepositories->DecrUsersDetailNum($video_row->user_id, 'support_num', 1);
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
        $msg_data['message'] = $user_data->username . '评论了你的视频';
        $msg_data['send_id'] = $user_id;
        $msg_data['receive_id'] = $video_row->user_id;
        $this->messageRepositories->InsertMessage($msg_data);

        if(!empty($parent_id)) {

            $parent_discuss_data = $this->discussRepositories->getDiscussById($parent_id);

            if(empty($parent_discuss_data)){
                return $data = ['code'=>-1, 'msg'=>'操作失败'];
            }

            $msg_data['message_type'] = MessageRepositories::MESSAGE_TYPE_DISCUSS;
            $msg_data['message'] = $user_data->username . '回复了你的评论';
            $msg_data['send_id'] = $user_id;
            $msg_data['receive_id'] = $parent_discuss_data->from_uid;
            $this->messageRepositories->InsertMessage($msg_data);

        }

        return $data = ['code'=>200, 'msg'=>'评论成功'];
    }

    /**
     * 喜歡這條評論
     */
    public function DoFavorDiscuss()
    {
        $discuss_id = app('request')->input('discuss_id');

        if(empty($discuss_id)) {
            return $data = ['code'=>-1, 'msg'=>'參數錯誤'];
        }

        $discuss_data = $this->discussRepositories->getDiscussById($discuss_id);

        if(empty($discuss_data)){
            return $data = ['code'=>-1, 'msg'=>'評論不存在'];
        }


        $this->discussRepositories->IncrDiscussfavorById($discuss_id);
        return $data = ['code'=>200, 'msg'=>'操作成功'];
    }

    /**
     * 喜歡這條評論
     */
    public function CancelFavorDiscuss()
    {
        $discuss_id = app('request')->input('discuss_id');

        if(empty($discuss_id)) {
            return $data = ['code'=>-1, 'msg'=>'參數錯誤'];
        }

        $discuss_data = $this->discussRepositories->getDiscussById($discuss_id);

        if(empty($discuss_data)){
            return $data = ['code'=>-1, 'msg'=>'評論不存在'];
        }

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
        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        $discuss_list = $this->discussRepositories->getDiscussList($video_id);

        $data = ['code'=>200, 'data'=>[]];

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
        $file__name = time().rand(0, 1000).'.'.$file->guessExtension();
        $file->move($dir, $file__name);
        $file_url = $upload_url.$file__name;
        return ['code'=>200, 'data'=>['id'=>rand(1,10000),'file_name'=>$file_url]];

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
        $video_label = app('request')->input('video_label');

        if(empty($video_image) || empty($video_label) || empty($video_label) || empty($video)){
            return ['code'=>-1, 'msg'=>'参数错误'];

        }

        $video_data = [];
        $user_id = Auth::id();
        $video_data['user_id'] = $user_id;
        $video_data['video_title'] = $title;
        $video_data['video_image'] = $video_image;
        $video_data['video_url'] = $video;
        $video_data['video_label'] = $video_label;
        $video_data['add_time'] = date('Y-m-d H:i:s');
        $this->videoRepositories->InsertVideo($video_data);
        $this->usersRepositories->IncrUsersDetailNum($user_id, 'upload_num');
        return ['code'=>200, 'msg'=>'上传成功'];

    }


}