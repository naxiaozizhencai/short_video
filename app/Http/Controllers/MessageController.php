<?php
namespace App\Http\Controllers;
use App\Service\MessageService;
use Illuminate\Http\Request;

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
    public function SendChatMessage(Request $request)
    {
        $data = $this->messageService->SendChatMessage($request);
        return response()->json($data);
    }



    public function GetChatMessageList()
    {

    }

    /**
     * 聊天列表
     */
    public function ChatList()
    {

    }

    /**
     * 讨论消息列表
     */
    public function MessageList(Request $request)
    {
        $data = $this->messageService->GetMessageData($request);
        return response()->json($data);
    }


    /**
     * 公告消息列表
     */
    public function NoticeMessageList()
    {

    }


}
