<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('log-in', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
  Route::post('log-out', [AuthController::class, 'logout']);
  Route::get('get-users', [UserController::class, 'GetUser']);
});