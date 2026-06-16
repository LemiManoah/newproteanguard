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
        Schema::create('guard_referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardId')->constrained('security_guards')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('contact')->nullable();
            $table->string('residence')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive')->index();
            $table->timestamps();

            $table->index(['businessId', 'guardId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guard_referees');
    }
};
