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
        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clientId')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('title')->nullable();
            $table->integer('type')->comment('0=profile photo, 1=ID, 2=LC letter, 3=other');
            $table->string('file')->nullable()->comment('Legacy public filename/path');
            $table->string('disk')->default('client_documents');
            $table->string('path')->nullable();
            $table->string('original_name')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive')->index();
            $table->timestamps();

            $table->index(['businessId', 'clientId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_documents');
    }
};
