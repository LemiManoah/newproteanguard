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
        Schema::create('client_guards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clientId')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('guardId')->constrained('security_guards')->cascadeOnDelete();
            $table->date('from')->nullable()->index();
            $table->date('to')->nullable()->index();
            $table->boolean('status')->default(true)->index();
            $table->integer('schedule_type')->default(2)->comment('0=day, 1=night, 2=full time');
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'clientId', 'status']);
            $table->index(['businessId', 'guardId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_guards');
    }
};
