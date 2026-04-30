@extends('theme::layouts.app', ['title' => $title, 'heading' => $heading])

@section('content')
<div class="card" style="max-width:100%;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0;">{{ $heading }}</h1>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('settings.user') }}" class="linkbtn" style="{{ $scopeType === 'user' ? '' : 'opacity:.7;' }}">User Settings</a>
            <a href="{{ route('settings.business') }}" class="linkbtn" style="{{ $scopeType === 'business' ? '' : 'opacity:.7;' }}">Business Settings</a>
        </div>
    </div>

    @if(session('status'))
        <p style="margin-top:10px;color:#16a34a;">{{ session('status') }}</p>
    @endif

    @if($errors->any())
        <div style="margin-top:10px;color:#ef4444;">
            {{ $errors->first() }}
        </div>
    @endif

    @if(!$hasScope)
        <div style="margin-top:14px;border:1px solid var(--border);border-radius:12px;padding:14px;" class="muted">
            {{ $scopeType === 'business' ? 'No business found. Complete business onboarding first.' : 'No user scope found.' }}
        </div>
    @else
    @php
        $tabNames = $tabs->keys()->values();
        $activeTab = request('tab', $tabNames->first() ?? 'general');
    @endphp

    @if($tabs->isNotEmpty())
        <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
            @foreach($tabNames as $tabName)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $tabName]) }}"
                   class="linkbtn"
                   style="{{ $activeTab === $tabName ? '' : 'opacity:.75;' }}">
                    {{ ucfirst($tabName) }}
                </a>
            @endforeach
        </div>
        <div style="margin-top:12px;border:1px solid var(--border);border-radius:12px;padding:14px;display:grid;gap:10px;">
            @foreach($tabs->get($activeTab, collect()) as $setting)
                <div style="border:1px solid var(--border);border-radius:12px;padding:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:700;">{{ $setting['name'] }}</div>
                            <div class="muted" style="font-size:12px;">Key: {{ $setting['key'] }}</div>
                            @if($setting['description'])
                                <div class="muted" style="margin-top:4px;font-size:13px;">{{ $setting['description'] }}</div>
                            @endif
                        </div>
                        <span class="pkg-badge">{{ strtoupper($setting['type']) }}</span>
                    </div>

                    <form method="post" action="{{ route('settings.store') }}" style="margin-top:10px;display:grid;gap:8px;">
                        @csrf
                        <input type="hidden" name="scope" value="{{ $scopeType }}">
                        <input type="hidden" name="key" value="{{ $setting['key'] }}">

                        @if($setting['type'] === 'select')
                            <select name="value"
                                    {{ $setting['required'] ? 'required' : '' }}
                                    {{ $setting['is_disabled'] ? 'disabled' : '' }}
                                    style="padding:10px;border:1px solid var(--border);border-radius:10px;background:var(--card);color:var(--text);">
                                @foreach($setting['options'] as $option)
                                    <option value="{{ $option['value'] ?? '' }}" {{ (string) ($setting['value'] ?? '') === (string) ($option['value'] ?? '') ? 'selected' : '' }}>
                                        {{ $option['label'] ?? $option['value'] ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($setting['type'] === 'textarea')
                            <textarea name="value"
                                      placeholder="{{ $setting['placeholder'] }}"
                                      {{ $setting['required'] ? 'required' : '' }}
                                      {{ $setting['is_disabled'] ? 'disabled' : '' }}
                                      rows="3"
                                      style="padding:10px;border:1px solid var(--border);border-radius:10px;background:var(--card);color:var(--text);">{{ $setting['value'] }}</textarea>
                        @elseif($setting['type'] === 'checkbox')
                            <label style="display:flex;align-items:center;gap:8px;">
                                <input type="hidden" name="value" value="0">
                                <input type="checkbox"
                                       name="value"
                                       value="1"
                                       {{ $setting['value'] ? 'checked' : '' }}
                                       {{ $setting['is_disabled'] ? 'disabled' : '' }}>
                                <span class="muted">{{ $setting['placeholder'] ?: 'Toggle this setting' }}</span>
                            </label>
                        @else
                            <input type="{{ $setting['type'] === 'number' ? 'number' : 'text' }}"
                                   name="value"
                                   value="{{ $setting['value'] }}"
                                   placeholder="{{ $setting['placeholder'] }}"
                                   {{ $setting['required'] ? 'required' : '' }}
                                   {{ $setting['is_disabled'] ? 'disabled' : '' }}
                                   style="padding:10px;border:1px solid var(--border);border-radius:10px;background:var(--card);color:var(--text);">
                        @endif

                        <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;">
                            <div class="muted" style="font-size:12px;">
                                {{ $setting['required'] ? 'Required' : 'Optional' }} • {{ $setting['is_enabled'] ? 'Enabled' : 'Disabled' }}
                            </div>
                            <div style="display:flex;gap:8px;">
                                <button type="submit" {{ $setting['is_disabled'] ? 'disabled' : '' }}>Save</button>
                    </form>
                                <form method="post" action="{{ route('settings.destroy') }}">
                                    @csrf
                                    @method('delete')
                                    <input type="hidden" name="scope" value="{{ $scopeType }}">
                                    <input type="hidden" name="key" value="{{ $setting['key'] }}">
                                    <button type="submit">Reset</button>
                                </form>
                            </div>
                        </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="margin-top:14px;" class="muted">No {{ $scopeType }} settings yet.</p>
    @endif
    @endif
</div>
@endsection
