<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create technicians table
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('specialization')->nullable(); // Installation, Repair, Both
            $table->string('area_coverage')->nullable(); // Areas they serve
            $table->enum('status', ['available', 'busy', 'offduty'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Create installation_jobs table
        Schema::create('installation_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('job_type', ['new_installation', 'repair', 'reconnection', 'upgrade', 'transfer'])->default('new_installation');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
        });

        // Add technician fields to clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->enum('installation_status', ['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('pending')->after('status');
            $table->dateTime('installation_date')->nullable()->after('installation_status');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['technician_id']);
            $table->dropColumn(['technician_id', 'installation_status', 'installation_date']);
        });

        Schema::dropIfExists('installation_jobs');
        Schema::dropIfExists('technicians');
    }
};
