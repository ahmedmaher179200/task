<?php

use App\Http\Controllers\ExamController;
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
Route::get('/exams/{id}', [ExamController::class,'show']);
Route::post('/exams', [ExamController::class,'create']);
Route::post('/exams/{id}/start', [ExamController::class,'start']);
Route::post('/exams/{id}/pausing', [ExamController::class,'pausing']);
Route::post('/exams/{id}/resuming', [ExamController::class,'resuming']);
Route::post('/exams/{id}/completed', [ExamController::class,'completed']);

