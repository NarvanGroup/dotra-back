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
        Schema::create('applications', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('customer_id')
                ->constrained('customers', 'uuid')
                ->cascadeOnDelete();
            $table->foreignUuid('vendor_id')
                ->constrained('vendors', 'uuid')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('total_amount');
            $table->unsignedSmallInteger('number_of_installments');
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->unsignedBigInteger('suggested_total_amount');
            $table->unsignedSmallInteger('suggested_number_of_installments');
            $table->decimal('suggested_interest_rate', 5, 2)->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
