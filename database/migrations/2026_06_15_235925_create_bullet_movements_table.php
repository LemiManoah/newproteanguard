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
        Schema::create('bullet_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gunId')->constrained('guns')->cascadeOnDelete();
            $table->date('date')->nullable()->index();
            $table->decimal('quantity_in', 15, 2)->default(0);
            $table->decimal('quantity_out', 15, 2)->default(0);
            $table->string('description')->nullable();
            $table->integer('type')->default(0)->comment('0=opening, 1=addition, 2=usage');
            $table->unsignedBigInteger('tid')->index();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'gunId', 'date']);
            $table->index(['businessId', 'type', 'tid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bullet_movements');
    }
};
