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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'key')) {
                $table->string('key')->unique()->after('id');
            }
            if (!Schema::hasColumn('settings', 'group')) {
                $table->string('group')->default('general')->after('content');
            }
            if (!Schema::hasColumn('settings', 'type')) {
                $table->enum('type', ['text', 'textarea', 'html'])->default('text')->after('group');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'key')) {
                $table->dropColumn('key');
            }
            if (Schema::hasColumn('settings', 'group')) {
                $table->dropColumn('group');
            }
            if (Schema::hasColumn('settings', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
