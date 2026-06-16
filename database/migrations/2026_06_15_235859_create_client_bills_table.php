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
        Schema::create('client_bills', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice')->index();
            $table->integer('invoice_number')->index();
            $table->foreignId('clientId')->constrained('clients')->cascadeOnDelete();
            $table->integer('bill_cycle')->nullable()->comment('Monthly, annual, etc.');
            $table->decimal('cycles', 10, 2)->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->date('start_date')->nullable()->index();
            $table->date('date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['businessId', 'clientId', 'status']);
            $table->index(['businessId', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_bills');
    }
};
