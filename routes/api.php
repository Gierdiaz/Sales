<?php

use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/contacts')->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/search', [ContactController::class, 'search'])->name('contacts.search');
    Route::post('/', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/{id}', [ContactController::class, 'show'])->name('contacts.show');
    Route::put('/{id}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});
