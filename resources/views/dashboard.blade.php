@extends('theme::layouts.app', ['title' => 'Overview', 'heading' => 'Overview'])

@section('content')
@php
    $business = auth()->user()->businesses()->latest()->first();
@endphp

@if(!$business)
    <style>
        .wizard-shell{
            min-height:calc(100vh - 92px);
            display:grid;
            place-items:center;
            width:100%;
            margin:-28px;
            padding:0;
            background:var(--bg);
        }
        .wizard-panel{
            width:100%;
            min-height:calc(100vh - 92px);
            border:none;
            border-radius:0;
            padding:36px;
            background:
                radial-gradient(circle at 10% 10%, color-mix(in srgb, var(--primary) 24%, transparent), transparent 45%),
                var(--card);
            box-shadow:none;
        }
        .wizard-head{display:flex;justify-content:center;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:26px}
        .wizard-title{margin:0 0 8px;font-size:34px;line-height:1.1}
        .wizard-sub{margin:0;color:var(--muted)}
        .wizard-step{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border-radius:999px;border:none;color:var(--muted);font-size:14px}
        .wizard-step .dot{width:8px;height:8px;border-radius:50%;background:var(--primary)}
        .wizard-body{max-width:760px;margin:0 auto;text-align:center}
        .wizard-card{animation:fadeSlide .35s ease}
        .wizard-q{margin:0 0 8px;font-size:28px}
        .wizard-help{margin:0 0 18px;color:var(--muted)}
        .wiz-input,.wiz-textarea{width:100%;padding:14px 16px;border-radius:14px;border:none;background:transparent;color:var(--text);outline:none}
        .wiz-input:focus,.wiz-textarea:focus{border-color:var(--primary)}
        .wiz-textarea{resize:vertical;min-height:130px}
        .wizard-actions{margin-top:18px;display:flex;gap:10px;justify-content:center}
        .btn-soft{background:#475569}
        @keyframes fadeSlide{
            from{opacity:0;transform:translateY(10px)}
            to{opacity:1;transform:translateY(0)}
        }
    </style>

    <div class="wizard-shell">
        <div class="wizard-panel">
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
@else
    @if(session('status'))
        <div class="card" style="margin-bottom:14px;padding:12px 16px;max-width:100%;border-color:#22c55e;">
            <div style="color:#22c55e;font-weight:600;">{{ session('status') }}</div>
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
