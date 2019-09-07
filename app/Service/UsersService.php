<?php
namespace App\Service;
use App\Repositories\IpPopularRepositories;
use App\Repositories\MessageRepositories;
use App\Repositories\PopularListRepositories;
use App\Repositories\TempDataRepositories;
use App\Repositories\UsersDetailRepositories;
use App\Repositories\UsersFansRepositories;
use App\Repositories\UsersRepositories;
use App\Repositories\VideoRepositories;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class UsersService
{

    protected $UsersRepositories;
    protected $popularListRepositories;
    protected $fansRepositories;
    protected $tempDataRepositories;
    protected $videoRepositories;
    protected $messageRepositories;
    protected $messageService;

    public function __construct(UsersRepositories $UsersRepositories, UsersDetailRepositories $usersDetailRepositories,
                                PopularListRepositories $popularListRepositories,UsersFansRepositories $fansRepositories,
                                TempDataRepositories $tempDataRepositories, VideoRepositories $videoRepositories,
                                 MessageRepositories $messageRepositories,MessageService $messageService
    )
    {
        $this->UsersRepositories = $UsersRepositories;
        $this->UsersDetailRepositories = $usersDetailRepositories;
        $this->popularListRepositories = $popularListRepositories;
        $this->fansRepositories = $fansRepositories;
        $this->tempDataRepositories = $tempDataRepositories;
        $this->videoRepositories = $videoRepositories;
        $this->messageRepositories = $messageRepositories;
        $this->messageService = $messageService;
    }



    /**
     * 刷新token
     * @return array
     */
    public function DoRefreshToken()
    {

        $user_id = Auth::id();
        $userData = $this->UsersRepositories->getUserInfoById($user_id);
        $resultData = ['code'=>200, 'data'=>[]];
        $data['user_id'] = $userData->id;
        $data['uuid'] = $userData->uuid;
        $data['vip_level'] = $userData->vip_level;
        $data['is_phone_login'] = $userData->is_phone_login;
        $data['vip_expired_time'] = $userData->vip_expired_time;
        $data['viewed_times'] = 0;
        $data['total_viewed_times'] = 10;
        $data['viewed_times'] = 0;
        $token_data = [];
        if (!$token = Auth::refresh()) {
            $resultData['code']     = 5000;
            $resultData['msg'] = '系统错误，无法生成令牌';
        } else {
            $token_data['user_id']      = intval($userData->id);
            $token_data['access_token'] = $token;
            $token_data['expires_in']   = strval(time() + 3600);
        }

        $resultData['data']['user_data'] = $data;
        $resultData['data']['token_data'] = $token_data;
        return $resultData;
    }

    /**
     * 匿名登录
     * @param $uuid
     * @return array
     */
    public function Login($request)
    {
        $uuid = $request->input("uuid");

        if(empty($uuid)){
            return ['code'=>-1, 'msg'=>'参数不能为空'];
        }


        $userData = $this->UsersRepositories->GetUserDataByUuid($uuid);

        if(empty($userData)){
            $username = '游客账号_' . rand(100000000, 9999999999);
            $user_data = [
                'uuid'=>$uuid,
                'username'=>$username,
                'vip_level'=>0,
                'is_phone_login'=>0,
                'add_time'=>date('Y-m-d H:i:s'),
            ];

            $userId = $this->UsersRepositories->InsertUser($user_data);
            $popular_num = randomString();
            $avatar = env("AVATAR_DIR") . rand(1, 10) . '.png';
            $user_detail = [
                'avatar'=>$avatar,
                'user_id' =>$userId,
                'city'=>'',
                'popular_num'=>$popular_num,
                'add_time'=>date('Y-m-d H:i:s'),
            ];

            $user_detail_id = $this->UsersRepositories->InsertUserDetail($user_detail);
            //生成二维码
            //实例化观看次数
            $temp_data = [];
            $temp_data['user_id'] = $userId;
            $temp_data['temp_key'] = TempDataRepositories::PLAY_VIDEO_TIMES;
            $temp_data['temp_value'] = 0;
            $insert_temp_data = $temp_data;
            $insert_temp_data['add_time'] = date('Y-m-d H:i:s');
            $this->tempDataRepositories->UpateOrInsertTempData($temp_data, $insert_temp_data);

            //实例化观看到哪里
            $temp_data = [];
            $temp_data['user_id'] = $userId;
            $temp_data['temp_key'] = TempDataRepositories::VIDEO_RECOMMEND_MAX_ID;
            $temp_data['temp_value'] = 0;
            $insert_temp_data = $temp_data;
            $insert_temp_data['add_time'] = date('Y-m-d H:i:s');
            $this->tempDataRepositories->UpateOrInsertTempData($temp_data, $insert_temp_data);


            
            $qr_name =  env("QRCODE_DIR") . $popular_num . '.png';
            $qr_url = env("UPLOAD_APP_URL") . $popular_num;
            file_put_contents($qr_name, QrCode::format('png')->size(253)->generate($qr_url));
            $userData = $this->UsersRepositories->GetUserDataByUuid($uuid);
        }

        $ip = $request->ip();
        $ip_popular_condition = [];
        $ip_popular_condition['ip'] = $ip;

        $ipPopularRepositories = new IpPopularRepositories();
        $ip_popular = $ipPopularRepositories->GetIpPopularData($ip_popular_condition);

        if(!empty($ip_popular)) {

            if($ip_popular->status == 0) {
                $request->offsetSet('popular_num', $ip_popular->popular_num);
                $request->offsetSet('user_id', $userData->id);

                $this->AddPopularNum($request);

                $updata = [];
                $updata['status'] = 1;

                $update_condition = [];
                $update_condition['id'] = $ip_popular->id;

                $ipPopularRepositories->UpdateIpPopularData($update_condition, $updata);
            }
        }


        $resultData = ['code'=>200, 'data'=>[]];
        $data['user_id'] = $userData->id;
        $data['uuid'] = $userData->uuid;
        $data['vip_level'] = $userData->vip_level;
        $data['phone'] = $userData->phone;
        $data['is_phone_login'] = $userData->is_phone_login;
        $data['vip_expired_time'] = $userData->vip_expired_time;
        $play_video_times_data = $this->tempDataRepositories->GetValue($userData->id, 'play_video_times');
        $data['viewed_times'] = empty($play_video_times_data) ? 0 : intval($play_video_times_data->temp_value);
        $total_viewed_times_data = $this->tempDataRepositories->GetValue($userData->id, 'total_viewed_times');
        $data['total_viewed_times'] = empty($total_viewed_times_data) ? 10 :$total_viewed_times_data->temp_value;
        $user_info = $this->UsersRepositories->GetAuthUserData($uuid);
        $token_data = [];
        if (!$token = Auth::login($user_info, true)) {
            $resultData['code']     = -1;
            $resultData['msg'] = '系统错误，无法生成令牌';
        } else {
            $token_data['user_id']      = intval($user_info->id);
            $token_data['access_token'] = $token;
            $token_data['expires_in']   = strval(time() + 3600);
        }

        $resultData['data']['user_data'] = $data;
        $resultData['data']['token_data'] = $token_data;
        return $resultData;
    }


    /**
     * 手机注册
     * @param $request
     * @return array
     */
    public function PhoneRegister($request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $password = $request->input('password');
        $user_id = Auth::id();
        if(empty($phone) || empty($code) || empty($password)){
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $user_data = $this->UsersRepositories->getUserInfoById($user_id);
        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
        }

        if($user_data->phone == $phone){
            return ['code'=>-1, 'msg'=>'用户已经绑定,不能注册'];
        }

        $phone_user_data = $this->UsersRepositories->GetUserInfoByPhone($phone);

        if(!empty($phone_user_data)){
            return ['code'=>-1, 'msg'=>'手机号已经注册不能再次注册'];
        }

        $temp_data = $this->tempDataRepositories->GetValue($user_id, $phone);

        if(empty($temp_data)){
            return ['code'=>-1, 'msg'=>'验证码还未发送'];
        }

        if($temp_data->expire_time < time()){
            return ['code'=>-1, 'msg'=>'验证码已经过期'];
        }

        if($temp_data->temp_value != $code){
            return ['code'=>-1, 'msg'=>'验证码错误'];
        }

        $data['phone'] = $phone;
        $data['password'] = md5($password);
        $data['is_phone_login'] = 1;
        $data['is_register'] = 1;
        $this->UsersRepositories->UpdateUserById($user_id, $data);
        return ['code'=>200, 'msg'=>'注册成功'];
    }


    /**
     * 登录
     * @param $request
     * @return array
     */
    public function PhoneLogin($request)
    {

        $phone = $request->input('phone');
        $password = $request->input('password');
        if(empty($phone)){
            return ['code'=>-1, 'msg'=>'请输入手机号'];
        }
        $md_password = md5($password);

        $user_data = $this->UsersRepositories->GetUserInfoByPhonePasswd($phone, $md_password);

        if(empty($user_data)) {
            return ['code'=>-1, 'msg'=>'密码错误'];
        }

        $update_data['is_phone_login'] = 1;
        $this->UsersRepositories->UpdateUserById($user_data->id, $update_data);

        $resultData = ['code'=>200, 'data'=>[]];
        $data['user_id'] = $user_data->id;
        $data['uuid'] = $user_data->uuid;
        $data['vip_level'] = $user_data->vip_level;
        $data['phone'] = $user_data->phone;
        $data['is_phone_login'] = $user_data->is_phone_login;
        $data['vip_expired_time'] = $user_data->vip_expired_time;
        $play_video_times_data = $this->tempDataRepositories->GetValue($user_data->id, TempDataRepositories::PLAY_VIDEO_TIMES);
        $data['viewed_times'] = empty($play_video_times_data) ? 0 : $play_video_times_data->temp_value;
        $total_viewed_times_data = $this->tempDataRepositories->GetValue($user_data->id, TempDataRepositories::TOTAL_VIEWED_TIMES);
        $data['total_viewed_times'] = empty($total_viewed_times_data) ? 10 :$total_viewed_times_data->temp_value;
        $data['viewed_times'] = 10;
        $data['play_video_second'] = 15;
        $user_info = $this->UsersRepositories->GetAuthUserData($user_data->uuid);

        if (!$token = Auth::login($user_info, true)) {
            $resultData['code']     = -1;
            $resultData['msg'] = '系统错误，无法生成令牌';
        } else {
            $token_data['user_id']      = intval($user_info->id);
            $token_data['access_token'] = $token;
            $token_data['expires_in']   = strval(time() + 3600);
        }

        $resultData['data']['user_data'] = $data;
        $resultData['data']['token_data'] = $token_data;

        $resultData['data']['user_info']['user_id'] = $user_info->id;
        return $resultData;
    }

    public function UpdateUsersInfo($request)
    {
        $user_id = Auth::id();
        $sign = $request->input('sign', '');
        $username = $request->input('username', '');
        $sex = $request->input('sex', '');
        $birthday = $request->input('birthday', '');
        $city = $request->input('city', '');
        $avatar = $request->input('avatar', '');
        $update_data = [];


        if(!empty($avatar)){
            $update_data['avatar'] = $avatar;
        }

        if(!empty($sign)){
            $update_data['sign'] = $sign;
        }

        if(!empty($username)){
            $update_data['username'] = $username;
        }

        if(!empty($sex)){
            $update_data['sex'] = $sex;
        }

        if(!empty($birthday)){
            $update_data['birthday'] = $birthday;
        }

        if(!empty($city)){
            $update_data['city'] = $city;
        }

        if(empty($update_data)){
            return ['code'=>-1, 'msg'=>'数据不能为空'];
        }
        if(!empty($username)) {

            $this->UsersRepositories->UpdateUserById($user_id, ['username'=>$update_data['username']]);
            unset($update_data['username']);
        }

        $this->UsersRepositories->UpdateUsersInfo($user_id, $update_data);
        return ['code'=>200, 'msg'=>'更新成功'];
    }

    /**
     * 登出
     * @return array
     */
    public function Logout()
    {
        $user_id = Auth::id();
        $user_data = $this->UsersRepositories->getUserInfoById($user_id);

        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
        }
        $update_data['is_phone_login'] = 0;
        $this->UsersRepositories->UpdateUserById($user_id, $update_data);
        return ['code'=>200, 'msg'=>'操作成功'];
    }

    /**
     * 发送验证码
     * @param $request
     * @return array
     */
    public function SendCode($request)
    {
        $phone = $request->input('phone');

        if(empty($phone)){
            return ['code'=>-1, 'msg'=>'请输入手机号'];
        }

        $user_id = Auth::id();
        $code = rand(1000, 9999);
        $temp_data = [];
        $temp_data['user_id'] = $user_id;
        $temp_data['temp_key'] = $phone;
        $find_data = $temp_data;

        $temp_data['temp_value'] = $code;
        $temp_data['expire_time'] = time() + 3600;
        $temp_data['add_time'] = date("Y-m-d H:i:s");

        $this->tempDataRepositories->UpateOrInsertTempData($find_data, $temp_data);



        $appkey = env('MESSAGE_APPKEY');//你的appkey
        $mobile = $phone;//手机号 超过1024请用POST
        $content = '你的注册码是'.$code.'【鲨鹰供应】';//utf8
        $url = "https://api.jisuapi.com/sms/send?appkey=$appkey&mobile=$mobile&content=$content";

        $result = curlOpen($url, ['ssl'=>true]);
        $jsonarr = json_decode($result, true);

        if($jsonarr['status'] != 0)
        {

            return ['code'=>-1, 'msg'=>'发送失败'];

        }

        return ['code'=>200, 'msg'=>'发送成功'];
    }

    /**
     * 找回密码
     * @param $request
     * @return array
     */
    public function ForgetPassword($request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $new_password = $request->input('new_password');
        $user_id = Auth::id();
        if(empty($phone) || empty($code) || empty($new_password)){
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        $phone_user_data = $this->UsersRepositories->GetUserInfoByPhone($phone);

        if(empty($phone_user_data)){
            return ['code'=>-1, 'msg'=>'手机号还未注册'];
        }

        $temp_data = $this->tempDataRepositories->GetValue($user_id, $phone);

        if(empty($temp_data)){
            return ['code'=>-1, 'msg'=>'验证码还未发送'];
        }

        if($temp_data->expire_time < time()){
            return ['code'=>-1, 'msg'=>'验证码已经过期'];
        }

        if($temp_data->temp_value != $code){
            return ['code'=>-1, 'msg'=>'验证码错误'];
        }
        $update_data = [];
        $update_data['password'] = md5($new_password);
        $update_data['is_phone_login'] = 1;

        $this->UsersRepositories->UpdateUserById($phone_user_data->id, $update_data);
        return ['code'=>200, 'msg'=>'修改密码成功'];
    }

    /**
     * 填写推广吗
     */
    public function AddPopularNum($request)
    {
        $popular_num =$request->input('popular_num');
        $return_data = [];
        $user_id = Auth::id();

        if(empty($user_id)) {
            $user_id = $request->input('user_id');
        }

        if(empty($popular_num)){
            return ['code'=>-1, 'msg'=>'推广码不存在'];
        }
        $users_condition['popular_num'] = $popular_num;
        $user_data = $this->UsersRepositories->GetUserInfoByCondition($users_condition);

        if(empty($user_id)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
        }

        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'推广码不存在'];
        }


        $condition_data['user_id'] = $user_id;
        $exist_popular_data = $this->popularListRepositories->GetUserPopularData($condition_data);

        if(!empty($exist_popular_data)){
            return ['code'=>-1, 'msg'=>'你已经填写过推广码！'];
        }

        if($user_id == $user_data->id) {
            return ['code'=>-1, 'msg'=>'不能填写自己的推广码！'];
        }

        $popular_data = [];
        $popular_data['user_id'] = $user_id;
        $popular_data['popular_num'] = $user_data->popular_num;
        $popular_data['popular_uid'] = $user_data->id;
        $popular_data['add_time'] = date("Y-m-d H:i:s");
        $this->popularListRepositories->InsertPopularData($popular_data);
        //增加会员时间
        $this->UsersRepositories->NewUpdateVipTime($user_data->id, 86400 * 3);
        $this->UsersRepositories->IncrUsersDetailNum($user_data->id, 'invitation_num');
        $user_detail = [];
        $user_detail['orther_popular_num'] = $popular_num;
        $this->UsersRepositories->UpdateUsersInfo($user_id, $user_detail);
        $return_data['code'] = 200;
        $return_data['msg'] = '操作成功';
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
        $user_data = $this->UsersRepositories->getUserInfoById($uid);


        if(empty($fans_data)) {
            return ['code'=>-1, 'msg'=>'参数错误'];
        }

        if($fans_id == $uid){
            return ['code'=>-1, 'msg'=>'不能关注自己'];
        }

        $is_fans = $this->fansRepositories->GetUserFansByUidFanId($uid, $fans_id);
        if(!empty($is_fans)){
            return ['code'=>-1, 'msg'=>'已经关注'];
        }
        $fans_data  = [];
        $fans_data['user_id'] = $uid;
        $fans_data['fans_id'] = $fans_id;
        $fans_data['add_time'] = date('Y-m-d H:i:s');

        $this->fansRepositories->InsertFans($fans_data);
        $this->UsersRepositories->IncrUsersDetailNum($uid, 'follow_num');
        $this->UsersRepositories->IncrUsersDetailNum($fans_id, 'fans_num');

        $msg_data = [];
        $msg_data['message_type'] = MessageRepositories::MESSAGE_TYPE_FOLLOW;
        $msg_data['message'] = $user_data->username . '关注了你';
        $msg_data['send_id'] = $uid;
        $msg_data['receive_id'] = $fans_id;
        $msg_data['send_time'] = time();
        $msg_data['add_time'] = date('Y-m-d H:i:s');
        $this->messageRepositories->InsertMessage($msg_data);

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
            return ['code'=>-1, 'msg'=>'关注用户不存在'];
        }

        $is_fans = $this->fansRepositories->GetUserFansByUidFanId($uid, $fans_id);
        if(empty($is_fans)){
            return ['code'=>-1, 'msg'=>'已经取消'];
        }

        if($fans_id == $uid){
            return ['code'=>-1, 'msg'=>'不能取消关注自己'];
        }

        $this->fansRepositories->DeleteFans($uid, $fans_id);
        $this->UsersRepositories->DecrUsersDetailNum($uid, 'follow_num');
        $this->UsersRepositories->DecrUsersDetailNum($fans_id, 'fans_num');

        return ['code'=>200, 'msg'=>'取消成功'];

    }

    /**
     * 关注列表
     * @return array
     */
    public function FollowList()
    {
        $user_id = Auth::id();
        $follow_data = $this->fansRepositories->GetUsersFollowList($user_id);

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
     * 粉丝列表
     * @return array
     */
    public function FansList()
    {

        $user_id = Auth::id();
        $follow_data = $this->fansRepositories->GetUsersFansList($user_id);

        if(empty($follow_data['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        foreach($follow_data['data'] as $key=>$value) {
            $temp_data = [];
            $temp_data['user_id'] = $value->id;
            $temp_data['vip_level'] = $value->vip_level;
            $temp_data['username'] = $value->username;
            $temp_data['avatar'] = $value->avatar;
            $temp_data['is_follow'] = 0;
            $data['data']['fans_data'][] = $temp_data;
        }

        unset($follow_data['data']);
        $data['page'] = $follow_data;
        return $data;
    }


    /**
     * 获取用户详情
     */
    public function UserInfo($request)
    {
        $my_user_id = Auth::id();
        $my_user_data = $this->UsersRepositories->getUserInfoById($my_user_id);

        $user_id = $request->input('user_id', $my_user_id);
        $user_data = $this->UsersRepositories->getUserInfoById($user_id);

        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'用户数据不存在'];
        }

        if(empty($my_user_data)){
            return ['code'=>-1, 'msg'=>'用户数据不存在'];
        }

        $user_info = [];
        $user_info['id'] = $user_data->id;
        $user_info['username'] = $user_data->username;
        $user_info['avatar'] = $user_data->avatar;
        $user_info['vip_level'] = $user_data->vip_level;
        $user_info['vip_expired_time'] = $user_data->vip_expired_time;
        $user_info['is_phone_login'] = $user_data->is_phone_login;
        $user_info['sex'] = $user_data->sex;
        $user_info['sign'] = $user_data->sign;
        $user_info['city'] = $user_data->city;
        $user_info['fans_num'] = $user_data->fans_num;
        $user_info['follow_num'] = $user_data->follow_num;
        $user_info['support_num'] = $user_data->support_num;
        $user_info['upload_num'] = $user_data->upload_num;
        $user_info['favorite_num'] = $user_data->favorite_num;
        $user_info['orther_popular_num'] = $user_data->orther_popular_num;
        $user_info['is_self'] = ($user_id == $my_user_id) ? 1 : 0;


        if($user_id != $my_user_id){
            $follows_ids = $this->fansRepositories->GetUsersFollowData($my_user_id);
            $user_info['is_follow'] = isset($follows_ids[$user_id]) ? 1 : 0;
            $user_info['room_id'] = $this->messageService->MakeRoomId($user_data, $my_user_data);
        }

        $play_video_times_data = $this->tempDataRepositories->GetValue($user_id, TempDataRepositories::PLAY_VIDEO_TIMES);
        $user_info['viewed_times'] = empty($play_video_times_data) ? 0 : intval($play_video_times_data->temp_value);
        $total_viewed_times_data = $this->tempDataRepositories->GetValueByKey(TempDataRepositories::TOTAL_VIEWED_TIMES);
        $user_info['total_viewed_times'] = empty($total_viewed_times_data) ? 10 : intval($total_viewed_times_data->temp_value);

        $data = [];
        $data['code'] = 200;
        $data['data'] = ['user_info'=>$user_info];
        return $data;
    }


    /**
     * 用户自己上传列表
     */
    public function UserVideoList($request)
    {

        $my_user_id = Auth::id();
        $user_id = $request->input('user_id', $my_user_id);
        $video_list = $this->videoRepositories->GetVideoData(['user_id'=>$user_id]);

        if(empty($video_list['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        $fans_ids = $this->fansRepositories->GetUsersFollowData($user_id);

        foreach($video_list['data'] as $key=>$value){

            $video_data['video_id'] = $value->video_id;
            $video_data['video_user_avatar'] = $value->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $value->vip_level;
            $video_data['video_username'] = $value->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->video_favorite_num;
            $video_data['reply_number'] = $value->reply_num;

            if($my_user_id == $user_id){
                $video_data['is_follow'] = 1;
            }else{
                $video_data['is_follow'] = isset($fans_ids[$value->user_id]) ? 1 : 0;
            }

            $data['data']['video_data'][] = $video_data;
        }
        $data['code'] = 200;
        unset($video_list['data']);
        $data['data']['page'] = $video_list;

       return $data;
    }

    /**
     * 用户喜欢的列表
     * @param $request
     * @return array
     */
    public function UserFavoriteList($request)
    {
        $my_user_id = Auth::id();
        $user_id = $request->input('user_id', $my_user_id);
        $video_list = $this->videoRepositories->GetFavoriteVideoList($user_id);

        if(empty($video_list['data'])){
            return ['code'=>200, 'data'=>[]];
        }
        $fans_ids = $this->fansRepositories->GetUsersFollowData($user_id);
        $data = ['code'=>200];
        foreach($video_list['data'] as $key=>$value){
            $user_data = $this->UsersRepositories->getUserInfoById($value->user_id);
            $video_data['video_id'] = $value->video_id;
            $video_data['video_user_avatar'] = $user_data->avatar;
            $video_data['video_user_id'] = $value->user_id;
            $video_data['video_vip_level'] = $user_data->vip_level;
            $video_data['video_username'] = $user_data->username;
            $video_data['video_title'] = $value->video_title;
            $video_data['video_image'] = $value->video_image;
            $video_data['video_url'] = $value->video_url;
            $video_data['video_label'] = $value->video_label;
            $video_data['favorite_number'] = $value->favorite_num;
            $video_data['reply_number'] = $value->reply_num;
            $video_data['is_follow'] =  1;

            $data['data']['video_data'][] = $video_data;
        }

        unset($video_list['data']);
        $data['data']['page'] = $video_list;
        return $data;
    }

    /**
     * 模糊查询用户列表
     * @param $request
     * @return array
     */
    public function UsersList($request)
    {

        $query_string = $request->input("query_string", '');
        $search_name['username'] = $query_string;
        $user_data = $this->UsersRepositories->GetUsersList($search_name);
        $user_id = Auth::id();
        if(empty($user_data['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        $fans_ids = $this->fansRepositories->GetUsersFollowData($user_id);
        $data = ['code'=>200];
        foreach ($user_data['data'] as $key=>$value){
            if($value->id == $user_id){
                continue;
            }
            $temp_data = [];
            $temp_data['user_id'] = $value->id;
            $temp_data['username'] = $value->username;
            $temp_data['avatar'] = $value->avatar;
            $temp_data['popular_num'] = $value->popular_num;
            $temp_data['is_follow'] = isset($fans_ids[$value->id]) ? 1 : 0;

            if($this->fansRepositories->GetUserFansByUidFanId($value->id, $user_id)){
                $temp_data['is_follow'] = 2;
            }

            $data['data']['search_result'][] = $temp_data;
        }
        unset($user_data['data']);
        $data['page'] = $user_data;
        return $data;

    }

    /**
     * 用户分享数据
     */
    public function UserShareData($request)
    {
        $user_id = Auth::id();
        $user_data = $this->UsersRepositories->getUserInfoById($user_id);
        $data = ['code'=>200, 'data'=>[]];

        if(empty($user_data)){
            return ['code'=>-1, 'msg'=>'用户不存在'];
        }

        $share_data['popular_num'] = $user_data->popular_num;
        $share_data['share_url'] =  env('UPLOAD_APP_URL') . $user_data->popular_num;
        $share_data['qrcode'] = env('QRCODE_URL') . $user_data->popular_num . '.png';
        $data['data']['share_data'] = $share_data;
        return $data;

    }

    /**
     * 推广记录
     * @return array
     */
    public function ShareList()
    {
        $user_id = Auth::id();
        $condition['popular_uid'] = $user_id;
        $popolar_result = $this->popularListRepositories->GetUserPopolarList($condition);

        if(empty($popolar_result['data'])){
            return ['code'=>200, 'data'=>[]];
        }

        $data = ['code'=>200, 'data'=>[]];
        foreach ($popolar_result['data'] as $key=>$value){
            $temp['popular_num'] = $value->popular_num;
            $temp['phone'] = $value->phone;
            $temp['is_register'] = $value->is_register;
            $data['data']['share_data'][] = $temp;
        }

        unset($popolar_result['data']);
        $data['data']['page'] = $popolar_result;
        return $data;
    }

}