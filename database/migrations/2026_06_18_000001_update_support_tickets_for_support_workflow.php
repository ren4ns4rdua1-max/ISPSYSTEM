<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            // Technician assignment/workflow
            $table->unsignedBigInteger('technician_id')->nullable()->after('client_id');
            $table->timestamp('assigned_at')->nullable()->after('technician_id');

            // Technician work
            $table->longText('troubleshooting_notes')->nullable()->after('priority');
            $table->longText('solution')->nullable()->after('troubleshooting_notes');

            // Resolution workflow
            // status values already exist in app as: open, in_progress, resolved, closed
            $table->timestamp('resolved_at')->nullable()->after('replied_at');
            $table->timestamp('client_confirmed_at')->nullable()->after('resolved_at');
            $table->timestamp('closed_at')->nullable()->after('client_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropColumn([
                'technician_id',
                'assigned_at',
                'troubleshooting_notes',
                'solution',
                'resolved_at',
                'client_confirmed_at',
                'closed_at',
            ]);
        });
    }
};

