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
        Schema::create('coa_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable()->index();
            $table->foreignId('coa')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->unsignedBigInteger('clientId')->nullable()->index();
            $table->unsignedBigInteger('staffId')->nullable()->index();
            $table->string('ref_no')->index();
            $table->decimal('dr_amount', 15, 2)->default(0);
            $table->decimal('cr_amount', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->string('type')->nullable()->index();
            $table->unsignedBigInteger('txnId')->index();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'date']);
            $table->index(['businessId', 'type', 'txnId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coa_transactions');
    }
};
