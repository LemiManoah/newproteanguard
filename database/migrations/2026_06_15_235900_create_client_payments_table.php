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
        Schema::create('client_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('receipt')->index();
            $table->integer('receipt_number')->index();
            $table->foreignId('clientId')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('modeId')->constrained('payment_modes')->restrictOnDelete();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('payment_date')->nullable()->index();
            $table->string('ref_number')->nullable()->index();
            $table->string('description')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['businessId', 'clientId', 'status']);
            $table->index(['businessId', 'receipt_number']);
            $table->index(['businessId', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_payments');
    }
};
