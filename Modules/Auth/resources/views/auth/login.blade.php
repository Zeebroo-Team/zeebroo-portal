@extends('theme::layouts.auth', ['title' => 'Login'])

@section('content')
    <h1>Welcome back</h1>
    <p class="sub">Login to continue to your role-based dashboard.</p>
    <form method="post" action="{{ route('login.submit') }}">
        @csrf
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            <div class="error">@error('email'){{ $message }}@enderror</div>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            <div class="error">@error('password'){{ $message }}@enderror</div>
        </div>
        <div class="row"><label><input style="width:auto" type="checkbox" name="remember"> Remember me</label></div>
        <button type="submit">Sign in</button>
    </form>
    <div class="meta">New here? <a href="{{ route('register') }}">Create account</a></div>
    <div class="theme">Theme:
        <a href="#" data-theme="night">Night</a> ·
        <a href="#" data-theme="light">Amber light</a> ·
        <a href="#" data-theme="light_blue">Blue light</a> ·
        <a href="#" data-theme="night_blue">Blue dark</a> ·
        <a href="#" data-theme="ocean">Ocean</a>
    </div>
@endsection
