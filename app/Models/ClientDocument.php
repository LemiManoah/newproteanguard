<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
