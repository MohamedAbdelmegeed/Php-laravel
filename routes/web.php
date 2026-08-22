<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramLoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/telegram/callback', [TelegramLoginController::class, 'callback'])
    ->name('telegram.callback');