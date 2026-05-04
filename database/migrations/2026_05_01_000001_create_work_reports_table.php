<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('installation_job_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained()->onDelete('cascade');
            $table->text('work_performed');
            $table->text('materials_used')->nullable();
            $table->text('issues_encountered')->nullable();
            $table->string('proof_image')->nullable();
            $table->timestamp('completion_time')->nullable();
            $table->timestamps();
        });

        Schema::table('installation_jobs', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('completion_notes');
            $table->string('mac_address')->nullable()->after('ip_address');
            $table->string('router_ssid')->nullable()->after('mac_address');
            $table->string('router_password')->nullable()->after('router_ssid');
            $table->enum('fail_reason', ['no_show', 'equipment_issue', 'access_denied', 'other'])->nullable()->after('router_password');
            $table->dateTime('rescheduled_date')->nullable()->after('fail_reason');
        });

        if (!Schema::hasColumn('technicians', 'user_id')) {
            Schema::table('technicians', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('work_reports');

        Schema::table('installation_jobs', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'mac_address', 'router_ssid', 'router_password', 'fail_reason', 'rescheduled_date']);
        });
    }
};
