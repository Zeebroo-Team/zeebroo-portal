<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Account\Models\Account;
use Modules\Account\Models\Bank;
use Modules\Account\Models\BankType;
use Modules\Account\Services\AccountService;
use Modules\Business\Models\Business;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function index()
    {
        $accounts = Account::with(['bankType', 'business', 'bank'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        if (request()->expectsJson()) {
            return response()->json(['data' => $accounts]);
        }

        return view('account::index', compact('accounts'));
    }

    public function create()
    {
        return view('account::create', [
            'bankTypes' => BankType::orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
            'businesses' => Business::where('user_id', Auth::id())->orderBy('name')->get(),
        ]);
    }

    public function onboarding()
    {
        return view('account::onboarding', [
            'bankTypes' => BankType::orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
            'businesses' => Business::where('user_id', Auth::id())->orderBy('name')->get(),
            'defaultBusiness' => Business::where('user_id', Auth::id())->latest()->first(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'business_id' => ['required', Rule::exists('businesses', 'id')->where('user_id', Auth::id())],
            'account_name' => ['required', 'string', 'max:255'],
            'bank_type_id' => ['required', 'exists:bank_types,id'],
            'bank_id' => ['required', 'exists:banks,id'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'bank_officer_contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $account = $this->accountService->create($request->user(), $data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Account created.', 'data' => $account->load(['bankType', 'business', 'bank'])], 201);
        }

        if ($request->boolean('from_onboarding')) {
            return redirect()->route('dashboard')->with('status', 'Current account setup completed.');
        }

        return redirect()->route('account.index')->with('status', 'Account created.');
    }

    public function show($id)
    {
        $account = Account::with(['bankType', 'business', 'bank'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json(['data' => $account]);
        }

        return view('account::show', compact('account'));
    }

    public function edit($id)
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);

        return view('account::edit', [
            'account' => $account,
            'bankTypes' => BankType::orderBy('name')->get(),
            'banks' => Bank::orderBy('name')->get(),
            'businesses' => Business::where('user_id', Auth::id())->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id): JsonResponse|RedirectResponse
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'business_id' => ['required', Rule::exists('businesses', 'id')->where('user_id', Auth::id())],
            'account_name' => ['required', 'string', 'max:255'],
            'bank_type_id' => ['required', 'exists:bank_types,id'],
            'bank_id' => ['required', 'exists:banks,id'],
            'bank_account_number' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'current_balance' => ['required', 'numeric', 'min:0'],
            'bank_officer_contact' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $account = $this->accountService->update($account, $data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Account updated.', 'data' => $account->load(['bankType', 'business', 'bank'])]);
        }

        return redirect()->route('account.index')->with('status', 'Account updated.');
    }

    public function destroy($id): JsonResponse|RedirectResponse
    {
        $account = Account::where('user_id', Auth::id())->findOrFail($id);
        $account->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Account deleted.']);
        }

        return redirect()->route('account.index')->with('status', 'Account deleted.');
    }
}
