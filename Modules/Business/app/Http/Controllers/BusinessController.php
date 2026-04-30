<?php

namespace Modules\Business\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Business\Services\BusinessService;

class BusinessController extends Controller
{
    public function __construct(private readonly BusinessService $businessService)
    {
    }

    public function storeOnboarding(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->businessService->upsertForUser($request->user(), $data);

        return redirect()->route('dashboard')->with('status', 'Business profile saved.');
    }
}
