<?php
namespace App\Service;
use App\Repositories\UsersRepositories;
class UsersService
{

    public $UsersRepositories;
    public function __construct(UsersRepositories $UsersRepositories)
    {
        $this->UsersRepositories = $UsersRepositories;
    }

    /**
     * 匿名登录
     * @param $uuid
     * @return array
     */
    public function Login($uuid)
    {
        $userData = $this->UsersRepositories->GetUserDataByUuid($uuid);
        //需要初始化数据
        if(empty($userData)){

        }

        
        print_r($userData);
        exit;
    }

    /**
     * 初始化数据
     */
    public function InitUserData()
    {

    }

}