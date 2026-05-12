<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('magic_login_token')->nullable()->after('portal_temp_password');
            $table->timestamp('magic_token_expires_at')->nullable()->after('magic_login_token');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['magic_login_token', 'magic_token_expires_at']);
        });
    }
};
