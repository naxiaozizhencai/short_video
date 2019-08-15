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
     * @param $uid
     * @param $page
     * @return mixed
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
        $video_row = DB::table($this->table_name)->find($video_id);
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
    public function IncrVideoNum($video_id, $column, $num = 1)
    {
        return DB::table('video_list')->where(['id'=>$video_id])->increment($column, $num);
    }

    /**
     * @param $video_id
     * @param $column
     * @param int $num
     * @return int
     */
    public function DecrVideoNum($video_id, $column, $num = 1)
    {
        return DB::table('video_list')->where(['id'=>$video_id])->decrement($column, $num);
    }



    /**
     * 获取支持最多的额
     * @return mixed
     */
    public function GetVideoSupportRankData()
    {
        return DB::table($this->table_name)->where('favorite_num', '>', 0)->
        orderBy('favorite_num', 'desc')->paginate(20)->toarray();
    }

    /**
     * 获取喜欢的视频列表
     * @param $user_id
     * @return mixed
     */
    public function GetFavoriteVideoList($user_id, $page_size = 6)
    {
        return DB::table('video_favorite_list')->leftjoin('video_list', 'video_favorite_list.video_id', '=', 'video_list.id')->
        where('video_favorite_list.user_id', '=', $user_id)->orderby('video_favorite_list.add_time', 'desc')->paginate($page_size)->toarray();
    }

    /**
     * 获取我的作品列表
     * @param $user_id
     * @return mixed
     */
    public function GetUsersVideoList($user_id, $page_size = 6)
    {
        return DB::table('video_list')->where(['is_check'=>1, 'user_id'=>$user_id])
            ->orderBy('add_time', 'desc')->paginate($page_size)->toarray();
    }

}