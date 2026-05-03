@extends('theme::layouts.app', [
    'title' => __('Department · :name', ['name' => $department->name]),
    'heading' => __('Department'),
])

@section('content')
@php($tab = $activeTab ?? 'overview')
<style>
    .dept-show{max-width:100%;margin:0;}
    .dept-show__head{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-start;justify-content:space-between;margin-bottom:14px;}
    .dept-show__title{margin:0;font-size:clamp(1.15rem,2.2vw,1.35rem);font-weight:800;}
    .dept-show__muted{margin:6px 0 0;font-size:13px;line-height:1.45;color:var(--muted);max-width:56ch;}
    .dept-tabs{display:flex;gap:6px;margin:0 0 16px;flex-wrap:wrap;border-bottom:1px solid var(--border);padding-bottom:10px;}
    .dept-tabs a{padding:9px 14px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:600;color:var(--muted);border:1px solid transparent;}
    .dept-tabs a:hover{color:var(--text);border-color:color-mix(in srgb,var(--primary) 28%,var(--border));background:color-mix(in srgb,var(--primary) 8%,transparent);}
    .dept-tabs a.active{color:var(--text);border-color:color-mix(in srgb,var(--primary) 45%,var(--border));background:color-mix(in srgb,var(--primary) 12%,transparent);}
    .dept-panel{display:none;}
    .dept-panel.is-active{display:block;}
    .dept-stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-bottom:14px;}
    .dept-stat-grid--employment{grid-template-columns:repeat(3,minmax(0,1fr));}
    @@media (max-width:720px){.dept-stat-grid--employment{grid-template-columns:1fr;}}
    .dept-stat{border-radius:14px;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 96%,transparent);padding:14px 16px;}
    .dept-stat dt{margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);font-weight:700;}
    .dept-stat dd{margin:6px 0 0;font-size:22px;font-weight:820;color:var(--text);}
    .dept-stat--employment{display:flex;flex-direction:column;gap:12px;padding:14px;}
    .dept-stat__top{display:flex;gap:12px;align-items:flex-start;}
    .dept-stat__iconWrap{flex-shrink:0;width:40px;height:40px;border-radius:11px;display:flex;align-items:center;justify-content:center;background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary);font-size:17px;line-height:1;}
    .dept-stat__iconWrap i{font-style:normal;display:inline-block;line-height:1;}
    .dept-stat__title{margin:0;font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);font-weight:700;}
    .dept-stat__hint{margin:5px 0 0;font-size:12px;line-height:1.45;color:var(--muted);}
    .dept-stat__count{margin:0;font-size:24px;font-weight:820;color:var(--text);letter-spacing:-0.02em;}
    .dept-sub{font-size:15px;font-weight:750;margin:16px 0 8px;}
    .dept-chart-card{border-radius:12px;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 96%,transparent);padding:12px 14px;margin-bottom:14px;}
    .dept-chart-note{margin:0 0 8px;font-size:12px;line-height:1.45;color:var(--muted);max-width:72ch;}
    .dept-chart-foot{margin:10px 0 0;display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:8px 14px;font-size:12px;line-height:1.45;color:var(--muted);}
    .dept-chart-foot a{color:var(--primary);font-weight:600;text-decoration:none;white-space:nowrap;}
    .dept-chart-foot a:hover{text-decoration:underline;}
    .dept-empty-hint{margin:0 0 14px;line-height:1.5;font-size:13px;color:var(--muted);}
    .dept-empty-hint a{color:var(--primary);font-weight:600;text-decoration:none;}
    .dept-empty-hint a:hover{text-decoration:underline;}
    .cat-banner{margin:0 0 12px;padding:10px 12px;border-radius:10px;font-size:13px;}
    .cat-banner--ok{border:1px solid color-mix(in srgb,#22c55e 40%,var(--border));background:color-mix(in srgb,#22c55e 9%,transparent);font-weight:600;}
    .cat-banner--err{border:1px solid color-mix(in srgb,#f87171 40%,var(--border));background:color-mix(in srgb,#f87171 8%,transparent);}
    .dept-field label{display:block;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);margin-bottom:6px;}
    .dept-field input,.dept-field select{width:100%;box-sizing:border-box;padding:9px 10px;font-size:13px;border-radius:8px;border:1px solid var(--border);background:var(--card);color:var(--text);}
    .dept-table-wrap{border:1px solid var(--border);border-radius:11px;overflow:auto;}
    .dept-table{width:100%;border-collapse:collapse;font-size:13px;min-width:420px;}
    .dept-table th{text-align:left;padding:9px 12px;background:color-mix(in srgb,var(--card) 92%,transparent);font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);border-bottom:1px solid var(--border);}
    .dept-table td{padding:10px 12px;border-bottom:1px solid color-mix(in srgb,var(--border) 82%,transparent);vertical-align:top;}
    .dept-table tr:last-child td{border-bottom:none;}
    .dept-mgmt-card{border-radius:14px;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 98%,transparent);padding:16px 18px;margin-bottom:16px;}
    .dept-back{display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--primary);font-weight:600;text-decoration:none;margin-bottom:12px;}
    .dept-back:hover{text-decoration:underline;}
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;}
    .dept-lead-hint{margin:-4px 0 10px;font-size:11px;line-height:1.45;color:var(--muted);}
    .dept-combo{position:relative;margin-bottom:12px;}
    .dept-combo__control{display:flex;gap:6px;align-items:center;}
    .dept-combo__q{flex:1;box-sizing:border-box;padding:9px 10px;font-size:13px;border-radius:8px;border:1px solid var(--border);background:var(--card);color:var(--text);}
    .dept-combo__clear{flex-shrink:0;width:34px;height:34px;border-radius:8px;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 94%,transparent);color:var(--muted);cursor:pointer;font-size:18px;line-height:1;padding:0;}
    .dept-combo__clear:hover{color:var(--text);border-color:color-mix(in srgb,var(--primary) 35%,var(--border));}
    .dept-combo__list{position:absolute;left:0;right:0;top:100%;margin:4px 0 0;padding:4px 0;list-style:none;max-height:220px;overflow:auto;border-radius:10px;border:1px solid var(--border);background:var(--card);box-shadow:0 12px 28px rgba(0,0,0,.18);z-index:40;}
    .dept-combo__list li{margin:0;padding:8px 12px;font-size:13px;cursor:pointer;color:var(--text);}
    .dept-combo__list li:hover,.dept-combo__list li.is-active{background:color-mix(in srgb,var(--primary) 12%,transparent);}
    .dept-combo__empty{padding:8px 12px;font-size:12px;color:var(--muted);}
