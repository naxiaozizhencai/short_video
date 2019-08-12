<?php
namespace App\Repositories;
use App\models\UserModel;
class UsersRepositories
{

    /**
     * 获取用户信息
     * @param $uuid
     * @return mixed
     */
    public function GetUserDataByUuid($uuid)
    {
        $result = app('db')->selectone("SELECT * FROM users WHERE uuid=?", [$uuid]);
        return $result;
    }

    public function GetAuthUserData($uuid)
    {
        $user_model = new UserModel();
        $user_info = $user_model->where('uuid', '=', $uuid)->first();
        return $user_info;
    }
}