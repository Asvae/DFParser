<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('workshops', 'WorkshopController@index');
Route::get('workshops/populate', 'WorkshopController@populate');
Route::get('workshops/{id}', 'WorkshopController@show');

// Logs to be tested
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
