<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'country_name',
    'country_code',
    'dial_code',
    'currency_code',
    'currency',
    'capital_city',
    'country_dormain',
])]
class Country extends Model
{
    public $timestamps = false;
}
