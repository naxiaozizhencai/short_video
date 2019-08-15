<?php
namespace App\Service;
use App\Repositories\PopularListRepositories;
use App\Repositories\UsersDetailRepositories;
use App\Repositories\UsersFansRepositories;
use App\Repositories\UsersRepositories;
use Illuminate\Support\Facades\Auth;
class UsersService
{

    public $UsersRepositories;
    protected $popularListRepositories;
    protected $fansRepositories;
    public function __construct(UsersRepositories $UsersRepositories,
                                UsersDetailRepositories $usersDetailRepositories,
                                PopularListRepositories $popularListRepositories,UsersFansRepositories $fansRepositories)
    {
        $this->UsersRepositories = $UsersRepositories;
        $this->UsersDetailRepositories = $usersDetailRepositories;
        $this->popularListRepositories = $popularListRepositories;
        $this->fansRepositories = $fansRepositories;
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
            $data['coin_num'] = $userDetail->coin_num;
            $data['viewed_times'] = $userDetail->coin_num;
        }


        $user_info = $this->UsersRepositories->GetAuthUserData($uuid);
        $token_data = [];
        if (!$token = Auth::login($user_info, true)) {
            $resultData['code']     = 5000;
            $resultData['msg'] = '系统错误，无法生成令牌';
        } else {
            $token_data['user_id']      = strval($user_info->id);
            $token_data['access_token'] = $token;
            $token_data['expires_in']   = strval(time() + 86400);
        }

        $resultData['data']['user_data'] = $data;
        $resultData['data']['token_data'] = $token_data;
        return $resultData;
    }

    /**
     * 填写推广吗
     */
    public function AddPopularNum()
    {
        $popular_num = app('request')->input('popular_num');
        $return_data = [];

        if(empty($popular_num)){
            return ['code'=>-1, 'msg'=>'推广码不存在'];
        }

        $user_data = $this->UsersRepositories->GetUserInfoByCondition($popular_num);

        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'推广码不存在'];
        }

        $condition_data['user_id'] = Auth::id();
        $popular_data = $this->popularListRepositories->GetUserPopularData($condition_data);

        if(!empty($popular_data)){
            return ['code'=>-1, 'msg'=>'你已经推广过不能再次推广！'];
        }

        $popular_data = [];
        $popular_data['user_id'] = Auth::id();
        $popular_data['popular_num'] = $user_data->popular_num;
        $popular_data['popular_uid'] = $user_data->id;
        $popular_data['add_time'] = date("Y-m-d H:i:s");
        $this->popularListRepositories->InsertPopularData($popular_data);
        //增加会员时间
        $this->UsersRepositories->UpdateVipTime(Auth::id(), 86400);
        $this->UsersRepositories->IncrUsersDetailNum($user_data->id, 'invitation_num');
        $return_data['code'] = 200;
        $return_data['msg'] = '添加成功';
        return $return_data;
    }

    /**
     * 關注用戶
     */
    public function DoFollow()
    {

        $uid = Auth::id();
        $fans_id = app('request')->input('fans_id');

        if(empty($fans_id)) {
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $fans_data = $this->UsersRepositories->getUserInfoById($fans_id);

        if(empty($fans_data)) {
            return ['code'=>-1, 'msg'=>'参数错误'];
        }


        $is_fans = $this->fansRepositories->GetUserFansByUidFanId($uid, $fans_id);
        if(!empty($is_fans)){
            return ['code'=>-1, 'msg'=>'已经关注'];
        }
        $fans_data  = [];
        $fans_data['user_id'] = Auth::id();
        $fans_data['fans_id'] = $fans_id;
        $fans_data['add_time'] = date('Y-m-d H:i:s');

        $this->fansRepositories->InsertFans($fans_data);
        $this->UsersRepositories->IncrUsersDetailNum($fans_id, 'follow_num');
        return ['code'=>200, 'msg'=>'关注成功'];

    }

    /**
     * 取消关注
     * @return array
     */
    public function CancelFollow()
    {
        $fans_id = app('request')->input('fans_id');
        $uid = Auth::id();
        if(empty($fans_id)) {
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $fans_data = $this->UsersRepositories->getUserInfoById($fans_id);

        if(empty($fans_data)) {
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $is_fans = $this->fansRepositories->GetUserFansByUidFanId($uid, $fans_id);
        if(empty($is_fans)){
            return ['code'=>-1, 'msg'=>'已经取消'];
        }

        $this->fansRepositories->DeleteFans($uid, $fans_id);
        $this->UsersRepositories->DecrUsersDetailNum($fans_id, 'follow_num');

        return ['code'=>200, 'msg'=>'取消成功'];

    }

    public function FollowList()
    {
        $user_id = Auth::id();
        $follow_data = $this->fansRepositories->GetUsersFans($user_id);

        if(empty($follow_data['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        foreach($follow_data['data'] as $key=>$value) {
            $temp_data = [];
            $temp_data['user_id'] = $value->id;
            $temp_data['vip_level'] = $value->vip_level;
            $temp_data['username'] = $value->username;
            $temp_data['avatar'] = $value->avatar;
            $data['data']['follow_data'][] = $temp_data;
        }

        unset($follow_data['data']);
        $data['page'] = $follow_data;
        return $data;
    }

    /**
     * 获取用户详情
     */
    public function UserInfo()
    {
        $user_id = Auth::id();
        $user_data = $this->UsersRepositories->getUserInfoById($user_id);
        print_r($user_data);
        exit;
    }


}