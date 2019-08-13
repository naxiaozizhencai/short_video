<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class DiscussRepositories
{

    protected $table_name = 'video_discuss';

    /**
     * 添加评论
     * @param $data
     */
    public function InsertDiscuss($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }

    /**
     * 获取评论列表
     * @param $video_id
     * @return array
     */
    public function getDiscussList($video_id)
    {
        return DB::table($this->table_name)->
        where('video_id' ,'=',$video_id)->orderBy('discuss_time', 'desc')->paginate(5)->toarray();

    }
}