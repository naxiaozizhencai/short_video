<?php
namespace App\Repositories;

class UsersDetailRepositories
{
    /**
     * 获取用户信息
     * @param $uuid
     * @return mixed
     */
    public function GetUserDetailByUid($user_id)
    {
        $result = app('db')->selectone("SELECT * FROM users_detail WHERE user_id=?", [$user_id]);
        return $result;
    }



}