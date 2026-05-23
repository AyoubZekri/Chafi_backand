<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\UserController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/translate', [UserController::class, 'translateActivities']);
