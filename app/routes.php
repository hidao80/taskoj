<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get( '/', function()
    {
        return View::make( 'login' );
    } );
    
Route::get( 'login', function()
    {
        return View::make( 'login' );
    } );

Route::post( 'login', "AuthController@login");

Route::group(['before' => 'auth'], function() { 
    /*
    Route::get( 'hello', function()
        {
            return View::make( 'task/list' );
        } );
     */
    Route::get( 'logout', "AuthController@logout");

    Route::get( 'user/add', [function()
        {
            return View::make( 'user/add', ['user_rank' => Auth::user()->user_rank, 'msg' => ""]);
        }] );
    Route::post( 'user/add', ['uses' => 'UserController@create', function()
        {
            return View::make( 'user/add', ['user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'user/show/{id?}', ['uses' => 'UserController@show'] )->where('id', '[a-zA-Z0-9_-]+');

    Route::get( 'user/update/{id?}', ['uses' => 'UserController@show'] )->where('id', '[a-zA-Z0-9_-]+');
    Route::post( 'user/update', ['uses' => 'UserController@update', function()
        {
            return View::make( 'user/update', ['user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'user/delete', [function()
        {
            return View::make( 'user/delete', ['user_rank' => Auth::user()->user_rank, 'msg' => ""]);
        }] );
    Route::post( 'user/delete', ['uses' => 'UserController@delete', function()
        {
            return View::make( 'user/delete', ['user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'task/edit', function()
        {
            return View::make( 'task/edit', [
                'task_id' => null, 
                'user_rank' => Auth::user()->user_rank, 
                'team' => Auth::user()->team,
                'team_info' => array(
                    'user_list' => User::getUserList(), 
                    'staff_list' => [],
                    ),
                'record' => null,
                ]);
        } );
    Route::get( 'task/edit/{id}', ['uses' => 'TaskController@getId'] )->where('id', '[0-9]+');
    Route::get( 'task/delete/{id}', ['uses' => 'TaskController@delete'] )->where('id', '[0-9]+');
    Route::post( 'task/edit', ['uses' => 'TaskController@edit'] );

    Route::get( 'task/list', ['uses' => 'ListController@get'] );
});