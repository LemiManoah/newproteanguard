<?php

namespace App\Enums;

enum IdentityDocumentType: int
{
    case NationalId = 0;
    case Passport = 1;
    case DrivingLicense = 2;
    case Other = 3;

    public function label(): string
    {
        return match ($this) {
            self::NationalId => 'National ID',
            self::Passport => 'Passport',
            self::DrivingLicense => 'Driving License',
            self::Other => 'Other',
        };
    }
}
