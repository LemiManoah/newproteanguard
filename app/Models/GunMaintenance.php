<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'gunId',
    'date',
    'work_by',
    'description',
])]
class GunMaintenance extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'gunId' => 'integer',
            'date' => 'date',
            'businessId' => 'integer',
            'userId' => 'integer',
        ];
    }

    public function gun(): BelongsTo
    {
        return $this->belongsTo(Gun::class, 'gunId');
    }
}
