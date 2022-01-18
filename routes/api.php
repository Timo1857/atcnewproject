<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::POST('/login', [Controller::class, 'login']);
Route::POST('/signup',[Controller::class, 'signup']);
Route::POST('/forgotPassword',[Controller::class, 'forgotPassword']);
Route::POST('/createNewPassword',[Controller::class, 'createNewPassword']);
Route::POST('/passwordChanged',[Controller::class, 'passwordChanged']);
Route::POST('/upload',[Controller::class, 'upload']);
Route::POST('chooseplan',[Controller::class, 'chooseplan']);
