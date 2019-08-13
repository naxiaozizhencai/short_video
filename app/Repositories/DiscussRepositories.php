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
}