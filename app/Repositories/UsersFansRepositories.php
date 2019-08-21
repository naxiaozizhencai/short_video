<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class UsersFansRepositories
{

    protected $table_name = 'users_fans';

    /**
     * @param $data
     * @return int
     */
    public function InsertFans($data)
    {
        return DB::table($this->table_name)->insertGetId($data);
    }


    public function DeleteFans($uid, $fans_id)
    {
        return DB::table($this->table_name)->where(['user_id'=>$uid, 'fans_id'=>$fans_id])->delete();
    }

    public function GetUserFansByUidFanId($uid, $fans_id)
    {
        return DB::table($this->table_name)->where(['user_id'=>$uid, 'fans_id'=>$fans_id])->first();
    }

    /**
     * 获取用户粉丝
     * @param $uid
     * @return mixed
     */
    public function GetUsersFansList($user_id)
    {
        return DB::table($this->table_name)->leftjoin('users', 'users_fans.user_id', '=', 'users.id')->
        leftjoin('users_detail', 'users_detail.user_id', '=', 'users.id')->where('users_fans.fans_id', '=', $user_id)
            ->paginate(15, ['users.id', 'users.vip_level','users.username', 'users_detail.avatar'])->toarray();

    }

    /**
     * 获取用户关注列表
     * @param $uid
     * @return \Illuminate\Support\Collection
     */
    public function GetUsersFollowList($user_id)
    {

        return DB::table($this->table_name)->leftjoin('users', 'users_fans.fans_id', '=', 'users.id')->
        leftjoin('users_detail', 'users_detail.user_id', '=', 'users.id')->where('users_fans.user_id', '=', $user_id)->orderby('users_fans.add_time', 'desc')
            ->paginate(15, ['users.id', 'users.vip_level','users.username', 'users_detail.avatar'])->toarray();
    }

    /**
     * 获取用户已经关注的
     * @param $uid
     * @return \Illuminate\Support\Collection
     */
    public function GetUsersFollowData($uid)
    {
        return DB::table($this->table_name)->where(['user_id'=>$uid])->pluck('user_id', 'fans_id');
    }
}