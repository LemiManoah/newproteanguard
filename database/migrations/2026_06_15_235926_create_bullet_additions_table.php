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
        Schema::create('bullet_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gunId')->constrained('guns')->cascadeOnDelete();
            $table->date('date')->nullable()->index();
            $table->decimal('quantity', 15, 2)->nullable();
            $table->string('brought_by')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'gunId', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bullet_additions');
    }
};
