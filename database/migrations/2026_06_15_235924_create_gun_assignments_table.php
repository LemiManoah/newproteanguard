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
        Schema::create('gun_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gunId')->constrained('guns')->cascadeOnDelete();
            $table->foreignId('guardId')->constrained('security_guards')->cascadeOnDelete();
            $table->date('start_date')->nullable()->index();
            $table->date('end_date')->nullable()->index();
            $table->string('description')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['businessId', 'gunId', 'status']);
            $table->index(['businessId', 'guardId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gun_assignments');
    }
};
