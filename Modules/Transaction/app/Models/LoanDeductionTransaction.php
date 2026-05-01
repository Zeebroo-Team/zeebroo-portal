<?php

namespace Modules\Transaction\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Account\Models\Account;
use Modules\Account\Models\Loan;
use Modules\Business\Models\Business;

class LoanDeductionTransaction extends Model
{
    protected $table = 'loan_deduction_transactions';

    protected $fillable = [
        'business_id',
        'user_id',
        'loan_id',
        'deduct_account_id',
        'deduction_date',
        'period_number',
        'amount',
        'currency',
        'cadence_snapshot',
        'periods_total_snapshot',
        'borrowed_principal_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'deduction_date' => 'date',
            'period_number' => 'integer',
            'amount' => 'decimal:2',
            'periods_total_snapshot' => 'integer',
            'borrowed_principal_snapshot' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function deductAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'deduct_account_id');
    }
}
