<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'history'])->name('transactions.history');
    Route::post('/transactions/topup', [TransactionController::class, 'topupSaldo'])->name('transactions.topup');
    Route::post('/transactions/purchase', [TransactionController::class, 'purchaseGame'])->name('transactions.purchase');
});



require __DIR__.'/auth.php';
