<!doctype html>
<html lang="en" data-theme="night">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Overview' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    <style>
        :root{--bg:#0f172a;--card:#111827;--text:#e5e7eb;--muted:#9ca3af;--border:#334155;--primary:#7c3aed;--btn-bg:#7c3aed;--btn-hover:#facc15}
        html[data-theme="light"]{--bg:#f3f4f6;--card:#fff;--text:#111827;--muted:#4b5563;--border:#d1d5db;--primary:#2563eb;--btn-bg:#111827;--btn-hover:#facc15}
        html[data-theme="ocean"]{--bg:#082f49;--card:#0c4a6e;--text:#e0f2fe;--muted:#bae6fd;--border:#0369a1;--primary:#06b6d4;--btn-bg:#0891b2;--btn-hover:#facc15}
        body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,system-ui,sans-serif}
        .layout{min-height:100vh}
        .sidebar{
            width:260px;
            background:var(--card);
            border-right:1px solid var(--border);
            box-shadow:2px 0 0 rgba(0,0,0,.06);
            padding:24px 18px;
            position:fixed;
            left:0;
            top:0;
            bottom:0;
            z-index:30;
            overflow:auto;
        }
        .brand{font-weight:700;font-size:20px;margin-bottom:22px}
        .menu{display:flex;flex-direction:column;gap:8px}
        .menu a{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid transparent;border-radius:10px;text-decoration:none;color:var(--text)}
        .menu a.active,.menu a:hover{border-color:var(--border);background:color-mix(in srgb,var(--primary) 14%,transparent)}
        .content{padding:0;margin-left:297px;min-height:100vh;border-left:1px solid var(--border)}
        .navbar{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 28px;border-bottom:1px solid var(--border);background:var(--card);position:sticky;top:0;z-index:20}
        .navtitle{font-weight:700}
        .navmeta{color:var(--muted);font-size:14px}
        .nav-right{display:flex;align-items:center;gap:10px}
        .navchip{display:inline-block;border:1px solid var(--border);border-radius:999px;padding:5px 10px;color:var(--muted);font-size:13px}
        .user-dropdown{position:relative}
        .user-trigger{display:flex;align-items:center;gap:10px;border:1px solid var(--border);background:color-mix(in srgb,var(--card) 90%,transparent);color:var(--text);padding:7px 10px;border-radius:12px;cursor:pointer}
        .avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,var(--primary),color-mix(in srgb,var(--primary) 35%,#fff));font-weight:700;color:#fff}
        .user-menu{position:absolute;right:0;top:calc(100% + 8px);min-width:280px;background:color-mix(in srgb,var(--card) 94%,transparent);border:1px solid var(--border);border-radius:14px;padding:12px;display:none;box-shadow:0 18px 36px rgba(0,0,0,.28);backdrop-filter:blur(10px)}
        .user-menu.open{display:block}
        .menu-head{padding:8px 10px;border-bottom:1px solid var(--border);margin-bottom:8px}
        .menu-name{font-weight:600}
        .menu-email{font-size:13px;color:var(--muted)}
        .menu-row{display:flex;justify-content:space-between;gap:12px;padding:8px 10px;font-size:14px}
        .pkg-badge{font-size:12px;border:1px solid var(--border);border-radius:999px;padding:3px 8px;color:var(--muted)}
        .dropdown-action-btn{
            width:100%;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            box-sizing:border-box;
            border-radius:10px;
            padding:10px 12px;
            font-size:13px;
            font-weight:600;
            line-height:1.2;
            background:var(--btn-bg);
            color:#fff !important;
            text-decoration:none;
            border:1px solid color-mix(in srgb,var(--btn-bg) 72%,var(--border));
            white-space:nowrap;
        }
        .dropdown-action-btn:hover{
            background:var(--btn-hover);
            color:#111827 !important;
        }
        .dropdown-select{
            width:100%;
            box-sizing:border-box;
            border:1px solid var(--border);
            background:color-mix(in srgb,var(--card) 90%,transparent);
            color:var(--text);
            border-radius:10px;
            padding:9px 10px;
            font-size:13px;
            outline:none;
        }
        .dropdown-select:focus{border-color:var(--primary)}
        .theme-switch{display:flex;justify-content:space-between;align-items:center;padding:8px 10px}
        .switch{position:relative;width:46px;height:26px}
        .switch input{opacity:0;width:0;height:0}
        .slider{position:absolute;inset:0;cursor:pointer;background:#475569;border-radius:999px;transition:.2s}
        .slider:before{content:"";position:absolute;height:20px;width:20px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s}
        .switch input:checked + .slider{background:#22c55e}
        .switch input:checked + .slider:before{transform:translateX(20px)}
        .content-inner{padding:28px}
        .card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;max-width:920px}
        .muted{color:var(--muted)}
        .chip{display:inline-block;border:1px solid var(--border);padding:6px 12px;border-radius:999px;margin:8px 8px 0 0}
        button,.linkbtn{border:0;border-radius:10px;padding:10px 14px;background:var(--btn-bg);color:#fff;cursor:pointer;text-decoration:none;display:inline-block;transition:all .2s ease}
        button:hover,.linkbtn:hover{background:var(--btn-hover);color:#111827;transform:translateY(-1px)}
        @media (max-width:900px){.sidebar{position:static;width:auto;height:auto;border-right:0;border-bottom:1px solid var(--border)}.content{margin-left:0;border-left:0}}
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">SociBiz Panel</div>
        <nav class="menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa fa-gauge-high"></i><span>Overview</span></a>
            <a href="{{ route('settings.user') }}" class="{{ request()->routeIs('settings.user') ? 'active' : '' }}"><i class="fa fa-user-gear"></i><span>User Settings</span></a>
            <a href="{{ route('settings.business') }}" class="{{ request()->routeIs('settings.business') ? 'active' : '' }}"><i class="fa fa-briefcase"></i><span>Business Settings</span></a>
            @if(auth()->user()?->hasRole('admin'))
                <a href="{{ route('admin.panel') }}" class="{{ request()->routeIs('admin.panel') ? 'active' : '' }}"><i class="fa fa-user-shield"></i><span>Admin Panel</span></a>
            @endif
        </nav>
    </aside>
    <main class="content">
        <div class="navbar">
            <div>
                <div class="navtitle">{{ $heading ?? 'Overview' }}</div>
                <div class="navmeta">Welcome, {{ auth()->user()->name ?? 'User' }}</div>
            </div>
            <div class="nav-right">
                <div class="navchip">{{ now()->format('d M Y') }}</div>
                @php
                    $business = auth()->user()?->businesses()->latest()->first();
                    $accounts = $business
                        ? \Modules\Account\Models\Account::with(['bankType', 'bank'])
                            ->where('user_id', auth()->id())
                            ->where('business_id', $business->id)
                            ->latest()
                            ->get()
                        : collect();
                    $selectedAccountId = (int) session('selected_account_id');
                    $assignedAccount = $accounts->firstWhere('id', $selectedAccountId) ?: $accounts->first();
                    if ($assignedAccount && $selectedAccountId !== (int) $assignedAccount->id) {
                        session(['selected_account_id' => $assignedAccount->id]);
                    }
                    if (!$assignedAccount) {
                        session()->forget('selected_account_id');
                    }
                @endphp
                <div class="user-dropdown">
                    <button type="button" class="user-trigger" id="businessDropdownBtn">
                        <i class="fa fa-briefcase"></i>
                        <span>{{ $business?->name ?? 'Your Business' }}</span>
                        <i class="fa fa-chevron-down"></i>
                    </button>
                    <div class="user-menu" id="businessDropdownMenu">
                        <div class="menu-head">
                            <div class="menu-name">{{ $business?->name ?? 'No Business Yet' }}</div>
                            <div class="menu-email">{{ $business?->category ?? 'Complete onboarding in Overview' }}</div>
                        </div>
                        @if($business)
                            <div class="menu-row">
                                <span><i class="fa fa-layer-group" style="margin-right:6px;"></i>Category</span>
                                <span class="pkg-badge">{{ $business->category }}</span>
                            </div>
                            <div class="menu-row" style="display:block;">
                                <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">About Business</div>
                                <div style="font-size:13px;line-height:1.4;">{{ $business->description ?: 'No description added yet.' }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="user-dropdown">
                    <button type="button" class="user-trigger" id="accountDropdownBtn" style="background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 24%,var(--card)),var(--card));">
                        <i class="fa fa-building-columns"></i>
                        <span>Account</span>
                        <i class="fa fa-chevron-down"></i>
                    </button>
                    <div class="user-menu" id="accountDropdownMenu" style="min-width:310px;">
                        <div class="menu-head">
                            <div class="menu-name">
                                <i class="fa fa-wallet" style="margin-right:6px;color:var(--primary);"></i>
                                {{ $assignedAccount?->account_name ?? 'No Assigned Account' }}
                            </div>
                            <div class="menu-email">
                                {{ $assignedAccount?->bankType?->name ?? 'Complete account onboarding in Overview' }}
                            </div>
                        </div>
                        @if($accounts->count() > 1)
                            <div class="menu-row" style="display:block;">
                                <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Selected Account</div>
                                <form method="post" action="{{ route('account.select') }}">
                                    @csrf
                                    <select name="account_id" class="dropdown-select" onchange="this.form.submit()">
                                        @foreach($accounts as $accountOption)
                                            <option value="{{ $accountOption->id }}" {{ (int) $assignedAccount?->id === (int) $accountOption->id ? 'selected' : '' }}>
                                                {{ $accountOption->account_name }} - {{ $accountOption->bankType?->name ?? 'Type' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                        @if($assignedAccount)
                            <div class="menu-row">
                                <span><i class="fa fa-building" style="margin-right:6px;"></i>Bank</span>
                                <span class="pkg-badge">{{ $assignedAccount->bank?->name ?? $assignedAccount->bank_name }}</span>
                            </div>
                            <div class="menu-row">
                                <span><i class="fa fa-hashtag" style="margin-right:6px;"></i>Account No</span>
                                <span>{{ $assignedAccount->bank_account_number }}</span>
                            </div>
                            <div class="menu-row">
                                <span><i class="fa fa-code-branch" style="margin-right:6px;"></i>Branch</span>
                                <span>{{ $assignedAccount->branch }}</span>
                            </div>
                            <div class="menu-row" style="display:block;">
                                <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Current Balance</div>
                                <div style="font-size:20px;font-weight:700;color:var(--primary);">
                                    {{ number_format((float) $assignedAccount->current_balance, 2) }}
                                </div>
                            </div>
                            <div class="menu-row" style="display:block;padding-top:4px;">
                                <a href="{{ route('account.onboarding') }}" class="dropdown-action-btn">
                                    <i class="fa fa-pen-to-square" style="margin-right:6px;"></i>Open Account Onboarding
                                </a>
                            </div>
                        @else
                            <div class="menu-row" style="display:block;">
                                <div style="font-size:13px;color:var(--muted);line-height:1.5;">
                                    No account is assigned to this business yet. Complete the account onboarding to see details here.
                                </div>
                            </div>
                            <div class="menu-row" style="display:block;padding-top:4px;">
                                <a href="{{ route('account.onboarding') }}" class="dropdown-action-btn">
                                    <i class="fa fa-plus-circle" style="margin-right:6px;"></i>Start Account Onboarding
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="user-dropdown">
                    <button type="button" class="user-trigger" id="userDropdownBtn">
                        <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        <span>{{ auth()->user()->name ?? 'User' }}</span>
                        <i class="fa fa-chevron-down"></i>
                    </button>
                    <div class="user-menu" id="userDropdownMenu">
                        <div class="menu-head">
                            <div class="menu-name">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="menu-email">{{ auth()->user()->email ?? '' }}</div>
                        </div>
                        <div class="menu-row">
                            <span><i class="fa fa-box" style="margin-right:6px;"></i>Purchased Package</span>
                            <span class="pkg-badge">Free Trial</span>
                        </div>
                        <div class="theme-switch">
                            <span><i class="fa fa-sun" style="margin-right:6px;"></i>Light Theme</span>
                            <label class="switch">
                                <input type="checkbox" id="lightThemeToggle">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <form method="post" action="{{ route('logout') }}" style="margin-top:6px;">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;">
                                <i class="fa fa-right-from-bracket"></i><span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-inner">
            @yield('content')
        </div>
    </main>
</div>
<script>
    const root = document.documentElement;
    const savedTheme = localStorage.getItem('ui_theme') || root.getAttribute('data-theme') || 'night';
    root.setAttribute('data-theme', savedTheme);
    const dropdownBtn = document.getElementById('userDropdownBtn');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    const businessDropdownBtn = document.getElementById('businessDropdownBtn');
    const businessDropdownMenu = document.getElementById('businessDropdownMenu');
    const accountDropdownBtn = document.getElementById('accountDropdownBtn');
    const accountDropdownMenu = document.getElementById('accountDropdownMenu');
    const themeToggle = document.getElementById('lightThemeToggle');
    if (themeToggle) {
        themeToggle.checked = savedTheme === 'light';
        themeToggle.addEventListener('change', () => {
            const nextTheme = themeToggle.checked ? 'light' : 'night';
            root.setAttribute('data-theme', nextTheme);
            localStorage.setItem('ui_theme', nextTheme);
        });
    }
    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', () => dropdownMenu.classList.toggle('open'));
    }
    if (businessDropdownBtn && businessDropdownMenu) {
        businessDropdownBtn.addEventListener('click', () => businessDropdownMenu.classList.toggle('open'));
    }
    if (accountDropdownBtn && accountDropdownMenu) {
        accountDropdownBtn.addEventListener('click', () => accountDropdownMenu.classList.toggle('open'));
    }
    document.addEventListener('click', (event) => {
        if (dropdownBtn && dropdownMenu && !dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('open');
        }
        if (businessDropdownBtn && businessDropdownMenu && !businessDropdownBtn.contains(event.target) && !businessDropdownMenu.contains(event.target)) {
            businessDropdownMenu.classList.remove('open');
        }
        if (accountDropdownBtn && accountDropdownMenu && !accountDropdownBtn.contains(event.target) && !accountDropdownMenu.contains(event.target)) {
            accountDropdownMenu.classList.remove('open');
        }
    });
</script>
</body>
</html>
