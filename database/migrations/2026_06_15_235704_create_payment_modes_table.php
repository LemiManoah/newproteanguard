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
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('e.g Edson');
            $table->string('type')->comment('e.g Bank');
            $table->string('type_name')->nullable()->comment('e.g Stanbic or MTN');
            $table->string('account')->nullable()->comment('e.g 0774367210');
            $table->decimal('opening_balance', 15, 2)->nullable();
            $table->boolean('status')->default(true)->comment('0=deleted 1=active')->index();
            $table->boolean('is_default')->default(false)->comment('0=no 1=yes')->index();
            $table->foreignId('businessId')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_modes');
    }
};
