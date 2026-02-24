<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_rates', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name');
            $table->string('plan_type'); // e.g., Residential, Business, Prepaid, Postpaid
            $table->string('speed'); // e.g., "10 Mbps", "100 Mbps"
            $table->decimal('monthly_fee', 10, 2);
            $table->decimal('installation_fee', 10, 2)->default(0);
            $table->decimal('activation_fee', 10, 2)->default(0);
            $table->decimal('router_fee', 10, 2)->default(0);
            $table->string('billing_cycle'); // e.g., Monthly, Quarterly, Yearly
            $table->integer('lock_in_period')->nullable(); // in months
            $table->decimal('late_penalty', 10, 2)->default(0);
            $table->decimal('reconnection_fee', 10, 2)->default(0);
            $table->string('data_limit')->nullable(); // e.g., "Unlimited", "100GB"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_rates');
    }
};
