@extends('theme::layouts.auth', ['title' => 'Register'])

@section('content')
    <h1>Create your account</h1>
    <p class="sub">Register and choose your role to continue.</p>
    <form method="post" action="{{ route('register.submit') }}">
        @csrf
        <div class="field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required>
            <div class="error">@error('name'){{ $message }}@enderror</div>
        </div>
        <div class="field">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            <div class="error">@error('email'){{ $message }}@enderror</div>
        </div>
        <div class="field">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="user" @selected(old('role') === 'user')>User</option>
                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
            </select>
            <div class="error">@error('role'){{ $message }}@enderror</div>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required>
            <div class="error">@error('password'){{ $message }}@enderror</div>
        </div>
        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
            <div class="error">@error('password_confirmation'){{ $message }}@enderror</div>
        </div>
        <button type="submit">Create account</button>
    </form>
    <div class="meta">Already have account? <a href="{{ route('login') }}">Sign in</a></div>
    <div class="theme">Theme:
        <a href="#" data-theme="night">Night</a> ·
        <a href="#" data-theme="light">Amber light</a> ·
        <a href="#" data-theme="light_blue">Blue light</a> ·
        <a href="#" data-theme="night_blue">Blue dark</a> ·
        <a href="#" data-theme="ocean">Ocean</a>
    </div>
@endsection
