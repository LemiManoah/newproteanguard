<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $guardId
 * @property string|null $name
 * @property string|null $contact
 * @property string|null $residence
 * @property bool $status
 * @property int|null $userId
 * @property int $businessId
 */
#[Fillable([
    'guardId',
    'name',
    'contact',
    'residence',
])]
class GuardReferee extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'guardId' => 'integer',
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
