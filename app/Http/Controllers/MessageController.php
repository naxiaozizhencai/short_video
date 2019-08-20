<?php
namespace App\Http\Controllers;


use App\Service\MessageService;
use http\Env\Response;

class MessageController extends Controller
{

    protected $messageService;

    public function __construct(MessageService $msgService)
    {
        $this->messageService = $msgService;
    }

    /**
     * 聊天发送消息
     */
    public function ChatSendMessage()
    {

    }

    /**
     * 聊天列表
     */
    public function ChatList()
    {

    }

    public function GetChatMessageList()
    {

    }


    /**
     * 点赞消息列表
     */
    public function SupportMessageList()
    {

    }

    /**
     * 讨论消息列表
     */
    public function DiscussMessageList()
    {

    }

    /**
     * 关注消息列表
     */
    public function FollowMessageList()
    {

        $data = $this->messageService->GetFollowMessageData();

        return response()->json($data);
    }

    /**
     * 公告消息列表
     */
    public function NoticeMessageList()
    {

    }


}
