<?php

use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\StaffDetailsController;
use App\Http\Controllers\SubcontractorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PhotoReportController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthCheckController;

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

// Auth check route
Route::get('/check-auth', [AuthCheckController::class, 'checkAuth'])->name('auth.check');

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    // Training schedule route
    Route::get('/training_shedule', [
        TrainingScheduleController::class,
        'index',
    ]);
    Route::post('/training_shedule', [
        TrainingScheduleController::class,
        'store',
    ]);
    Route::get('/training_shedule/{id}', [
        TrainingScheduleController::class,
        'show',
    ]);
    Route::put('/training_shedule/{id}', [
        TrainingScheduleController::class,
        'update',
    ]);
    Route::delete('/training_shedule/{id}', [
        TrainingScheduleController::class,
        'destroy',
    ]);

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

    // Document management routes
    Route::resource('documents', DocumentController::class)->names([
        'index' => 'documents.index',
        'store' => 'documents.store',
        'show' => 'documents.show',
        'update' => 'documents.update',
        'destroy' => 'documents.destroy',
    ]);

    // Job application routes
    Route::resource('job_applications', JobApplicationController::class)->names(
        [
            'index' => 'job-applications.index',
            'store' => 'job-applications.store',
            'show' => 'job-applications.show',
            'update' => 'job-applications.update',
            'destroy' => 'job-applications.destroy',
        ]
    );

    // Projects routes
    Route::resource('projects', ProjectController::class)->names([
        'index' => 'projects.index',
        'store' => 'projects.store',
        'show' => 'projects.show',
        'update' => 'projects.update',
        'destroy' => 'projects.destroy',
    ]);

    // Folder routes
    Route::resource('folders', FolderController::class)->names([
        'index' => 'folders.index',
        'store' => 'folders.store',
        'show' => 'folders.show',
        'update' => 'folders.update',
        'destroy' => 'folders.destroy',
    ]);

    // Photoreport routes
    Route::apiResource('photo_reports', PhotoReportController::class)
    ->names([
        'index'   => 'photo_reports.index',
        'store'   => 'photo_reports.store',
        'show'    => 'photo_reports.show',
        'update'  => 'photo_reports.update',
        'destroy' => 'photo_reports.destroy',
    ]);

    // Sendmail Route
    Route::post('/send_email', [EmailController::class, 'sendEmail'])->name('send.email');

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
