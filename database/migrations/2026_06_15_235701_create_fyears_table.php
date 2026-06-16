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
        Schema::create('fyears', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start')->index();
            $table->date('end')->index();
            $table->boolean('status')->default(true)->index();
            $table->boolean('Active')->default(true)->index();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fyears');
    }
};
