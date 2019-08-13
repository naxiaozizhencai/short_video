<?php
namespace App\Service;
use App\Repositories\DiscussRepositories;
use App\Repositories\FavoriteRepositories;
use App\Repositories\ReplyRepositories;
use App\Repositories\TempDataRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoService
{

    protected $videoRepositories;
    protected $tempDataRepositories;
    protected $favoriteRepositories;
    protected $discussRepositories;
    protected $usersRepositories;
    public function __construct(VideoRepositories $videoRepositories,
                                TempDataRepositories $tempDataRepositories,
                                FavoriteRepositories $favoriteRepositories, DiscussRepositories $discussRepositories,
                                UsersRepositories $usersRepositories
)
    {
        $this->videoRepositories = $videoRepositories;
        $this->tempDataRepositories = $tempDataRepositories;
        $this->favoriteRepositories = $favoriteRepositories;
        $this->discussRepositories = $discussRepositories;
        $this->usersRepositories = $usersRepositories;
    }

    /**
     *随机返回一个
     * @return array
     */
    public function RandViewVideo($uid)
    {
        $result = $this->videoRepositories->getViewVideoData($uid);

        $data = ['code'=>200, 'data'=>[]];

        $video_data['avatar'] = '';
        $video_data['video_id'] = '';
        $video_data['video_user_id'] = '';
        $video_data['username'] = '';
        $video_data['video_title'] = '';
        $video_data['video_image'] = '';
        $video_data['video_url'] = '';
        $video_data['video_label'] = '';
        $video_data['favorite_number'] = '';
        $video_data['reply_number'] = '';

        $video_data['is_favorite'] = 0;

        if(!empty($result)){
            $video_data['video_id'] = $result->id;
            $video_data['avatar'] = $result->avatar;
            $video_data['video_user_id'] = $result->user_id;
            $video_data['username'] = '';
            $video_data['video_title'] = $result->video_title;
            $video_data['video_image'] = $result->video_image;
            $video_data['video_url'] = $result->video_url;
            $video_data['video_label'] = $result->video_label;
            $video_data['favorite_number'] = $result->favorite_number;
            $video_data['reply_number'] = $result->reply_number;
        }

        $this->tempDataRepositories->UpdateValue($uid, 'view_max_id');
        $data['data']['video_data'] = $video_data;
        return $data;
    }

    /**
     * 点击爱心
     * @param $video_id
     * @param $user_id
     * @return array
     */
    public function DoFavorite($video_id, $user_id)
    {

        $data = ['code'=>200, 'data'=>[]];

        $video_row = $this->videoRepositories->getVideoById($video_id);
        if(empty($video_row)){
            return $data = ['code'=>-1, 'errMsg'=>'视频数据不存在'];
        }
        $favor_row = $this->favoriteRepositories->FindFavoriteRow($user_id, $video_id);
        $find_data['user_id'] = $user_id;
        $find_data['video_id'] = $video_id;
        $update_data = $find_data;

        if(empty($favor_row)){
            $update_data['status'] = 1;
        }else{
            $update_data['status'] = ($favor_row->status == 1) ? 0 : 1;
        }
        $favor_num = ($update_data['status'] == 1) ? 1 : -1;
        $update_data['add_time'] = date("Y-m-d H:i:s");
        $this->favoriteRepositories->UpdateFavoriteVideo($find_data, $update_data);
        $this->videoRepositories->IncrVideoFavoriteNum($video_id, $favor_num);

        $favor_data['favorite_num'] = $video_row->favorite_number + $favor_num;
        $favor_data['video_id'] = $video_id;
        $favor_data['status'] = $update_data['status'];
        $data['data']['video_data'] = $favor_data;

        return $data;
    }

    /**
     * @param $video_id
     * @param $content
     */
    public function AddDiscuss($video_id, $content)
    {
        $video_row = $this->videoRepositories->getVideoById($video_id);

        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        if(empty($content)){
            return $data = ['code'=>-1, 'msg'=>'評論内容不能爲空'];
        }
        $parent_id = app('request')->input('parent_id', 0);
        $discuss_data['parent_id'] = $parent_id;
        $discuss_data['video_id'] = $video_id;
        $discuss_data['content'] = $content;
        $discuss_data['from_uid'] = Auth::id();
        $discuss_data['favorite_number'] = 0;
        $discuss_data['discuss_time'] = time();
        $discuss_data['add_time'] = date("y-m-d H:i:s");
        $this->discussRepositories->InsertDiscuss($discuss_data);

        return $data = ['code'=>200, 'msg'=>'评论成功'];
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



}