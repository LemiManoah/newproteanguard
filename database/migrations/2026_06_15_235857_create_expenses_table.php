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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('date')->nullable()->index();
            $table->foreignId('modeId')->constrained('payment_modes')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->foreignId('categoryId')->constrained('expense_categories')->restrictOnDelete();
            $table->foreignId('yearId')->constrained('fyears')->restrictOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'date']);
            $table->index(['businessId', 'categoryId', 'date']);
            $table->index(['businessId', 'modeId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
