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
        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2)->nullable();
            $table->foreignId('categoryId')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignId('yearId')->constrained('fyears')->cascadeOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->unique(['businessId', 'categoryId', 'yearId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_budgets');
    }
};
