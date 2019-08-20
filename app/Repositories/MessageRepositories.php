<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MessageRepositories
{

    protected $table_name = 'video_message';

    const MESSAGE_TYPE_CHAT = 1;//聊天消息类型
    const MESSAGE_TYPE_SUPPORT = 2;//爱心支持类型
    const MESSAGE_TYPE_DISCUSS = 3;//评论消息类型
    const MESSAGE_TYPE_FOLLOW= 4;//关注消息类型


    /**
     * 插入消息数据
     * @param $data
     * @return int
     */
    public function InsertMessage($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }
}