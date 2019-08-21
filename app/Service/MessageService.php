<?php
namespace App\Service;

use App\Repositories\MessageRepositories;
use App\Repositories\UsersRepositories;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    protected $messageRepositories;
    protected $usersRepositories;

    public function __construct(MessageRepositories $messageRepositories, UsersRepositories $usersRepositories)
    {
        $this->messageRepositories = $messageRepositories;
        $this->usersRepositories = $usersRepositories;
    }

    /**
     * @param $sender_info
     * @param $receiver_info
     * @return string
     */
    public function MakeRoomId($sender_info, $receiver_info)
    {

        if($sender_info->id > $receiver_info->id){
            $room_id = md5($sender_info->uuid . $receiver_info->uuid);
        }else{
            $room_id = md5($receiver_info->uuid . $sender_info->uuid);
        }

        return $room_id;
    }
    /**
     *发送聊天信息
     * @param $request
     */
    public function SendChatMessage($request)
    {

        $receive_id = $request->input('receive_id');
        $message = $request->input('message');
        $user_id = Auth::id();

        if(empty($message) || empty($receive_id)){
            return ['code'>-1, 'msg'=>'参数不能为空'];
        }

        $sender_user_data = $this->usersRepositories->getUserInfoById($user_id);

        if(empty($sender_user_data)){
            return ['code'>-1, 'msg'=>'用户不存在'];
        }

        $receiver_user_data = $this->usersRepositories->getUserInfoById($receive_id);

        if(empty($receiver_user_data)){
            return ['code'>-1, 'msg'=>'用户不存在'];
        }

        $room_id = $this->MakeRoomId($sender_user_data, $receiver_user_data);


        $message_data['room_id'] = $room_id;
        $message_data['message_type'] = MessageRepositories::MESSAGE_TYPE_CHAT;
        $message_data['message'] = $message;
        $message_data['send_id'] = $user_id;
        $message_data['receive_id'] = $receive_id;
        $message_data['send_time'] = time();
        $message_data['add_time'] = date('Y-m-d H:i:s');
        $this->messageRepositories->InsertMessage($message_data);
        $data = ['code'=>200, 'msg'=>'发送成功'];

        return $data;

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