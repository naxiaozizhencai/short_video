<?php
namespace App\Service;
use App\Repositories\UsersDetailRepositories;
use App\Repositories\UsersRepositories;
use Illuminate\Support\Facades\Auth;
class UsersService
{

    public $UsersRepositories;
    public function __construct(UsersRepositories $UsersRepositories, UsersDetailRepositories $usersDetailRepositories)
    {
        $this->UsersRepositories = $UsersRepositories;
        $this->UsersDetailRepositories = $usersDetailRepositories;
    }

    /**
     * 匿名登录
     * @param $uuid
     * @return array
     */
    public function Login($uuid)
    {

        $userData = $this->UsersRepositories->GetUserDataByUuid($uuid);

        if(empty($userData)){
            $this->UsersRepositories->InsertUser($uuid);
            $userData = $this->UsersRepositories->GetUserDataByUuid($uuid);
        }

        $resultData = ['code'=>200, 'data'=>[]];
        $data['user_id'] = $userData->id;
        $data['uuid'] = $userData->uuid;
        $data['vip_level'] = $userData->vip_level;
        $data['is_phone_login'] = $userData->is_phone_login;
        $data['vip_expired_time'] = $userData->vip_expired_time;
        $userDetail = $this->UsersDetailRepositories->GetUserDetailByUid($userData->id);
        $data['coin_num'] = 0;
        $data['viewed_times'] = 0;
        $data['total_viewed_times'] = 10;

        if(!empty($userDetail)){
            $data['coin_num'] = $userDetail['coin_num'];
            $data['viewed_times'] = $userDetail['coin_num'];
        }


        $user_info = $this->UsersRepositories->GetAuthUserData($uuid);
        $token_data = [];
        if (!$token = Auth::login($user_info, true)) {
            $resultData['code']     = 5000;
            $resultData['errorMsg'] = '系统错误，无法生成令牌';
        } else {
            $token_data['user_id']      = strval($user_info->id);
            $token_data['access_token'] = $token;
            $token_data['expires_in']   = strval(time() + 86400);
        }

        $resultData['data']['user_data'] = $data;
        $resultData['data']['token_data'] = $token_data;
        return $resultData;
    }

}