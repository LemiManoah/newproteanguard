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
        Schema::create('client_guard_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deploymentId')->nullable()->constrained('client_guards')->nullOnDelete();
            $table->foreignId('clientId')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('guardId')->nullable()->constrained('security_guards')->nullOnDelete();
            $table->foreignId('replacedBy')->nullable()->constrained('security_guards')->nullOnDelete();
            $table->date('date')->nullable()->index();
            $table->integer('schedule_type')->default(2)->comment('0=day, 1=night, 2=full time');
            $table->integer('attended')->default(1)->comment('0=absent, 1=present, 2=replaced');
            $table->integer('absentCategory')->nullable()->comment('0=sick, 1=leave, 2=special duty, 3=unknown');
            $table->string('reason')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['businessId', 'date']);
            $table->index(['businessId', 'guardId', 'date']);
            $table->index(['businessId', 'clientId', 'date']);
            $table->index(['businessId', 'attended']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_guard_attendances');
    }
};
