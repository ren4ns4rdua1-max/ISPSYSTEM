<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* ── Sidebar ── */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
            background: linear-gradient(180deg, #0c0e1a 0%, #111827 60%, #0c0e1a 100%);
        }
        #main-content { transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1); }

        .collapsible {
            transition: opacity 0.2s ease, max-width 0.3s ease;
            overflow: hidden; white-space: nowrap;
        }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }

        /* active bar */
        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 4px 4px 0;
            background: linear-gradient(180deg, #dc2626, #f87171);
        }

        /* tooltip */
        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1f2937; color: #f9fafb; font-size: 12px; font-weight: 600;
            padding: 4px 10px; border-radius: 8px; white-space: nowrap;
            pointer-events: none; opacity: 0; transition: opacity 0.15s ease; z-index: 999;
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 5px solid transparent; border-right-color: #1f2937;
        }

        /* ── Topbar ── */
        .topbar {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226,232,240,0.8);
        }

        .avatar-grad { background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%); }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        html { overflow-x: hidden; }

        /* ── Form inputs ── */
        .fi {
            width: 100%;
            padding: 0.7rem 1rem 0.7rem 2.6rem;
            font-size: 0.875rem; color: #111827;
            background: #f9fafb; border: 1.5px solid #e5e7eb;
            border-radius: 12px; outline: none;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .fi:focus { background: #fff; border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,.1); }
        .fi::placeholder { color: #9ca3af; }
        .fi.err { border-color: #fca5a5; background: #fff5f5; }
        .fi-wrap { position: relative; }
        .fi-icon {
            position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
            color: #d1d5db; pointer-events: none; transition: color 0.2s;
            display: flex; align-items: center;
        }
        .fi-wrap:focus-within .fi-icon { color: #dc2626; }
        .fi-eye {
            position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
            color: #d1d5db; cursor: pointer; transition: color 0.2s;
            display: flex; align-items: center; background: none; border: none;
        }
        .fi-eye:hover { color: #dc2626; }

        /* ── Strength bar ── */
        .sbar { height: 3px; border-radius: 99px; background: #e5e7eb; overflow: hidden; margin-top: 6px; }
        .sfill { height: 100%; border-radius: 99px; transition: width .4s ease, background .4s ease; width: 0; }

        /* ── Tabs ── */
        .tab-pill {
            flex: 1; display: flex; align-items: center; justify-content: center;
            gap: 6px; padding: 10px 16px; border-radius: 12px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            color: #6b7280; border: none; background: none;
            transition: all .2s ease; font-family: 'DM Sans', sans-serif;
        }
        .tab-pill.on { background: white; color: #dc2626; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .tab-pill:hover:not(.on) { background: rgba(255,255,255,0.5); color: #374151; }

        /* ── Cards ── */
        .card {
            background: white; border-radius: 20px;
            border: 1.5px solid #f1f5f9; overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .card-head {
            padding: 1.25rem 1.75rem; border-bottom: 1.5px solid #f8fafc;
            display: flex; align-items: center; gap: 12px;
        }
        .card-icon {
            width: 38px; height: 38px; border-radius: 11px;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .card-body { padding: 1.75rem; }

        /* ── Buttons ── */
        .btn-save {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 22px; font-size: 13px; font-weight: 700;
            color: white; border: none; border-radius: 12px; cursor: pointer;
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            box-shadow: 0 4px 14px rgba(220,38,38,.3);
            transition: all .25s ease; font-family: 'DM Sans', sans-serif;
            position: relative; overflow: hidden;
        }
        .btn-save::after {
            content: ''; position: absolute; top: -50%; left: -60%;
            width: 40%; height: 200%; background: rgba(255,255,255,.15);
            transform: skewX(-20deg); transition: left .4s;
        }
        .btn-save:hover::after { left: 120%; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(220,38,38,.4); }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; font-size: 13px; font-weight: 600;
            color: #6b7280; border: 1.5px solid #e5e7eb; border-radius: 12px;
            cursor: pointer; background: white; transition: all .2s; font-family: 'DM Sans', sans-serif;
        }
        .btn-cancel:hover { background: #f9fafb; border-color: #d1d5db; }

        /* ── Alerts ── */
        .alert-ok {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 12px;
            background: #f0fdf4; border: 1.5px solid #bbf7d0;
            font-size: 13px; font-weight: 600; color: #166534;
        }
        .alert-err {
            font-size: 11px; font-weight: 600; color: #dc2626;
            display: flex; align-items: center; gap: 4px; margin-top: 4px;
        }

        /* ── Label ── */
        .lbl {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            color: #9ca3af; margin-bottom: 6px;
        }

        /* ── Section label in sidebar ── */
        .sec-lbl {
            padding: 3px 10px 5px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            transition: opacity .2s;
        }
        .sidebar-collapsed .sec-lbl { opacity: 0; }

        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeUp .35s ease both; }
    </style>
</head>

<!-- Mobile Menu Button -->
<div class="mobile-menu-btn">
    <button onclick="toggleMobileSidebar()"
            class="p-2.5 rounded-xl bg-white shadow-lg text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeMobileSidebar()"></div>
<body class="bg-gray-50 min-h-screen flex">

<!-- Mobile Menu Button -->
<div class="mobile-menu-btn">
    <button onclick="toggleMobileSidebar()"
            class="p-2.5 rounded-xl bg-white shadow-lg text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</div>

<!-- Mobile Overlay -->
<div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden" onclick="closeMobileSidebar()"></div>


<!-- ═══════════════════ SIDEBAR ═══════════════════ -->

@include('partials.sidebar')


<!-- ══════════════════════ MAIN ══════════════════════ -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen" style="margin-left:260px">

    <!-- Topbar -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[19px] leading-tight">My Profile</h1>
                <p class="text-gray-400 text-[11px] mt-0.5">Manage your account settings</p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
        </div>
    </header>

    <!-- Body -->
    <main class="flex-1 p-6 max-w-4xl w-full mx-auto space-y-5">

        <!-- Profile Hero Card -->
        <div class="card fade-up">
            <div class="p-6">
                <div class="flex items-center gap-5">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-2xl avatar-grad flex items-center justify-center text-white font-bold text-3xl shadow-lg font-display flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-white shadow-md flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-1">
                            <p class="font-display font-bold text-gray-900 text-lg leading-tight">{{ Auth::user()->name }}</p>
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-lg" style="color:#059669;background:#ecfdf5;">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>Active
                            </span>
                        </div>
                        <p class="text-gray-400 text-[13px]">{{ Auth::user()->email }}</p>
                        <p class="text-gray-300 text-[11px] mt-1">Member since {{ Auth::user()->created_at->format('F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-1 p-1.5 bg-gray-100 rounded-2xl fade-up" style="animation-delay:.05s">
            <button onclick="showTab('info')" id="t-info" class="tab-pill on">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile Info
            </button>
            <button onclick="showTab('pw')" id="t-pw" class="tab-pill">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Password
            </button>
            <button onclick="showTab('danger')" id="t-danger" class="tab-pill">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Account
            </button>
        </div>

        <!-- ══ TAB: PROFILE INFO ══ -->
        <div id="p-info" class="card fade-up" style="animation-delay:.1s">
            <div class="card-head">
                <div class="card-icon">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="font-display font-bold text-gray-800 text-[13px]">Profile Information</p>
                    <p class="text-gray-400 text-[11px]">Update your name and email address</p>
                </div>
            </div>
            <div class="card-body">

                @if (session('status') === 'profile-updated')
                    <div class="alert-ok mb-5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Profile updated successfully.
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Name -->
                        <div>
                            <label class="lbl">Full Name</label>
                            <div class="fi-wrap">
                                <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       required autofocus autocomplete="name" placeholder="Your full name"
                                       class="fi {{ $errors->updateProfileInformation->has('name') ? 'err' : '' }}">
                            </div>
                            @error('name', 'updateProfileInformation')
                                <p class="alert-err mt-1"><svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="lbl">Email Address</label>
                            <div class="fi-wrap">
                                <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       required autocomplete="username" placeholder="your@email.com"
                                       class="fi {{ $errors->updateProfileInformation->has('email') ? 'err' : '' }}">
                            </div>
                            @error('email', 'updateProfileInformation')
                                <p class="alert-err mt-1"><svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="flex items-start gap-3 p-3.5 rounded-xl mt-5" style="background:#fffbeb;border:1.5px solid #fde68a;">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div class="flex-1">
                                <p class="text-amber-800 text-[12px] font-semibold">Email not verified.</p>
                                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-700 text-[11px] underline font-medium hover:text-amber-900 transition-colors mt-0.5">Resend verification email</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-gray-100">
                        <button type="submit" class="btn-save">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ══ TAB: PASSWORD ══ -->
        <div id="p-pw" class="card fade-up hidden" style="animation-delay:.1s">
            <div class="card-head">
                <div class="card-icon">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <p class="font-display font-bold text-gray-800 text-[13px]">Change Password</p>
                    <p class="text-gray-400 text-[11px]">Use a strong, unique password to protect your account</p>
                </div>
            </div>
            <div class="card-body">

                @if (session('status') === 'password-updated')
                    <div class="alert-ok mb-5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Password updated successfully.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div>
                        <label class="lbl">Current Password</label>
                        <div class="fi-wrap">
                            <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                                   placeholder="Enter current password"
                                   class="fi {{ $errors->updatePassword->has('current_password') ? 'err' : '' }}">
                            <button type="button" class="fi-eye" onclick="togglePw('current_password', this)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <p class="alert-err mt-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- New Password -->
                        <div>
                            <label class="lbl">New Password</label>
                            <div class="fi-wrap">
                                <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></span>
                                <input id="new_password" name="password" type="password" autocomplete="new-password"
                                       placeholder="Min. 8 characters"
                                       class="fi {{ $errors->updatePassword->has('password') ? 'err' : '' }}"
                                       oninput="checkStr(this.value)">
                                <button type="button" class="fi-eye" onclick="togglePw('new_password', this)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <div class="sbar"><div id="sfill" class="sfill"></div></div>
                            <p id="slabel" class="text-[10px] font-semibold mt-1"></p>
                            @error('password', 'updatePassword')
                                <p class="alert-err mt-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm -->
                        <div>
                            <label class="lbl">Confirm New Password</label>
                            <div class="fi-wrap">
                                <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></span>
                                <input id="pw_confirm" name="password_confirmation" type="password" autocomplete="new-password"
                                       placeholder="Confirm new password" class="fi" oninput="checkMatch()">
                                <button type="button" class="fi-eye" onclick="togglePw('pw_confirm', this)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            <p id="mlabel" class="text-[10px] font-semibold mt-1"></p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-5 mt-2 border-t border-gray-100">
                        <button type="submit" class="btn-save">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ══ TAB: DELETE ACCOUNT ══ -->
        <div id="p-danger" class="card fade-up hidden" style="animation-delay:.1s;border-color:#fecaca;">
            <div class="card-head" style="border-color:#fecaca;background:linear-gradient(90deg,#fff5f5,#fff);">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <div>
                    <p class="font-display font-bold text-gray-800 text-[13px]">Delete Account</p>
                    <p class="text-gray-400 text-[11px]">Permanent and irreversible action</p>
                </div>
            </div>
            <div class="card-body">
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    Once your account is deleted, all of its resources and data will be permanently removed.
                    Please download any data you wish to keep before proceeding.
                </p>
                <button onclick="document.getElementById('del-modal').classList.add('show')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition-all hover:-translate-y-0.5"
                        style="background:linear-gradient(135deg,#dc2626,#991b1b);box-shadow:0 4px 14px rgba(220,38,38,.3)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete My Account
                </button>
            </div>
        </div>

    </main>
</div>

<!-- ══════════════════════ DELETE MODAL ══════════════════════ -->
<div id="del-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,.5);backdrop-filter:blur(6px);">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4" style="animation:fadeUp .25s ease;">
        <div class="flex items-start gap-4 mb-6">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="flex-1">
                <p class="font-display font-bold text-gray-900 text-lg">Delete Account</p>
                <p class="text-gray-500 text-sm mt-1 leading-relaxed">Enter your password to confirm. This action cannot be undone.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')
            <div>
                <label class="lbl">Password</label>
                <div class="fi-wrap">
                    <span class="fi-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg></span>
                    <input name="password" type="password" placeholder="Enter your password"
                           class="fi {{ $errors->userDeletion->has('password') ? 'err' : '' }}">
                </div>
                @error('password', 'userDeletion')
                    <p class="alert-err mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('del-modal').classList.add('hidden');document.getElementById('del-modal').classList.remove('flex')"
                        class="btn-cancel flex-1 justify-center">Cancel</button>
                <button type="submit" class="btn-save flex-1 justify-center">Delete Account</button>
            </div>
        </form>
    </div>
</div>


@include('partials.sidebar-js')

</body>
</html>