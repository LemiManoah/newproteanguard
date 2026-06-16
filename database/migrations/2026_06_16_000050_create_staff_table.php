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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->integer('gender')->nullable()->comment('0=male, 1=female');
            $table->string('contact1')->nullable();
            $table->string('contact2')->nullable();
            $table->string('nin')->nullable()->index();
            $table->string('address')->nullable();
            $table->date('dob')->nullable();
            $table->foreignId('positionId')->nullable()->constrained('staff_positions')->nullOnDelete();
            $table->foreignId('guardId')->nullable()->constrained('security_guards')->nullOnDelete();
            $table->foreignId('salaryCategoryId')->nullable()->constrained('salary_categories')->nullOnDelete();
            $table->decimal('salary', 15, 2)->nullable();
            $table->date('dop')->nullable()->index();
            $table->boolean('status')->default(true)->index();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'status']);
            $table->index(['businessId', 'guardId']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
