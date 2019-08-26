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

    public function getVideoById($video_id)
    {
        return DB::table($this->table_name)->find($video_id);
    }
    /**
     * 获取视频数据
     * @param $search_arr
     * @return mixed
     */
    public function GetVideoData($search_arr)
    {
        $query = DB::table('video_list');
        $query->where(function ($query) use ($search_arr){
            foreach($search_arr as $key=>$search){
                switch ($key){
                    case 'video_title':
                        $query->where('video_list.video_title', 'like', '%'.$search.'%');
                        break;
                        case 'user_id':
                        $query->where('video_list.user_id', '=', $search);
                        break;
                    case 'is_recommend':
                        $query->where('video_list.is_recommend', '=', $search);
                        break;
                    case 'video_id':
                        $query->where('video_list.id', '=', $search);
                        break;
                }
            }
        })->where('is_check', '=', 1);

        if(!empty($search_arr['add_time'])){
            $query->orderby('video_list.add_time', $search_arr['add_time']);
        }

        if(!empty($search_arr['favorite_num'])){
            $query->orderby('video_list.favorite_num', $search_arr['add_time']);
        }

        if(!empty($search_arr['favorite_num'])){
            $query->orderby('video_list.favorite_num', $search_arr['favorite_num']);
        }

        if(!empty($search_arr['reply_num'])){
            $query->orderby('video_list.reply_num', $search_arr['reply_num']);
        }

        if(!empty($search_arr['play_num'])){
            $query->orderby('video_list.play_num', $search_arr['play_num']);
        }
        $query->leftjoin('users', 'video_list.user_id', '=', 'users.id')->leftjoin('users_detail', 'users.id', '=', 'users_detail.user_id');

        return $query->paginate(6, ['video_list.id as video_id', 'video_list.*', 'users.*', 'users_detail.*'])->toarray();
    }
    /**
     * @param $uid
     * @param $page
     * @return mixed
     */
    public function getViewVideoData($uid, $page)
    {
        return DB::table('video_list')->where(['is_check'=>1])
            ->orderBy('add_time', 'desc')->paginate(6, ['*'], 'page', $page)->toarray();

    }

    /**
     * 获取随机观看视频
     * @param $uid
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function GetFollowVideoData($uid)
    {

        return DB::table("users_fans")->leftjoin('video_list', 'users_fans.fans_id', '=', 'video_list.user_id')
            ->where('users_fans.user_id', '=', $uid)->where('video_list.is_check', '=', 1)->orderby('video_list.add_time', 'desc')
            ->paginate(6, ['video_list.*'])->toarray();
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
     * 获取喜欢的视频列表
     * @param $user_id
     * @return mixed
     */
    public function GetFavoriteVideoList($user_id, $page_size = 6)
    {
        return DB::table('video_favorite_list')->leftjoin('video_list', 'video_favorite_list.video_id', '=', 'video_list.id')->
        where('video_favorite_list.user_id', '=', $user_id)->orderby('video_favorite_list.add_time', 'desc')->paginate($page_size)->toarray();
    }



}