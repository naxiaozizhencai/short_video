<?php
namespace App\Repositories;
use App\models\UserModel;
use Illuminate\Support\Facades\DB;
class UsersRepositories
{

    protected $users_table_name = 'users';
    protected $users_detail_table_name = 'users_detail';
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

    public function GetUsersList($search_arr)
    {
        $query = DB::table('users')->leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->select(['users.*','users_detail.*']);

        return  $query->where(function ($query) use ($search_arr){
            foreach($search_arr as $key=>$search){
                switch ($key){
                    case 'username':
                        $query->where('username', 'like', '%'.$search.'%');
                        break;
                }
            }
        })->paginate()->toarray();
    }

    /**
     * 更新用户的vip过期时间
     * @param $uid
     * @param $amount
     * @return int
     */
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
    public function GetUserInfoByCondition($search_arr)
    {

        $query = DB::table('users')->leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->select(['users.*','users_detail.*']);

        return $query->where(function ($query) use ($search_arr){
            foreach($search_arr as $key=>$search){
                switch ($key){
                    case 'username':
                        $query->where('username', 'like', '%'.$search.'%');
                        break;
                        case 'popular_num':
                        $query->where('users_detail.popular_num', '=', $search);
                        break;
                        case 'uuid':
                        $query->where('users.uuid', '=', $search);
                        break;
                }
            }
        })->first();

    }

    public function GetUserInfoByPhone($phone)
    {
        return $users = DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
        select(['users.*','users_detail.*'])->where(['users.phone'=>$phone])->first();
    }


    public function GetUserInfoByPhonePasswd($phone, $passwd)
    {
        return $users = DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
        select(['users.*','users_detail.*'])->where(['users.phone'=>$phone, 'users.password'=>$passwd])->first();
    }

    /**
     * 更新某一列的数据
     * @param $uid
     * @param $column
     * @param int $amount
     * @return int
     */
    public function IncrUsersDetailNum($uid, $column, $amount = 1)
    {
        return DB::table('users_detail')->where('user_id', '=', $uid)->increment($column, $amount);
    }

    public function DecrUsersDetailNum($uid, $column, $amount = 1)
    {
        return DB::table('users_detail')->where('user_id', '=', $uid)->decrement($column, $amount);
    }

    /**
     * 获取邀请排行
     */
    public function GetInvitationRankData()
    {
        return DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->where('users.detail.invitation_num', '>', 0)->
        select(['users.*','users_detail.*'])->orderBy('invitation_num', 'desc')->limit(50)->get();
    }

    /**
     * 获取邀请排行
     */
    public function GetUploadRankData()
    {
        return DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
        select(['users.*','users_detail.*'])->orderBy('invitation_num', 'desc')->limit(50)->get();
    }

    /**
     * 获取邀请排行
     */
    public function GetSupportRankData()
    {
        return DB::table('users')->
        leftJoin('users_detail', 'users.id', '=', 'users_detail.user_id')->
        select(['users.*','users_detail.*'])->orderBy('support_num', 'desc')->limit(50)->get();
    }


    /**
     * 初始化用户
     * @param $uuid
     * @return bool
     */
    public function InsertUser($user_data)
    {

        return DB::table("users")->insertGetId($user_data);
    }

    public function InsertUserDetail($user_detail)
    {
        return DB::table('users_detail')->insertGetId($user_detail);
    }

    /**
     * @param $uid
     * @param $update_data
     * @return int
     */
    public function UpdateUserById($uid, $update_data)
    {
        return DB::table($this->users_table_name)->where('id', '=', $uid)->update($update_data);
    }

    /**
     * @param $uid
     * @param $update_data
     * @return int
     */
    public function UpdateUsersInfo($uid, $update_data)
    {
        return DB::table($this->users_detail_table_name)->where('user_id', '=', $uid)->update($update_data);
    }



}