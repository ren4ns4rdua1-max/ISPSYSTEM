<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $welcomeSettings = [
            // Navigation
            ['welcome_nav_logo', 'ISP BILLING SYSTEM', 'welcome', 'text'],
            ['welcome_nav_login_btn', 'Login', 'welcome', 'text'],

            // Hero Section
            ['welcome_hero_eyebrow', 'Fast · Reliable · Affordable', 'welcome', 'text'],
            ['welcome_hero_title_1', 'Reliable Internet for', 'welcome', 'text'],
            ['welcome_hero_title_2', 'Every Connection', 'welcome', 'text'],
            ['welcome_hero_subtitle', 'Fiber & wireless internet built for homes and businesses. Blazing speeds, zero downtime, and support that actually picks up.', 'welcome', 'textarea'],
            ['welcome_hero_cta_primary', 'View Plans', 'welcome', 'text'],
            ['welcome_hero_cta_secondary', 'Sign In to Portal', 'welcome', 'text'],

            // Hero Stats
            ['welcome_hero_stat_uptime_label', 'Uptime SLA', 'welcome', 'text'],
            ['welcome_hero_stat_speed_label', 'Max Speed', 'welcome', 'text'],
            ['welcome_hero_stat_support_label', 'Support', 'welcome', 'text'],
            ['welcome_hero_stat_price_label', 'Starts At', 'welcome', 'text'],

            // Plans Section (exclude dynamic content, only static)
            ['welcome_plans_eyebrow', 'Internet Plans', 'welcome', 'text'],
            ['welcome_plans_title', 'Pick Your Perfect Plan', 'welcome', 'text'],
            ['welcome_plans_subtitle', 'Transparent pricing. No hidden fees. Cancel anytime.', 'welcome', 'text'],

            // Features Section
            ['welcome_features_eyebrow', 'Why Choose Us', 'welcome', 'text'],
            ['welcome_features_title', 'Built Different', 'welcome', 'text'],
            ['welcome_features_subtitle', "We're not just another ISP. Here's what sets us apart.", 'welcome', 'textarea'],
            ['welcome_features_blazing_title', 'Blazing Speeds', 'welcome', 'text'],
            ['welcome_features_blazing_desc', 'Low latency, high throughput. Stream 4K, game online, and video call — all at the same time without a hitch.', 'welcome', 'textarea'],
            ['welcome_features_coverage_title', 'Wide Coverage', 'welcome', 'text'],
            ['welcome_features_coverage_desc', 'Our fiber network spans homes and businesses across the entire region. Reliable connectivity wherever you are.', 'welcome', 'textarea'],
            ['welcome_features_support_title', '24/7 Support', 'welcome', 'text'],
            ['welcome_features_support_desc', 'Real humans answer your calls. Technical assistance available around the clock, every day of the year.', 'welcome', 'textarea'],

            // Contact Section
            ['welcome_contact_eyebrow', 'Get In Touch', 'welcome', 'text'],
            ['welcome_contact_title', "Let's Get You Connected", 'welcome', 'text'],
            ['welcome_contact_subtitle', "Drop us a message and we'll reach out within 24 hours.", 'welcome', 'textarea'],
            ['welcome_contact_form_name_ph', 'Your Full Name', 'welcome', 'text'],
            ['welcome_contact_form_email_ph', 'Email Address', 'welcome', 'text'],
            ['welcome_contact_form_message_ph', 'Tell us how we can help you…', 'welcome', 'textarea'],
            ['welcome_contact_submit', 'Send Message →', 'welcome', 'text'],

            // Modal
            ['welcome_modal_welcome_back', 'Welcome Back', 'welcome', 'text'],
            ['welcome_modal_signin_sub', 'Sign in to your NetManager account', 'welcome', 'textarea'],
            ['welcome_modal_tab_signin', 'Sign In', 'welcome', 'text'],
            ['welcome_modal_tab_register', 'Create Account', 'welcome', 'text'],

            // Footer
            ['welcome_footer_brand', 'NetManager', 'welcome', 'text'],
            ['welcome_footer_desc', 'Your trusted partner for reliable internet. Fast, secure, and affordable connectivity for every home and business.', 'welcome', 'textarea'],
            ['welcome_footer_quicklinks_title', 'Quick Links', 'welcome', 'text'],
            ['welcome_footer_contact_title', 'Contact', 'welcome', 'text'],
            ['welcome_footer_copyright', '© 2024 ISP Billing Management System. All rights reserved.', 'welcome', 'text'],
        ];

        foreach ($welcomeSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting[0]],
                [
'content' => $setting[1],
                    'group' => $setting[2],
                    'type' => $setting[3],
                ]
            );
        }
    }
}

