<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ReportController;
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

Route::group(['prefix' => 'exams'], function () {
    Route::get('/{id}', [ExamController::class,'show']);
    Route::post('/', [ExamController::class,'create']);
    Route::post('/{id}/start', [ExamController::class,'start']);
    Route::post('/{id}/pausing', [ExamController::class,'pausing']);
    Route::post('/{id}/resuming', [ExamController::class,'resuming']);
    Route::post('/{id}/completed', [ExamController::class,'completed']);
});

Route::group(['prefix' => 'reports'], function () {
    Route::get('/general', [ReportController::class,'general']);
    Route::get('/results', [ReportController::class,'results']);
    Route::get('/questions-analysis', [ReportController::class,'questionsAnalysis']);
    Route::get('/average-time', [ReportController::class,'averageTime']);
    Route::get('/leaderboard', [ReportController::class,'leaderboard']);
});

Route::get('/users', [Controller::class,'users']);
Route::get('/courses', [Controller::class,'courses']);


