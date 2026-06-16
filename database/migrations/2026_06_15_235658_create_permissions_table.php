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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('userId')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('businessId')->constrained('businesses')->cascadeOnDelete();
            $table->boolean('status')->default(true)->index();
            $table->boolean('view_users')->default(false);
            $table->boolean('add_users')->default(false);
            $table->boolean('edit_users')->default(false);
            $table->boolean('delete_users')->default(false);
            $table->boolean('manage_permission')->default(false);
            $table->boolean('view_logs')->default(false);
            $table->boolean('view_guards')->default(false);
            $table->boolean('add_guards')->default(false);
            $table->boolean('edit_guards')->default(false);
            $table->boolean('delete_guards')->default(false);
            $table->boolean('view_clients')->default(false);
            $table->boolean('add_client')->default(false);
            $table->boolean('edit_clients')->default(false);
            $table->boolean('delete_clients')->default(false);
            $table->boolean('assign_guards')->default(false);
            $table->boolean('view_client_schedule')->default(false);
            $table->boolean('manage_attendance')->default(false);
            $table->boolean('manage_guns')->default(false);
            $table->boolean('view_client_bills')->default(false);
            $table->boolean('generate_client_bills')->default(false);
            $table->boolean('delete_client_bills')->default(false);
            $table->boolean('print_client_bills')->default(false);
            $table->boolean('view_client_balances')->default(false);
            $table->boolean('record_client_payments')->default(false);
            $table->boolean('view_client_payments')->default(false);
            $table->boolean('view_expenses')->default(false);
            $table->boolean('record_expenses')->default(false);
            $table->boolean('edit_expenses')->default(false);
            $table->boolean('delete_expenses')->default(false);
            $table->boolean('view_staff')->default(false);
            $table->boolean('add_staff')->default(false);
            $table->boolean('edit_staff')->default(false);
            $table->boolean('delete_staff')->default(false);
            $table->boolean('manage_staff_positions')->default(false);
            $table->boolean('view_payroll')->default(false);
            $table->boolean('generate_payroll')->default(false);
            $table->boolean('export_payroll')->default(false);
            $table->boolean('approve_payroll')->default(false);
            $table->boolean('view_staff_payments')->default(false);
            $table->boolean('Record_staff_payments')->default(false);
            $table->boolean('manage_staff_deductions')->default(false);
            $table->boolean('manage_salary_categories')->default(false);
            $table->boolean('manage_inventory')->default(false);
            $table->boolean('add_paymodes')->default(false);
            $table->boolean('view_paymodes')->default(false);
            $table->boolean('edit_paymodes')->default(false);
            $table->boolean('delete_paymodes')->default(false);
            $table->boolean('record_money_transfer')->default(false);
            $table->boolean('view_mode_statement')->default(false);
            $table->boolean('mange_client_categories')->default(false);
            $table->boolean('view_dashboard')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
