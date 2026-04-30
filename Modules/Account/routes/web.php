<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\AccountController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('account/onboarding', [AccountController::class, 'onboarding'])->name('account.onboarding');
    Route::resource('accounts', AccountController::class)->names('account');
});
