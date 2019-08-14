<?php
namespace App\Repositories;
use App\models\UserModel;
use Illuminate\Support\Facades\DB;
class UsersRepositories
{

    protected $users_table_name = 'users';
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

    /**
     * 返回用户信息
     * @param $uid
     * @return array
     */
    public function getUserInfoById($uid)
    {
       return  $users = DB::table('users')->
       leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
       select(['users.*','users_detail.*'])->where(['users.id'=>$uid])->first();

    }

    public function UpdateVipTime($uid, $amount)
    {
        $user_data = $this->getUserInfoById($uid);

        if($user_data->vip_expired_time > time()) {
            $vip_expired_time = $user_data->vip_expired_time + $amount;
        }else{
            $vip_expired_time = time() + $amount;
        }

        return DB::table($this->users_table_name)->where('id', '=', $uid)->update(['vip_expired_time'=>$vip_expired_time]);
    }

    /**
     * 通过条件获取用户信息
     * @param $condition
     * @return \Illuminate\Support\Collection
     */
    public function GetUserInfoByCondition($popular_num)
    {
        return $users = DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
        select(['users.*','users_detail.*'])->where(['users_detail.popular_num'=>$popular_num])->first();
    }

    public function IncrUsersDetailNum($uid, $column, $amount = 1)
    {
        return DB::table('users_detail')->where('user_id', '=', $uid)->increment($column, $amount);
    }
    /**
     * 初始化用户
     * @param $uuid
     * @return bool
     */
    public function InsertUser($uuid)
    {


        $userId = DB::table("users")->insertGetId(
            [

                'uuid'=>$uuid,
                'username'=>'游客账号_' . rand(100000000, 9999999999),
                'vip_level'=>0,
                'is_phone_login'=>0,
                'add_time'=>date('Y-m-d H:i:s'),
            ]
        );

        if($userId > 0){
            $userDetailId = DB::table('users_detail')->insertGetId(
                [
                    'avatar'=>'a.png',
                    'user_id' =>$userId,
                    'avatar'=>'a.png',
                    'city'=>'深圳',
                    'popular_num'=>randomString(),
                    'add_time'=>date('Y-m-d H:i:s'),
                ]
            );
        }

        return true;

    }
}