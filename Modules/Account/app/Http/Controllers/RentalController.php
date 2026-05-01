<?php

namespace Modules\Account\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Account\Models\Account;
use Modules\Account\Models\Rental;
use Modules\Account\Services\RentalService;
use Modules\Business\Models\Business;

class RentalController extends Controller
{
    public function __construct(private readonly RentalService $rentalService)
    {
    }

    public function index(Request $request)
    {
        $business = Business::currentForNavbar($request->user());
        $rentals = $business
            ? $this->rentalService->listForBusiness($business)
            : collect();

        $accounts = $business
            ? Account::query()
                ->with(['bankType', 'bank', 'warehouse'])
                ->where('user_id', $request->user()->id)
                ->where('business_id', $business->id)
                ->orderBy('account_name')
                ->get()
            : collect();

        return view('account::rentals.index', array_merge([
            'business' => $business,
            'rentals' => $rentals,
            'accounts' => $accounts,
            'recurringTypes' => Rental::recurringTypes(),
        ], $this->warehousesFormContext($request)));
    }

    public function store(Request $request): RedirectResponse
    {
        $business = Business::currentForNavbar($request->user());
        if (!$business) {
            return redirect()->route('dashboard')->withErrors(['business' => 'Select or create a business first.']);
        }

        $request->merge([
            'branch_id' => $request->filled('branch_id') ? $request->integer('branch_id') : null,
            'deduct_account_id' => $request->filled('deduct_account_id') ? $request->integer('deduct_account_id') : null,
            'key_money' => $request->filled('key_money') ? $request->input('key_money') : null,
        ]);

        $data = $request->validate([
            'property_type' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:2000'],
            'key_money' => ['nullable', 'numeric', 'min:0'],
            'agreement_valid_until_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn ($q) => $q->where('business_id', $business->id)),
            ],
            'deduct_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounts', 'id')->where(fn ($q) => $q
                    ->where('user_id', $request->user()->id)
                    ->where('business_id', $business->id)),
            ],
            'recurring_cost' => ['required', 'numeric', 'min:0'],
            'recurring_type' => ['required', Rule::in([
                Rental::RECURRING_PER_DAY,
                Rental::RECURRING_PER_MONTH,
                Rental::RECURRING_PER_YEAR,
            ])],
        ]);

        $data = $this->finalizeWarehouseBranchOnRental($business, $data);

        $this->rentalService->create($request->user(), $business, $data);

        return redirect()->route('account.rentals.index')->with('status', 'Rental saved.');
    }

    public function destroy(Request $request, Rental $rental): RedirectResponse
    {
        abort_unless($this->rentalService->deleteForUser($request->user(), $rental), 403);

        return redirect()->route('account.rentals.index')->with('status', 'Rental removed.');
    }

    /**
     * @return array{accountBusinessMultiWarehouse: array<int, bool>, accountBranchesByBusiness: array<int, list<array{id: int, name: string}>>}
     */
    private function warehousesFormContext(Request $request): array
    {
        $businesses = Business::query()
            ->where('user_id', Auth::id())
            ->with(['branches' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        $multiWarehouse = [];
        $byBusiness = [];

        foreach ($businesses as $biz) {
            $multiWarehouse[$biz->id] = $biz->multiWarehouseBranchEnabled();
            $byBusiness[$biz->id] = $biz->branches->map(fn ($br) => [
                'id' => $br->id,
                'name' => $br->name,
            ])->values()->all();
        }

        return [
            'accountBusinessMultiWarehouse' => $multiWarehouse,
            'accountBranchesByBusiness' => $byBusiness,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function finalizeWarehouseBranchOnRental(Business $business, array $data): array
    {
        if (!$business->multiWarehouseBranchEnabled()) {
            $data['branch_id'] = null;
        } elseif (empty($data['branch_id'])) {
            $data['branch_id'] = null;
        } else {
            $data['branch_id'] = (int) $data['branch_id'];
        }

        return $data;
    }
}
