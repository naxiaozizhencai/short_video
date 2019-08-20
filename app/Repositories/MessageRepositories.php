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

    /**
     * 获取消息
     * @param $data
     */
    public function GetMyMessages($data)
    {
        $query =  DB::table($this->table_name);

        return $query ->leftJoin('users', 'video_message.receive_id', '=', 'users.id')->
            leftJoin('users_detail', 'users_detail.user_id', '=', 'users.id')->
            where(function ($query) use ($data){
            foreach($data as $key=>$search){
                switch ($key){
                    case 'message_type':
                        $query->where('video_message.message_type', '=', $search);
                        break;
                    case 'receive_id':
                        $query->where('video_message.receive_id', '=', $search);
                        break;
                    case 'send_id':
                        $query->where('video_message.send_id', '=', $search);
                        break;
                }
            }
        })->orderBy('send_time', 'desc')->paginate(15,['video_message.*', 'users.*', 'users_detail.*'])->toarray();
    }
}