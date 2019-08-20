<?php
namespace App\Service;

use App\Repositories\MessageRepositories;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    protected $messageRepositories;

    public function __construct(MessageRepositories $messageRepositories)
    {
        $this->messageRepositories = $messageRepositories;
    }

    /**
     *发送聊天信息
     * @param $request
     */
    public function SendChatMessage($request)
    {
        $room_id = $request->input('room_id');
        $receive_id = $request->input('receive_id');
        $message = $request->input('message');
        $user_id = Auth::id();
        $message_data['message_type'] = MessageRepositories::MESSAGE_TYPE_CHAT;
        $message_data['message'] = $message;
        $message_data['send_id'] = $user_id;
        $message_data['receive_id'] = $receive_id;
        $message_data['send_time'] = time();
        $message_data['add_time'] = date('Y-m-d H:i:s');
        $this->messageRepositories->InsertMessage($message_data);

        return ['code'>200, 'msg'=>'发送成功'];

    }
    /**
     * 获取关注列表信息
     */
    public function GetMessageData($request)
    {
        $message_type = $request->input('message_type');

        if(!empty($message_type)){
            $data['message_type'] = $message_type;
        }

        $user_id = Auth::id();
        $data['receive_id'] = $user_id;
        $message_data = $this->messageRepositories->GetMyMessages($data);

        if(empty($message_data['data'])) {
            return ['code'=>200, 'data'=>[]];
        }
        $data = ['code'=>200];

        foreach ($message_data['data'] as $value){

            $temp_data['user_info']['user_id'] = $value->user_id;
            $temp_data['user_info']['username'] = $value->username;
            $temp_data['user_info']['vip_level'] = $value->vip_level;
            $temp_data['user_info']['avatar'] = $value->avatar;
            $temp_data['user_info']['sex'] = $value->sex;

            if($value->message_type == MessageRepositories::MESSAGE_TYPE_FOLLOW) {
                $temp_data['user_info']['is_follow'] = 1;
            }

            $temp_data['message_info']['message_id'] = $value->message_id;
            $temp_data['message_info']['message_type'] = $value->message_type;
            $temp_data['message_info']['message'] = $value->message;
            $temp_data['message_info']['send_time'] = $value->send_time;
            $data['data']['messages'][] = $temp_data;
        }

        unset($message_data['data']);
        $data['data']['page'] = $message_data;

        return $data;

    }
}