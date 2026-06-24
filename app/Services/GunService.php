<?php

namespace App\Services;

use App\Enums\AvailabilityStatus;
use App\Enums\BulletMovementType;
use App\Models\BulletAddition;
use App\Models\BulletMovement;
use App\Models\BulletUsage;
use App\Models\Gun;
use App\Models\GunAssignment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GunService
{
    public function availableBullets(Gun $gun): int
    {
        if (! $gun->bulletMovements()->exists()) {
            return (int) ($gun->bullets ?? 0);
        }

        $in = (int) $gun->bulletMovements()->sum('quantity_in');
        $out = (int) $gun->bulletMovements()->sum('quantity_out');

        return max(0, $in - $out);
    }

    public function assignGun(Gun $gun, int $guardId, ?string $startDate, ?string $description, int $businessId, int $userId): GunAssignment
    {
        if ($gun->available === AvailabilityStatus::Unavailable) {
            throw new RuntimeException('This gun is already assigned.');
        }

        return DB::transaction(function () use ($gun, $guardId, $startDate, $description, $businessId, $userId): GunAssignment {
            $assignment = new GunAssignment;
            $assignment->forceFill([
                'gunId' => $gun->getKey(),
                'guardId' => $guardId,
                'start_date' => $startDate,
                'description' => $description,
                'status' => true,
                'businessId' => $businessId,
                'userId' => $userId,
            ])->save();

            $gun->forceFill(['available' => AvailabilityStatus::Unavailable->value])->save();

            return $assignment;
        });
    }

    public function removeAssignment(GunAssignment $assignment, ?string $endDate): void
    {
        DB::transaction(function () use ($assignment, $endDate): void {
            $assignment->forceFill([
                'status' => false,
                'end_date' => $endDate ?? now()->toDateString(),
            ])->save();

            $assignment->gun?->forceFill(['available' => AvailabilityStatus::Available->value])->save();
        });
    }

    public function addBullets(BulletAddition $addition): void
    {
        DB::transaction(function () use ($addition): void {
            $gun = Gun::query()->where('businessId', $addition->businessId)->findOrFail($addition->gunId);
            $date = is_string($addition->date) ? $addition->date : ($addition->date?->toDateString() ?? now()->toDateString());
            $this->ensureOpeningMovement($gun, $date, $addition->userId);

            $addition->save();

            $movement = new BulletMovement;
            $movement->forceFill([
                'gunId' => $addition->gunId,
                'date' => $addition->date,
                'quantity_in' => $addition->quantity,
                'quantity_out' => 0,
                'description' => $addition->description ?: 'Bullet addition',
                'type' => BulletMovementType::Addition->value,
                'tid' => $addition->getKey(),
                'businessId' => $addition->businessId,
                'userId' => $addition->userId,
            ])->save();
        });
    }

    public function recordUsage(BulletUsage $usage): void
    {
        $gun = Gun::query()->where('businessId', $usage->businessId)->findOrFail($usage->gunId);
        $date = is_string($usage->date) ? $usage->date : ($usage->date?->toDateString() ?? now()->toDateString());
        $this->ensureOpeningMovement($gun, $date, $usage->userId);

        if ($this->availableBullets($gun) < (int) $usage->quantity) {
            throw new RuntimeException('The selected gun does not have enough available bullets.');
        }

        DB::transaction(function () use ($usage): void {
            $usage->save();

            $movement = new BulletMovement;
            $movement->forceFill([
                'gunId' => $usage->gunId,
                'date' => $usage->date,
                'quantity_in' => 0,
                'quantity_out' => $usage->quantity,
                'description' => $usage->description ?: 'Bullet usage',
                'type' => BulletMovementType::Usage->value,
                'tid' => $usage->getKey(),
                'businessId' => $usage->businessId,
                'userId' => $usage->userId,
            ])->save();
        });
    }

    public function deleteAddition(BulletAddition $addition): void
    {
        DB::transaction(function () use ($addition): void {
            BulletMovement::query()
                ->where('businessId', $addition->businessId)
                ->where('type', BulletMovementType::Addition->value)
                ->where('tid', $addition->getKey())
                ->delete();

            $addition->delete();
        });
    }

    public function deleteUsage(BulletUsage $usage): void
    {
        DB::transaction(function () use ($usage): void {
            BulletMovement::query()
                ->where('businessId', $usage->businessId)
                ->where('type', BulletMovementType::Usage->value)
                ->where('tid', $usage->getKey())
                ->delete();

            $usage->delete();
        });
    }

    public function ensureOpeningMovement(Gun $gun, string $date, ?int $userId = null): void
    {
        if ($gun->bulletMovements()->exists() || (int) ($gun->bullets ?? 0) <= 0) {
            return;
        }

        $movement = new BulletMovement;
        $movement->forceFill([
            'gunId' => $gun->getKey(),
            'date' => $date,
            'quantity_in' => $gun->bullets,
            'quantity_out' => 0,
            'description' => 'Opening bullet balance',
            'type' => BulletMovementType::Opening->value,
            'tid' => $gun->getKey(),
            'businessId' => $gun->businessId,
            'userId' => $userId,
        ])->save();
    }
}
