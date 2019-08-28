<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class VideoRankRepositories
{

    const DAY_RANK_TYPE = 1;
    protected $table_name = 'video_rank';

    /**
     * 更新或者插入
     * @param $attributes
     * @param $values
     * @return bool
     */
    public function UpdateOrInsert($attributes, $values)
    {
        return DB::table($this->table_name)->updateOrInsert($attributes, $values);
    }

    /**
     *
     * @param $condition
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function GetVideoRankData($condition)
    {
        return DB::table($this->table_name)->where($condition)->first();
    }

    /**
     *
     * @param $data
     * @return mixed
     */
    public function GetVideoRankList($data)
    {

        return DB::table("video_rank")->leftjoin('video_list', 'video_rank.rank_video_id', '=', 'video_list.id')
            ->leftJoin('users', 'users.id', '=', 'video_list.user_id')
            ->leftJoin('users_detail', 'users_detail.user_id', '=', 'users.id')
            ->where($data)->where('video_list.is_check', '=', 1)->orderby('video_rank.rank_num', 'desc')
            ->paginate(6, ['video_list.id as video_id', 'video_list.*', 'users.*', 'users_detail.*'])->toarray();
    }


}