<?php

namespace Modules\Settings\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Business\Models\Business;
use Modules\Settings\Services\SettingsService;

class SeedBusinessSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $businessId)
    {
    }

    public function handle(SettingsService $settingsService): void
    {
        $business = Business::query()->find($this->businessId);
        if (!$business) {
            return;
        }

        $defaultBusinessSettings = $settingsService->getDefaultSettingsByScope('business');
        if (empty($defaultBusinessSettings)) {
            return;
        }

        $settingsService->setMany($business, $defaultBusinessSettings);
    }
}
