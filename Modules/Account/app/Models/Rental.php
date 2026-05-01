<?php

namespace Modules\Account\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Business\Models\Branch;
use Modules\Business\Models\Business;

class Rental extends Model
{
    public const RECURRING_PER_DAY = 'per_day';

    public const RECURRING_PER_MONTH = 'per_month';

    public const RECURRING_PER_YEAR = 'per_year';

    protected $fillable = [
        'user_id',
        'business_id',
        'branch_id',
        'property_type',
        'purpose',
        'key_money',
        'agreement_valid_until_year',
        'deduct_account_id',
        'recurring_cost',
        'recurring_type',
    ];

    protected function casts(): array
    {
        return [
            'key_money' => 'decimal:2',
            'recurring_cost' => 'decimal:2',
            'agreement_valid_until_year' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function deductAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'deduct_account_id');
    }

    public static function recurringTypes(): array
    {
        return [
            self::RECURRING_PER_DAY => 'Per day',
            self::RECURRING_PER_MONTH => 'Per month',
            self::RECURRING_PER_YEAR => 'Per year',
        ];
    }
}
