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
        Schema::create('sent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receiver')->nullable()->index();
            $table->string('message_type')->nullable()->index();
            $table->text('message')->nullable();
            $table->unsignedSmallInteger('size')->nullable()->index();
            $table->string('message_id')->nullable()->index();
            $table->foreignId('businessId')->nullable()->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_messages');
    }
};
