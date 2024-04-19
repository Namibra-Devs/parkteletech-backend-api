<?php

use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::resource('products', ProductController::class);

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);


// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/training_shedule', [TrainingScheduleController::class, 'index']);
    Route::post('/training_shedule', [TrainingScheduleController::class, 'store']);
    Route::get('/training_shedule/{id}', [TrainingScheduleController::class, 'show']);
    Route::put('/training_shedule/{id}', [TrainingScheduleController::class, 'update']);
    Route::delete('/training_shedule/{id}', [TrainingScheduleController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
