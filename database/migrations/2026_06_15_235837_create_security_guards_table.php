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
        Schema::create('security_guards', function (Blueprint $table) {
            $table->id();
            $table->integer('code_number')->nullable()->index();
            $table->string('code')->nullable()->index();
            $table->string('fname')->nullable();
            $table->string('lname')->nullable();
            $table->string('contact1')->nullable();
            $table->string('contact2')->nullable();
            $table->string('email')->nullable()->index();
            $table->date('dob')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->date('join_date')->nullable()->index();
            $table->integer('gender')->default(0)->comment('0=male, 1=female');
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('tribe')->nullable();
            $table->integer('marital_status')->default(0)->comment('0=single, 1=married');
            $table->string('address')->nullable();
            $table->string('home_contact')->nullable();
            $table->string('home_location')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('father_occupation')->nullable();
            $table->integer('fdeath_status')->default(0)->comment('0=alive, 1=deceased');
            $table->string('mother_name')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->integer('mdeath_status')->default(0)->comment('0=alive, 1=deceased');
            $table->string('nok')->nullable();
            $table->string('nok_contact')->nullable();
            $table->string('nok_relationship')->nullable();
            $table->string('nok_residence')->nullable();
            $table->integer('id_type')->default(0)->comment('0=national ID, 1=passport, 2=driving, 3=other');
            $table->string('id_no')->nullable()->index();
            $table->date('id_expiry')->nullable()->index();
            $table->string('languages')->nullable();
            $table->boolean('medical_history')->default(false)->comment('0=no, 1=yes');
            $table->string('medical_history_details')->nullable();
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive')->index();
            $table->date('left_date')->nullable()->index();
            $table->string('left_reason')->nullable();
            $table->boolean('assigned')->default(false)->comment('1=yes, 0=no')->index();
            $table->boolean('doc_verified')->default(false)->comment('1=yes, 0=no')->index();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index(['businessId', 'status', 'assigned']);
            $table->index(['businessId', 'code_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_guards');
    }
};
