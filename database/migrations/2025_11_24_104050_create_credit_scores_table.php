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
        Schema::create('credit_scores', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('customer_id');
            $table->uuidMorphs('initiator');
            $table->date('issued_on');
            $table->string('status')->default('pending');
            $table->unsignedSmallInteger('overall_score')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('uuid')
                ->on('customers')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_scores');
    }
};

