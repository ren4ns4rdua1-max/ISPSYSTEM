@extends('layouts.guest')

@section('title', 'Template Customization')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-display">Template Customization</h1>
            <p class="mt-2 text-lg text-gray-600">Edit all texts on the welcome/landing page (plans excluded).</p>
            @if (session('success'))
                <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Edit Form -->
            <div class="bg-white shadow-xl rounded-3xl p-8 border border-gray-100">
                <form method="POST" action="{{ route('admin.templates.update') }}">
                    @csrf
                    <div class="space-y-8">
                        <!-- Navigation -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Navigation</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nav Logo Text</label>
                                    <textarea name="welcome_nav_logo" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none @error('welcome_nav_logo') border-red-300 @enderror">{{ old('welcome_nav_logo', $settings->get('welcome_nav_logo', 'ISP BILLING SYSTEM')->value ?? 'ISP BILLING SYSTEM') }}</textarea>
                                    @error('welcome_nav_logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Login Button</label>
                                    <textarea name="welcome_nav_login_btn" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none @error('welcome_nav_login_btn') border-red-300 @enderror">{{ old('welcome_nav_login_btn', $settings->get('welcome_nav_login_btn', 'Login')->value ?? 'Login') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Hero Section -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Hero Section</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Eyebrow</label>
                                    <textarea name="welcome_hero_eyebrow" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_hero_eyebrow', $settings->get('welcome_hero_eyebrow', 'Fast · Reliable · Affordable')->value ?? 'Fast · Reliable · Affordable') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero CTA Primary</label>
                                    <textarea name="welcome_hero_cta_primary" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_hero_cta_primary', $settings->get('welcome_hero_cta_primary', 'View Plans')->value ?? 'View Plans') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Title Line 1</label>
                                    <textarea name="welcome_hero_title_1" rows="2" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_hero_title_1', $settings->get('welcome_hero_title_1', 'Reliable Internet for')->value ?? 'Reliable Internet for') }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hero Subtitle</label>
                                    <textarea name="welcome_hero_subtitle" rows="3" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_hero_subtitle', $settings->get('welcome_hero_subtitle')->value ?? 'Fiber & wireless internet built for homes and businesses. Blazing speeds, zero downtime, and support that actually picks up.') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Features Section</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Features Title</label>
                                    <textarea name="welcome_features_title" rows="2" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_features_title', $settings->get('welcome_features_title', 'Built Different')->value ?? 'Built Different') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Blazing Speeds Title</label>
                                    <textarea name="welcome_features_blazing_title" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_features_blazing_title', $settings->get('welcome_features_blazing_title', 'Blazing Speeds')->value ?? 'Blazing Speeds') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Blazing Speeds Description</label>
                                    <textarea name="welcome_features_blazing_desc" rows="3" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_features_blazing_desc', $settings->get('welcome_features_blazing_desc')->value ?? 'Low latency, high throughput. Stream 4K, game online, and video call — all at the same time without a hitch.') }}</textarea>
                                </div>
                                <!-- Add more features similarly -->
                            </div>
                        </div>

                        <!-- Contact & Footer (abridged for brevity, extend as needed) -->
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Contact & Footer</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Title</label>
                                    <textarea name="welcome_contact_title" rows="2" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_contact_title', $settings->get('welcome_contact_title', "Let\'s Get You Connected")->value ?? "Let\'s Get You Connected") }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Brand</label>
                                    <textarea name="welcome_footer_brand" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_footer_brand', $settings->get('welcome_footer_brand', 'NetManager')->value ?? 'NetManager') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Copyright</label>
                                    <textarea name="welcome_footer_copyright" rows="1" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none">{{ old('welcome_footer_copyright', $settings->get('welcome_footer_copyright', '© 2024 ISP Billing Management System. All rights reserved.')->value ?? '© 2024 ISP Billing Management System. All rights reserved.') }}</textarea>
                                </div>
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
                <h3 class="text-xl font-bold text-gray-900 mb-4 font-display">Live Preview</h3>
                <iframe src="{{ url('/') }}" class="w-full h-96 bg-white rounded-2xl shadow-2xl border-0" style="min-height: 600px;"></iframe>
                <p class="mt-4 text-sm text-gray-500 text-center">Changes appear instantly after save (clear browser cache if needed)</p>
            </div>
        </div>
    </div>
</div>
@endsection

