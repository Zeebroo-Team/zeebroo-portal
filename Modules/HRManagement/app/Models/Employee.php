<?php

namespace Modules\HRManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Account\Models\Bank;
use Modules\Business\Models\Business;

class Employee extends Model
{
    protected $table = 'hr_employees';

    /** Sentinel for dept / job-title dropdowns matching `option value`; creates related row before saving employee. */
    public const SELECT_NEW_ROW = 'new';

    public const EMPLOYMENT_FULL_TIME = 'full_time';

    public const EMPLOYMENT_PART_TIME = 'part_time';

    public const EMPLOYMENT_CONTRACT = 'contract';

    /** @var list<string> */
    public const EMPLOYMENT_TYPES = [
        self::EMPLOYMENT_FULL_TIME,
        self::EMPLOYMENT_PART_TIME,
        self::EMPLOYMENT_CONTRACT,
    ];

    protected $fillable = [
        'full_name',
        'date_of_birth',
        'nic_passport_number',
        'permanent_address',
        'current_address',
        'phone_number',
        'personal_email',
        'employee_id',
        'job_title_id',
        'department_id',
        'date_of_joining',
        'employment_type',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'bank_account_holder_name',
        'bank_id',
        'bank_branch',
        'bank_account_number',
        'epf_number',
        'etf_number',
        'tax_tin',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function employmentTypeLabel(): string
    {
        return match ($this->employment_type) {
            self::EMPLOYMENT_FULL_TIME => 'Full-Time',
            self::EMPLOYMENT_PART_TIME => 'Part-Time',
            self::EMPLOYMENT_CONTRACT => 'Contract',
            default => $this->employment_type,
        };
    }
}
