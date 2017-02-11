<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('prototype');
});

Route::group(['namespace' => 'api', 'middleware' => 'api'], function($router) {
  $router->post('/1.0/csv/upload', 'CSVController@upload');
  $router->get('/1.0/media', 'MediaController@getAll');
  $router->get('/1.0/media/{uid}','MediaController@getOne');
  $router->get('/1.0/media/info/{uid}','MediaController@getInfo');
});
