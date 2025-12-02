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
        Schema::create('contracts', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')
                ->constrained('applications', 'id')
                ->cascadeOnDelete();
            $table->foreignUuid('contract_template_id')
                ->nullable()
                ->constrained('contract_templates', 'id')
                ->nullOnDelete();
            $table->text('contract_text')
                ->nullable();
            $table->boolean('signed_by_customer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
