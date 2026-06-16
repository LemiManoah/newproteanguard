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
        Schema::create('inventory_stock_usages', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->index();
            $table->foreignId('itemId')->constrained('inventory_items')->cascadeOnDelete();
            $table->foreignId('guardId')->nullable()->constrained('security_guards')->nullOnDelete();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'itemId', 'date']);
            $table->index(['businessId', 'guardId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_usages');
    }
};
