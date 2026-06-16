<?php

namespace App\Models;

use App\Enums\CoaCategoryName;
use App\Enums\CoaCategoryType;
use App\Enums\DebitCredit;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'type',
    'dr_cr',
    'code_number',
])]
class CoaCategory extends Model
{
    use BelongsToBusiness, HasFactory;

    protected function casts(): array
    {
        return [
            'name' => CoaCategoryName::class,
            'type' => CoaCategoryType::class,
            'dr_cr' => DebitCredit::class,
            'code_number' => 'integer',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'categoryId');
    }
}
