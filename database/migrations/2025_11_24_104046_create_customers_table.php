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
        Schema::create('customers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('national_code')->unique();
            $table->string('mobile');
            $table->nullableUuidMorphs('creator');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('otp')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('mobile');
            $table->index('national_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
