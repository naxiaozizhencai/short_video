<?php
namespace App\Service;
use App\Repositories\TempDataRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRepositories;

class VideoService
{
    protected $videoRepositories;
    protected $tempDataRepositories;
    public function __construct(VideoRepositories $videoRepositories, TempDataRepositories $tempDataRepositories)
    {
        $this->videoRepositories = $videoRepositories;
        $this->tempDataRepositories = $tempDataRepositories;
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



}