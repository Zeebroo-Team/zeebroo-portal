<?php

use Illuminate\Database\Eloquent\Model;
use Modules\Settings\Services\SettingsService;

if (!function_exists('scope_setting')) {
    function scope_setting(Model $scope, string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($scope, $key, $default);
    }
}
