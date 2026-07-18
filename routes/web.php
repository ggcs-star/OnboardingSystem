<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/users', function () {
        return "User List";
    })->name('users.index');
});


Route::middleware(['auth', 'verified', 'role:client'])->group(function () {

    Route::get('/client/dashboard', function () {
        return view('client.dashboard');
    })->name('client.dashboard');
});


Route::middleware(['auth', 'permission:users.create'])->group(function () {

    Route::get('/user/create', function () {
        return "Create User";
    })->name('users.create');
});

require __DIR__ . '/auth.php';