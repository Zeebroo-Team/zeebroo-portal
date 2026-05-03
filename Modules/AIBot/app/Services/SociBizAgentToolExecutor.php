<?php

namespace Modules\AIBot\Services;

use App\Models\User;
use Modules\Account\Models\Account;
use Modules\Account\Services\BillService;
use Modules\Account\Services\LoanOverviewTooltipService;
use Modules\Account\Services\LoanService;
use Modules\Account\Services\RentalService;
use Modules\Business\Models\Business;
use Modules\HRManagement\Services\EmployeeService;
use Modules\Transaction\Models\LedgerTransaction;

readonly class SociBizAgentToolExecutor
{
    public function __construct(
        private LoanService $loanService,
        private LoanOverviewTooltipService $loanOverview,
        private RentalService $rentalService,
        private BillService $billService,
        private EmployeeService $employeeService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(User $user, ?Business $business, string $name, array $args): array
    {
        try {
            return match ($name) {
                'soci_biz_workspace_overview' => $this->workspaceOverview($user, $business),
                'soci_biz_list_accounts' => $this->listAccounts($user, $business),
                'soci_biz_list_loans' => $this->listLoans($user, $business),
                'soci_biz_list_rentals' => $this->listRentals($user, $business),
                'soci_biz_list_bills' => $this->listBills($user, $business),
                'soci_biz_list_recent_transactions' => $this->listRecentTransactions($user, $business, $args),
                'soci_biz_list_employees' => $this->listEmployees($user, $business),
                'soci_biz_list_departments' => $this->listDepartments($user, $business),
                'soci_biz_list_job_titles' => $this->listJobTitles($user, $business),
                'soci_biz_list_branches' => $this->listBranches($user, $business),
                default => ['error' => 'Unknown tool: '.$name],
            };
        } catch (\Throwable $e) {
            return ['error' => 'Tool failed: '.$e->getMessage()];
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function functionDeclarations(): array
    {
        return [
            [
                'name' => 'soci_biz_workspace_overview',
                'description' => 'Summary of the signed-in user, selected business, display currency, HR payroll opt-in, and record counts (accounts, loans, rentals, bills, employees, departments, job titles, branches). Use this alone for “how many departments/employees/…” when counts are enough. Call first when you need grounding.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_accounts',
                'description' => 'Bank / cash accounts for the current business with balances and bank labels.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_loans',
                'description' => 'Loans for the business: principal, cadence, approximate monthly service, overdue flag.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_rentals',
                'description' => 'Rentals / leases: property, cadence, recurring cost, overdue flag, next billing hint.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_bills',
                'description' => 'Recurring or one-off bills for the business with overdue flag (per SociBiz rules).',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_recent_transactions',
                'description' => 'Recent ledger transactions (loan/rental/bill payments) with amounts and source labels.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => [
                            'type' => 'integer',
                            'description' => 'Max rows (default 25, max 75).',
                        ],
                    ],
                    'required' => [],
                ],
            ],
            [
                'name' => 'soci_biz_list_employees',
                'description' => 'Employees for HR — only populated when payroll/HR onboarding is opted in.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_departments',
                'description' => 'Lists department names and ids for HR. Includes `count`. Use soci_biz_workspace_overview first for “how many departments” unless the user needs names.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_job_titles',
                'description' => 'Job titles / designations for HR.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
            [
                'name' => 'soci_biz_list_branches',
                'description' => 'Branches / warehouses when multi-location mode is enabled for the business.',
                'parameters' => ['type' => 'object', 'properties' => (object) [], 'required' => []],
            ],
        ];
    }

    /** @param  array<string, mixed>  $args */
    private function workspaceOverview(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return [
                'user' => $user->only(['id', 'name', 'email']),
                'business' => null,
                'message' => 'No business selected or access denied — ask the user to pick a business in the header dropdown.',
            ];
        }

        $currency = (string) (get_settings('business.currency', '', $business) ?: '');
        $hrOptedIn = (bool) get_settings('hr.payroll.opted_in', false, $business);

        return [
            'user' => $user->only(['id', 'name', 'email']),
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'category' => $business->category,
                'display_currency_setting' => $currency,
                'multi_warehouse_branch_enabled' => $business->multiWarehouseBranchEnabled(),
            ],
            'hr_payroll_opted_in' => $hrOptedIn,
            'counts' => [
                'accounts' => Account::query()->where('user_id', $user->id)->where('business_id', $business->id)->count(),
                'loans' => $business->loans()->count(),
                'rentals' => $business->rentals()->count(),
                'bills' => $business->bills()->count(),
                'employees' => $business->employees()->count(),
                'departments' => $business->departments()->count(),
                'job_titles' => $business->jobTitles()->count(),
                'branches' => $business->branches()->count(),
            ],
            'rules' => 'Use tools for facts; never invent balances or overdue status. Read-only — you cannot post payments from chat.',
        ];
    }

    private function listAccounts(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        $rows = Account::query()
            ->with(['bankType', 'bank'])
            ->where('user_id', $user->id)
            ->where('business_id', $business->id)
            ->orderBy('account_name')
            ->get();

        return [
            'currency_setting' => (string) (get_settings('business.currency', '', $business) ?: ''),
            'accounts' => $rows->map(fn (Account $a) => [
                'id' => $a->id,
                'label' => $a->deductOptionLabel(),
                'current_balance' => (string) $a->current_balance,
            ])->values()->all(),
        ];
    }

    private function listLoans(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        $loans = $this->loanService->listForBusiness($business);
        $currency = (string) (get_settings('business.currency', '', $business) ?: '');
        $out = [];

        foreach ($loans as $loan) {
            $summary = $this->loanOverview->summarizeLoan($loan);
            $out[] = [
                'id' => $loan->id,
                'name' => $loan->name,
                'borrowed_amount' => (string) $loan->borrowed_amount,
                'recurring_type' => $loan->recurring_type,
                'approx_monthly_service' => $summary['approx_monthly'] ?? null,
                'has_overdue_installment' => $this->loanOverview->loanHasOverdueInstallments($loan),
                'detail_url_hint' => 'account.loans.show',
            ];
        }

        return ['currency_setting' => $currency, 'loans' => $out];
    }

    private function listRentals(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        $currency = (string) (get_settings('business.currency', '', $business) ?: '');
        $overdueMap = $this->rentalService->rentalOverdueMapForBusiness($business);
        $rentals = $this->rentalService->listForBusiness($business);
        $out = [];

        foreach ($rentals as $rental) {
            $insight = $this->rentalService->nextPaymentInsight($rental);
            $out[] = [
                'id' => $rental->id,
                'title' => $rental->purpose ?: $rental->property_type,
                'property_type' => $rental->property_type,
                'recurring_type' => $rental->recurring_type,
                'recurring_cost' => (string) $rental->recurring_cost,
                'overdue_billing_flag' => (bool) ($overdueMap[$rental->id] ?? false),
                'next_billing_days_until' => $insight ? $insight['days_until'] : null,
                'next_billing_date' => $insight ? $insight['next_date']->toDateString() : null,
            ];
        }

        return ['currency_setting' => $currency, 'rentals' => $out];
    }

    private function listBills(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        $currency = (string) (get_settings('business.currency', '', $business) ?: '');
        $overdueMap = $this->billService->billOverdueMapForBusiness($business);
        $bills = $this->billService->listForBusiness($business);
        $out = [];

        foreach ($bills as $bill) {
            $insight = $this->billService->nextPaymentInsight($bill);
            $out[] = [
                'id' => $bill->id,
                'name' => $bill->name,
                'recurring_type' => $bill->recurring_type,
                'recurring_cost' => (string) $bill->recurring_cost,
                'overdue_flag' => (bool) ($overdueMap[$bill->id] ?? false),
                'next_payment_days_until' => $insight ? $insight['days_until'] : null,
                'next_payment_date' => $insight ? $insight['next_date']->toDateString() : null,
            ];
        }

        return ['currency_setting' => $currency, 'bills' => $out];
    }

    /** @param  array<string, mixed>  $args */
    private function listRecentTransactions(User $user, ?Business $business, array $args): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        $limit = (int) ($args['limit'] ?? 25);
        $limit = max(1, min(75, $limit));

        $rows = LedgerTransaction::query()
            ->where('business_id', $business->id)
            ->with([
                'transactionable',
                'deductAccount.bankType',
                'deductAccount.bank',
            ])
            ->orderByDesc('occurrence_date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return [
            'limit' => $limit,
            'transactions' => $rows->map(function (LedgerTransaction $row) {
                return [
                    'id' => $row->id,
                    'occurrence_date' => $row->occurrence_date?->toDateString(),
                    'amount' => (string) $row->amount,
                    'currency' => $row->currency,
                    'source_kind' => $row->sourceKindLabel(),
                    'source_title' => $row->sourceTitle(),
                    'deduct_account_label' => $row->deductAccount ? $row->deductAccount->deductOptionLabel() : null,
                    'period_number' => $row->period_number,
                ];
            })->values()->all(),
        ];
    }

    private function listEmployees(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        if (! $this->hrOptedIn($business)) {
            return [
                'employees' => [],
                'note' => 'HR payroll is not opted in — complete HR setup in the workspace to access employees.',
            ];
        }

        $employees = $this->employeeService->listForBusiness($business);

        return [
            'employees' => $employees->map(fn ($e) => [
                'id' => $e->id,
                'full_name' => $e->full_name,
                'employment_type' => $e->employment_type,
                'date_of_joining' => $e->date_of_joining?->toDateString(),
                'department' => $e->department?->name,
                'job_title' => $e->jobTitle?->name,
                'employee_id_ref' => $e->employee_id,
            ])->values()->all(),
        ];
    }

    private function listDepartments(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        if (! $this->hrOptedIn($business)) {
            $deptCount = $business->departments()->count();

            return [
                'count' => $deptCount,
                'departments' => [],
                'note' => 'HR payroll not opted in — name list is hidden here; use soci_biz_workspace_overview `counts.departments` for the total, or complete HR setup to list names.',
            ];
        }

        $rows = $business->departments()->orderBy('name')->get(['id', 'name']);

        return [
            'count' => $rows->count(),
            'departments' => $rows->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->all(),
        ];
    }

    private function listJobTitles(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        if (! $this->hrOptedIn($business)) {
            return ['job_titles' => [], 'note' => 'HR payroll not opted in.'];
        }

        $rows = $business->jobTitles()->orderBy('name')->get(['id', 'name']);

        return [
            'job_titles' => $rows->map(fn ($j) => ['id' => $j->id, 'name' => $j->name])->all(),
        ];
    }

    private function listBranches(User $user, ?Business $business): array
    {
        if (! $this->ownsBusiness($user, $business)) {
            return $this->needBusiness();
        }

        if (! $business->multiWarehouseBranchEnabled()) {
            return [
                'branches' => [],
                'note' => 'Multi-warehouse / branch mode is disabled for this business.',
            ];
        }

        $rows = $business->branches()->orderBy('name')->get(['id', 'name']);

        return [
            'branches' => $rows->map(fn ($b) => ['id' => $b->id, 'name' => $b->name])->all(),
        ];
    }

    private function ownsBusiness(User $user, ?Business $business): bool
    {
        return $business !== null && $user->businesses()->whereKey($business->id)->exists();
    }

    private function hrOptedIn(?Business $business): bool
    {
        return $business !== null && (bool) get_settings('hr.payroll.opted_in', false, $business);
    }

    /** @return array<string, string> */
    private function needBusiness(): array
    {
        return ['error' => 'No authorised business selected. Ask the user to choose their business from the SociBiz header.'];
    }
}
