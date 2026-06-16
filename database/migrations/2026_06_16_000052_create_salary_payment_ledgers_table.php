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
        Schema::create('salary_payment_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffId')->constrained('staff')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('date')->nullable()->index();
            $table->foreignId('mode')->constrained('payment_modes')->restrictOnDelete();
            $table->foreignId('salaryId')->constrained('salary_payments')->cascadeOnDelete();
            $table->tinyInteger('channel')->default(0)->comment('Legacy unused/default channel');
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'staffId', 'salaryId']);
            $table->index(['businessId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payment_ledgers');
    }
};
