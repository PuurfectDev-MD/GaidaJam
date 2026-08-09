<?php

use App\Http\Controllers\Auth\HackClubController;
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

    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
