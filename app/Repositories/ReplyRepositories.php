<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReplyRepositories
{
    protected $table_name = 'video_reply';

    public function InsertReply($reply_data)
    {
        return DB::table($this->table_name)->insertGetId($reply_data);
    }
}