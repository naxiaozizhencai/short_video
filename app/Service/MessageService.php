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
            return ['code'=>-1, 'msg'=>'参数不能为空'];
        }

        $sender_user_data = $this->usersRepositories->getUserInfoById($user_id);

        if(empty($sender_user_data)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
        }

        $receiver_user_data = $this->usersRepositories->getUserInfoById($receive_id);

        if(empty($receiver_user_data)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
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

    public function GetNoticeMessageData()
    {
        return ['code'=>200, 'data'=>['id'=>1, 'title'=>'我靠前端真的吊','notice_time'=>date('Y-m-d H:i:s'),'notice_message'=>'laravel-admin 是一个用于为Laravel提供后台界面的构建器，仅仅通过数行代码，就可以帮助我们构建CRUD后台。
能够快速生成数据表格和表单,不需要在界面上花太多时间,只需要专注入业务逻辑,大大减轻了UI的工作量。

第一步：安装laravel
使用composer安装或中文官网下载一键安装包,官网网址:http://laravelacademy.org/resources-download
composer安装使用命令如下：
composer create-project --prefer-dist laravel/laravel yourproject

第二步：安装laravel-admin及相关配置
a.使用composer安装,命令如下：
composer require encore/laravel-admin "1.4.*"

b.添加相关服务
在config/app.php文件中添加服务
Encore\Admin\Providers\AdminServiceProvider::class;

c.发布admin.php配置文件和相关assets
php artisan vendor:publish --tag=laravel-admin

d.生成配置文件admin.php,完成安装
php artisan admin:install
注意在运行该步骤命令之前,确保laravel中.env中数据库连接配置正确
github参考网址:https://github.com/z-song/laravel-admin

安装完成后,打开浏览器访问http://localhost/admin,输入用户名和密码登录
用户名:admin 密码:admin
登录后界面如下图所示']];
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

    /**
     * 获取和谁聊天列表
     */
    public function GetChatList()
    {
        $user_id = Auth::id();
        $chat_list = $this->messageRepositories->GetChatList($user_id);

        if(empty($chat_list['data'])){
            return ['code'=>200, 'data'=>[]];
        }
        $data = ['code'=>200];
        foreach ($chat_list['data'] as $key=>$value){
            $chat_user_id = ($value->send_id == $user_id) ? $value->receive_id : $value->send_id;
            $user_data = $this->usersRepositories->getUserInfoById($chat_user_id);

            $user_info['user_id'] = $user_data->id;
            $user_info['username'] = $user_data->username;
            $user_info['avatar'] = $user_data->avatar;
            $user_info['vip_level'] = $user_data->vip_level;
            $user_info['vip_expired_time'] = $user_data->vip_expired_time;
            $chat_info['room_id'] = $value->room_id;
            $chat_info['message'] = $value->message;
            $chat_info['send_time'] = $value->send_time;
            $data['data']['chat_list'][$key]['user_info'] = $user_info;
            $data['data']['chat_list'][$key]['chat_info'] = $chat_info;
        }

        return $data;

    }
}