</style>

<div class="dept-show card" style="max-width:100%;padding:14px 16px;">
    <a href="{{ route('hr.departments.index') }}" class="dept-back"><i class="fa fa-arrow-left"></i>{{ __('Departments catalogue') }}</a>

    @if(session('status'))
        <div class="cat-banner cat-banner--ok">{{ session('status') }}</div>
    @endif

    <div class="dept-show__head">
        <div>
            <h1 class="dept-show__title">{{ $department->name }}</h1>
            <p class="dept-show__muted">{{ __(':business · Headcount overview and assigning people to this team.', ['business' => $business->name]) }}</p>
            @if($department->headEmployee || $department->coHeadEmployee)
                <p style="margin:8px 0 0;font-size:12px;line-height:1.5;color:var(--muted);">
                    @if($department->headEmployee)
                        <span>{{ __('Head') }}: <strong style="color:var(--text);font-weight:650;">{{ $department->headEmployee->full_name }}</strong></span>
                    @endif
                    @if($department->headEmployee && $department->coHeadEmployee)<span aria-hidden="true"> · </span>@endif
                    @if($department->coHeadEmployee)
                        <span>{{ __('Co-head') }}: <strong style="color:var(--text);font-weight:650;">{{ $department->coHeadEmployee->full_name }}</strong></span>
                    @endif
                </p>
            @endif
        </div>
        <span class="muted" style="font-size:13px;line-height:1.4;"><i class="fa fa-users" aria-hidden="true"></i>
            @if($members->count() === 0)
                {{ __('No members yet') }}
            @else
                {{ trans_choice(':count member|:count members', $members->count(), ['count' => $members->count()]) }}
            @endif
        </span>
    </div>

    @php($overviewUrl = route('hr.departments.show', $department))
    @php($billsTabUrl = route('hr.departments.show', ['department' => $department, 'tab' => 'bills']))
    @php($managementUrl = route('hr.departments.show', ['department' => $department, 'tab' => 'management']))

    <nav class="dept-tabs" aria-label="{{ __('Department sections') }}">
        <a href="{{ $overviewUrl }}" @class(['active' => $tab === 'overview'])>{{ __('Overview') }}</a>
        @if(($showDepartmentBillsTab ?? false))
            <a href="{{ $billsTabUrl }}" @class(['active' => $tab === 'bills'])>{{ __('Assigned bills') }}</a>
        @endif
        <a href="{{ $managementUrl }}" @class(['active' => $tab === 'management'])>{{ __('Department management') }}</a>
    </nav>

    <div id="dept-tab-overview" class="dept-panel @if($tab === 'overview') is-active @endif">
        <div class="dept-stat-grid" role="region" aria-labelledby="dept-overview-heading">
            <h2 id="dept-overview-heading" class="sr-only">{{ __('Summary') }}</h2>
            <dl class="dept-stat">
                <dt>{{ __('Employees in department') }}</dt>
                <dd>{{ $members->count() }}</dd>
            </dl>
            <dl class="dept-stat">
                <dt>{{ __('Designations in use') }}</dt>
                <dd>{{ $members->pluck('job_title_id')->filter()->unique()->count() }}</dd>
            </dl>
        </div>

        <h2 class="dept-sub">{{ __('Employment breakdown') }}</h2>
        <div class="dept-stat-grid dept-stat-grid--employment" role="group" aria-label="{{ __('Employment breakdown') }}">
            @foreach($employmentBreakdown as $etype => $ecount)
                @php($card = $employmentCardMeta[$etype] ?? ['icon_class' => 'fa-solid fa-user', 'description' => ''])
                <article class="dept-stat dept-stat--employment">
                    <div class="dept-stat__top">
                        <span class="dept-stat__iconWrap" aria-hidden="true"><i class="{{ $card['icon_class'] }}"></i></span>
                        <div>
                            <h3 class="dept-stat__title">{{ $employmentTypeLabels[$etype] ?? ucfirst(str_replace('_', ' ', $etype)) }}</h3>
                            <p class="dept-stat__hint">{{ $card['description'] }}</p>
                        </div>
                    </div>
                    <p class="dept-stat__count">{{ $ecount }}</p>
                </article>
            @endforeach
        </div>

        <h2 class="dept-sub">{{ __('Headcount trend') }}</h2>
        @if(($departmentGrowthChart['hasData'] ?? false))
            <div class="dept-chart-card">
                <p class="dept-chart-note">{{ $departmentGrowthChart['note'] }}</p>
                @include('hrmanagement::departments.partials.hr-line-chart', [
                    'canvasId' => 'dept-detail-growth-' . $department->id,
                    'chartAriaLabel' => __(':dept cumulative headcount', ['dept' => $department->name]),
                    'chartLabels' => $departmentGrowthChart['labels'],
                    'chartDatasets' => $departmentGrowthChart['datasets'],
                    'chartWrapStyle' => 'position:relative;height:min(260px,42vh);width:100%;',
                ])
                @if(count($departmentGrowthChart['labels']) > 0)
                    <div class="dept-chart-foot">
                        <span>{{ trans_choice(':count month on this timeline|:count months on this timeline', count($departmentGrowthChart['labels']), ['count' => count($departmentGrowthChart['labels'])]) }}</span>
                        <a href="{{ route('hr.departments.growth') }}">{{ __('Compare departments') }}</a>
                    </div>
                @endif
            </div>
        @else
            <p class="dept-empty-hint">
                {{ __('When people with hire dates are assigned here, cumulative headcount appears on this chart.') }}
                <a href="{{ route('hr.departments.growth') }}">{{ __('Department growth workspace') }}</a>
            </p>
        @endif

        <h2 class="dept-sub">{{ __('Latest joiners') }}</h2>
        @if($recentJoiners->isEmpty())
            <p class="muted" style="margin:0;line-height:1.5;font-size:13px;">{{ __('Nobody is assigned yet. Use Department management to add members.') }}</p>
        @else
            <div class="dept-table-wrap">
                <table class="dept-table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Joined') }}</th>
                            <th>{{ __('Designation') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentJoiners as $emp)
                            <tr>
                                <td><strong style="color:var(--text);">{{ $emp->full_name }}</strong></td>
                                <td class="muted">{{ $emp->date_of_joining?->format('M j, Y') }}</td>
                                <td class="muted">{{ $emp->jobTitle?->name ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if(($showDepartmentBillsTab ?? false))
        <div id="dept-tab-bills" class="dept-panel @if($tab === 'bills') is-active @endif">
            <h2 class="dept-sub" style="margin-top:0;">{{ __('Bills assigned to this department') }}</h2>
            @if(($departmentBills ?? collect())->isEmpty())
                <p class="muted" style="margin:0 0 12px;line-height:1.5;font-size:13px;">
                    {{ __('No bills are tagged to this department yet. When you create or edit a bill in Account, choose this department under “Assign to department”.') }}
                </p>
                <p style="margin:0;">
                    <a href="{{ route('account.bills.index') }}" class="linkbtn" style="padding:8px 14px;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;"><i class="fa fa-file-invoice-dollar" aria-hidden="true"></i>{{ __('Open Bills') }}</a>
                </p>
            @else
                <div class="dept-table-wrap">
                    <table class="dept-table">
                        <thead>
                            <tr>
                                <th>{{ __('Bill') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>{{ __('Pattern') }}</th>
                                <th style="text-align:right;">{{ __('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentBills as $bill)
                                <tr>
                                    <td>
                                        <a href="{{ route('account.bills.show', $bill) }}" style="color:var(--primary);font-weight:650;text-decoration:none;">{{ $bill->name }}</a>
                                    </td>
                                    <td class="muted">{{ \Illuminate\Support\Str::limit($bill->categoryDisplayLabel(), 40) }}</td>
                                    <td class="muted">
                                        @if($bill->isOneTime())
                                            {{ __('One-time') }}
                                        @else
                                            {{ (\Modules\Account\Models\Bill::paymentModes()[$bill->payment_mode] ?? $bill->payment_mode).' · '.(\Modules\Account\Models\Bill::recurringTypes()[$bill->recurring_type] ?? $bill->recurring_type) }}
                                        @endif
                                    </td>
                                    <td style="text-align:right;font-variant-numeric:tabular-nums;">
                                        @if(($departmentBillCurrency ?? '') !== '')
                                            <span style="font-size:10px;opacity:.75;text-transform:uppercase;">{{ $departmentBillCurrency }}</span>
                                        @endif
                                        {{ number_format((float) $bill->recurring_cost, 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="muted" style="margin:14px 0 0;font-size:12px;line-height:1.45;">
                    <a href="{{ route('account.bills.index') }}" style="color:var(--primary);font-weight:600;text-decoration:none;">{{ __('All bills') }}</a>
                </p>
            @endif
        </div>
    @endif

    <div id="dept-tab-management" class="dept-panel @if($tab === 'management') is-active @endif">
        <div class="dept-mgmt-card">
            <h2 style="margin:0 0 10px;font-size:15px;font-weight:800;">{{ __('Rename department') }}</h2>
            @if($errors->has('name'))
                <p class="cat-banner cat-banner--err" role="alert" style="margin-bottom:12px;">{{ $errors->first('name') }}</p>
            @endif
            <form method="post" action="{{ route('hr.departments.update', $department) }}">
                @csrf
                @method('PATCH')
                <div class="dept-field">
                    <label for="dept-rename">{{ __('Display name') }}</label>
                    <input type="text" name="name" id="dept-rename" value="{{ old('name', $department->name) }}" required maxlength="255" autocomplete="organization">
                </div>
                <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                    <button type="submit" class="linkbtn" style="padding:8px 16px;font-size:13px;">{{ __('Save name') }}</button>
                </div>
            </form>
        </div>

        <div class="dept-mgmt-card">
            <h2 style="margin:0 0 10px;font-size:15px;font-weight:800;">{{ __('Department leadership') }}</h2>
            <p class="dept-lead-hint">{{ __('Optional roles for this team. Only employees already assigned to this department can be selected—assign members below first if needed.') }}</p>
            @if($errors->has('head_employee_id'))
                <p class="cat-banner cat-banner--err" role="alert" style="margin-bottom:12px;">{{ $errors->first('head_employee_id') }}</p>
            @endif
            @if($errors->has('co_head_employee_id'))
                <p class="cat-banner cat-banner--err" role="alert" style="margin-bottom:12px;">{{ $errors->first('co_head_employee_id') }}</p>
            @endif
            <form method="post" action="{{ route('hr.departments.leadership', $department) }}">
                @csrf
                @method('PATCH')
                <div class="dept-field">
                    <label for="dept-lead-head-q">{{ __('Department head') }}</label>
                    <div class="dept-combo" data-dept-combo data-search-url="{{ route('hr.departments.employees.search', $department) }}">
                        <input type="hidden" name="head_employee_id" id="dept-lead-head-id" value="{{ old('head_employee_id', $department->head_employee_id) }}">
                        <div class="dept-combo__control">
                            <input type="text" id="dept-lead-head-q" class="dept-combo__q" value="{{ $leadershipHeadLabel }}" autocomplete="off" placeholder="{{ __('Search by name or employee ID…') }}" aria-autocomplete="list" aria-expanded="false">
                            <button type="button" class="dept-combo__clear" data-dept-combo-clear aria-label="{{ __('Clear') }}">×</button>
                        </div>
                        <ul class="dept-combo__list" hidden role="listbox"></ul>
                    </div>
                </div>
                <div class="dept-field">
                    <label for="dept-lead-co-q">{{ __('Department co-head') }}</label>
                    <div class="dept-combo" data-dept-combo data-search-url="{{ route('hr.departments.employees.search', $department) }}">
                        <input type="hidden" name="co_head_employee_id" id="dept-lead-co-id" value="{{ old('co_head_employee_id', $department->co_head_employee_id) }}">
                        <div class="dept-combo__control">
                            <input type="text" id="dept-lead-co-q" class="dept-combo__q" value="{{ $leadershipCoHeadLabel }}" autocomplete="off" placeholder="{{ __('Search by name or employee ID…') }}" aria-autocomplete="list" aria-expanded="false">
                            <button type="button" class="dept-combo__clear" data-dept-combo-clear aria-label="{{ __('Clear') }}">×</button>
                        </div>
                        <ul class="dept-combo__list" hidden role="listbox"></ul>
                    </div>
                </div>
                <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                    <button type="submit" class="linkbtn" style="padding:8px 16px;font-size:13px;">{{ __('Save leadership') }}</button>
                </div>
            </form>
        </div>

        <div class="dept-mgmt-card">
            <h2 style="margin:0 0 10px;font-size:15px;font-weight:800;">{{ __('Assign employees to this department') }}</h2>
            @if($assignableEmployees->isEmpty())
                <p class="muted" style="margin:0;line-height:1.45;font-size:13px;">{{ __('Everyone is already in this department, or no employees exist yet — register staff on Employees first.') }}</p>
            @else
                @if($errors->has('attach_employee_ids'))
                    <div class="cat-banner cat-banner--err" role="alert" style="margin-bottom:12px;">{{ $errors->first('attach_employee_ids') }}</div>
                @endif
                <form method="post" action="{{ route('hr.departments.members.attach', $department) }}">
                    @csrf
                    <div class="dept-field">
                        <label for="dept-assign-select">{{ __('Select people (Hold Ctrl/Cmd for multiple)') }}</label>
                        <select name="attach_employee_ids[]" id="dept-assign-select" multiple required size="{{ min(max(8, min($assignableEmployees->count(), 14)), 14) }}">
                            @foreach($assignableEmployees as $emp)
                                @php($otherDept = optional($emp->department)->name)
                                @php($parts = [$emp->full_name, ($emp->jobTitle?->name ?: '—'), $otherDept ? __('Currently:').' '.$otherDept : __('Unassigned')])
                                @php($optLabel = implode(' · ', $parts))
                                <option value="{{ $emp->id }}" @selected(collect(old('attach_employee_ids', []))->map(fn ($id) => (int) $id)->contains((int) $emp->id))>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                        <button type="submit" class="linkbtn" style="padding:8px 16px;font-size:13px;">{{ __('Assign to department') }}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@php($deptComboMsgs = ['searching' => __('Searching…'), 'nomatch' => __('No matches'), 'failed' => __('Search failed')])
<script>
(function () {
    var MSGS = @json($deptComboMsgs);

    function debounce(fn, ms) {
        var t;
        return function () {
            var ctx = this, args = arguments;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, ms);
        };
    }

    document.querySelectorAll('[data-dept-combo]').forEach(function (root) {
        var url = root.getAttribute('data-search-url');
        var hidden = root.querySelector('input[type="hidden"]');
        var q = root.querySelector('.dept-combo__q');
        var list = root.querySelector('.dept-combo__list');
        var clearBtn = root.querySelector('[data-dept-combo-clear]');
        if (!url || !hidden || !q || !list) return;

        if (hidden.value && q.value) {
            q.dataset.pickedText = q.value;
        }

        function hideList() {
            list.hidden = true;
            list.innerHTML = '';
            q.setAttribute('aria-expanded', 'false');
        }

        function showLoading() {
            list.innerHTML = '<li class="dept-combo__empty" role="presentation">' + MSGS.searching + '</li>';
            list.hidden = false;
            q.setAttribute('aria-expanded', 'true');
        }

        function render(results) {
            list.innerHTML = '';
            if (!results.length) {
                list.innerHTML = '<li class="dept-combo__empty" role="presentation">' + MSGS.nomatch + '</li>';
                list.hidden = false;
                q.setAttribute('aria-expanded', 'true');
                return;
            }
            results.forEach(function (row) {
                var li = document.createElement('li');
                li.setAttribute('role', 'option');
                li.textContent = row.text;
                li.addEventListener('mousedown', function (e) { e.preventDefault(); });
                li.addEventListener('click', function () {
                    hidden.value = String(row.id);
                    q.value = row.text;
                    q.dataset.pickedText = row.text;
                    hideList();
                });
                list.appendChild(li);
            });
            list.hidden = false;
            q.setAttribute('aria-expanded', 'true');
        }

        var runSearch = debounce(function () {
            var term = q.value.trim();
            if (term.length < 1) {
                hideList();
                return;
            }
            showLoading();
            fetch(url + '?q=' + encodeURIComponent(term), { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (data) { render(data.results || []); })
                .catch(function () {
                    list.innerHTML = '<li class="dept-combo__empty" role="presentation">' + MSGS.failed + '</li>';
                    list.hidden = false;
                });
        }, 300);

        q.addEventListener('input', function () {
            if (q.dataset.pickedText !== undefined && q.value !== q.dataset.pickedText) {
                hidden.value = '';
                delete q.dataset.pickedText;
            }
            runSearch();
        });
        q.addEventListener('focus', function () {
            if (q.value.trim().length >= 1) runSearch();
        });

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                hidden.value = '';
                q.value = '';
                delete q.dataset.pickedText;
                hideList();
                q.focus();
            });
        }

        document.addEventListener('click', function (e) {
            if (!root.contains(e.target)) hideList();
        });
        q.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') hideList();
        });
    });
})();
</script>
@endsection
