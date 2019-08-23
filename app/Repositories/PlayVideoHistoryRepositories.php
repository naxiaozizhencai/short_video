<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PlayVideoHistoryRepositories
{

    protected $table_name = 'play_video_history';

    /**
     * @param $data
     * @return int
     */
    public function InsertPlayVideoHistory($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    /**
     * @param $user_id
     * @param $video_id
     */
    public function ExistHistory($user_id, $video_id)
    {
        return DB::table($this->table_name)->where(['user_id'=>$user_id, 'video_id'=>$video_id])->exists();
    }
}