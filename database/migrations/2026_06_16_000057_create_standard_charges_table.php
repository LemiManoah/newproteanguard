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
        Schema::create('standard_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staffId')->constrained('staff')->cascadeOnDelete();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('mma', 15, 2)->default(0);
            $table->decimal('rent', 15, 2)->default(0);
            $table->decimal('uniform', 15, 2)->default(0);
            $table->decimal('payee', 15, 2)->default(0);
            $table->decimal('nssf', 15, 2)->default(0);
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['businessId', 'staffId', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_charges');
    }
};
