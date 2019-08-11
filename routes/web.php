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


$router->post('/login', 'LoginController@Login');
$router->get('user', 'UserController@login');
$router->get('UserDetail', 'UserController@UserDetail');
$router->get('UserVideoList', 'UserController@UserVideoList');
$router->get('UserFavoriteList', 'UserController@UserFavoriteList');
