@extends('theme::layouts.app', ['title' => 'Overview', 'heading' => 'Overview'])

@section('content')
@php
    $business = auth()->user()->businesses()->latest()->first();
    $hasCurrentAccount = $business
        ? \Modules\Account\Models\Account::where('user_id', auth()->id())
            ->where('business_id', $business->id)
            ->whereHas('bankType', fn ($query) => $query->where('slug', 'current-account'))
            ->exists()
        : false;
@endphp

@if(!$business)
    <style>
        .content-inner{padding:0 !important;}
        .wizard-shell{
            min-height:calc(100vh - 73px);
            display:grid;
            place-items:center;
            width:100%;
            margin:0;
            padding:0;
            background:var(--bg);
            overflow:hidden;
        }
        .wizard-panel{
            position:relative;
            overflow:hidden;
            width:100%;
            min-height:calc(100vh - 92px);
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
            border:none;
            border-radius:0;
            padding:36px;
            background:#ffffff;
            color:#1f2937;
            box-shadow:none;
        }
        .wizard-bg{
            position:absolute;
            inset:0;
            overflow:hidden;
            pointer-events:none;
        }
        .wizard-circles{
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
            margin:0;
            padding:0;
            list-style:none;
        }
        .wizard-circles li{
            position:absolute;
            display:block;
            width:20px;
            height:20px;
            background:rgba(107,114,128,0.18);
            bottom:-150px;
            animation:wizardFloat 25s linear infinite;
        }
        .wizard-circles li:nth-child(1){left:25%;width:80px;height:80px;animation-delay:0s;}
        .wizard-circles li:nth-child(2){left:10%;width:20px;height:20px;animation-delay:2s;animation-duration:12s;}
        .wizard-circles li:nth-child(3){left:70%;width:20px;height:20px;animation-delay:4s;}
        .wizard-circles li:nth-child(4){left:40%;width:60px;height:60px;animation-delay:0s;animation-duration:18s;}
        .wizard-circles li:nth-child(5){left:65%;width:20px;height:20px;animation-delay:0s;}
        .wizard-circles li:nth-child(6){left:75%;width:110px;height:110px;animation-delay:3s;}
        .wizard-circles li:nth-child(7){left:35%;width:150px;height:150px;animation-delay:7s;}
        .wizard-circles li:nth-child(8){left:50%;width:25px;height:25px;animation-delay:15s;animation-duration:45s;}
        .wizard-circles li:nth-child(9){left:20%;width:15px;height:15px;animation-delay:2s;animation-duration:35s;}
        .wizard-circles li:nth-child(10){left:85%;width:150px;height:150px;animation-delay:0s;animation-duration:11s;}
        .wizard-head{display:flex;justify-content:center;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:18px}
        .wizard-head,.wizard-body,.wizard-help,.wizard-step{color:#4b5563}
        .wizard-title{margin:0 0 8px;font-size:34px;line-height:1.1}
        .wizard-sub{margin:0;color:var(--muted)}
        .wizard-step{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;border:none;color:var(--muted);font-size:14px}
        .wizard-step .dot{width:8px;height:8px;border-radius:50%;background:#9ca3af}
        .wizard-body{width:min(760px,100%);margin:0 auto;text-align:center}
        .wizard-card{animation:fadeSlide .35s ease}
        .wizard-body,.wizard-head{position:relative;z-index:1}
        .wizard-q{margin:0 0 8px;font-size:28px}
        .wizard-help{margin:0 0 18px;color:var(--muted)}
        .wiz-input,.wiz-textarea{
            width:100%;
            padding:14px 16px;
            border-radius:12px;
            border:1px solid #d1d5db;
            background:#f9fafb;
            color:#111827;
            outline:none;
            transition:all .2s ease;
        }
        .wiz-input:focus,.wiz-textarea:focus{
            border-color:#a78bfa;
            background:#ffffff;
        }
        .wiz-textarea{resize:vertical;min-height:130px}
        .wizard-actions{margin-top:18px;display:flex;gap:10px;justify-content:center}
        .btn-soft{background:#475569}
        @keyframes fadeSlide{
            from{opacity:0;transform:translateY(10px)}
            to{opacity:1;transform:translateY(0)}
        }
        @keyframes wizardFloat{
            0%{transform:translateY(0) rotate(0deg);opacity:1;border-radius:0;}
            100%{transform:translateY(-1000px) rotate(720deg);opacity:0;border-radius:50%;}
        }
    </style>

    <div class="wizard-shell">
        <div class="wizard-panel">
            <div class="wizard-bg" aria-hidden="true">
                <ul class="wizard-circles">
                    <li></li><li></li><li></li><li></li><li></li>
                    <li></li><li></li><li></li><li></li><li></li>
                </ul>
            </div>
            <div class="wizard-head">
                <span class="wizard-step" id="stepBadge"><span class="dot"></span>Step 1 of 2</span>
            </div>

            <form id="businessWizardForm" method="post" action="{{ route('business.onboarding.store') }}" class="wizard-body">
                @csrf
                <div id="wizardStep1" class="wizard-card">
                    <h2 class="wizard-q">What is your business/company name?</h2>
                    <p class="wizard-help">Use your public brand name.</p>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="e.g. SociBiz Solutions"
                        required
                        class="wiz-input"
                    >
                    @error('name')
                        <div style="color:#ef4444;margin-top:8px;">{{ $message }}</div>
                    @enderror
                    <div class="wizard-actions">
                        <button type="button" class="linkbtn" id="nextStepBtn">Next</button>
                    </div>
                </div>

                <div id="wizardStep2" style="display:none;" class="wizard-card">
                    <h2 class="wizard-q">What is your business category?</h2>
                    <p class="wizard-help">Tell us about your business in this quick quiz.</p>
                    <div style="display:grid;gap:14px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;color:var(--muted);">Business Category</label>
                            <input
                                type="text"
                                name="category"
                                value="{{ old('category') }}"
                                placeholder="e.g. SaaS, Retail, Healthcare"
                                required
                                class="wiz-input"
                            >
                            @error('category')
                                <div style="color:#ef4444;margin-top:8px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;color:var(--muted);">Tell me about that business</label>
                            <textarea
                                name="description"
                                placeholder="Describe what your company does, your target customers, and your key offering."
                                class="wiz-textarea"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div style="color:#ef4444;margin-top:8px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="wizard-actions">
                        <button type="button" class="linkbtn btn-soft" id="backStepBtn">Back</button>
                        <button type="submit" class="linkbtn">Finish Setup</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@elseif(!$hasCurrentAccount)
    <style>
        .account-notice-shell{
            min-height:calc(100vh - 73px);
            display:grid;
            place-items:center;
            width:100%;
            margin:0;
            padding:0;
        }
        .account-notice-panel{
            width:100%;
            min-height:calc(100vh - 92px);
            display:grid;
            place-items:center;
            background:#ffffff;
            color:#1f2937;
        }
        .account-notice-card{
            text-align:center;
            max-width:620px;
            padding:24px;
        }
    </style>
    <div class="account-notice-shell">
        <div class="account-notice-panel">
            <div class="account-notice-card">
                <h2 style="margin:0 0 8px;">Account not setup for your business</h2>
                <p style="margin:0 0 18px;color:#6b7280;">
                    Your business is ready, but a Current Account is required to continue.
                </p>
                <a class="linkbtn" href="{{ route('account.onboarding') }}">Please add account</a>
            </div>
        </div>
    </div>
@else
    @if(session('status'))
        <div class="card" style="margin-bottom:14px;max-width:100%;padding:0;border:none;">
            <div style="display:flex;gap:12px;align-items:flex-start;padding:14px 16px;border-radius:14px;background:linear-gradient(135deg,#ecfdf5,#dcfce7);border:1px solid #86efac;">
                <div style="width:28px;height:28px;border-radius:999px;background:#22c55e;color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0;">✓</div>
                <div>
                    <div style="color:#166534;font-weight:700;">{{ session('status') }}</div>
                    <div style="color:#15803d;font-size:13px;margin-top:2px;">
                        Great job! Your current account is now connected to this business. You can continue with daily operations and financial tracking.
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="card">
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <div class="muted">{{ auth()->user()->email }}</div>
        <div class="muted" style="margin-top:8px;">Roles:</div>
        @foreach(auth()->user()->getRoleNames() as $role)
            <span class="chip">{{ $role }}</span>
        @endforeach
    </div>
@endif

<script>
    const stepBadge = document.getElementById('stepBadge');
    const wizardStep1 = document.getElementById('wizardStep1');
    const wizardStep2 = document.getElementById('wizardStep2');
    const nextStepBtn = document.getElementById('nextStepBtn');
    const backStepBtn = document.getElementById('backStepBtn');
    const wizardForm = document.getElementById('businessWizardForm');

    if (nextStepBtn && wizardStep1 && wizardStep2 && stepBadge && wizardForm) {
        nextStepBtn.addEventListener('click', () => {
            const nameInput = wizardForm.querySelector('input[name="name"]');
            if (!nameInput.value.trim()) {
                nameInput.focus();
                return;
            }
            wizardStep1.style.display = 'none';
            wizardStep2.style.display = 'block';
            stepBadge.textContent = 'Step 2 of 2';
        });
    }

    if (backStepBtn && wizardStep1 && wizardStep2 && stepBadge) {
        backStepBtn.addEventListener('click', () => {
            wizardStep2.style.display = 'none';
            wizardStep1.style.display = 'block';
            stepBadge.textContent = 'Step 1 of 2';
        });
    }

    @if($errors->has('category') || $errors->has('description'))
        if (wizardStep1 && wizardStep2 && stepBadge) {
            wizardStep1.style.display = 'none';
            wizardStep2.style.display = 'block';
            stepBadge.textContent = 'Step 2 of 2';
        }
    @endif
</script>
@endsection
