<?php

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

Broadcast::routes(['middleware' => 'auth:sanctum']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->middleware(['auth:sanctum'])->group(function(){
    Route::get('goals', [\App\Http\Controllers\Api\GoalsController::class, 'list']);
    Route::get('goals/{goal}', [\App\Http\Controllers\Api\GoalsController::class, 'edit']);
    Route::post('goals/store', [\App\Http\Controllers\Api\GoalsController::class, 'store'])->name('goals.store');
    Route::post('goals/update', [\App\Http\Controllers\Api\GoalsController::class, 'update'])->name('goals.update');
    Route::post('goal/delete', [\App\Http\Controllers\Api\GoalsController::class, 'delete'])->name('goals.delete');

    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
});

Route::prefix('auth')->group(function (){
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');
});

