<?php

namespace Modules\HRManagement\Services;

use Modules\Business\Models\Business;
use Modules\HRManagement\Models\Department;

class DepartmentService
{
    public function create(Business $business, string $name): Department
    {
        return $business->departments()->create([
            'name' => trim($name),
        ]);
    }
}
