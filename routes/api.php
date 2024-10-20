<?php

use App\Http\Controllers\{AuthController, ContactController};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'api'     => 'operational',
    ]);
});

Route::prefix('auth')
    ->middleware('guest')
    ->group(function () {
        /**
         * Auth / Reset Password
         */
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    });

Route::prefix('/v1')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        /**
        * Contacts
        */
        Route::get('contacts/', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/search', [ContactController::class, 'search'])->name('contacts.search');
        Route::post('contacts/', [ContactController::class, 'store'])->name('contacts.store');
        Route::get('contacts/{id}', [ContactController::class, 'show'])->name('contacts.show');
        Route::put('contacts/{id}', [ContactController::class, 'update'])->name('contacts.update');
        Route::delete('contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    });
