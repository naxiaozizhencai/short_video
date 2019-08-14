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
$router->post('user/postLogin', 'AuthController@postLogin');
$router->post('login', 'LoginController@Login');

$router->group(['prefix'=>'api', 'middleware'=>'jwt.auth'], function() use ($router){
    $router->get('video', 'VideoController@ViewVideo');
    $router->post('favorite', 'VideoController@DoFavorite');
    $router->get('discusslist', 'VideoController@DiscussList');
    $router->post('adddiscuss', 'VideoController@AddDiscuss');
    $router->post('favordiscuss', 'VideoController@FavorDiscuss');
    $router->post('reportdiscuss', 'VideoController@ReportDiscuss');
    $router->post('uploadvideo', 'VideoController@UploadVideo');


    $router->post('addpopularnum', 'UserController@AddPopularizeNum');
    $router->get('invitationrank', 'HotspotController@InvitationRank');
});


