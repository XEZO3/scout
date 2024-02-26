<?php

use App\Http\Controllers\admin\ActivitiesController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\GroupController;
use App\Http\Controllers\admin\UserActivitiesController;
use App\Http\Controllers\admin\StudentController as AdminUserController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/user/store',[UserController::class,"store"]);
Route::post('/user/login',[UserController::class,"login"]);

// Route::get('/students/getall',[AdminUserController::class,"index"]);


Route::post('/admin/store',[AdminController::class,"store"]);
Route::post('/admin/login',[AdminController::class,"login"]);



Route::resource('activities', ActivitiesController::class)->middleware('auth:admin');
Route::resource('groups', GroupController::class)->middleware('auth:sanctum');
Route::resource('students', AdminUserController::class)->middleware('auth:sanctum');
Route::put('/students/changeGroup/{student}',[AdminUserController::class,"changeUserGroup"]);

// Route::post("/activity/save/{activity_id}",[UserActivitiesController::class,"store"]);

// Route::get('/user',function(){
//     return auth()->guard('admin')->check();
// })->middleware("auth:sanctum");

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
