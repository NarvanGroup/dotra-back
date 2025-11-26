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
        Schema::create('vendors', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('mobile')->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('type')->nullable();
            $table->string('reffered_from')->nullable();
            $table->string('national_code')->unique();
            $table->string('owner_first_name')->nullable();
            $table->string('owner_last_name')->nullable();
            $table->date('owner_birth_date')->nullable();
            $table->string('business_license_code')->nullable();
            $table->string('website_url')->nullable();
            $table->string('industry')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
