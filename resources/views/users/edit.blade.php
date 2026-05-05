<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — NetManager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* Fix for body scrolling */
        body {
            overflow: hidden;
            height: 100vh;
        }

        /* Enhanced Sidebar Styling */
        #sidebar { 
            transition: width 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1); 
            background: linear-gradient(180deg, #0a0c18 0%, #0f111e 100%);
            backdrop-filter: blur(2px);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 50;
        }
        #main-content { 
            transition: margin-left 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            margin-left: 260px;
            width: calc(100% - 260px);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Mobile sidebar */
        @media (max-width: 1023px) {
            #sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            #sidebar.mobile-open {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0;
                width: 100%;
            }
            .mobile-menu-btn {
                display: block !important;
            }
        }

        .collapsible { 
            transition: opacity 0.25s ease, max-width 0.3s ease; 
            overflow: hidden; 
            white-space: nowrap; 
        }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        .sidebar-collapsed .sec-lbl { opacity: 0; height: 0; margin: 0; padding: 0; overflow: hidden; }

        /* Active Navigation Bar */
        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 6px 6px 0;
            background: linear-gradient(180deg, #ef4444, #f97316);
            box-shadow: 0 0 6px rgba(239,68,68,0.6);
        }

        /* Navigation item hover effect */
        .nav-item-inner {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
        }
        .nav-item-inner:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        /* Section labels */
        .sec-lbl {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            padding: 6px 10px 4px;
            margin-top: 8px;
            transition: all 0.2s;
        }

        /* Tooltip for collapsed sidebar */
        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #f1f5f9; font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 10px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.2s ease; z-index: 999;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            letter-spacing: 0.3px;
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 6px solid transparent; border-right-color: #1e293b;
        }

        /* Submenu styles */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 2rem;
        }
        .submenu.open {
            max-height: 300px;
        }
        .submenu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #9ca3af;
            text-decoration: none;
            transition: all 0.2s;
        }
        .submenu-item:hover {
            color: #fca5a5;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(3px);
        }
        .chevron-icon {
            transition: transform 0.3s ease;
        }
        .chevron-icon.rotated {
            transform: rotate(90deg);
        }

        /* Topbar glass effect */
        .topbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        /* Avatar gradient with animation */
        .avatar-grad { 
            background: linear-gradient(125deg, #dc2626, #f97316, #ec4899);
            background-size: 200% 200%;
            animation: shimmerAvatar 4s ease infinite;
        }
        @keyframes shimmerAvatar {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Main content scrollbar */
        .main-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Form input styles */
        .form-input {
            width: 100%;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
            color: #111827;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s ease;
            font-family: 'DM Sans', sans-serif;
        }
        .form-input:focus {
            background: #fff;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }
        .form-input::placeholder { color: #9ca3af; }
        .form-input.error { border-color: #fca5a5; background: #fff5f5; }

        /* Input group icons */
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; pointer-events: none;
            transition: color 0.2s ease;
        }
        .input-wrapper:focus-within .input-icon { color: #dc2626; }
        .input-wrapper .form-input { padding-left: 2.5rem; }
        .input-wrapper-select { position: relative; }
        .input-wrapper-select .input-icon { transform: translateY(-50%); top: 50%; }
        .input-wrapper-select:focus-within .input-icon { color: #dc2626; }
        .input-wrapper-select .form-input { padding-left: 2.5rem; appearance: none; -webkit-appearance: none; }

        /* Card entrance */
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .form-card { animation: slideUp 0.4s ease both; }

        /* Submit button shine */
        .btn-submit {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            position: relative; overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; top: -50%; left: -60%; width: 40%; height: 200%;
            background: rgba(255,255,255,.15); transform: skewX(-20deg);
            transition: left 0.4s ease;
        }
        .btn-submit:hover::after { left: 120%; }

        /* Password section toggle */
        #pw-section { transition: max-height 0.4s ease, opacity 0.3s ease; max-height: 0; opacity: 0; overflow: hidden; }
        #pw-section.open { max-height: 500px; opacity: 1; }
        
        /* Strength bar */
        .strength-bar { height: 4px; border-radius: 100px; background: #e5e7eb; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 100px; transition: width 0.4s ease, background 0.4s ease; width: 0%; }
        
        /* Toggle password button */
        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; cursor: pointer; transition: color 0.2s;
        }
        .toggle-pw:hover { color: #dc2626; }
        
        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
        }
        @media (max-width: 1023px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>
<body class="bg-slate-100">

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


<!-- ===================== MAIN CONTENT ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Edit User</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editing account for {{ $user->name }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY - Scrollable -->
    <main class="flex-1 main-scroll p-6 flex flex-col items-center justify-start">

        <!-- Breadcrumb -->
        <div class="w-full max-w-3xl mb-5">
            <nav class="flex items-center gap-2 text-[12px] text-gray-400">
                <a href="{{ route('dashboard') }}" class="hover:text-red-500 transition-colors font-medium">Dashboard</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('users.index') }}" class="hover:text-red-500 transition-colors font-medium">Users</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-semibold">Edit #{{ $user->id }}</span>
            </nav>
        </div>

        <div class="form-card w-full max-w-3xl space-y-4">

            <!-- User Meta Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4 flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl overflow-hidden flex-shrink-0 shadow-md"
                     id="avatar-preview">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="w-full h-full object-cover" id="avatar-img">
                    @else
                        <div class="w-full h-full avatar-grad flex items-center justify-center text-white font-bold text-2xl" id="avatar-initials" style="font-family:'Syne',sans-serif;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-display font-bold text-gray-900 text-base leading-tight" id="name-preview">{{ $user->name }}</p>
                    <p class="text-gray-400 text-[12px]">{{ $user->email }}</p>
                    <p class="text-gray-300 text-[10px] mt-0.5">Member since {{ $user->created_at->format('F d, Y') }} · {{ $user->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex-shrink-0 text-right hidden sm:block">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-3 py-1.5 rounded-lg" style="color:#059669;background:#ecfdf5;">
                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                        Active Account
                    </span>
                    <p class="text-gray-400 text-[10px] mt-1">ID #{{ $user->id }}</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Card Header -->
                <div class="px-7 py-5 border-b border-gray-100 flex items-center gap-4"
                     style="background:linear-gradient(90deg,#fff5f5,#fff);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-base">Update Account Details</h2>
                        <p class="text-gray-400 text-[12px]">Leave password fields blank to keep the current password</p>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data" class="p-7 space-y-6">
                    @csrf
                    @method('PATCH')

                    <!-- Basic Info -->
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Basic Information</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <!-- Name -->
                            <div class="space-y-1.5">
                                <label for="name" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Full Name
                                </label>
                                <div class="input-wrapper">
                                    <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <input id="name" name="name" type="text"
                                           value="{{ old('name', $user->name) }}"
                                           required autofocus autocomplete="name"
                                           placeholder="Full name"
                                           class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                                           oninput="updatePreview(this.value)"/>
                                </div>
                                @error('name')
                                    <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="space-y-1.5">
                                <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Email Address
                                </label>
                                <div class="input-wrapper">
                                    <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <input id="email" name="email" type="email"
                                           value="{{ old('email', $user->email) }}"
                                           required autocomplete="username"
                                           placeholder="Email address"
                                           class="form-input {{ $errors->has('email') ? 'error' : '' }}"/>
                                </div>
                                @error('email')
                                    <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Photo -->
                    <div class="border-t border-gray-100 pt-5">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3">Profile Photo</p>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center flex-shrink-0">
                                @if($user->photo)
                                    <img id="photo-preview" src="{{ asset('storage/' . $user->photo) }}" alt="" class="w-full h-full object-cover">
                                    <svg id="photo-placeholder" class="w-7 h-7 text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden">
                                    <svg id="photo-placeholder" class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @endif
                            </div>
                            <div class="flex-1 space-y-1">
                                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhoto(this)" class="form-input">
                                <p class="text-[11px] text-gray-400">Upload a new photo to replace the current one</p>
                            </div>
                        </div>
                    </div>

                    <!-- Password Toggle Trigger -->
                    <div class="border-t border-gray-100 pt-5">
                        <button type="button" onclick="togglePasswordSection()"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border-2 border-dashed transition-all hover:border-red-300 hover:bg-red-50/50 group"
                                style="border-color:#e5e7eb;" id="pw-toggle-btn">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-semibold text-gray-700 group-hover:text-red-700 transition-colors">Change Password</p>
                                    <p class="text-[11px] text-gray-400">Click to set a new password (optional)</p>
                                </div>
                            </div>
                            <svg id="pw-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Password Fields — Hidden by default -->
                        <div id="pw-section">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-4">

                                <!-- New Password -->
                                <div class="space-y-1.5">
                                    <label for="password" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        New Password
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <input id="password" name="password" type="password"
                                               autocomplete="new-password"
                                               placeholder="Min. 8 characters"
                                               class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                                               oninput="checkStrength(this.value)"/>
                                        <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="strength-bar mt-2">
                                        <div id="strength-fill" class="strength-fill"></div>
                                    </div>
                                    <p id="strength-label" class="text-[10px] font-semibold text-gray-400 mt-1"></p>
                                    @error('password')
                                        <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-1.5">
                                    <label for="password_confirmation" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">
                                        Confirm New Password
                                    </label>
                                    <div class="input-wrapper">
                                        <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <input id="password_confirmation" name="password_confirmation" type="password"
                                               autocomplete="new-password"
                                               placeholder="Re-enter new password"
                                               class="form-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                                               oninput="checkMatch()"/>
                                        <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p id="match-label" class="text-[10px] font-semibold mt-1"></p>
                                    @error('password_confirmation')
                                        <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 pt-5 flex items-center justify-between gap-3 flex-wrap">
                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn-submit inline-flex items-center gap-2 px-7 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>

            <!-- Info tip -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-700 mb-1">Edit Notes</p>
                        <ul class="space-y-1">
                            <li class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <svg class="w-3 h-3 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Name and email changes take effect immediately
                            </li>
                            <li class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <svg class="w-3 h-3 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Leave password fields empty to keep the existing password
                            </li>
                            <li class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                <svg class="w-3 h-3 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                If changing password, both fields must match and be at least 8 characters
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>