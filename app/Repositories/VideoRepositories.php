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
     * @param $user_id
     * @return array|mixed
     */
    public function getViewVideoData($uid, $page)
    {
        return DB::table('video_list')->where(['is_check'=>1])
            ->orderBy('add_time', 'desc')->paginate(1, ['*'], 'page', $page)->toarray();

    }

    /**
     * 获取随机观看视频
     * @param $uid
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function GetFollowVideoData($uid, $page)
    {
        return DB::table("users_fans")->leftjoin('video_list', 'users_fans.fans_id', '=', 'video_list.user_id')
            ->where('users_fans.user_id', '=', $uid)->where('video_list.is_check', '=', 1)->orderby('video_list.add_time', 'desc')
            ->paginate(1, ['video_list.*',],'page', $page)->toarray();
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