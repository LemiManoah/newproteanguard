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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->integer('category')->default(0)->comment('0=all, 1=staff, 2=security guards');
            $table->integer('month')->index();
            $table->integer('year')->index();
            $table->date('approval_date')->nullable()->index();
            $table->date('review_date')->nullable()->index();
            $table->string('review_comment')->nullable();
            $table->string('approval_comment')->nullable();
            $table->integer('status')->default(0)->comment('0=pending, 1=reviewed, 2=approved, 3=rejected');
            $table->decimal('guard_overtime', 15, 2)->default(0);
            $table->decimal('savings', 15, 2)->default(0);
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['businessId', 'month', 'year', 'category']);
            $table->index(['businessId', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
