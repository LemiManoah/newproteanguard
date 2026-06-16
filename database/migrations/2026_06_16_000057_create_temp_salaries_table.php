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
        Schema::create('temp_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffId')->constrained('staff')->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('percent', 8, 2)->nullable();
            $table->foreignId('salaryId')->constrained('salary_payments')->cascadeOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'staffId', 'salaryId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_salaries');
    }
};
