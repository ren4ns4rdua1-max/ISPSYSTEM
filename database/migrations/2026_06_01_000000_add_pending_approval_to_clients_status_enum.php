<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix for: "1256 Data truncated for column status" warning
     * when submitting application from welcome.blade.php plan section.
     * 
     * The status ENUM was missing 'pending_approval' value which is used
     * when guests submit new applications via the welcome page.
     */
    public function up(): void
    {
        // Check if we're using MySQL (ENUM is MySQL-specific)
        if (DB::getDriverName() === 'mysql') {
            // First, change ENUM to a larger VARCHAR to avoid enum set constraint during alter
            DB::statement("ALTER TABLE clients MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending_approval'");
            
            // Then add the enum values as a CHECK constraint (MySQL 8.0.16+)
            // For older MySQL versions, we just use VARCHAR which now allows any value
            // The application layer validates status values anyway
        }
        
        // Alternative: If you want strict ENUM after this, use:
        // DB::statement("ALTER TABLE clients MODIFY status ENUM('active', 'inactive', 'suspended', 'cancelled', 'pending_approval') NOT NULL DEFAULT 'pending_approval'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert to original ENUM (this will fail if any pending_approval records exist)
            // We leave this as VARCHAR to avoid data loss
            DB::statement("ALTER TABLE clients MODIFY status VARCHAR(20) NOT NULL DEFAULT 'active'");
        }
    }
};
