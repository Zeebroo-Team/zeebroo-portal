<?php

use Illuminate\Support\Facades\Route;
use Modules\AIBot\Http\Controllers\AIBotController;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('ai-bot', [AIBotController::class, 'index'])->name('aibot.index');
});
