<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Customization — ISP Admin</title>
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

        /* Modern Sidebar Styling */
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

        .nav-item-inner {
            transition: all 0.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
        }
        .nav-item-inner:hover {
            background: rgba(255,255,255,0.08);
            transform: translateX(4px);
        }

        .sec-lbl {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .13em;
            color: rgba(255,255,255,.22);
            font-family: 'Syne', sans-serif;
            padding: 6px 10px 4px;
            margin-top: 8px;
            transition: all 0.2s;
        }

        .nav-tooltip {
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #f1f5f9; font-size: 12px; font-weight: 600;
            padding: 5px 12px; border-radius: 10px; white-space: nowrap; pointer-events: none;
            opacity: 0; transition: opacity 0.2s ease; z-index: 999;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .sidebar-collapsed .nav-wrapper:hover .nav-tooltip { opacity: 1; }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 6px solid transparent; border-right-color: #1e293b;
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

        /* Main scrollbar */
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
        }
        .form-input:focus {
            background: #fff;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.1);
        }
        .form-input.error { border-color: #fca5a5; background: #fff5f5; }

        /* Mobile menu button */
        .mobile-menu-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 60;
            display: none;
        }
        
        /* Animation */
        @keyframes fadeIn { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease forwards; }
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


<!-- ===================== MAIN CONTENT (TEMPLATE CUSTOMIZATION) ===================== -->
<div id="main-content" class="flex flex-col flex-1 min-h-screen">

    <!-- TOP BAR -->
    <header class="topbar sticky top-0 z-40 flex items-center justify-between px-7 py-3.5">
        <div>
            <h1 class="font-display font-bold text-gray-900 text-[20px] leading-tight">Template Customization</h1>
            <p class="text-gray-400 text-[12px] flex items-center gap-1.5 mt-0.5">
                <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Edit all texts on the welcome/landing page (plans excluded)
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile.edit') }}" class="w-9 h-9 rounded-xl avatar-grad flex items-center justify-center text-white font-bold text-sm shadow-md transition-all hover:scale-105">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </a>
        </div>
    </header>

    <!-- PAGE BODY (Scrollable) -->
    <main class="flex-1 main-scroll p-6">
        @if (session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 rounded-xl border animate-fadeIn" style="background:#f0fdf4;border-color:#bbf7d0;">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-emerald-800 text-sm font-semibold">{{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Edit Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                <form method="POST" action="{{ route('admin.templates.update') }}">
                    @csrf
                    <div class="space-y-8">
                        <!-- Navigation Section -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                Navigation
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nav Logo Text</label>
                                    <textarea name="welcome_nav_logo" rows="1" class="form-input @error('welcome_nav_logo') error @enderror">{{ old('welcome_nav_logo', $settings->get('welcome_nav_logo', 'ISP BILLING SYSTEM')->value ?? 'ISP BILLING SYSTEM') }}</textarea>
                                    @error('welcome_nav_logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Login Button Text</label>
                                    <textarea name="welcome_nav_login_btn" rows="1" class="form-input">{{ old('welcome_nav_login_btn', $settings->get('welcome_nav_login_btn', 'Login')->value ?? 'Login') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Section -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Hero Section
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Eyebrow</label>
                                    <textarea name="welcome_hero_eyebrow" rows="1" class="form-input">{{ old('welcome_hero_eyebrow', $settings->get('welcome_hero_eyebrow', 'Fast · Reliable · Affordable')->value ?? 'Fast · Reliable · Affordable') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero CTA Primary</label>
                                    <textarea name="welcome_hero_cta_primary" rows="1" class="form-input">{{ old('welcome_hero_cta_primary', $settings->get('welcome_hero_cta_primary', 'View Plans')->value ?? 'View Plans') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Title Line 1</label>
                                    <textarea name="welcome_hero_title_1" rows="2" class="form-input">{{ old('welcome_hero_title_1', $settings->get('welcome_hero_title_1', 'Reliable Internet for')->value ?? 'Reliable Internet for') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Subtitle</label>
                                    <textarea name="welcome_hero_subtitle" rows="3" class="form-input">{{ old('welcome_hero_subtitle', $settings->get('welcome_hero_subtitle')->value ?? 'Fiber & wireless internet built for homes and businesses. Blazing speeds, zero downtime, and support that actually picks up.') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Title Line 2 (gradient part)</label>
                                    <textarea name="welcome_hero_title_2" rows="2" class="form-input">{{ old('welcome_hero_title_2', $settings->get('welcome_hero_title_2', 'Every Home & Business')->value ?? 'Every Home & Business') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Features Section (Condensed) -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Features Section</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Features Title</label>
                                    <textarea name="welcome_features_title" rows="2" class="form-input">{{ old('welcome_features_title', $settings->get('welcome_features_title', 'Built Different')->value ?? 'Built Different') }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">Blazing Speeds Title</label><textarea name="welcome_features_blazing_title" rows="1" class="form-input">{{ old('welcome_features_blazing_title', $settings->get('welcome_features_blazing_title', 'Blazing Speeds')->value ?? 'Blazing Speeds') }}</textarea></div>
                                    <div><label class="block text-sm font-semibold text-gray-700 mb-2">24/7 Support Title</label><textarea name="welcome_features_support_title" rows="1" class="form-input">{{ old('welcome_features_support_title', $settings->get('welcome_features_support_title', '24/7 Local Support')->value ?? '24/7 Local Support') }}</textarea></div>
                                    <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">Blazing Speeds Description</label><textarea name="welcome_features_blazing_desc" rows="3" class="form-input">{{ old('welcome_features_blazing_desc', $settings->get('welcome_features_blazing_desc')->value ?? 'Low latency, high throughput. Stream 4K, game online, and video call — all at the same time without a hitch.') }}</textarea></div>
                                    <div class="md:col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-2">24/7 Support Description</label><textarea name="welcome_features_support_desc" rows="3" class="form-input">{{ old('welcome_features_support_desc', $settings->get('welcome_features_support_desc')->value ?? 'Real humans, not robots. Our local support team picks up within 60 seconds, 24 hours a day.') }}</textarea></div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Footer -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Contact Section
                            </h3>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Contact Eyebrow</label><textarea name="welcome_contact_eyebrow" rows="1" class="form-input">{{ old('welcome_contact_eyebrow', $settings->get('welcome_contact_eyebrow', 'Get In Touch')->value ?? 'Get In Touch') }}</textarea></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Contact Title</label><textarea name="welcome_contact_title" rows="2" class="form-input">{{ old('welcome_contact_title', $settings->get('welcome_contact_title', "Let's Get You Connected")->value ?? "Let's Get You Connected") }}</textarea></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Contact Subtitle</label><textarea name="welcome_contact_subtitle" rows="2" class="form-input">{{ old('welcome_contact_subtitle', $settings->get('welcome_contact_subtitle', "Drop us a message and we'll reach out within 24 hours.")->value ?? "Drop us a message and we'll reach out within 24 hours.") }}</textarea></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Submit Button Text</label><textarea name="welcome_contact_submit" rows="1" class="form-input">{{ old('welcome_contact_submit', $settings->get('welcome_contact_submit', 'Send Message →')->value ?? 'Send Message →') }}</textarea></div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Contact Info (Footer)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">📍 Address</label>
                                    <textarea name="welcome_contact_address" rows="2" class="form-input">{{ old('welcome_contact_address', $settings->get('welcome_contact_address', '123 Internet Street, Tech City, TC 12345')->value ?? '123 Internet Street, Tech City, TC 12345') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">📞 Phone Number</label>
                                    <textarea name="welcome_contact_phone" rows="1" class="form-input">{{ old('welcome_contact_phone', $settings->get('welcome_contact_phone', '+1 (555) 123-4567')->value ?? '+1 (555) 123-4567') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">✉️ Email Address</label>
                                    <textarea name="welcome_contact_email" rows="1" class="form-input">{{ old('welcome_contact_email', $settings->get('welcome_contact_email', 'support@netmanager.com')->value ?? 'support@netmanager.com') }}</textarea>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">📘 Facebook URL</label>
                                        <input type="text" name="welcome_contact_social_fb" value="{{ old('welcome_contact_social_fb', $settings->get('welcome_contact_social_fb', '#')->value ?? '#') }}" class="form-input" placeholder="https://facebook.com/...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">🐦 Twitter URL</label>
                                        <input type="text" name="welcome_contact_social_twitter" value="{{ old('welcome_contact_social_twitter', $settings->get('welcome_contact_social_twitter', '#')->value ?? '#') }}" class="form-input" placeholder="https://twitter.com/...">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">📷 Instagram URL</label>
                                        <input type="text" name="welcome_contact_social_instagram" value="{{ old('welcome_contact_social_instagram', $settings->get('welcome_contact_social_instagram', '#')->value ?? '#') }}" class="form-input" placeholder="https://instagram.com/...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Footer
                            </h3>
                            <div class="space-y-4">
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Brand Name</label><textarea name="welcome_footer_brand" rows="1" class="form-input">{{ old('welcome_footer_brand', $settings->get('welcome_footer_brand', 'NetManager')->value ?? 'NetManager') }}</textarea></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Footer Description</label><textarea name="welcome_footer_desc" rows="3" class="form-input">{{ old('welcome_footer_desc', $settings->get('welcome_footer_desc', 'Your trusted partner for reliable internet.')->value ?? 'Your trusted partner for reliable internet.') }}</textarea></div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-2">Copyright Text</label><textarea name="welcome_footer_copyright" rows="1" class="form-input">{{ old('welcome_footer_copyright', $settings->get('welcome_footer_copyright', '© 2024 ISP Billing Management System. All rights reserved.')->value ?? '© 2024 ISP Billing Management System. All rights reserved.') }}</textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-4 px-8 rounded-2xl text-lg shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                            💾 Save All Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Preview -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4 font-display flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Live Preview
                </h3>
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                    <iframe src="{{ url('/') }}" class="w-full bg-white" style="min-height: 600px; border:0;"></iframe>
                </div>
                <p class="mt-4 text-sm text-gray-500 text-center">Changes appear instantly after save (clear browser cache if needed)</p>
            </div>
        </div>
    </main>
</div>


@include('partials.sidebar-js')

</body>
</html>