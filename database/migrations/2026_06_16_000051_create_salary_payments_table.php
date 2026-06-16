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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffId')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('payrollId')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->integer('month')->index();
            $table->integer('year')->index();
            $table->decimal('salary', 15, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->decimal('savings', 15, 2)->default(0);
            $table->decimal('days_worked', 10, 2)->default(0);
            $table->decimal('overtime_worked', 10, 2)->default(0);
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'payrollId']);
            $table->index(['businessId', 'staffId', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
