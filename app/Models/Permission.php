<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int|null $userId
 * @property int $businessId
 * @property bool $status
 */
#[Fillable([
    'name',
])]
class Permission extends Model
{
    use BelongsToBusiness, HasFactory;

    public const PERMISSION_COLUMNS = [
        'view_users',
        'add_users',
        'edit_users',
        'delete_users',
        'manage_permission',
        'view_logs',
        'view_guards',
        'add_guards',
        'edit_guards',
        'delete_guards',
        'view_clients',
        'add_client',
        'edit_clients',
        'delete_clients',
        'assign_guards',
        'view_client_schedule',
        'manage_attendance',
        'manage_guns',
        'view_client_bills',
        'generate_client_bills',
        'delete_client_bills',
        'print_client_bills',
        'view_client_balances',
        'record_client_payments',
        'view_client_payments',
        'view_expenses',
        'record_expenses',
        'edit_expenses',
        'delete_expenses',
        'view_staff',
        'add_staff',
        'edit_staff',
        'delete_staff',
        'manage_staff_positions',
        'view_payroll',
        'generate_payroll',
        'export_payroll',
        'approve_payroll',
        'view_staff_payments',
        'Record_staff_payments',
        'manage_staff_deductions',
        'manage_salary_categories',
        'manage_inventory',
        'add_paymodes',
        'view_paymodes',
        'edit_paymodes',
        'delete_paymodes',
        'record_money_transfer',
        'view_mode_statement',
        'mange_client_categories',
        'view_dashboard',
    ];

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return array_merge([
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ], array_fill_keys(self::PERMISSION_COLUMNS, 'boolean'));
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'roleId');
    }
}
