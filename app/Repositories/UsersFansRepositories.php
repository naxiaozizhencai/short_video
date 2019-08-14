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


    public function GetUsersFans($uid)
    {
        return DB::table($this->table_name)->leftjoin('users', 'users_fans.fans_id', '=', 'users.id')->
            leftjoin('users_detail', 'users_detail.user_id', '=', 'users.id')->where('users_fans.user_id', '=', $uid)->get();
    }

    public function GetUsersFlowFans($uid)
    {
        return DB::table($this->table_name)->leftjoin('users', 'users_fans.user_id', '=', 'users.id')->
            leftjoin('users_detail', 'users_detail.user_id', '=', 'users.id')->where('users_fans.fans_id', '=', $uid)->get();
    }
}