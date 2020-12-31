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
    
    Route::get( 'hello', function()
        {
            return View::make( 'hello' );
        } );
     
    Route::get( 'logout', "AuthController@logout");

    Route::get( 'user/add', [function()
        {
            return View::make( 'user', ['type' => 'add', 'user_rank' => Auth::user()->user_rank, 'msg' => ""]);
        }] );
    Route::post( 'user/add', ['uses' => 'UserController@create', function()
        {
            return View::make( 'user', ['type' => 'add', 'user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'user/update', [function()
        {
            return View::make( 'user', ['type' => 'update', 'user_rank' => Auth::user()->user_rank, 'msg' => ""]);
        }] );
    Route::post( 'user/update', ['uses' => 'UserController@update', function()
        {
            return View::make( 'user', ['type' => 'update', 'user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'user/delete', [function()
        {
            return View::make( 'user', ['type' => 'delete', 'user_rank' => Auth::user()->user_rank, 'msg' => ""]);
        }] );
    Route::post( 'user/delete', ['uses' => 'UserController@delete', function()
        {
            return View::make( 'user', ['type' => 'delete', 'user_rank' => Auth::user()->user_rank]);
        }] );

    Route::get( 'task/edit', function()
        {
            return View::make( 'task_edit', [
                'task_id' => null, 
                'user' => Auth::user(), 
                'team_info' => array('staff' => User::where('team', Auth::user()->team)->get(), 'staff_id' => []),
                'record' => null,
                ]);
        } );
    Route::get( 'task/edit/{id}', ['uses' => 'TaskController@getId'] )->where('id', '[0-9]+');
    Route::post( 'task/edit', ['uses' => 'TaskController@edit'] );

});