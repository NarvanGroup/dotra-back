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
        Schema::create('customer_vendor', function (Blueprint $table): void {
            $table->uuid('customer_id');
            $table->uuid('vendor_id');
            $table->timestamps();

            $table->primary(['customer_id', 'vendor_id']);
            $table->foreign('customer_id')
                ->references('uuid')
                ->on('customers')
                ->onDelete('cascade');
            $table->foreign('vendor_id')
                ->references('uuid')
                ->on('vendors')
                ->onDelete('cascade');

            $table->index('customer_id');
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_vendor');
    }
};
