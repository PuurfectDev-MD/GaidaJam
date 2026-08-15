<?php

use App\Http\Controllers\Auth\HackClubController;
use App\Http\Controllers\Auth\HackatimeController;
use App\Http\Controllers\HackatimeProjectController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectHackatimeLinkController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
})->name('home');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/auth/hackclub', [HackClubController::class, 'redirect'])
    ->name('auth.hackclub');

Route::get('/auth/hackclub/callback', [HackClubController::class, 'callback'])
    ->name('auth.hackclub.callback');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/auth/hackatime', [HackatimeController::class, 'redirect'])->name('auth.hackatime');
    Route::get('/auth/hackatime/callback', [HackatimeController::class, 'callback'])->name('auth.hackatime.callback');
    Route::post('/auth/hackatime/disconnect', [HackatimeController::class, 'disconnect'])->name('auth.hackatime.disconnect');

    Route::resource('projects', ProjectController::class);
    Route::get('/hackatime-projects', [HackatimeProjectController::class, 'index'])->name('hackatime-projects.index');
    Route::post('/hackatime-projects', [HackatimeProjectController::class, 'store'])->name('hackatime-projects.store');
    Route::post('/hackatime-projects/sync', [HackatimeProjectController::class, 'syncFromApi'])->name('hackatime-projects.sync');
    Route::put('/hackatime-projects/{hackatimeProject}', [HackatimeProjectController::class, 'update'])->name('hackatime-projects.update');
    Route::delete('/hackatime-projects/{hackatimeProject}', [HackatimeProjectController::class, 'destroy'])->name('hackatime-projects.destroy');

    Route::put('/projects/{project}/hackatime-projects', [ProjectHackatimeLinkController::class, 'sync'])
        ->name('projects.hackatime-projects.sync');
    Route::post('/projects/{project}/hackatime-projects/{hackatimeProject}', [ProjectHackatimeLinkController::class, 'attach'])
        ->name('projects.hackatime-projects.attach');
    Route::delete('/projects/{project}/hackatime-projects/{hackatimeProject}', [ProjectHackatimeLinkController::class, 'detach'])
        ->name('projects.hackatime-projects.detach');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');
});
