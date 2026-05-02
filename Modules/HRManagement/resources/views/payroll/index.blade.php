@extends('theme::layouts.app', ['title' => 'Payroll', 'heading' => 'Payroll'])

@section('content')
    <div class="card" style="max-width:720px;">
        <p class="muted" style="margin:0;line-height:1.45;font-size:13px;">
            Payroll workflows for <strong>{{ $business->name }}</strong> will go here — cycles, payouts, reimbursements aligned with your selected salary handling account.
        </p>
    </div>
@endsection
