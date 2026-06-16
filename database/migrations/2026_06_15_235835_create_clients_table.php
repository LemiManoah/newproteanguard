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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoryId')->constrained('client_categories')->cascadeOnDelete();
            $table->string('name')->nullable()->index();
            $table->string('contact1')->nullable();
            $table->string('contact2')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('id_no')->nullable()->index();
            $table->string('tin')->nullable()->index();
            $table->string('vat_no')->nullable();
            $table->string('address')->nullable();
            $table->integer('billing_cycle')->default(0)->comment('0=monthly, 1=quarterly, 2=annual, 3=one time');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('no_guards', 10, 2)->default(1);
            $table->decimal('actual_guards', 10, 2)->nullable();
            $table->date('bill_start')->nullable()->index();
            $table->integer('schedule_type')->default(2)->comment('0=day, 1=night, 2=full time');
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive')->index();
            $table->boolean('assigned')->default(false)->comment('1=yes, 0=no')->index();
            $table->timestamps();

            $table->index(['businessId', 'status', 'assigned']);
            $table->index(['businessId', 'billing_cycle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
