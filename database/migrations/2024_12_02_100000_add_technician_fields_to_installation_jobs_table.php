<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('installation_jobs', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('completion_notes');
            $table->string('mac_address')->nullable()->after('ip_address');
            $table->string('router_ssid')->nullable()->after('mac_address');
            $table->string('router_password')->nullable()->after('router_ssid');
            $table->json('speed_test_result')->nullable()->after('router_password');
            $table->text('materials_used')->nullable()->after('speed_test_result');
            $table->string('proof_image')->nullable()->after('materials_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installation_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
                'mac_address',
                'router_ssid',
                'router_password',
                'speed_test_result',
                'materials_used',
                'proof_image',
            ]);
        });
    }
};

