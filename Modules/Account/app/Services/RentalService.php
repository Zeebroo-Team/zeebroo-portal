<?php

namespace Modules\Account\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Modules\Account\Models\Rental;
use Modules\Business\Models\Business;

class RentalService
{
    public function listForBusiness(Business $business): Collection
    {
        return Rental::query()
            ->with(['warehouse', 'deductAccount.bank', 'deductAccount.bankType'])
            ->where('business_id', $business->id)
            ->latest()
            ->get();
    }

    public function create(User $user, Business $business, array $data): Rental
    {
        $data['user_id'] = $user->id;
        $data['business_id'] = $business->id;

        return Rental::create($data);
    }

    public function deleteForUser(User $user, Rental $rental): bool
    {
        $businessIds = $user->businesses()->pluck('id')->all();
        if ((int) $rental->user_id !== (int) $user->id || !in_array((int) $rental->business_id, $businessIds, true)) {
            return false;
        }

        $rental->delete();

        return true;
    }
}
