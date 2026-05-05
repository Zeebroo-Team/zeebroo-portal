@extends('theme::layouts.app', ['title' => __('Payroll cycle'), 'heading' => __('Payroll cycle')])

@php
    $pclCurrency = $cycle->ruleSet?->currency ?: ($business->currency ?? 'LKR');
    $pclStatusRaw = strtolower((string) $cycle->status);
    $pclStatusLabel = match ($pclStatusRaw) {
        'finalized' => __('Finalized'),
        'computed' => __('Computed'),
        'draft' => __('Draft'),
        default => ucfirst((string) $cycle->status),
    };
    $pclIsLocked = $cycle->isFinalized();
@endphp

@section('content')
    <style>
        .pcl-page{max-width:1280px;margin:0 auto;display:grid;gap:14px}
        .pcl-hero{
            border:1px solid color-mix(in srgb,var(--border)90%,transparent);
            border-radius:14px;
            background:var(--card);
            padding:16px 18px;
            box-shadow:0 1px 0 color-mix(in srgb,var(--border)55%,transparent) inset,0 8px 22px rgba(0,0,0,.045);
        }
        .pcl-hero__row{display:flex;flex-wrap:wrap;gap:12px 16px;justify-content:space-between;align-items:flex-start}
        .pcl-hero__title{margin:0;font-size:1.08rem;font-weight:800;letter-spacing:-.02em;line-height:1.25;color:var(--text)}
        .pcl-hero__subtitle{margin:6px 0 0;font-size:12px;line-height:1.45;color:var(--muted);max-width:720px}
        .pcl-status-badge{display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:5px 12px;border-radius:999px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;border:1px solid var(--border);background:color-mix(in srgb,var(--card)94%,transparent);color:var(--muted)}
        .pcl-status-badge--draft{border-color:color-mix(in srgb,var(--primary)42%,var(--border));background:color-mix(in srgb,var(--primary)12%,transparent);color:color-mix(in srgb,var(--primary)85%,var(--text));}
        .pcl-status-badge--computed{border-color:color-mix(in srgb,#6366f1 42%,var(--border));background:color-mix(in srgb,#6366f1 14%,transparent);color:color-mix(in srgb,#4f46e5 92%,var(--text));}
        .pcl-status-badge--final{border-color:color-mix(in srgb,#22c55e 48%,var(--border));background:color-mix(in srgb,#22c55e 12%,transparent);color:#15803d;}

        .pcl-meta{display:flex;flex-wrap:wrap;gap:8px 10px;margin:12px 0 0;padding:0;list-style:none}
        .pcl-meta li{
            display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:750;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);
            padding:5px 10px;border-radius:999px;border:1px solid color-mix(in srgb,var(--border)82%,transparent);
            background:color-mix(in srgb,var(--card)94%,transparent);
        }
        .pcl-meta li span{font-weight:800;color:var(--text);text-transform:none;letter-spacing:0;font-size:11px}
        .pcl-meta li i.fa{font-size:11px;opacity:.88;color:color-mix(in srgb,var(--primary)72%,var(--muted));}

        .pcl-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
        .pcl-actions__grp{display:flex;flex-wrap:wrap;gap:6px;align-items:center;padding-right:12px;margin-right:4px;border-right:1px solid color-mix(in srgb,var(--border)76%,transparent)}
        .pcl-actions__grp:last-of-type{border-right:none;padding-right:0;margin-right:0}
        .pcl-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:7px 12px;border-radius:10px;font-size:11.5px;font-weight:750;text-decoration:none;cursor:pointer;border:1px solid color-mix(in srgb,var(--primary)42%,var(--border));background:color-mix(in srgb,var(--primary)12%,transparent);color:var(--text);transition:background .15s ease;-webkit-appearance:none;appearance:none;font-family:inherit}
        .pcl-btn:hover{background:color-mix(in srgb,var(--primary)20%,transparent);color:var(--text)}
        .pcl-btn--muted{border-color:color-mix(in srgb,var(--border)88%,transparent);background:color-mix(in srgb,var(--card)96%,transparent)}
        .pcl-btn--muted:hover{background:color-mix(in srgb,var(--primary)10%,transparent)}
        .pcl-btn--ok{border-color:color-mix(in srgb,#22c55e 48%,var(--border));background:color-mix(in srgb,#22c55e 14%,transparent);color:#14532d}
        .pcl-btn--ok:hover{background:color-mix(in srgb,#22c55e 22%,transparent)}
        form.pcl-inline-form{display:inline;margin:0;padding:0}

        .pcl-kpis{display:grid;gap:11px;grid-template-columns:repeat(3,minmax(0,1fr))}
        .pcl-kpi{
            position:relative;padding:12px 14px;border-radius:12px;border:1px solid color-mix(in srgb,var(--border)88%,transparent);
            background:linear-gradient(165deg,color-mix(in srgb,var(--card)99%,transparent),color-mix(in srgb,var(--primary)6%,transparent));
            overflow:hidden;
        }
        .pcl-kpi::after{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:3px 0 0 3px;background:color-mix(in srgb,var(--primary)50%,transparent);opacity:.8}
        .pcl-kpi--net::after{background:color-mix(in srgb,#22c55e 58%,var(--primary));}
        .pcl-kpi--ded::after{background:color-mix(in srgb,#f97316 55%,var(--primary));}
        .pcl-kpi--stat::after{background:color-mix(in srgb,#64748b 70%,var(--primary));opacity:.65}
        .pcl-kpi-h{display:flex;align-items:center;gap:6px;margin:0 0 6px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--muted)}
        .pcl-kpi-h i.fa{font-size:11px;color:color-mix(in srgb,var(--primary)65%,var(--muted));}
        .pcl-kpi-v{margin:0;font-size:clamp(14px,1.85vw,17px);font-weight:800;font-variant-numeric:tabular-nums;letter-spacing:-.02em;line-height:1.2}
        .pcl-kpi-c{display:block;margin-top:4px;font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em}

        .pcl-section{
            border:1px solid color-mix(in srgb,var(--border)88%,transparent);
            border-radius:14px;background:var(--card);
            padding:14px 16px;
            box-shadow:0 1px 0 color-mix(in srgb,var(--border)50%,transparent) inset;
        }
        .pcl-section__head{margin:0 0 12px;font-size:.95rem;font-weight:800;letter-spacing:-.01em;color:var(--text)}
        .pcl-section__hint{margin:4px 0 0;font-size:11px;font-weight:600;color:var(--muted);line-height:1.4}

        .pcl-scroll{
            overflow:auto;margin:0 -2px;border:1px solid color-mix(in srgb,var(--border)82%,transparent);
            border-radius:10px;background:color-mix(in srgb,var(--card)98%,transparent);
            -webkit-overflow-scrolling:touch;
            max-height:min(68vh,880px);
        }
        .pcl-scroll::-webkit-scrollbar{height:10px;width:10px}
        .pcl-scroll::-webkit-scrollbar-thumb{border-radius:8px;background:color-mix(in srgb,var(--border)65%,transparent)}

        .pcl-table{width:100%;min-width:960px;border-collapse:separate;border-spacing:0}
        .pcl-table thead th{
            position:sticky;top:0;z-index:3;
            background:color-mix(in srgb,var(--card)92%,transparent);
            color:var(--muted);
            font-size:9.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:800;
            padding:10px 8px;border-bottom:1px solid color-mix(in srgb,var(--border)78%,transparent);
            white-space:nowrap;
            box-shadow:0 1px 0 color-mix(in srgb,var(--border)70%,transparent);
        }
        .pcl-table tbody td{
            padding:9px 8px;border-bottom:1px solid color-mix(in srgb,var(--border)70%,transparent);
            vertical-align:middle;font-size:12px;
        }
        .pcl-table tbody tr:nth-child(even) td{background:color-mix(in srgb,var(--card)97%,transparent)}
        .pcl-table tbody tr:hover td{background:color-mix(in srgb,var(--primary)7%,transparent)}
        .pcl-table tbody tr:last-child td{border-bottom:none}
        .pcl-table th + th,.pcl-table td + td{border-left:1px solid color-mix(in srgb,var(--border)55%,transparent)}

        .pcl-table th.pcl-th--emp,.pcl-table td.pcl-td--emp{
            position:sticky;left:0;z-index:2;min-width:160px;max-width:220px;
            box-shadow:1px 0 0 color-mix(in srgb,var(--border)65%,transparent);
        }
        .pcl-table thead th.pcl-th--emp{z-index:4;background:color-mix(in srgb,var(--card)92%,transparent)}
        .pcl-table td.pcl-td--emp{background:var(--card)}
        .pcl-table tbody tr:nth-child(even) td.pcl-td--emp{background:color-mix(in srgb,var(--card)97%,transparent)}
        .pcl-table tbody tr:hover td.pcl-td--emp{background:color-mix(in srgb,var(--primary)7%,transparent)}

        .pcl-name{display:block;font-size:12.5px;font-weight:750;line-height:1.3;color:var(--text)}
        .pcl-id{display:block;margin-top:3px;font-size:10.5px;color:var(--muted);font-variant-numeric:tabular-nums}
        .pcl-num{text-align:right;font-variant-numeric:tabular-nums;font-weight:650;font-size:12px}
        .pcl-num--net{font-weight:800;color:color-mix(in srgb,var(--primary)38%,var(--text))}
        .pcl-center{text-align:center}
        .pcl-pill{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;font-size:10px;font-weight:800;border:1px solid var(--border);background:color-mix(in srgb,var(--card)96%,transparent);color:var(--muted);white-space:nowrap}
        .pcl-pill--ok{border-color:color-mix(in srgb,#22c55e 45%,var(--border));color:#15803d;background:color-mix(in srgb,#22c55e 10%,transparent)}

        .pcl-mini{display:grid;gap:5px;min-width:168px;max-width:200px}
        .pcl-input{
            width:100%;box-sizing:border-box;border:1px solid color-mix(in srgb,var(--border)88%,transparent);
            background:color-mix(in srgb,var(--card)97%,transparent);color:var(--text);
            border-radius:8px;padding:6px 8px;font-size:11px;line-height:1.35;outline:none;font-family:inherit
        }
        .pcl-input:focus{border-color:color-mix(in srgb,var(--primary)48%,var(--border));box-shadow:0 0 0 3px color-mix(in srgb,var(--primary)12%,transparent)}
        .pcl-act-row{display:flex;flex-wrap:wrap;gap:6px;align-items:center}
        .pcl-btn--sm{padding:5px 9px;font-size:10.5px;border-radius:8px}
        .pcl-locked{font-size:11px;font-weight:650;color:var(--muted);font-style:italic}

        .pcl-empty{padding:28px 16px;text-align:center;font-size:13px;color:var(--muted);line-height:1.5}
        .pcl-empty i.fa{display:block;margin:0 auto 10px;font-size:26px;opacity:.35}

        @media (max-width:1100px){.pcl-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media (max-width:720px){
            .pcl-kpis{grid-template-columns:1fr}
            .pcl-actions__grp{width:100%;border-right:none;padding-right:0;margin-right:0;padding-bottom:8px;border-bottom:1px solid color-mix(in srgb,var(--border)70%,transparent)}
            .pcl-actions__grp:last-of-type{border-bottom:none;padding-bottom:0}
            .pcl-scroll{max-height:min(62vh,720px)}
        }

        @media print{
            .pcl-actions,.sidebar,.navbar{display:none!important}
            .content,.content-inner{margin:0!important;padding:8px!important}
            .pcl-scroll{max-height:none;overflow:visible}
            .pcl-table th,.pcl-table td.pcl-td--emp{position:static!important;box-shadow:none!important}
            .pcl-table tbody tr:hover td{background:transparent!important}
        }
    </style>

    @if(session('status'))
        <p class="emp-show__flash" role="status" style="max-width:1280px;margin:0 auto;">{{ session('status') }}</p>
    @endif
    @if(session('warning'))
        <p class="emp-show__err" role="alert" style="max-width:1280px;margin:0 auto;">{{ session('warning') }}</p>
    @endif
    @if($errors->any())
        <div class="emp-show__err" role="alert" style="max-width:1280px;margin:0 auto;">
            <ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $msg)<li>{{ $msg }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="pcl-page">
        <header class="pcl-hero">
            <div class="pcl-hero__row">
                <div>
                    <h1 class="pcl-hero__title">{{ $cycle->name }}</h1>
                    <p class="pcl-hero__subtitle">{{ __('Review computed amounts, statutory components, and payslips. Amounts use :currency.', ['currency' => $pclCurrency]) }}</p>
                    @php
                        $statusBadgeClass = match ($pclStatusRaw) {
                            'finalized' => 'pcl-status-badge--final',
                            'computed' => 'pcl-status-badge--computed',
                            default => 'pcl-status-badge--draft',
                        };
                    @endphp
                    <span class="pcl-status-badge {{ $statusBadgeClass }}"><i class="fa fa-circle-dot" aria-hidden="true"></i>{{ $pclStatusLabel }}</span>
                    <ul class="pcl-meta" aria-label="{{ __('Cycle details') }}">
                        <li><i class="fa fa-calendar-days" aria-hidden="true"></i>{{ __('Period') }} <span>{{ $cycle->period_start?->format('M j, Y') }} — {{ $cycle->period_end?->format('M j, Y') }}</span></li>
                        <li><i class="fa fa-layer-group" aria-hidden="true"></i>{{ __('Rule set') }} <span>{{ $cycle->ruleSet?->name ?? '—' }}</span></li>
                        <li><i class="fa fa-users" aria-hidden="true"></i>{{ __('Employees') }} <span>{{ $cycle->items->count() }}</span></li>
                        <li><i class="fa fa-coins" aria-hidden="true"></i>{{ __('Currency') }} <span>{{ $pclCurrency }}</span></li>
                    </ul>
                </div>
                <div class="pcl-actions">
                    <div class="pcl-actions__grp">
                        <a href="{{ route('hr.payroll.index') }}" class="pcl-btn pcl-btn--muted"><i class="fa fa-arrow-left" aria-hidden="true"></i>{{ __('Payroll') }}</a>
                    </div>
                    <div class="pcl-actions__grp">
                        <form method="post" action="{{ route('hr.payroll.cycles.salary-sheet.generate', $cycle) }}" class="pcl-inline-form">@csrf<button type="submit" class="pcl-btn"><i class="fa fa-table" aria-hidden="true"></i>{{ __('Generate sheet') }}</button></form>
                        <a href="{{ route('hr.payroll.cycles.salary-sheet', $cycle) }}" class="pcl-btn pcl-btn--muted"><i class="fa fa-eye" aria-hidden="true"></i>{{ __('Salary sheet') }}</a>
                    </div>
                    @if(! $pclIsLocked)
                        <div class="pcl-actions__grp">
                            <form method="post" action="{{ route('hr.payroll.cycles.compute', $cycle) }}" class="pcl-inline-form">@csrf<button type="submit" class="pcl-btn"><i class="fa fa-calculator" aria-hidden="true"></i>{{ __('Compute all') }}</button></form>
                            <form method="post" action="{{ route('hr.payroll.cycles.finalize', $cycle) }}" class="pcl-inline-form" onsubmit="return confirm(@json(__('Finalize this cycle? You will not be able to recompute afterward.')))">@csrf<button type="submit" class="pcl-btn pcl-btn--ok"><i class="fa fa-lock" aria-hidden="true"></i>{{ __('Finalize') }}</button></form>
                        </div>
                    @endif
                </div>
            </div>

            <div class="pcl-kpis" role="region" aria-label="{{ __('Cycle summary') }}" style="margin-top:18px;">
                <article class="pcl-kpi">
                    <p class="pcl-kpi-h"><i class="fa fa-arrow-trend-up" aria-hidden="true"></i>{{ __('Total gross') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['total_gross'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
                <article class="pcl-kpi pcl-kpi--ded">
                    <p class="pcl-kpi-h"><i class="fa fa-arrow-trend-down" aria-hidden="true"></i>{{ __('Total deductions') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['total_deductions'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
                <article class="pcl-kpi pcl-kpi--net">
                    <p class="pcl-kpi-h"><i class="fa fa-wallet" aria-hidden="true"></i>{{ __('Total net') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['total_net'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
                <article class="pcl-kpi pcl-kpi--stat">
                    <p class="pcl-kpi-h"><i class="fa fa-piggy-bank" aria-hidden="true"></i>{{ __('EPF (employee)') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['epf'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
                <article class="pcl-kpi pcl-kpi--stat">
                    <p class="pcl-kpi-h"><i class="fa fa-building-columns" aria-hidden="true"></i>{{ __('ETF (employer)') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['etf'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
                <article class="pcl-kpi pcl-kpi--stat">
                    <p class="pcl-kpi-h"><i class="fa fa-file-invoice-dollar" aria-hidden="true"></i>{{ __('APIT') }}</p>
                    <p class="pcl-kpi-v">{{ number_format((float) $summary['apit'], 2) }}</p>
                    <span class="pcl-kpi-c">{{ $pclCurrency }}</span>
                </article>
            </div>
        </header>

        <section class="pcl-section" aria-labelledby="pcl-items-heading">
            <h2 id="pcl-items-heading" class="pcl-section__head">{{ __('Employee payroll items') }}</h2>
            <p class="pcl-section__hint" id="pcl-items-hint">{{ __('Adjust overtime inputs where needed, then recompute individual rows or use Compute all. Finalized cycles are read-only.') }}</p>
            <div class="pcl-scroll" role="region" aria-labelledby="pcl-items-heading" aria-describedby="pcl-items-hint" tabindex="0">
                <table class="pcl-table">
                    <thead>
                        <tr>
                            <th scope="col" class="pcl-th--emp">{{ __('Employee') }}</th>
                            <th scope="col">{{ __('Basic') }}</th>
                            <th scope="col">{{ __('Overtime') }}</th>
                            <th scope="col">{{ __('Gross') }}</th>
                            <th scope="col">{{ __('Deductions') }}</th>
                            <th scope="col">{{ __('Net') }}</th>
                            <th scope="col">{{ __('Status') }}</th>
                            <th scope="col">{{ __('Payslip') }}</th>
                            <th scope="col">{{ __('Recompute') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cycle->items as $item)
                            @php
                                $rowSt = strtolower((string) $item->status);
                                $rowPillClass = str_contains($rowSt, 'final') || str_contains($rowSt, 'paid') || str_contains($rowSt, 'ok') ? ' pcl-pill--ok' : '';
                            @endphp
                            <tr>
                                <td class="pcl-td--emp">
                                    <span class="pcl-name">{{ $item->employee?->full_name ?? '—' }}</span>
                                    <span class="pcl-id">{{ __('ID') }}: {{ $item->employee?->employee_id ?: '—' }}</span>
                                </td>
                                <td class="pcl-num">{{ number_format((float) $item->basic_salary, 2) }}</td>
                                <td class="pcl-num">{{ number_format((float) $item->overtime_amount, 2) }}</td>
                                <td class="pcl-num">{{ number_format((float) $item->gross_earnings, 2) }}</td>
                                <td class="pcl-num">{{ number_format((float) $item->total_deductions, 2) }}</td>
                                <td class="pcl-num pcl-num--net">{{ number_format((float) $item->net_pay, 2) }}</td>
                                <td class="pcl-center"><span class="pcl-pill{{ $rowPillClass }}">{{ ucfirst((string) $item->status) }}</span></td>
                                <td>
                                    <div class="pcl-act-row">
                                        <a href="{{ route('hr.payroll.cycles.items.payslip', [$cycle, $item]) }}" class="pcl-btn pcl-btn--sm pcl-btn--muted">{{ __('View') }}</a>
                                        <a href="{{ route('hr.payroll.cycles.items.payslip.download', [$cycle, $item]) }}" class="pcl-btn pcl-btn--sm">{{ __('Download') }}</a>
                                    </div>
                                </td>
                                <td>
                                    @if(! $pclIsLocked)
                                        <form method="post" action="{{ route('hr.payroll.cycles.items.recompute', [$cycle, $item]) }}" class="pcl-mini">
                                            @csrf
                                            <input type="number" step="0.01" min="0" name="overtime_hours" class="pcl-input" placeholder="{{ __('OT hours') }}" value="{{ $item->inputs_json['overtime_hours'] ?? '' }}">
                                            <input type="number" step="0.01" min="0" name="overtime_rate" class="pcl-input" placeholder="{{ __('OT rate') }}" value="{{ $item->inputs_json['overtime_rate'] ?? '' }}">
                                            <button type="submit" class="pcl-btn pcl-btn--sm">{{ __('Recompute') }}</button>
                                        </form>
                                    @else
                                        <span class="pcl-locked"><i class="fa fa-lock" aria-hidden="true"></i>{{ __('Cycle locked') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="pcl-empty">
                                    <i class="fa fa-inbox" aria-hidden="true"></i>
                                    {{ __('No payroll items yet. Run Compute all to populate this cycle.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
