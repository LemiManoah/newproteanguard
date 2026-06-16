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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoryId')->constrained('inventory_categories')->restrictOnDelete();
            $table->foreignId('unitId')->constrained('units')->restrictOnDelete();
            $table->string('name')->index();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('opening_stock', 15, 2)->default(0);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('last_buying_price', 15, 2)->default(0);
            $table->boolean('status')->default(true)->index();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'categoryId', 'status']);
            $table->index(['businessId', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
