<?php

use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\StaffDetailsController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\SubcontractorController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\PhotoReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthCheckController;
use App\Http\Controllers\FileControllerNew;
use App\Http\Controllers\FolderControllerNew;
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


// Public routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/admin/login', [AuthController::class, 'adminLogin']);
// Route::post('/staff/login', [StaffAuthController::class, 'login']);

// Authentication routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

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


    // Resource routes for users
    Route::resource('users', UserController::class)->except([
        'create',
        'edit'
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

    // New Folder routes
    Route::get('folders', [FolderControllerNew::class, 'index']);
    Route::get('user-folders', [FolderControllerNew::class, 'getAllFolders']);
    Route::get('all-folders', [FolderControllerNew::class, 'getAllFoldersWithoutUserId']);
    Route::post('folders', [FolderControllerNew::class, 'create']);
    Route::put('folders/{id}/rename', [FolderControllerNew::class, 'rename']);
    Route::delete('folders/delete', [FolderControllerNew::class, 'delete']); // Bulk or single delete
    Route::post('folders/restore', [FolderControllerNew::class, 'restore']); // Bulk or single restore
    Route::delete('folders/permanently-delete', [FolderControllerNew::class, 'permanentlyDelete']); // Bulk or single permanently delete
    Route::get('folders/trash', [FolderControllerNew::class, 'trash']);
    Route::get('folders/{id}', [FolderControllerNew::class, 'show']);

    // New File routes
    Route::get('files', [FileControllerNew::class, 'index']);
    Route::get('files/trash', [FileControllerNew::class, 'trash']);
    Route::get('files/{id}', [FileControllerNew::class, 'show']);
    Route::get('user-files', [FileControllerNew::class, 'getAllFiles']);
    Route::get('all-files', [FileControllerNew::class, 'getAllFilesWithoutUserId']);
    Route::post('files', [FileControllerNew::class, 'upload']);
    Route::put('files/{id}/rename', [FileControllerNew::class, 'rename']);
    Route::delete('files/delete', [FileControllerNew::class, 'delete']); // Bulk or single delete
    Route::post('files/restore', [FileControllerNew::class, 'restore']); // Bulk or single restore
    Route::delete('files/permanently-delete', [FileControllerNew::class, 'permanentlyDelete']);

    // Quota limit route
    Route::get('/quota-limit', [ConfigController::class, 'getQuotaLimit']);

    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
