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
       return  DB::selectOne("SELECT users.*, d .user_id, d .avatar, d .phone_number,d.sign,d.sex,d.birthday,d.age, d.city, d.fans_num, d.follow_num, d.support_num, d.invitation_num,
                                        d.upload_num, d.coin_num, d.popularize_number
                                        FROM `users` LEFT JOIN `users_detail` as d 
                                        ON `users`.`id` = d.`user_id`WHERE users.`id` = $uid");

    }

    public function UpdateVipTime($uid, $amount)
    {
        return DB::table($this->users_table_name)->where('id', '=', $uid)->increment('vip_expired_time', $amount);
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
                    'popularize_number'=>randomString(),
                    'add_time'=>date('Y-m-d H:i:s'),
                ]
            );
        }

        return true;

    }
}