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
        Schema::create('installments', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('application_id')
                ->constrained('applications', 'uuid')
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->unsignedBigInteger('amount');
            $table->timestamp('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
