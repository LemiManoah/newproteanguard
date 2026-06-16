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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
            $table->string('contact')->nullable()->after('username');
            $table->foreignId('roleId')->nullable()->after('password')->constrained('permissions')->nullOnDelete();
            $table->foreignId('businessId')->nullable()->after('roleId')->constrained('businesses')->nullOnDelete();
            $table->foreignId('userId')->nullable()->after('businessId')->constrained('users')->nullOnDelete();
            $table->boolean('status')->default(true)->after('userId')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['roleId']);
            $table->dropForeign(['businessId']);
            $table->dropForeign(['userId']);
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'contact', 'roleId', 'businessId', 'userId', 'status']);
        });
    }
};
