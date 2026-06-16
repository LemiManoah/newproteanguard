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
        Schema::create('guns', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable()->index();
            $table->string('mark_number')->nullable()->index();
            $table->integer('bullets')->nullable();
            $table->integer('owner')->default(0)->comment('0=owned, 1=hired');
            $table->integer('available')->default(1)->comment('0=no, 1=yes');
            $table->string('vendor_name')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['businessId', 'status', 'available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guns');
    }
};
