<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('login', 'LoginController@Login');

$router->post('user/postLogin', 'AuthController@postLogin');

$router->group(['prefix'=>'api', 'middleware'=>['settoken','jwt.auth']], function() use ($router){

    $router->post('loginorreg', 'LoginController@PhoneLoginOrRegister');//登录或者注册
    $router->post('phonelogin', 'LoginController@PhoneLogin');//手机登录
    $router->post('register', 'LoginController@Register');//注册
    $router->post('sendcode', 'LoginController@SendCode');//发送手机验证码
    $router->post('forgetpasswd', 'LoginController@ForgetPassword');//忘记密码
    $router->get('logout', 'LoginController@Logout');//注销登录
    $router->post('refreshtoken', 'LoginController@RefreshToken');//刷新token

    //视频列表
    $router->get('video', 'VideoController@ViewVideo');//获取视频列表
    $router->post('playvideo', 'VideoController@PlayVideo');//增加視頻播放次數
    $router->get('followvideo', 'VideoController@FollowViewVideo');//获取关注视频列表
    $router->post('favorite', 'VideoController@DoFavorite');//喜爱这个视频
    $router->post('cancelfavorite', 'VideoController@CancelFavorite');//取消喜欢
    $router->get('discusslist', 'VideoController@DiscussList');//评论列表
    $router->post('adddiscuss', 'VideoController@AddDiscuss');//增加评论
    $router->post('favordiscuss', 'VideoController@FavorDiscuss');//喜爱这条评论
    $router->post('reportdiscuss', 'VideoController@ReportDiscuss');//举报这条评论
    $router->post('uploadvideo', 'VideoController@UploadVideo');//上传视频
    $router->post('upload', 'VideoController@Upload');//上传视频

    $router->post('addpopularnum', 'UserController@AddPopularizeNum');//填写推广码
    $router->post('dofollow', 'UserController@DoFollow');//关注用户
    $router->post('cancelfollow', 'UserController@CancelFollow');//取消关注
    $router->get('followlist', 'UserController@FollowList');//关注列表
    $router->get('fanslist', 'UserController@FansList');//粉丝列表
    $router->post('userinfo', 'UserController@UserInfo');//用户详情
    $router->post('updateuserinfo', 'UserController@UpdateUsersInfo');//更新用户信息
    $router->post('favorvideolist', 'UserController@UserFavoriteList');//喜欢视频列表
    $router->post('uservideolist', 'UserController@UserVideoList');//用户上传列表
//    $router->get('uservideolist', 'UserController@UserVideoList');
    $router->get('share', 'UserController@UserShare');//用户分享
    $router->get('sharelist', 'UserController@ShareList');//用户分享

    $router->post('searchusers', 'SearchController@SearchUsers');//用户搜索

    $router->get('invitationrank', 'HotspotController@InvitationRank');//分享排行
    $router->get('supportrank', 'HotspotController@SupportRank');//支持排行


    $router->post('messagelist', 'MessageController@MessageList');//message 消息列表
    $router->post('sendchat', 'MessageController@SendChatMessage');//message 发送消息
    $router->get('getchatmsg', 'MessageController@GetChatMessageList');//message 获取聊天列表
    $router->get('noticemsg', 'MessageController@NoticeMessage');//message 获取聊天列表

    $router->get('notice', 'MessageController@NoticeMessageList');//message公告


    $router->post('order', 'OrderController@CreateOrder');//生成订单
	$router->post('orderlist', 'OrderController@PayDetails');//充值记录
});


