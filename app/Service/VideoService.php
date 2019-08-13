<?php
namespace App\Service;
use App\Repositories\DiscussRepositories;
use App\Repositories\FavoriteRepositories;
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
    protected $replyRepositories;
    public function __construct(VideoRepositories $videoRepositories,
                                TempDataRepositories $tempDataRepositories,
                                FavoriteRepositories $favoriteRepositories, DiscussRepositories $discussRepositories,
                                ReplyRepositories $replyRepositories
)
    {
        $this->videoRepositories = $videoRepositories;
        $this->tempDataRepositories = $tempDataRepositories;
        $this->favoriteRepositories = $favoriteRepositories;
        $this->discussRepositories = $discussRepositories;
        $this->replyRepositories = $replyRepositories;
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
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }

        $discuss_data['video_id'] = $video_id;
        $discuss_data['content'] = $content;
        $discuss_data['from_uid'] = Auth::id();
        $discuss_data['favorite_number'] = 0;
        $discuss_data['discuss_time'] = time();
        $discuss_data['add_time'] = date("y-m-d H:i:s");
        $this->discussRepositories->InsertDiscuss($discuss_data);

        return $data = ['code'=>200, 'msg'=>'评论成功'];
    }

    public function AddReply($request)
    {
        $video_row = $this->videoRepositories->getVideoById($request->input('video_id'));

        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }
        $reply_id = $request->input('reply_id', $request->input('discuss_id'));
        $reply_id = ($reply_id == 0) ? $request->input('discuss_id') : $reply_id;
        $reply['discuss_id'] = $request->input('discuss_id');
        $reply['reply_id'] = $reply_id;
        $reply['to_uid'] = $request->input('video_id');
        $reply['content'] = $request->input('content');
        $reply['from_uid'] = Auth::id();
        $reply['favorite_number'] = 0;
        $reply['reply_time'] = time();
        $reply['add_time'] = date("Y-m-d H:i:s");
        $this->replyRepositories->InsertReply($reply);

        return $data = ['code'=>200, 'msg'=>'回复成功'];

    }
    public function getDiscussList($video_id)
    {
        $video_row = $this->videoRepositories->getVideoById($video_id);
        if(empty($video_row)){
            return $data = ['code'=>-1, 'msg'=>'视频数据不存在'];
        }


    }



}