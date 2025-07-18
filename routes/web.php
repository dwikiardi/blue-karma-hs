<?php

use App\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

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

/* Auth Route */
Route::middleware('auth')->group(function () {
    Route::get('/', 'HomeController@index')->name('index');
    Route::delete('/', 'HomeController@index');
    Route::post('/show', 'HomeController@showIndex')->name('index.show');

    Route::get('/users', 'HomeController@users')->name('users');
    Route::post('/users/show', 'HomeController@showUsers')->name('users.show');
    Route::post('/users', 'HomeController@users');
    Route::delete('/users', 'HomeController@users');

    Route::get('/packet', 'HomeController@packet')->name('packet');
    Route::post('/packet/show', 'HomeController@showPacket')->name('packet.show');
    Route::post('/packet', 'HomeController@packet');
    Route::delete('/packet', 'HomeController@packet');

    Route::get('/client', 'HomeController@client')->name('client');
    Route::post('/client/show', 'HomeController@showClient')->name('client.show');
    Route::delete('/client', 'HomeController@client');

    Route::get('/session', 'HomeController@session')->name('session');
    Route::post('/session/show', 'HomeController@showSession')->name('session.show');
    Route::delete('/session', 'HomeController@session');

    Route::get('/router', 'HomeController@router')->name('router');

    Route::post('/qr-code', 'HomeController@qrcode')->name('qrcode');
    Route::post('/logout', 'LoginController@logout')->name('logout');

    Route::get('/email', 'HomeController@email')->name('email');
    Route::get('/email/show', 'HomeController@showEmail')->name('email.show');
    Route::delete('/email', 'HomeController@deleteEmail');
});
/* Auth Route */

/* Guest Route */
Route::middleware('guest')->group(function () {
    Route::view('/login', 'login.index')->name('login');

    Route::post('/login', 'LoginController@login');
    Route::post('/session', 'LoginController@session');
});
/* Guest Route */
