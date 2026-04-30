<?php

use Illuminate\Support\Facades\Route;
use Modules\Business\Http\Controllers\BusinessController;

Route::middleware(['auth'])->group(function (): void {
    Route::post('/business/onboarding', [BusinessController::class, 'storeOnboarding'])->name('business.onboarding.store');
});
