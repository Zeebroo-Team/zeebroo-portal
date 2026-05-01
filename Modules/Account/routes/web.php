<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;
use Modules\Account\Http\Controllers\LoanController;
use Modules\Account\Http\Controllers\RentalController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('account/onboarding', [AccountController::class, 'onboarding'])->name('account.onboarding');
    Route::get('loans', [LoanController::class, 'index'])->name('account.loans.index');
    Route::get('loans/{loan}', [LoanController::class, 'show'])->name('account.loans.show');
    Route::post('loans/{loan}/installments/settle', [LoanController::class, 'settleInstallment'])->name('account.loans.installments.settle');
    Route::post('loans', [LoanController::class, 'store'])->name('account.loans.store');
    Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('account.loans.destroy');
    Route::get('rentals', [RentalController::class, 'index'])->name('account.rentals.index');
    Route::post('rentals', [RentalController::class, 'store'])->name('account.rentals.store');
    Route::get('rentals/{rental}', [RentalController::class, 'show'])->name('account.rentals.show');
    Route::post('rentals/{rental}/billing/settle', [RentalController::class, 'settleBilling'])->name('account.rentals.billing.settle');
    Route::get('rentals/{rental}/edit', [RentalController::class, 'edit'])->name('account.rentals.edit');
    Route::patch('rentals/{rental}', [RentalController::class, 'update'])->name('account.rentals.update');
    Route::delete('rentals/{rental}', [RentalController::class, 'destroy'])->name('account.rentals.destroy');
    Route::resource('accounts', AccountController::class)->names('account');
});
