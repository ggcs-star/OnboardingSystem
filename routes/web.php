<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('client')) {
            if (! $user->client || $user->client->projects()->doesntExist()) {
                return redirect()->route('client.onboarding.index');
            }

            return redirect()->route('client.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/coming-soon/{label?}', function (?string $label = 'This section') {
        return view('admin.coming-soon', ['label' => $label]);
    })->name('coming-soon');
});


Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

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
require __DIR__ . '/admin.php';
require __DIR__ . '/client.php';