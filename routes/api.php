<?php

use App\Http\Controllers\admin\ActivitiesController;
use App\Http\Controllers\admin\AdminAuthController;
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


Route::prefix('admin')->group(function () {
    Route::post('/store', [AdminAuthController::class, 'store']);
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/refresh', [AdminAuthController::class, 'refresh']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
});



// Route::resource('activities', ActivitiesController::class)->middleware('jwt.auth');
// Route::resource('activities', ActivitiesController::class)->middleware('jwt.auth');
// Route::post('/takeabsent/{activity}', [ActivitiesController::class,'takeAbsent'])->middleware('auth:admin');
// Route::resource('groups', GroupController::class)->middleware('jwt.auth');
// Route::resource('students', AdminUserController::class)->middleware('jwt.auth');
// Route::put('/students/changeGroup/{student}',[AdminUserController::class,"changeUserGroup"]);


Route::middleware('jwt.auth')->group(function () {
    Route::prefix('activities')->group(function () {
        Route::resource('/', ActivitiesController::class);
        Route::post('/takeabsent/{activity}', [ActivitiesController::class, 'takeAbsent']);
    });

    Route::prefix('groups')->group(function () {
        Route::resource('/', GroupController::class);
    });

    Route::prefix('students')->group(function () {
        Route::resource('/', AdminUserController::class);
        Route::put('/changeGroup/{student}', [AdminUserController::class, 'changeUserGroup']);
    });
});

// Route::post("/activity/save/{activity_id}",[UserActivitiesController::class,"store"]);

// Route::get('/user',function(){
//     return auth()->guard('admin')->check();
// })->middleware("auth:sanctum");

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
