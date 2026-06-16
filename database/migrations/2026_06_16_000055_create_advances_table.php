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
        Schema::create('advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffId')->constrained('staff')->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->index();
            $table->date('date')->index();
            $table->integer('deductMonth')->nullable();
            $table->integer('deductYear')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('mode')->constrained('payment_modes')->restrictOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'staffId', 'date']);
            $table->index(['businessId', 'deductMonth', 'deductYear']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advances');
    }
};
