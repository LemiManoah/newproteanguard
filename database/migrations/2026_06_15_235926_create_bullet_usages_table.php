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
        Schema::create('bullet_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gunId')->constrained('guns')->cascadeOnDelete();
            $table->foreignId('guardId')->nullable()->constrained('security_guards')->nullOnDelete();
            $table->date('date')->nullable()->index();
            $table->decimal('quantity', 15, 2)->nullable();
            $table->string('description')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'gunId', 'date']);
            $table->index(['businessId', 'guardId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bullet_usages');
    }
};
