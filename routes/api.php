<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProjectController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::post('refresh', 'refresh')->middleware('auth:api');
});

Route::apiResource('department', DepartmentController::class);
Route::apiResource('employee', EmployeeController::class);
Route::apiResource('project', ProjectController::class);

Route::get('notes', [NoteController::class, 'index']);
Route::post('departmentNote/{department}', [NoteController::class, 'departmentStore']);
Route::post('employeeNote/{employee}', [NoteController::class, 'employeeStore']);
Route::get('noteShow/{note}', [NoteController::class, 'show']);
Route::put('noteUpdate/{note}', [NoteController::class, 'update']);
Route::delete('noteDelete/{note}', [NoteController::class, 'destroy']);
