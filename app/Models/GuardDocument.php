<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $guardId
 * @property string|null $title
 * @property int $type
 * @property string|null $file
 * @property string $disk
 * @property string|null $path
 * @property string|null $original_name
 * @property bool $status
 * @property int|null $userId
 * @property int $businessId
 * @property Carbon|null $created_at
 */
#[Fillable([
    'guardId',
    'title',
    'type',
    'file',
    'disk',
    'path',
    'original_name',
])]
class GuardDocument extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'disk' => 'guard_documents',
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'guardId' => 'integer',
            'type' => 'integer',
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function securityGuard(): BelongsTo
    {
        return $this->belongsTo(SecurityGuard::class, 'guardId');
    }
}
