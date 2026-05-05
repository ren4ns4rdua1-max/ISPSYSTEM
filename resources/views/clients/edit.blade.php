<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Client — NetManager</title>
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

        /* Form styles */
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

        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .form-card { animation: slideUp 0.4s ease both; }

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
            <a href="{{ route('clients.index') }}"
               class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Edit Client</h1>
                <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Update client information
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
                <a href="{{ route('clients.index') }}" class="hover:text-red-500 transition-colors font-medium">Clients</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-600 font-semibold">Edit</span>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="form-card w-full max-w-3xl">
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
                        <h2 class="font-display font-bold text-gray-900 text-base">Edit Client Information</h2>
                        <p class="text-gray-400 text-[12px]">Update the client details below</p>
                    </div>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('clients.update', $client->id) }}" enctype="multipart/form-data" class="p-7 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Full Name</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <input id="name" name="name" type="text"
                                       value="{{ old('name', $client->name) }}" placeholder="e.g. John Doe" autofocus
                                       class="form-input {{ $errors->has('name') ? 'error' : '' }}"/>
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
                            <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Email Address</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <input id="email" name="email" type="email"
                                       value="{{ old('email', $client->email) }}" placeholder="e.g. john@example.com"
                                       class="form-input {{ $errors->has('email') ? 'error' : '' }}"/>
                            </div>
                            @error('email')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
@enderror
                        </div>

                        <!-- Profile Photo -->
                        <div class="space-y-1.5">
                            <label for="photo" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Profile Photo (Optional)</label>
                            <div class="flex gap-3 items-start">
                                <div class="flex-shrink-0">
<div class="relative w-20 h-20 rounded-xl bg-gray-200 border-2 border-dashed border-gray-300 flex-shrink-0 overflow-hidden">
                                    @if($client->photo)
                                        <img id="photo-preview" src="{{ asset('storage/' . $client->photo) }}" alt="Preview" class="w-full h-full object-cover" onerror="this.nextElementSibling.style.display='flex'; this.style.display='none'">
                                    @endif
                                    <div id="photo-placeholder" class="w-full h-full flex items-center justify-center text-gray-400 text-xs font-medium absolute inset-0 {{ $client->photo ? 'hidden' : '' }}">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                    @if($client->photo)
                                    <button type="button" onclick="removePhoto()" class="mt-1 text-xs text-red-600 hover:text-red-800 font-medium">Remove Photo</button>
                                    @endif
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input id="photo" name="photo" type="file" accept="image/*" class="form-input">
                                    <input type="hidden" name="remove_photo" id="remove_photo" value="0">
                                </div>
                            </div>
                            @error('photo')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="space-y-1.5">
                            <label for="phone_number" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Phone Number</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                <input id="phone_number" name="phone_number" type="text"
                                       value="{{ old('phone_number', $client->phone_number) }}" placeholder="e.g. 09123456789"
                                       class="form-input {{ $errors->has('phone_number') ? 'error' : '' }}"/>
                            </div>
                            @error('phone_number')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- PPPoE Name -->
                        <div class="space-y-1.5">
                            <label for="pppoe_name" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">PPPoE Name</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="pppoe_name" name="pppoe_name" type="text"
                                       value="{{ old('pppoe_name', $client->pppoe_name) }}" placeholder="e.g. johndoe123"
                                       class="form-input {{ $errors->has('pppoe_name') ? 'error' : '' }}"/>
                            </div>
                            @error('pppoe_name')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Barangay -->
                        <div class="space-y-1.5">
                            <label for="barangay" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Barangay</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <input id="barangay" name="barangay" type="text"
                                       value="{{ old('barangay', $client->barangay) }}" placeholder="e.g. Poblacion"
                                       class="form-input {{ $errors->has('barangay') ? 'error' : '' }}"/>
                            </div>
                            @error('barangay')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- NAP Box -->
                        <div class="space-y-1.5">
                            <label for="nap_box" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">NAP Box</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                </svg>
                                <input id="nap_box" name="nap_box" type="text"
                                       value="{{ old('nap_box', $client->nap_box) }}" placeholder="e.g. NAP-001"
                                       class="form-input {{ $errors->has('nap_box') ? 'error' : '' }}"/>
                            </div>
                            @error('nap_box')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div class="space-y-1.5">
                            <label for="start_date" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Start Date</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input id="start_date" name="start_date" type="date"
                                       value="{{ old('start_date', $client->start_date->format('Y-m-d')) }}"
                                       class="form-input {{ $errors->has('start_date') ? 'error' : '' }}"/>
                            </div>
                            @error('start_date')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Plan Description -->
                        <div class="space-y-1.5">
                            <label for="plan_description" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Plan Description</label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <select id="plan_description" name="plan_description" class="form-input {{ $errors->has('plan_description') ? 'error' : '' }}">
                                    <option value="">Select Plan</option>
                                    @foreach($subscriptionRates as $rate)
                                        <option value="{{ $rate->plan_name }} - {{ $rate->speed }}" {{ old('plan_description', $client->plan_description) == $rate->plan_name . ' - ' . $rate->speed ? 'selected' : '' }}>
                                            {{ $rate->plan_name }} - {{ $rate->speed }} (₱{{ number_format($rate->monthly_fee, 2) }})
                                        </option>
                                    @endforeach
                                    <option value="Custom" {{ old('plan_description', $client->plan_description) == 'Custom' ? 'selected' : '' }}>Custom Plan</option>
                                </select>
                            </div>
                            @error('plan_description')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Due Date & Time -->
                        <div class="space-y-1.5">
                            <label for="due_date_time" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Due Date & Time</label>
                            <div class="input-wrapper">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <input id="due_date_time" name="due_date_time" type="datetime-local"
                                       value="{{ old('due_date_time', $client->due_date_time->format('Y-m-d\TH:i')) }}"
                                       class="form-input {{ $errors->has('due_date_time') ? 'error' : '' }}"/>
                            </div>
                            @error('due_date_time')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="space-y-1.5">
                            <label for="status" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Status</label>
                            <div class="input-wrapper input-wrapper-select">
                                <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <select id="status" name="status" class="form-input {{ $errors->has('status') ? 'error' : '' }}">
                                    <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $client->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="cancelled" {{ old('status', $client->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="pending_approval" {{ old('status', $client->status) == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Notes (Full Width) -->
                        <div class="md:col-span-2 space-y-1.5">
                            <label for="notes" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3"
                                      placeholder="Additional notes about this client..."
                                      class="form-input {{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes', $client->notes) }}</textarea>
                            @error('notes')
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-red-600 mt-1">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 pt-5 flex items-center justify-between gap-3 flex-wrap">
                        <a href="{{ route('clients.index') }}"
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
                            Update Client
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>

<script>
function removePhoto() {
    document.getElementById('photo').value = '';
    document.getElementById('remove_photo').value = '1';
    const preview = document.getElementById('photo-preview');
    if (preview) preview.style.display = 'none';
    document.getElementById('photo-placeholder').classList.remove('hidden');
}
document.getElementById('photo').addEventListener('change', function () {
    document.getElementById('remove_photo').value = '0';
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            let preview = document.getElementById('photo-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'photo-preview';
                preview.className = 'w-full h-full object-cover absolute inset-0';
                document.querySelector('.relative.w-20.h-20').prepend(preview);
            }
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('photo-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
