<?php

namespace App\Models;

use App\Enums\GuardGender;
use App\Enums\IdentityDocumentType;
use App\Enums\LifeStatus;
use App\Enums\MaritalStatus;
use App\Models\Concerns\BelongsToBusiness;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $code_number
 * @property string|null $code
 * @property string|null $fname
 * @property string|null $lname
 * @property string|null $contact1
 * @property string|null $contact2
 * @property string|null $email
 * @property Carbon|null $dob
 * @property Carbon|null $join_date
 * @property GuardGender $gender
 * @property MaritalStatus $marital_status
 * @property string|null $address
 * @property string|null $nok
 * @property string|null $nok_contact
 * @property string|null $nok_relationship
 * @property IdentityDocumentType $id_type
 * @property string|null $id_no
 * @property Carbon|null $id_expiry
 * @property bool $status
 * @property bool $assigned
 * @property int|null $userId
 * @property int $businessId
 */
#[Fillable([
    'code_number',
    'code',
    'fname',
    'lname',
    'contact1',
    'contact2',
    'email',
    'dob',
    'weight',
    'height',
    'join_date',
    'gender',
    'nationality',
    'religion',
    'tribe',
    'marital_status',
    'address',
    'home_contact',
    'home_location',
    'father_name',
    'father_contact',
    'father_occupation',
    'fdeath_status',
    'mother_name',
    'mother_contact',
    'mother_occupation',
    'mdeath_status',
    'nok',
    'nok_contact',
    'nok_relationship',
    'nok_residence',
    'id_type',
    'id_no',
    'id_expiry',
    'languages',
    'medical_history',
    'medical_history_details',
    'left_date',
    'left_reason',
    'photo',
])]
class SecurityGuard extends Model
{
    use BelongsToBusiness, HasFactory;

    protected $attributes = [
        'gender' => 0,
        'marital_status' => 0,
        'fdeath_status' => 0,
        'mdeath_status' => 0,
        'id_type' => 0,
        'medical_history' => false,
        'status' => true,
        'assigned' => false,
        'doc_verified' => false,
    ];

    protected function casts(): array
    {
        return [
            'code_number' => 'integer',
            'dob' => 'date',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'join_date' => 'date',
            'gender' => GuardGender::class,
            'marital_status' => MaritalStatus::class,
            'fdeath_status' => LifeStatus::class,
            'mdeath_status' => LifeStatus::class,
            'id_type' => IdentityDocumentType::class,
            'id_expiry' => 'date',
            'medical_history' => 'boolean',
            'status' => 'boolean',
            'left_date' => 'date',
            'assigned' => 'boolean',
            'doc_verified' => 'boolean',
            'userId' => 'integer',
            'businessId' => 'integer',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(ClientGuard::class, 'guardId');
    }

    public function activeClients(): HasMany
    {
        return $this->clients()->where('status', true);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(ClientGuardAttendance::class, 'guardId');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(GuardDocument::class, 'guardId');
    }

    public function activeDocuments(): HasMany
    {
        return $this->documents()->where('status', true);
    }

    public function referees(): HasMany
    {
        return $this->hasMany(GuardReferee::class, 'guardId');
    }

    public function activeReferees(): HasMany
    {
        return $this->referees()->where('status', true);
    }

    public function getNameAttribute(): string
    {
        return trim(($this->fname ?? '').' '.($this->lname ?? '')).' ('.($this->code ?? '').')';
    }
}
