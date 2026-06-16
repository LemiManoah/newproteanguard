<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $action
 * @property int|null $userId
 * @property int $businessId
 * @property bool $status
 * @property Carbon|null $created_at
 */
#[Fillable([
    'action',
])]
class AuditLog extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $table = 'logs';

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }
}
