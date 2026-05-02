<?php

namespace Modules\HRManagement\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Business\Models\Business;
use Modules\HRManagement\Models\Employee;

class EmployeeService
{
    /** @return Collection<int, Employee> */
    public function listForBusiness(Business $business): Collection
    {
        return $business->employees()->with(['bank', 'department', 'jobTitle'])->get();
    }

    /** @param  array<string, mixed>  $data */
    public function create(Business $business, array $data): Employee
    {
        foreach (['epf_number', 'etf_number', 'tax_tin'] as $key) {
            if (($data[$key] ?? null) === '') {
                $data[$key] = null;
            }
        }

        return $business->employees()->create($data);
    }
}
