<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/users', function () {
        return "User List";
    })->name('users.index');
});


Route::middleware(['auth', 'permission:users.create'])->group(function () {

    Route::get('/user/create', function () {
        return "Create User";
    })->name('users.create');
});

require __DIR__ . '/auth.php';