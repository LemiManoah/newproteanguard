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
 * @property int|null $clientId
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
    'clientId',
    'title',
    'type',
    'file',
    'disk',
    'path',
    'original_name',
])]
class ClientDocument extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'disk' => 'client_documents',
        'status' => true,
    ];

    protected function casts(): array
    {
        return [
            'clientId' => 'integer',
            'type' => 'integer',
            'status' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'clientId');
    }
}
