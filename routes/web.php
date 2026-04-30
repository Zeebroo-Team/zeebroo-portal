<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return view('dashboard');
    }

    return view('auth::auth.login');
})->name('home');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'adminPanel'])->middleware('role:admin')->name('admin.panel');
    Route::post('/account/select', function (\Illuminate\Http\Request $request) {
        $business = $request->user()?->businesses()->latest()->first();

        if (!$business) {
            return redirect()->back();
        }

        $accountId = (int) $request->input('account_id');
        $isValid = \Modules\Account\Models\Account::query()
            ->where('id', $accountId)
            ->where('user_id', $request->user()->id)
            ->where('business_id', $business->id)
            ->exists();

        if ($isValid) {
            session(['selected_account_id' => $accountId]);
        } else {
            session()->forget('selected_account_id');
        }

        return redirect()->back();
    })->name('account.select');
});
