<?php
namespace app\Service;

use App\Repositories\MessageRepositories;

class HotspotService
{
    protected $messageRepositories;
    
    public function __construct(MessageRepositories $messageRepositories)
    {
        $this->messageRepositories = $messageRepositories;
    }
}