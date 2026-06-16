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
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->index();
            $table->decimal('from', 15, 2)->nullable();
            $table->decimal('to', 15, 2)->nullable();
            $table->foreignId('itemId')->nullable()->constrained('inventory_items')->nullOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->nullable()->constrained('businesses')->nullOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'itemId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliations');
    }
};
