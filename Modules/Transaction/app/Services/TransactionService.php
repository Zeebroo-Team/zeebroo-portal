<?php

namespace Modules\Transaction\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Business\Models\Business;
use Modules\Transaction\Models\LoanDeductionTransaction;

class TransactionService
{
    /** @return LengthAwarePaginator<LoanDeductionTransaction>|Collection<int, LoanDeductionTransaction> */
    public function listForBusiness(?Business $business, int $perPage = 40): LengthAwarePaginator|Collection
    {
        if (! $business) {
            return collect();
        }

        return LoanDeductionTransaction::query()
            ->where('business_id', $business->id)
            ->with(['loan.bank', 'deductAccount.bankType', 'deductAccount.bank'])
            ->orderByDesc('deduction_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }
}
