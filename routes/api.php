<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
  Route::get('get-users', [UserController::class, 'GetUser']);
});