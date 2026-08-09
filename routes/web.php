<?php

use App\Http\Controllers\Auth\HackClubController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/auth/hackclub', [HackClubController::class, 'redirect'])
    ->name('auth.hackclub');

Route::get('/auth/hackclub/callback', [HackClubController::class, 'callback'])
    ->name('auth.hackclub.callback');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('projects', ProjectController::class);

    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
