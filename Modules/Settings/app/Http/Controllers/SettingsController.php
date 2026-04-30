<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Modules\Settings\Services\SettingsService;

class SettingsController extends Controller
{
    public function index(Request $request, SettingsService $settingsService)
    {
        return $this->user($request, $settingsService);
    }

    public function user(Request $request, SettingsService $settingsService)
    {
        $user = $request->user();

        return $this->renderSettingsPage(
            scopeType: 'user',
            scopeModel: $user,
            title: 'User Settings',
            heading: 'User Settings',
            settingsService: $settingsService
        );
    }

    public function business(Request $request, SettingsService $settingsService)
    {
        $business = $request->user()?->businesses()->latest()->first();

        return $this->renderSettingsPage(
            scopeType: 'business',
            scopeModel: $business,
            title: 'Business Settings',
            heading: 'Business Settings',
            settingsService: $settingsService
        );
    }

    public function store(Request $request, SettingsService $settingsService)
    {
        $validated = $request->validate([
            'scope' => ['required', 'in:user,business'],
            'key' => ['required', 'string', 'max:100'],
            'value' => ['nullable'],
        ]);

        $definition = $this->findDefinition($validated['scope'], $validated['key']);
        if (!$definition || !$definition['is_enabled'] || $definition['is_disabled']) {
            return redirect()->back()->withErrors(['key' => 'Invalid or disabled setting field.']);
        }

        $user = $request->user();
        $scope = $validated['scope'] === 'business'
            ? $user?->businesses()->latest()->first()
            : $user;

        if (!$scope) {
            return redirect()->back()->withErrors(['scope' => 'Business not found for this user.']);
        }

        $value = $this->normalizeInputValue($request, $definition);

        if ($definition['required'] && ($value === null || $value === '')) {
            return redirect()->back()->withErrors(['value' => 'This setting field is required.']);
        }

        $settingsService->set($scope, $validated['key'], $value);

        $routeName = $validated['scope'] === 'business' ? 'settings.business' : 'settings.user';

        return redirect()->route($routeName)->with('status', 'Setting saved successfully.');
    }

    public function destroy(Request $request, SettingsService $settingsService)
    {
        $validated = $request->validate([
            'scope' => ['required', 'in:user,business'],
            'key' => ['required', 'string', 'max:100'],
        ]);

        $user = $request->user();
        $scope = $validated['scope'] === 'business'
            ? $user?->businesses()->latest()->first()
            : $user;

        if (!$scope) {
            return redirect()->back()->withErrors(['scope' => 'Business not found for this user.']);
        }

        $settingsService->forget($scope, $validated['key']);

        $routeName = $validated['scope'] === 'business' ? 'settings.business' : 'settings.user';

        return redirect()->route($routeName)->with('status', 'Setting deleted successfully.');
    }

    private function renderSettingsPage(
        string $scopeType,
        ?Model $scopeModel,
        string $title,
        string $heading,
        SettingsService $settingsService
    ) {
        $definitions = $this->getDefinitionsByScope($scopeType);
        $settings = $scopeModel ? $settingsService->allForScope($scopeModel) : collect();
        $tabs = $this->buildTabs($definitions, $settings);

        return view('settings::index', [
            'title' => $title,
            'heading' => $heading,
            'scopeType' => $scopeType,
            'hasScope' => (bool) $scopeModel,
            'tabs' => $tabs,
        ]);
    }

    private function buildTabs(Collection $definitions, Collection $settings): Collection
    {
        if ($definitions->isEmpty()) {
            return collect();
        }

        return $definitions
            ->filter(fn (array $definition) => $definition['is_enabled'] && !$definition['is_disabled'])
            ->map(function (array $definition) use ($settings): array {
                $tab = (string) ($definition['tab'] ?: explode('.', $definition['key'])[0] ?? 'general');
                $key = $definition['key'];
                $hasValue = $settings->has($key);
                $currentValue = $hasValue ? $settings->get($key) : ($definition['default'] ?? null);

                return [
                    ...$definition,
                    'tab' => $tab,
                    'value' => $currentValue,
                ];
            })
            ->groupBy('tab')
            ->map(fn (Collection $items) => $items->values());
    }

    private function getDefinitionsByScope(string $scopeType): Collection
    {
        $path = base_path('Modules/Settings/database/seeders/settings-fields.json');
        if (!File::exists($path)) {
            return collect();
        }

        $payload = json_decode((string) File::get($path), true);
        $definitions = is_array($payload[$scopeType] ?? null) ? $payload[$scopeType] : [];

        return collect($definitions)
            ->filter(fn ($definition) => is_array($definition) && isset($definition['key']))
            ->map(function (array $definition): array {
                return [
                    'tab' => (string) ($definition['tab'] ?? ''),
                    'key' => (string) ($definition['key'] ?? ''),
                    'name' => (string) ($definition['name'] ?? ($definition['key'] ?? '')),
                    'type' => (string) ($definition['type'] ?? 'text'),
                    'default' => $definition['default'] ?? null,
                    'options' => is_array($definition['options'] ?? null) ? $definition['options'] : [],
                    'required' => (bool) ($definition['required'] ?? false),
                    'description' => (string) ($definition['description'] ?? ''),
                    'placeholder' => (string) ($definition['placeholder'] ?? ''),
                    'is_enabled' => (bool) ($definition['is_enabled'] ?? true),
                    'is_disabled' => (bool) ($definition['is_disabled'] ?? false),
                ];
            })
            ->values();
    }

    private function findDefinition(string $scopeType, string $key): ?array
    {
        return $this->getDefinitionsByScope($scopeType)
            ->first(fn (array $definition) => $definition['key'] === $key);
    }

    private function normalizeInputValue(Request $request, array $definition): mixed
    {
        $type = $definition['type'];
        $rawValue = $request->input('value');

        if ($type === 'checkbox') {
            return (bool) $request->boolean('value');
        }

        if ($type === 'number') {
            return is_numeric($rawValue) ? (int) $rawValue : 0;
        }

        if ($type === 'select') {
            $allowed = collect($definition['options'])
                ->map(fn ($option) => is_array($option) ? ($option['value'] ?? null) : null)
                ->filter(fn ($value) => $value !== null)
                ->values();

            if ($allowed->isNotEmpty() && !$allowed->contains($rawValue)) {
                return $definition['default'] ?? null;
            }
        }

        return $rawValue;
    }
}
