@extends('theme::layouts.app', ['title' => 'Rentals', 'heading' => 'Rentals'])

@section('content')
@php
    $rentalCurrency = $business ? (string) (get_settings('business.currency', '', $business) ?: '') : '';
@endphp
<div class="rental-page">
    <style>
        .rental-page{max-width:none;width:100%;margin:0;box-sizing:border-box;}
        .rental-hero{display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:flex-start;padding:0 0 12px;margin-bottom:2px;border-bottom:1px solid var(--border);}
        .rental-hero__actions{display:flex;flex-wrap:wrap;gap:7px;align-items:center;}
        .rental-btn--ghost{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:9px;font-size:12px;font-weight:600;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 92%,transparent);color:var(--text);text-decoration:none;}
        .rental-btn--ghost:hover{border-color:color-mix(in srgb,var(--primary) 50%,var(--border));}
        .rental-btn--primary{display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:9px;font-size:12px;font-weight:700;border:1px solid color-mix(in srgb,var(--btn-bg) 72%,var(--border));background:var(--btn-bg);color:#fff;cursor:pointer;}
        .rental-btn--primary:hover{background:var(--btn-hover);color:#111827;}
        .rental-body{padding:12px 0 0;}
        .rental-alert{padding:8px 11px;border-radius:10px;font-size:12px;margin-bottom:12px;display:flex;align-items:flex-start;gap:8px;}
        .rental-alert--ok{border:1px solid color-mix(in srgb,#22c55e 45%,var(--border));background:color-mix(in srgb,#22c55e 10%,transparent);}
        .rental-alert--err{border:1px solid color-mix(in srgb,#f87171 45%,var(--border));background:color-mix(in srgb,#f87171 10%,transparent);}
        .rental-modal__banner{margin:0 0 10px;}
        .rental-empty{text-align:center;padding:22px 16px;color:var(--muted);border:1px dashed color-mix(in srgb,var(--primary) 26%,var(--border));border-radius:11px;}
        .rental-cards{display:flex;flex-direction:column;gap:12px;}
        .rental-card{border-radius:12px;border:1px solid var(--border);padding:12px 14px;background:color-mix(in srgb,var(--card) 96%,transparent);}
        .rental-card h3{margin:0 0 6px;font-size:14px;font-weight:800;}
        .rental-card .muted{font-size:12px;line-height:1.45;color:var(--muted);}
        .rental-card__grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-top:10px;font-size:12px;}
        .rental-card__lab{color:var(--muted);font-size:10px;text-transform:uppercase;letter-spacing:.04em;}
        .rental-card__val{font-weight:700;color:var(--text);}
        .rental-btn-del{margin-top:10px;padding:5px 9px;font-size:11px;border-radius:8px;border:1px solid color-mix(in srgb,#ef4444 50%,var(--border));background:transparent;color:#f97373;cursor:pointer;}
        html[data-theme="light"] .rental-btn-del{color:#dc2626;}
        .rental-inline-create{box-sizing:border-box;width:100%;max-width:none;border-radius:14px;border:1px solid var(--border);background:var(--card);padding:14px 16px 18px;margin-top:4px;}
        .rental-inline-create__head{margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid var(--border);}
        .rental-inline-create__head h2{margin:0;font-size:16px;font-weight:800;}
        .rental-inline-create__lead{margin:6px 0 0;font-size:12px;color:var(--muted);max-width:56ch;line-height:1.45;}
        .rental-form-section{margin-bottom:14px;padding:12px;border-radius:10px;border:1px solid color-mix(in srgb,var(--border) 85%,transparent);background:color-mix(in srgb,var(--card) 92%,transparent);}
        .rental-form-section__head{display:flex;align-items:center;gap:7px;margin-bottom:10px;font-size:12px;font-weight:700;color:var(--text);}
        .rental-form-section__head i{color:var(--primary);}
        .rental-fields-grid{display:grid;gap:10px;}@media(min-width:640px){.rental-fields-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 14px;}}
        .rental-field--full{grid-column:1/-1;}
        .rental-field label{display:block;margin-bottom:4px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:var(--muted);}
        .rental-hint{font-weight:500;text-transform:none;letter-spacing:0;color:var(--muted);font-size:10px;}
        .rental-field input,.rental-field select,.rental-field textarea{width:100%;box-sizing:border-box;padding:8px 10px;font-size:13px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);}
        .rental-field textarea{min-height:64px;resize:vertical;font-family:inherit;}
        .rental-field-err{display:block;color:#f87171;font-size:12px;margin-top:4px;}
        .rental-submit-wrap{margin-top:8px;display:flex;flex-wrap:wrap;gap:10px;align-items:center;}
        .rental-submit-note{font-size:11px;color:var(--muted);}
        .rental-select{width:100%;box-sizing:border-box;padding:8px 10px;font-size:13px;border:1px solid var(--border);border-radius:8px;background:var(--card);color:var(--text);}
        .rental-modal{position:fixed;inset:0;z-index:120;display:flex;justify-content:center;align-items:flex-start;padding:max(12px,2.5vh) 14px calc(14px + env(safe-area-inset-bottom));overflow:auto;opacity:0;visibility:hidden;pointer-events:none;transition:opacity .22s ease,visibility .22s ease;}
        .rental-modal.rental-modal--open{opacity:1;visibility:visible;pointer-events:auto;}
        .rental-modal__backdrop{position:fixed;inset:0;z-index:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);}
        html[data-theme="light"] .rental-modal__backdrop{background:rgba(17,24,39,.38);}
        .rental-modal__panel{position:relative;z-index:1;width:100%;max-width:720px;max-height:min(94vh,calc(100dvh - 48px));display:flex;flex-direction:column;border-radius:14px;border:1px solid var(--border);background:var(--card);box-shadow:0 20px 48px rgba(0,0,0,.32);margin:auto;}
        .rental-modal__head{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:11px 14px;border-bottom:1px solid var(--border);flex-shrink:0;}
        .rental-modal__head h2{margin:0;font-size:15px;font-weight:800;}
        .rental-modal__close{width:32px;height:32px;display:grid;place-items:center;padding:0;border:1px solid var(--border);border-radius:9px;background:color-mix(in srgb,var(--card) 88%,transparent);cursor:pointer;font-size:17px;}
        .rental-modal__body{padding:12px 14px 16px;overflow:auto;}
        html.rental-modal-open-html,html.rental-modal-open-html body{overflow:hidden;}
    </style>

    <header class="rental-hero">
        <div class="muted" style="font-size:13px;margin:0;">Office, shop, and warehouse rent for the selected business.</div>
        <div class="rental-hero__actions">
            @if($business && $rentals->isNotEmpty())
                <button type="button" id="rental-modal-open" class="rental-btn--primary"><i class="fa fa-plus"></i>Add another rental</button>
            @endif
            <a class="rental-btn--ghost" href="{{ route('dashboard') }}"><i class="fa fa-arrow-left"></i> Overview</a>
        </div>
    </header>

    <div class="rental-body">
        @if(!$business)
            <div class="rental-empty">
                <h2 style="margin:0 0 8px;font-size:15px;">No business selected</h2>
                <p style="margin:0;">Choose a business from the navbar to manage rentals.</p>
            </div>
        @else
            @if(session('status'))
                <div class="rental-alert rental-alert--ok" role="status">
                    <i class="fa fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($rentals->isNotEmpty())
                <div class="rental-cards">
                    @foreach($rentals as $rental)
                        <article class="rental-card">
                            <h3><i class="fa fa-house" style="margin-right:6px;opacity:.85;"></i>{{ $rental->property_type }}</h3>
                            @if($rental->purpose)
                                <p class="muted" style="margin:0;">{{ \Illuminate\Support\Str::limit($rental->purpose, 180) }}</p>
                            @endif
                            <div class="rental-card__grid">
                                <div>
                                    <div class="rental-card__lab">Agreement until</div>
                                    <div class="rental-card__val">{{ $rental->agreement_valid_until_year }}</div>
                                </div>
                                <div>
                                    <div class="rental-card__lab">Recurring</div>
                                    <div class="rental-card__val">{{ $recurringTypes[$rental->recurring_type] ?? $rental->recurring_type }}</div>
                                </div>
                                <div>
                                    <div class="rental-card__lab">Cost / period</div>
                                    <div class="rental-card__val">
                                        @if($rentalCurrency)<span style="opacity:.75;font-size:11px;">{{ $rentalCurrency }}</span> @endif
                                        {{ number_format((float) $rental->recurring_cost, 2, '.', ',') }}
                                    </div>
                                </div>
                                @if($rental->key_money !== null && (float) $rental->key_money > 0)
                                    <div>
                                        <div class="rental-card__lab">Key money</div>
                                        <div class="rental-card__val">
                                            @if($rentalCurrency)<span style="opacity:.75;font-size:11px;">{{ $rentalCurrency }}</span> @endif
                                            {{ number_format((float) $rental->key_money, 2, '.', ',') }}
                                        </div>
                                    </div>
                                @endif
                                @if($rental->warehouse)
                                    <div>
                                        <div class="rental-card__lab">Branch</div>
                                        <div class="rental-card__val">{{ $rental->warehouse->name }}</div>
                                    </div>
                                @endif
                                @if($rental->deductAccount)
                                    <div style="grid-column:1/-1;">
                                        <div class="rental-card__lab">Deduct account</div>
                                        <div class="rental-card__val" style="font-weight:600;font-size:12px;">{{ \Illuminate\Support\Str::limit($rental->deductAccount->deductOptionLabel(), 120) }}</div>
                                    </div>
                                @endif
                            </div>
                            <form method="post" action="{{ route('account.rentals.destroy', $rental) }}" style="margin:0;" onsubmit="return confirm('Remove this rental record?');">
                                @csrf
                                @method('delete')
                                <button type="submit" class="rental-btn-del"><i class="fa fa-trash-can"></i> Remove</button>
                            </form>
                        </article>
                    @endforeach
                </div>
            @else
                <section class="rental-inline-create" aria-labelledby="rental-inline-title">
                    <header class="rental-inline-create__head">
                        <h2 id="rental-inline-title">Add your first rental</h2>
                        <p class="rental-inline-create__lead">Capture lease terms, recurring rent, and optional deduction account for this business.</p>
                    </header>
                    @include('account::rentals.partials.create-form')
                </section>
            @endif

            @if($rentals->isNotEmpty())
                <div id="rental-modal"
                    class="rental-modal {{ $errors->any() ? 'rental-modal--open' : '' }}"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="rental-modal-title"
                    aria-hidden="{{ $errors->any() ? 'false' : 'true' }}">
                    <div class="rental-modal__backdrop" data-rental-modal-close tabindex="-1"></div>
                    <div class="rental-modal__panel">
                        <div class="rental-modal__head">
                            <h2 id="rental-modal-title">Add rental</h2>
                            <button type="button" class="rental-modal__close" data-rental-modal-close aria-label="Close">&times;</button>
                        </div>
                        <div class="rental-modal__body">
                            @include('account::rentals.partials.create-form', ['rentalFormErrorBannerClass' => 'rental-modal__banner'])
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

@if($business && $rentals->isNotEmpty())
<script>
(function () {
    const modal = document.getElementById('rental-modal');
    const openBtn = document.getElementById('rental-modal-open');
    function lockScroll(on) {
        document.documentElement.classList.toggle('rental-modal-open-html', Boolean(on));
    }
    function openModal() {
        if (!modal) return;
        modal.classList.add('rental-modal--open');
        modal.setAttribute('aria-hidden', 'false');
        lockScroll(true);
        document.getElementById('rental-property-type')?.focus();
    }
    function closeModal() {
        if (!modal) return;
        modal.classList.remove('rental-modal--open');
        modal.setAttribute('aria-hidden', 'true');
        lockScroll(false);
        openBtn?.focus();
    }
    openBtn?.addEventListener('click', openModal);
    modal?.querySelectorAll('[data-rental-modal-close]').forEach((el) =>
        el.addEventListener('click', () => closeModal()),
    );
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (!modal?.classList.contains('rental-modal--open')) return;
        closeModal();
    });
    if (modal?.classList.contains('rental-modal--open')) lockScroll(true);
})();
</script>
@endif
@endsection
