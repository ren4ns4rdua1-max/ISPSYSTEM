<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User — ISP Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *, body { font-family: 'DM Sans', sans-serif; }
        .font-display { font-family: 'Syne', sans-serif; }

        /* Fix body scrolling layout */
        body {
            overflow: hidden;
            height: 100vh;
        }

        /* Modern Sidebar Styling (collapsible, gradient) */
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

        /* Mobile sidebar behavior */
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

        /* Collapsible elements */
        .collapsible { 
            transition: opacity 0.25s ease, max-width 0.3s ease; 
            overflow: hidden; 
            white-space: nowrap; 
        }
        .sidebar-collapsed .collapsible { opacity: 0; max-width: 0 !important; pointer-events: none; }
        .sidebar-collapsed .nav-item-inner { justify-content: center; padding-left: 0.75rem; padding-right: 0.75rem; }
        .sidebar-collapsed .sec-lbl { opacity: 0; height: 0; margin: 0; padding: 0; overflow: hidden; }

        /* Active nav bar indicator */
        .nav-active-bar {
            position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%; border-radius: 0 6px 6px 0;
            background: linear-gradient(180deg, #ef4444, #f97316);
            box-shadow: 0 0 6px rgba(239,68,68,0.6);
        }

        /* Nav item hover */
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

        /* Submenu styles (if needed) */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-left: 2rem;
        }
        .submenu.open { max-height: 300px; }
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
        .chevron-icon { transition: transform 0.3s ease; }
        .chevron-icon.rotated { transform: rotate(90deg); }

        /* Topbar glass effect */
        .topbar {
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        /* Avatar gradient with shimmer */
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

        /* Main scrollbar */
        .main-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
        }
        .main-scroll::-webkit-scrollbar { width: 6px; }
        .main-scroll::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .main-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Form input styles (from previous design) */
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
        }
        .form-input:focus {
            background: #fff;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }
        .form-input.error { border-color: #fca5a5; background: #fff5f5; }
        .input-wrapper { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; pointer-events: none;
            transition: color 0.2s ease;
        }
        .input-wrapper:focus-within .input-icon { color: #dc2626; }
        .input-wrapper .form-input { padding-left: 2.5rem; }
        .toggle-pw {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; cursor: pointer; transition: color 0.2s;
        }
        .toggle-pw:hover { color: #dc2626; }
        .strength-bar { height: 4px; border-radius: 100px; background: #e5e7eb; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 100px; transition: width 0.4s ease, background 0.4s ease; width: 0%; }
        .form-card { animation: slideUp 0.4s ease both; }
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
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

        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
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

<!-- ═══════════════════ MODERN SIDEBAR (COLLAPSIBLE) ═══════════════════ -->

@include('partials.sidebar')


<!-- ===================== MAIN CONTENT (CREATE USER) ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR with Create User header -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Create New User</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Fill in the details to register a new user
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY (Scrollable) -->
    <main class="flex-1 main-scroll p-6 flex flex-col items-center justify-start">

        <!-- Form Card -->
        <div class="form-card w-full max-w-3xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-7 py-5 border-b border-gray-100 flex items-center gap-4"
                     style="background:linear-gradient(90deg,#fff5f5,#fff);">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm"
                         style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-gray-900 text-base">Account Information</h2>
                        <p class="text-gray-400 text-[12px]">All fields are required to create a new account</p>
                    </div>
                    <div class="ml-auto">
                        <div id="avatar-preview"
                             class="w-12 h-12 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-xl shadow-md transition-all"
                             style="font-family:'Syne',sans-serif;">?</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="p-7 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Full Name</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="e.g. Juan dela Cruz" autofocus class="form-input" oninput="updateAvatar(this.value)"/>
                            </div>
                            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Email Address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="e.g. juan@example.com" class="form-input"/>
                            </div>
                            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <input id="password" name="password" type="password" placeholder="Min. 8 characters" class="form-input" oninput="checkStrength(this.value); checkMatch()"/>
                                <button type="button" class="toggle-pw" onclick="togglePassword('password', this)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            </div>
                            <div class="strength-bar mt-1"><div id="strength-fill" class="strength-fill"></div></div>
                            <p id="strength-label" class="text-[10px] font-semibold text-gray-400"></p>
                            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Confirm Password</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Re-enter your password" class="form-input" oninput="checkMatch()"/>
                                <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                            </div>
                            <p id="match-label" class="text-[10px] font-semibold mt-1"></p>
                            @error('password_confirmation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Photo upload -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Profile Photo <span class="text-gray-400">(Optional)</span></label>
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center flex-shrink-0">
                                    <img id="photo-preview" src="" alt="" class="w-full h-full object-cover hidden">
                                    <svg id="photo-placeholder" class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhoto(this)" class="form-input">
                                    <button type="button" id="remove-photo" onclick="removePhoto()" class="hidden text-xs font-semibold text-red-500 hover:text-red-700">× Remove</button>
                                </div>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="space-y-1.5">
                            <label for="role" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">User Role</label>
                            <select id="role" name="role" class="form-input" onchange="toggleTechnicianFields(this.value)">
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="technician" {{ old('role') == 'technician' ? 'selected' : '' }}>Technician</option>
                            </select>
                            @error('role') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Technician Extra Fields -->
                    <div id="technician-fields" class="grid grid-cols-1 md:grid-cols-2 gap-5 border-t border-gray-100 pt-5" style="display:none;">
                        <div class="md:col-span-2">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-1 h-5 rounded-full bg-red-500"></div>
                                <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Technician Details</p>
                            </div>
                        </div>
                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Phone Number <span class="text-red-500">*</span></label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <input name="phone_number" type="text" value="{{ old('phone_number') }}" placeholder="e.g. 09171234567" class="form-input"/>
                            </div>
                            @error('phone_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <!-- Specialization -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Specialization</label>
                            <select name="specialization" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="installation" {{ old('specialization') == 'installation' ? 'selected' : '' }}>Installation</option>
                                <option value="repair" {{ old('specialization') == 'repair' ? 'selected' : '' }}>Repair</option>
                                <option value="both" {{ old('specialization') == 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('specialization') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <!-- Area Coverage -->
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Area Coverage</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <input name="area_coverage" type="text" value="{{ old('area_coverage') }}" placeholder="e.g. Quezon City, Marikina" class="form-input"/>
                            </div>
                            @error('area_coverage') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <!-- Verification notice -->
                        <div class="md:col-span-2 flex items-start gap-3 px-4 py-3 rounded-xl" style="background:#fffbeb;border:1px solid #fde68a;">
                            <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-amber-700">A verification email will be sent to the technician. Their account status will be <strong>Pending</strong> until they verify their email. They cannot log in until verified.</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5 flex items-center justify-between gap-3 flex-wrap">
                        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all">Cancel</a>
                        <button type="submit" class="btn-submit inline-flex items-center gap-2 px-7 py-2.5 text-sm font-semibold text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>


<script>
function toggleTechnicianFields(role) {
    const fields = document.getElementById('technician-fields');
    fields.style.display = role === 'technician' ? 'grid' : 'none';
}
// Run on page load in case of old() repopulation
document.addEventListener('DOMContentLoaded', function() {
    toggleTechnicianFields(document.getElementById('role').value);
});
</script>

@include('partials.sidebar-js')

</body>
</html>