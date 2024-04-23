<?php

use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\StaffDetailsController;
use App\Http\Controllers\SubcontractorController;
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
    // Training schedule route
    Route::get('/training_shedule', [TrainingScheduleController::class, 'index']);
    Route::post('/training_shedule', [TrainingScheduleController::class, 'store']);
    Route::get('/training_shedule/{id}', [TrainingScheduleController::class, 'show']);
    Route::put('/training_shedule/{id}', [TrainingScheduleController::class, 'update']);
    Route::delete('/training_shedule/{id}', [TrainingScheduleController::class, 'destroy']);

    //Subcontractor routes
    Route::resource('subcontractor', SubcontractorController::class)->names([
        'index' => 'subcontractor.index',
        'store' => 'subcontractor.store',
        'show' => 'subcontractor.show',
        'update' => 'subcontractor.update',
        'destroy' => 'subcontractor.destroy',
    ]);

    //Job posting routes
    Route::resource('job_posting', JobPostingController::class)->names([
        'index' => 'subcontractor.index',
        'store' => 'subcontractor.store',
        'show' => 'subcontractor.show',
        'update' => 'subcontractor.update',
        'destroy' => 'subcontractor.destroy',
    ]);

    //Staff details routes
    Route::resource('staff', StaffDetailsController::class)->names([
        'index' => 'subcontractor.index',
        'store' => 'subcontractor.store',
        'show' => 'subcontractor.show',
        'update' => 'subcontractor.update',
        'destroy' => 'subcontractor.destroy',
    ]);


    Route::post('/logout', [AuthController::class, 'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
