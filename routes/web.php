<?php

use App\Http\Controllers\AccessTokenController;
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

Route::post('access-token/twilio', [AccessTokenController::class, 'twilio']);

Route::get('/', function () {
    return view('welcome');
});
