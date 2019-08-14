<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class VideoRepositories
{

    protected $table_name = 'video_list';
    protected $tempDataRepositories;
    public function __construct(TempDataRepositories $tempDataRepositories)
    {
        $this->tempDataRepositories = $tempDataRepositories;
    }

    public function InsertVideo($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }
    /**
     *
     * @param $view_max_id
     * @return array
     */
    public function getViewVideoData($user_id)
    {
        $temp_data = $this->tempDataRepositories->GetValue($user_id, 'view_max_id');
        $view_max_id = 0;

        if(!empty($temp_data)){
            $view_max_id = $temp_data->temp_value;
        }

        $max_id = $this->GetMaxVideoId();

        if($view_max_id >= $max_id){
            $view_max_id = 0;
            $this->tempDataRepositories->ClearValue($user_id, 'view_max_id');
        }

        $video_data = DB::selectOne('select video_list.*, users_detail.avatar from video_list left JOIN users_detail ON video_list.user_id=users_detail.id WHERE video_list.id > ? AND is_check=1 limit 1', [$view_max_id]);
        if(empty($video_data)){
            return [];
        }

        return $video_data;
    }

    /**
     * 获取视频目前最大的id
     * @return int
     */
    public function GetMaxVideoId()
    {
        $result = DB::selectOne('select MAX(id) as max_id from video_list');
        if(empty($result)){
            return 0;
        }

        return $result->max_id;
    }

    public function getVideoById($video_id)
    {
        $video_row = DB::table('video_list')->find($video_id);
        if(empty($video_row)){
            return false;
        }

        return $video_row;
    }
    /**
     * 更新喜欢数量
     * @param $video_id
     * @param int $num
     * @return bool
     */
    public function IncrVideoFavoriteNum($video_id, $num = 1)
    {
        $video_row = DB::table('video_list')->find($video_id);

        if(!empty($video_row)){
            DB::table('video_list')->increment('favorite_number', $num);
        }

        return true;

    }
}