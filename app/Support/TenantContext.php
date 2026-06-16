<?php

namespace App\Support;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class TenantContext
{
    public function user(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            throw new AuthorizationException('Authentication is required.');
        }

        return $user;
    }

    public function businessId(): int
    {
        $businessId = $this->user()->businessId;

        if (! $businessId) {
            throw new AuthorizationException('A business context is required.');
        }

        return $businessId;
    }

    public function business(): Business
    {
        $business = $this->user()->business;

        if (! $business instanceof Business) {
            throw new AuthorizationException('A business context is required.');
        }

        return $business;
    }
}
